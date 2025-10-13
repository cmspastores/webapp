<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Bill;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BillController extends Controller
{
    // list bills (paginated)
    public function index(Request $request)
    {
        $bills = Bill::with(['renter','room','agreement'])
            ->orderBy('period_start','desc')
            ->paginate(15);

        return view('bills.index', compact('bills'));
    }

    // show single bill / statement
    public function show(Bill $bill)
    {
        return view('bills.show', compact('bill'));
    }

    // form to choose billing period (month) and generate bills
    public function create()
    {
        return view('bills.create');
    }

    // Generate bills for a chosen period (e.g. month and year)
    public function store(Request $request)
    {
        // validate: accept month/year or explicit start date
        $data = $request->validate([
            'year' => 'required|integer|min:2000',
            'month' => 'required|integer|between:1,12',
        ]);

        $year = (int)$data['year'];
        $month = (int)$data['month'];

        // Decide billing period convention:
        // Option A: use calendar month: period_start = 1st of month, period_end = last day of month
        // Option B: rolling 30-day periods from agreement start. For now we’ll use calendar-month billing.
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();

        // Find active agreements that overlap the billing period
        $agreements = Agreement::where('is_active', true)
            ->whereDate('start_date', '<=', $periodEnd->toDateString())
            ->whereDate('end_date', '>=', $periodStart->toDateString())
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($agreements as $agreement) {
            // Prevent duplicates: already have a bill for same agreement + periodStart/periodEnd
            $exists = Bill::where('agreement_id', $agreement->agreement_id)
                ->where('period_start', $periodStart->toDateString())
                ->where('period_end', $periodEnd->toDateString())
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // create bill
            $bill = Bill::create([
                'agreement_id' => $agreement->agreement_id,
                'renter_id' => $agreement->renter_id,
                'room_id' => $agreement->room_id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'due_date' => $periodEnd->copy()->addDays(7)->toDateString(), // example due 7 days after period end
                'amount_due' => $agreement->monthly_rent ?? 0,
                'balance' => $agreement->monthly_rent ?? 0,
                'status' => 'unpaid',
            ]);

            $created++;
        }

        return redirect()->route('bills.index')
            ->with('success', "Bills generated: {$created}, skipped (already exist): {$skipped}");
    }
}