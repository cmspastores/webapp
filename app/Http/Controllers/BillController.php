<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Bill;
use App\Models\BillCharge; // 🔹 kept in case needed elsewhere
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

        $year = (int)$data['year'];
        $month = (int)$data['month'];
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();
        $created = 0;
        $skipped = 0;
        $hasBaseAmountColumn = Schema::hasColumn('bills', 'base_amount');

        $createBill = function ($agreement, $periodStart, $periodEnd) use (&$created, &$skipped, $hasBaseAmountColumn) {
            $room = $agreement->room ?? $agreement->load('room')->room;
            $roomType = $room->roomType ?? null;
            $isTransient = (($agreement->rate_unit ?? null) === 'daily') || ($roomType && ($roomType->is_transient ?? false));

            $exists = Bill::where('agreement_id', $agreement->agreement_id)
                ->where('period_start', $periodStart->toDateString())
                ->where('period_end', $periodEnd->toDateString())
                ->exists();

            if ($exists) { $skipped++; return; }

            if ($isTransient) {
                $days = $periodEnd->copy()->startOfDay()->diffInDays($periodStart->copy()->startOfDay()) + 1;
                $dailyRate = $agreement->rate ?? $room->room_price ?? ($agreement->monthly_rent ? ($agreement->monthly_rent / 30) : 0);
                $base = round((float)$dailyRate * (float)$days, 2);
                $dueDate = $periodEnd->copy()->setTime(12, 0, 0);
            } else {
                $base = round((float)($agreement->monthly_rent ?? ($agreement->rate ?? 0)), 2);
                $dueDate = $periodEnd->copy()->addDays(7)->endOfDay();
            }

            $billData = [
                'agreement_id' => $agreement->agreement_id,
                'renter_id' => $agreement->renter_id,
                'room_id' => $agreement->room_id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'due_date' => $dueDate,
                'amount_due' => $base,
                'balance' => $base,
                'status' => 'unpaid',
            ];

            if ($hasBaseAmountColumn) { $billData['base_amount'] = $base; }
            Bill::create($billData);
            $created++;
        };

        if (!empty($data['agreement_id'])) {
            $agreement = Agreement::findOrFail($data['agreement_id']);
            if (! $agreement->is_active || $agreement->start_date > $periodEnd->toDateString() || $agreement->end_date < $periodStart->toDateString()) {
                return back()->withInput()->withErrors(['agreement_id' => 'Selected agreement is not active for that period.']);
            }
            $createBill($agreement, $periodStart, $periodEnd);
        } else {
            $agreements = Agreement::where('is_active', true)
                ->whereDate('start_date', '<=', $periodEnd->toDateString())
                ->whereDate('end_date', '>=', $periodStart->toDateString())
                ->get();
            foreach ($agreements as $agreement) { $createBill($agreement, $periodStart, $periodEnd); }
        }

        return redirect()->route('bills.index')
            ->with('success', "Bills generated: {$created}, skipped (already exist): {$skipped}");
    }

    public function generateAll(Request $request)
    {
        $r = $request->merge(['agreement_id' => null]);
        return $this->store($r);
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

        return view('bills.reports', compact(
            'bills',
            'totalOutstanding',
            'transientOutstanding',
            'monthlyOutstanding',
            'totalOutstandingCombined',
            'periodType',
            'month',
            'year'
        ));
    }
}
