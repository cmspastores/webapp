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
    // list bills (paginated)
    public function index(Request $request)
    {
        $billsQuery = Bill::with(['renter', 'room', 'agreement']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $billsQuery = $billsQuery->whereHas('renter', fn($q) => 
                $q->where('full_name', 'like', "%{$search}%"))
                ->orWhereHas('room', fn($q) => 
                $q->where('room_number', 'like', "%{$search}%"));
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

        $monthlyBills = $bills->filter(fn($bill) => 
            !optional($bill->room->roomType)->is_transient && ($bill->agreement->rate_unit ?? '') !== 'daily');
        $transientBills = $bills->filter(fn($bill) => 
            optional($bill->room->roomType)->is_transient || ($bill->agreement->rate_unit ?? '') === 'daily');

        return view('bills.index', compact('bills', 'monthlyBills', 'transientBills'));
    }

    public function show(Bill $bill)
    {
        $bill->load('charges', 'agreement', 'room', 'renter');
        return view('bills.show', compact('bill'));
    }

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

        if (empty($data['agreement_id'])) {
            return back()->withInput()->withErrors([
                'agreement_id' => 'Please select an agreement. To generate for all dorm agreements use the "Generate All Dorms" button.'
            ]);
        }

        $year = (int)$data['year'];
        $month = (int)$data['month'];
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();
        $created = 0;
        $skipped = 0;
        $hasBaseAmountColumn = Schema::hasColumn('bills', 'base_amount');

        $agreement = Agreement::findOrFail($data['agreement_id']);
        if (!$agreement->is_active || $agreement->start_date > $periodEnd->toDateString() || $agreement->end_date < $periodStart->toDateString()) {
            return back()->withInput()->withErrors([
                'agreement_id' => 'Selected agreement is not active for that period.'
            ]);
        }

        $this->createBillForAgreement($agreement, $periodStart, $periodEnd, $hasBaseAmountColumn, $created, $skipped);

        return redirect()->route('bills.index')
            ->with('success', "Bills generated: {$created}, skipped (already exist): {$skipped}");
    }

    public function generateAll(Request $request)
    {
        $year = (int)$request->input('year', now()->year);
        $month = (int)$request->input('month', now()->month);

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

        return redirect()->route('bills.index')
            ->with('success', "Bills generated: {$created}, skipped (already exist or invalid): {$skipped}");
    }

    private function createBillForAgreement(\App\Models\Agreement $agreement, $periodStart, $periodEnd, bool $hasBaseAmountColumn, int &$created, int &$skipped)
    {
        $periodStart = Carbon::parse($periodStart)->startOfDay();
        $periodEnd = Carbon::parse($periodEnd)->endOfDay();

        $room = $agreement->room ?? $agreement->load('room')->room;
        $roomType = $room->roomType ?? null;
        $isTransient = (($agreement->rate_unit ?? null) === 'daily') || ($roomType && ($roomType->is_transient ?? false));

        if (!$isTransient) {
            $startDay = Carbon::parse($agreement->start_date)->day;
            $firstOfMonth = Carbon::create($periodStart->year, $periodStart->month, 1);
            $daysInMonth = $firstOfMonth->daysInMonth;
            $anchorDay = min($startDay, $daysInMonth);

            $periodStart = Carbon::create($periodStart->year, $periodStart->month, $anchorDay)->startOfDay();
            $periodEnd = (clone $periodStart)->addMonth()->subSecond();
        }

        $periodStartStr = $periodStart->toDateTimeString();
        $periodEndStr = $periodEnd->toDateTimeString();

        $exists = Bill::where('agreement_id', $agreement->agreement_id)
            ->where('period_start', $periodStartStr)
            ->where('period_end', $periodEndStr)
            ->exists();

        if ($exists) {
            $skipped++;
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
            Bill::create($billData);
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

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Bill deleted.');
    }

    // ✅ FIXED REPORTS FUNCTION (Unpaid + Paid)
    public function reports(Request $request)
    {
        $periodType = $request->input('period_type', 'monthly');
        $month = $request->input('month');
        $year = $request->input('year', now()->year);

        // ---- UNPAID ----
        $query = Bill::with(['renter', 'room.roomType', 'agreement'])->where('status', 'unpaid');

        if ($periodType === 'monthly' && $month && $year) {
            $query->whereYear('period_start', $year)->whereMonth('period_start', $month);
        } elseif ($periodType === 'annual' && $year) {
            $query->whereYear('period_start', $year);
        }

        $bills = $query->get();

        $transientOutstanding = $bills->filter(fn($b) =>
            optional($b->room->roomType)->is_transient || ($b->agreement->rate_unit ?? '') === 'daily'
        )->sum('balance');

        $monthlyOutstanding = $bills->filter(fn($b) =>
            !optional($b->room->roomType)->is_transient && ($b->agreement->rate_unit ?? '') !== 'daily'
        )->sum('balance');

        $totalOutstanding = $transientOutstanding + $monthlyOutstanding;

        // ---- PAID ----
        $paidQuery = Payment::with(['items.bill.room.roomType', 'items.bill.agreement']);
        if ($periodType === 'monthly' && $month && $year) {
            $paidQuery->whereYear('payment_date', $year)->whereMonth('payment_date', $month);
        } elseif ($periodType === 'annual' && $year) {
            $paidQuery->whereYear('payment_date', $year);
        }

        $payments = $paidQuery->get();

        $transientPaid = 0;
        $monthlyPaid = 0;
        foreach ($payments as $payment) {
            foreach ($payment->items as $item) {
                if ($item->bill) {
                    if (optional($item->bill->room->roomType)->is_transient || ($item->bill->agreement->rate_unit ?? '') === 'daily') {
                        $transientPaid += $item->amount;
                    } else {
                        $monthlyPaid += $item->amount;
                    }
                }
            }
        }

        $totalPaid = $transientPaid + $monthlyPaid;

        return view('bills.reports', compact(
            'bills',
            'totalOutstanding',
            'transientOutstanding',
            'monthlyOutstanding',
            'transientPaid',
            'monthlyPaid',
            'totalPaid',
            'periodType',
            'month',
            'year'
        ));
    }
}
