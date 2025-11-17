<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Bill;
use App\Models\BillCharge; 
use App\Models\Payment;
use App\Models\PaymentItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $billsQuery = Bill::with(['renter', 'room', 'agreement']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $billsQuery = $billsQuery->whereHas('renter', fn($q) => $q->where('full_name', 'like', "%{$search}%"))
                                     ->orWhereHas('room', fn($q) => $q->where('room_number', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            $billsQuery = $billsQuery->where('status', $status);
        }

        if ($request->input('sort') === 'asc') {
            $billsQuery = $billsQuery->orderBy('period_start', 'asc');
        } else {
            $billsQuery = $billsQuery->orderBy('period_start', 'desc');
        }

        $bills = $billsQuery->paginate(15)->appends($request->query());

        $monthlyBills = $bills->filter(fn($bill) => !optional($bill->room->roomType)->is_transient && ($bill->agreement->rate_unit ?? '') !== 'daily');
        $transientBills = $bills->filter(fn($bill) => optional($bill->room->roomType)->is_transient || ($bill->agreement->rate_unit ?? '') === 'daily');

        return view('bills.index', compact('bills', 'monthlyBills', 'transientBills'));
    }

    // 🔹 Updated show() to include payments and totalPaid
    public function show(Bill $bill)
    {
        $bill->load('charges', 'agreement', 'room', 'renter', 'payments');

        // Calculate total paid from all related PaymentItems for this bill
        $totalPaid = $bill->payments->sum('amount'); 

        return view('bills.show', compact('bill', 'totalPaid'));
    }

    public function create()
    {
        $agreements = Agreement::where('is_active', true)
            ->whereHas('room.roomType', function($q){
                $q->where('is_transient', false);
            })
            ->orderBy('start_date')
            ->get();
        return view('bills.create', compact('agreements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year' => 'required|integer|min:2000',
            'month' => 'required|integer|between:1,12',
            'agreement_id' => 'nullable|exists:agreements,agreement_id',
        ]);

        if (empty($data['agreement_id'])) {
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

        $agreement = Agreement::findOrFail($data['agreement_id']);
        if (! $agreement->is_active || $agreement->start_date > $periodEnd->toDateString() || $agreement->end_date < $periodStart->toDateString()) {
            return back()->withInput()->withErrors(['agreement_id' => 'Selected agreement is not active for that period.']);
        }

        if ( optional($agreement->room->roomType)->is_transient ) {
            return back()->withInput()
                ->withErrors(['agreement_id' => 'Cannot generate monthly bill for transient agreements. Choose a dorm agreement.']);
        }

        $this->createBillForAgreement($agreement, $periodStart, $periodEnd, $hasBaseAmountColumn, $created, $skipped);

        if ($created > 0) {
            $msg = "Success Bills generated: {$created}.";
            if ($skipped > 0) {
                return redirect()->route('bills.index')
                    ->with('success', $msg)
                    ->with('warning', "{$skipped} bill(s) were skipped because they already exist or the agreement did not cover the period.");
            }
            return redirect()->route('bills.index')
                ->with('success', $msg);
        }

        return redirect()->route('bills.create')
            ->withInput()
            ->with('warning', "No bills were created. {$skipped} bill(s) were skipped because they already exist or the agreement did not cover the selected period.");
    }

    public function generateAll(Request $request)
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();

        $created = 0;
        $skipped = 0;
        $hasBaseAmountColumn = Schema::hasColumn('bills', 'base_amount');

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

    private function createBillForAgreement(\App\Models\Agreement $agreement, $periodStart, $periodEnd, bool $hasBaseAmountColumn, int &$created, int &$skipped)
    {
        $periodStart = \Carbon\Carbon::parse($periodStart)->startOfDay();
        $periodEnd = \Carbon\Carbon::parse($periodEnd)->endOfDay();

        $room = $agreement->room ?? $agreement->load('room')->room;
        $roomType = $room->roomType ?? null;
        $isTransient = (($agreement->rate_unit ?? null) === 'daily') || ($roomType && ($roomType->is_transient ?? false));

        if (! $isTransient) {
            $startDay = \Carbon\Carbon::parse($agreement->start_date)->day;
            $firstOfMonth = \Carbon\Carbon::create($periodStart->year, $periodStart->month, 1);
            $daysInMonth = $firstOfMonth->daysInMonth;
            $anchorDay = min($startDay, $daysInMonth);

            $periodStart = \Carbon\Carbon::create($periodStart->year, $periodStart->month, $anchorDay)->startOfDay();
            $periodEnd = (clone $periodStart)->addMonth()->subSecond();
        }

        $periodStartStr = $periodStart->toDateTimeString();
        $periodEndStr = $periodEnd->toDateTimeString();

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
            $this->createBillAndConsumeUnallocated($billData, $agreement);
            $created++;
        } catch (\Illuminate\Database\QueryException $e) {
            $code = $e->errorInfo[1] ?? null;
            if ($code == 1062) {
                $skipped++;
            } else {
                throw $e;
            }
        }
    }

    private function createBillAndConsumeUnallocated(array $billData, \App\Models\Agreement $agreement)
    {
        $bill = \App\Models\Bill::create($billData);

        $paymentsWithCredit = Payment::where('agreement_id', $agreement->agreement_id)
            ->where('unallocated_amount', '>', 0)
            ->orderBy('payment_date', 'asc')
            ->get();

        foreach ($paymentsWithCredit as $payment) {
            if ($bill->balance <= 0) break;

            $available = (float) $payment->unallocated_amount;
            if ($available <= 0) continue;

            $applied = $bill->applyPaymentAmount($available);

            if ($applied > 0) {
                PaymentItem::create([
                    'payment_id' => $payment->billing_id,
                    'bill_id'    => $bill->id,
                    'amount'     => $applied,
                ]);

                $payment->unallocated_amount = round((float)$payment->unallocated_amount - $applied, 2);
                if ($payment->unallocated_amount < 0) $payment->unallocated_amount = 0;
                $payment->save();
            }
        }

        return $bill;
    }

    public function refund(\App\Models\Bill $bill)
    {
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        if (strtolower($bill->status) === 'refunded') {
            return redirect()->back()->with('error', 'This bill has already been refunded.');
        }

        if (strtolower($bill->status) !== 'paid') {
            return redirect()->back()->with('warning', 'Only fully paid bills can be marked refunded.');
        }

        $note = trim(($bill->notes ?? '') . "\n[Refunded by user_id: ".auth()->id()." on ".now()->toDateTimeString()."]");

        $bill->update([
            'status' => 'refunded',
            'notes'  => $note,
        ]);

        return redirect()->back()->with('success', 'Bill marked as refunded.');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Bill deleted.');
    }









   public function reports(Request $request)
{
    $periodType = $request->input('period_type', 'monthly');
    $month = $request->input('month');
    $year = $request->input('year', now()->year);

    // Base query for bills in the selected period
    $billsQuery = Bill::with('renter', 'room', 'agreement');

    if ($periodType === 'monthly' && $month && $year) {
        $billsQuery->whereYear('period_start', $year)
                   ->whereMonth('period_start', $month);
    } elseif ($periodType === 'annual' && $year) {
        $billsQuery->whereYear('period_start', $year);
    }

    // ❗ Exclude refunded bills entirely
    $allBills = $billsQuery->where('status', '!=', 'refunded')->get();

    // Receivables (unpaid portion)
    $transientOutstanding = $allBills
        ->filter(fn($b) => optional($b->room->roomType)->is_transient 
                        || ($b->agreement->rate_unit ?? '') === 'daily')
        ->sum('balance');

    $monthlyOutstanding = $allBills
        ->filter(fn($b) => !optional($b->room->roomType)->is_transient 
                        && ($b->agreement->rate_unit ?? '') !== 'daily')
        ->sum('balance');

    $totalOutstanding = $transientOutstanding + $monthlyOutstanding;

    // Earnings (paid portion)
    $transientPaid = $allBills
        ->filter(fn($b) => optional($b->room->roomType)->is_transient 
                        || ($b->agreement->rate_unit ?? '') === 'daily')
        ->sum(fn($b) => (float)$b->amount_due - (float)$b->balance);

    $monthlyPaid = $allBills
        ->filter(fn($b) => !optional($b->room->roomType)->is_transient 
                        && ($b->agreement->rate_unit ?? '') !== 'daily')
        ->sum(fn($b) => (float)$b->amount_due - (float)$b->balance);

    $totalPaid = $transientPaid + $monthlyPaid;

    return view('bills.reports', compact(
        'allBills', 'totalOutstanding', 'transientOutstanding', 'monthlyOutstanding',
        'totalPaid', 'transientPaid', 'monthlyPaid',
        'periodType', 'month', 'year'
    ));
}








}
