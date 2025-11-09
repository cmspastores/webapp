<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Bill;
use App\Models\BillCharge; // 🔹 kept in case needed elsewhere
use App\Models\Payment;
use App\Models\PaymentItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class BillController extends Controller
{
    // list bills (paginated) ✅ updated for search & sort & status filter
    public function index(Request $request)
    {
        $billsQuery = Bill::with(['renter', 'room', 'agreement']);

        // 🔍 Search by renter name or room number
        if ($request->filled('search')) {
            $search = $request->input('search');
            $billsQuery = $billsQuery->whereHas('renter', fn($q) => $q->where('full_name', 'like', "%{$search}%"))
                                     ->orWhereHas('room', fn($q) => $q->where('room_number', 'like', "%{$search}%"));
        }

        // 🔹 Filter by status (unpaid / paid)
        if ($request->filled('status')) {
            $status = $request->input('status');
            $billsQuery = $billsQuery->where('status', $status);
        }

        // ↕️ Sort by period_start
        if ($request->input('sort') === 'asc') {
            $billsQuery = $billsQuery->orderBy('period_start', 'asc');
        } else { // default desc
            $billsQuery = $billsQuery->orderBy('period_start', 'desc');
        }

        // 🔹 Paginate
        $bills = $billsQuery->paginate(15)->appends($request->query());

        // 🔹 NEW: Separate for ribbon tables
        $monthlyBills = $bills->filter(fn($bill) => !optional($bill->room->roomType)->is_transient && ($bill->agreement->rate_unit ?? '') !== 'daily');
        $transientBills = $bills->filter(fn($bill) => optional($bill->room->roomType)->is_transient || ($bill->agreement->rate_unit ?? '') === 'daily');

        return view('bills.index', compact('bills', 'monthlyBills', 'transientBills'));
    }

    // show single bill / statement
    public function show(Bill $bill)
    {
        $bill->load('charges', 'agreement', 'room', 'renter');
        return view('bills.show', compact('bill'));
    }

    // form to choose billing period (month) and generate bills
    public function create()
    {
        $agreements = Agreement::where('is_active', true)->orderBy('start_date')->get();
        return view('bills.create', compact('agreements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer|min:2000',
            'month' => 'required|integer|between:1,12',
            'agreement_id' => 'nullable|exists:agreements,agreement_id',
        ]);

        // require an agreement for the "generate for selected agreement" flow
        if (empty($data['agreement_id'])) {
            // Redirect to create with a clear warning — do not silently refresh
            return redirect()->route('bills.create')
                ->withInput()
                ->with('warning', 'Please select an agreement to generate a bill for a specific agreement. To generate bills for all dorm agreements use the "Generate All Dorms" button.');
        }

        $year = (int)$data['year'];
        $month = (int)$data['month'];
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();
        $created = 0;
        $skipped = 0;
        $hasBaseAmountColumn = Schema::hasColumn('bills', 'base_amount');

        // Single agreement flow
        $agreement = Agreement::findOrFail($data['agreement_id']);
        if (! $agreement->is_active || $agreement->start_date > $periodEnd->toDateString() || $agreement->end_date < $periodStart->toDateString()) {
            return back()->withInput()->withErrors(['agreement_id' => 'Selected agreement is not active for that period.']);
        }

        $this->createBillForAgreement($agreement, $periodStart, $periodEnd, $hasBaseAmountColumn, $created, $skipped);

        // after all creation logic in store()
        if ($created > 0) {
            $msg = "Success Bills generated: {$created}.";
            if ($skipped > 0) {
                // attach a friendly warning about skipped items
                return redirect()->route('bills.index')
                    ->with('success', $msg)
                    ->with('warning', "{$skipped} bill(s) were skipped because they already exist or the agreement did not cover the period.");
            }
            return redirect()->route('bills.index')
                ->with('success', $msg);
        }

        // nothing created
        return redirect()->route('bills.create')
            ->withInput()
            ->with('warning', "No bills were created. {$skipped} bill(s) were skipped because they already exist or the agreement did not cover the selected period.");
    }

    /**
     * Generate bills for all active DORM agreements for a given month/year.
     * This does not call store() because store() expects agreement_id.
     */
    public function generateAll(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();

        $created = 0;
        $skipped = 0;
        $hasBaseAmountColumn = Schema::hasColumn('bills', 'base_amount');

        // Find active agreements overlapping the selected period that are DORM (non-transient)
        $agreements = Agreement::where('is_active', true)
            ->whereDate('start_date', '<=', $periodEnd->toDateString())
            ->whereDate('end_date', '>=', $periodStart->toDateString())
            ->whereHas('room.roomType', function ($q) {
                $q->where('is_transient', false);
            })
            ->get();

        foreach ($agreements as $agreement) {
            $this->createBillForAgreement($agreement, $periodStart, $periodEnd, $hasBaseAmountColumn, $created, $skipped);
        }

        // after generating loop in generateAll()
        if ($created > 0) {
            $msg = "✅ Bills generated for dorm agreements: {$created}.";
            if ($skipped > 0) {
                return redirect()->route('bills.index')
                    ->with('success', $msg)
                    ->with('warning', "{$skipped} agreement(s) were skipped because a bill already existed for the target period or the agreement didn't cover that period.");
            }
            return redirect()->route('bills.index')->with('success', $msg);
        }

        return redirect()->route('bills.index')
            ->with('warning', "No new bills were created. {$skipped} agreement(s) were skipped (existing bills or incompatible agreement dates).");
    }

    /**
     * Create a single bill for the given agreement for the provided period.
     * $periodStart / $periodEnd can be Carbon or date string. This function
     * will compute dorm anchoring if needed. It updates $created/$skipped counters by reference.
     */
    private function createBillForAgreement(\App\Models\Agreement $agreement, $periodStart, $periodEnd, bool $hasBaseAmountColumn, int &$created, int &$skipped)
    {
        // Normalize to Carbon
        $periodStart = \Carbon\Carbon::parse($periodStart)->startOfDay();
        $periodEnd = \Carbon\Carbon::parse($periodEnd)->endOfDay();

        $room = $agreement->room ?? $agreement->load('room')->room;
        $roomType = $room->roomType ?? null;
        $isTransient = (($agreement->rate_unit ?? null) === 'daily') || ($roomType && ($roomType->is_transient ?? false));

        // If dorm (not transient), align to agreement start-day anchor
        if (! $isTransient) {
            $startDay = \Carbon\Carbon::parse($agreement->start_date)->day;
            $firstOfMonth = \Carbon\Carbon::create($periodStart->year, $periodStart->month, 1);
            $daysInMonth = $firstOfMonth->daysInMonth;
            $anchorDay = min($startDay, $daysInMonth);

            $periodStart = \Carbon\Carbon::create($periodStart->year, $periodStart->month, $anchorDay)->startOfDay();
            $periodEnd = (clone $periodStart)->addMonth()->subSecond(); // the day before next anchor
        }

        $periodStartStr = $periodStart->toDateTimeString();
        $periodEndStr = $periodEnd->toDateTimeString();

        // Check duplicate using exact datetimes (matches unique index that stores datetimes)
        $exists = \App\Models\Bill::where('agreement_id', $agreement->agreement_id)
            ->where('period_start', $periodStartStr)
            ->where('period_end', $periodEndStr)
            ->exists();

        if ($exists) {
            $skipped++;
            session()->flash('warning', "⚠️ A bill already exists for {$agreement->renter->full_name} ({$agreement->room->room_number}) 
                covering period {$periodStart->format('M d, Y')} - {$periodEnd->format('M d, Y')}.");
            return;
        }

        // compute base amount / due date
        if ($isTransient) {
            $days = $periodEnd->copy()->startOfDay()->diffInDays($periodStart->copy()->startOfDay()) + 1;
            $dailyRate = $agreement->rate ?? $room->room_price ?? ($agreement->monthly_rent ? ($agreement->monthly_rent / 30) : 0);
            $base = round((float)$dailyRate * (float)$days, 2);
            $dueDate = $periodEnd->copy()->setTime(12, 0, 0);
        } else {
            $base = round((float)($agreement->monthly_rent ?? ($agreement->rate ?? 0)), 2);
            $dueDate = (clone $periodStart)->addMonth()->startOfDay();
        }

        $billData = [
            'agreement_id' => $agreement->agreement_id,
            'renter_id' => $agreement->renter_id,
            'room_id' => $agreement->room_id,
            'period_start' => $periodStartStr,
            'period_end' => $periodEndStr,
            'due_date' => $dueDate,
            'amount_due' => $base,
            'balance' => $base,
            'status' => 'unpaid',
        ];

        if ($hasBaseAmountColumn) {
            $billData['base_amount'] = $base;
        }

        try {
            // use helper to create and immediately apply any unallocated payments for agreement
            $this->createBillAndConsumeUnallocated($billData, $agreement);
            $created++;
        } catch (\Illuminate\Database\QueryException $e) {
            $code = $e->errorInfo[1] ?? null;
            if ($code == 1062) { // duplicate entry
                $skipped++;
            } else {
                throw $e;
            }
        }
    }

    /**
     * Create a bill and immediately consume any unallocated payments for the same agreement.
     *
     * @param array $billData
     * @param \App\Models\Agreement $agreement
     * @return \App\Models\Bill
     */
    private function createBillAndConsumeUnallocated(array $billData, \App\Models\Agreement $agreement)
    {
        // create the bill
        $bill = \App\Models\Bill::create($billData);

        // find payments with unallocated_amount for this agreement (oldest first)
        $paymentsWithCredit = Payment::where('agreement_id', $agreement->agreement_id)
            ->where('unallocated_amount', '>', 0)
            ->orderBy('payment_date', 'asc')
            ->get();

        foreach ($paymentsWithCredit as $payment) {
            if ($bill->balance <= 0) break;

            $available = (float) $payment->unallocated_amount;
            if ($available <= 0) continue;

            // apply to bill (Bill::applyPaymentAmount returns amount actually applied)
            $applied = $bill->applyPaymentAmount($available);

            if ($applied > 0) {
                // create payment_item to record allocation
                PaymentItem::create([
                    'payment_id' => $payment->billing_id,
                    'bill_id'    => $bill->id,
                    'amount'     => $applied,
                ]);

                // deduct from payment.unallocated_amount
                $payment->unallocated_amount = round((float)$payment->unallocated_amount - $applied, 2);
                if ($payment->unallocated_amount < 0) $payment->unallocated_amount = 0;
                $payment->save();
            }
        }

        return $bill;
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Bill deleted.');
    }

    // 🔹 UPDATED REPORTS FUNCTION — focuses on unpaid, with monthly/annual filter
    public function reports(Request $request)
    {
        $periodType = $request->input('period_type', 'monthly'); // 'monthly' or 'annual'
        $month = $request->input('month');
        $year = $request->input('year', now()->year);

        $query = Bill::with('renter', 'room', 'agreement')->where('status', 'unpaid'); // only unpaid for now

        if ($periodType === 'monthly' && $month && $year) {
            // 📅 Filter for selected month & year
            $query->whereYear('period_start', $year)
                  ->whereMonth('period_start', $month);
        } elseif ($periodType === 'annual' && $year) {
            // 📆 Filter for whole year
            $query->whereYear('period_start', $year);
        }

        $bills = $query->get();

        // 💰 Compilation of all unpaid bills — "How much is C5 asking for?"
        $totalOutstanding = $bills->sum('balance');

        // 🔹 Breakdown: Transient vs Monthly/Dorm
        $transientOutstanding = $bills->filter(fn($b) => optional($b->room->roomType)->is_transient || ($b->agreement->rate_unit ?? '') === 'daily')
                                      ->sum('balance');

        $monthlyOutstanding = $bills->filter(fn($b) => !optional($b->room->roomType)->is_transient && ($b->agreement->rate_unit ?? '') !== 'daily')
                                    ->sum('balance');

        $totalOutstandingCombined = $transientOutstanding + $monthlyOutstanding;

        // --- Paid totals (for the Paid tab) ---------------------------------
        $paidQuery = Bill::with('renter', 'room', 'agreement')->where('status', 'paid');

        if ($periodType === 'monthly' && $month && $year) {
            $paidQuery->whereYear('period_start', $year)
                     ->whereMonth('period_start', $month);
        } elseif ($periodType === 'annual' && $year) {
            $paidQuery->whereYear('period_start', $year);
        }

        $paidBills = $paidQuery->get();

    // Compute amount actually paid per bill as (amount_due - balance). For paid bills balance is typically 0.
    $totalPaid = $paidBills->sum(fn($b) => (float) $b->amount_due - (float) $b->balance);

    $transientPaid = $paidBills->filter(fn($b) => optional($b->room->roomType)->is_transient || ($b->agreement->rate_unit ?? '') === 'daily')
                   ->sum(fn($b) => (float) $b->amount_due - (float) $b->balance);

    $monthlyPaid = $paidBills->filter(fn($b) => !optional($b->room->roomType)->is_transient && ($b->agreement->rate_unit ?? '') !== 'daily')
                 ->sum(fn($b) => (float) $b->amount_due - (float) $b->balance);

        // Pass both unpaid and paid aggregates to the view
        return view('bills.reports', compact(
            'bills',
            'totalOutstanding',
            'transientOutstanding',
            'monthlyOutstanding',
            'totalOutstandingCombined',
            'totalPaid',
            'transientPaid',
            'monthlyPaid',
            'paidBills',
            'periodType',
            'month',
            'year'
        ));
    }
}
