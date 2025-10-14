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
        $agreements = Agreement::where('is_active', true)->orderBy('start_date')->get();
        return view('bills.create', compact('agreements'));
    }

    /**
     * Generate bills for a chosen period (e.g. month and year).
     *
     * If request contains `agreement_id` -> generate only for that agreement.
     * If no `agreement_id` -> generate for all active agreements overlapping the period.
     */
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

        if (!empty($data['agreement_id'])) {
            // Generate for a single agreement
            $agreement = Agreement::findOrFail($data['agreement_id']);

            // Ensure agreement overlaps the billing period and is active
            if (! $agreement->is_active ||
                $agreement->start_date > $periodEnd->toDateString() ||
                $agreement->end_date < $periodStart->toDateString()
            ) {
                return back()->withInput()->withErrors(['agreement_id' => 'Selected agreement is not active for that period.']);
            }

            $exists = Bill::where('agreement_id', $agreement->agreement_id)
                ->where('period_start', $periodStart->toDateString())
                ->where('period_end', $periodEnd->toDateString())
                ->exists();

            if ($exists) {
                $skipped = 1;
            } else {
                Bill::create([
                    'agreement_id' => $agreement->agreement_id,
                    'renter_id' => $agreement->renter_id,
                    'room_id' => $agreement->room_id,
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'due_date' => $periodEnd->copy()->addDays(7)->toDateString(),
                    'amount_due' => $agreement->monthly_rent ?? 0,
                    'balance' => $agreement->monthly_rent ?? 0,
                    'status' => 'unpaid',
                ]);

                \Log::info("Bill created for agreement {$agreement->agreement_id} for {$periodStart->format('F Y')}");
                $created = 1;
            }

            return redirect()->route('bills.index')
                ->with('success', "Bills generated: {$created}, skipped (already exist): {$skipped}");
        }

        // No agreement specified -> generate for all active agreements that overlap the period
        $agreements = Agreement::where('is_active', true)
            ->whereDate('start_date', '<=', $periodEnd->toDateString())
            ->whereDate('end_date', '>=', $periodStart->toDateString())
            ->get();

        foreach ($agreements as $agreement) {
            $exists = Bill::where('agreement_id', $agreement->agreement_id)
                ->where('period_start', $periodStart->toDateString())
                ->where('period_end', $periodEnd->toDateString())
                ->exists();

            if ($exists) { $skipped++; continue; }

            Bill::create([
                'agreement_id' => $agreement->agreement_id,
                'renter_id' => $agreement->renter_id,
                'room_id' => $agreement->room_id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'due_date' => $periodEnd->copy()->addDays(7)->toDateString(),
                'amount_due' => $agreement->monthly_rent ?? 0,
                'balance' => $agreement->monthly_rent ?? 0,
                'status' => 'unpaid',
            ]);

            \Log::info("Bill created for agreement {$agreement->agreement_id} for {$periodStart->format('F Y')}");
            $created++;
        }

        return redirect()->route('bills.index')
            ->with('success', "Bills generated: {$created}, skipped (already exist): {$skipped}");
    }

    /**
     * Optional helper route to generate all — calls store() logic without agreement_id.
     * Useful for an explicit "Generate All" button that hits a shorter route.
     */
    public function generateAll(Request $request)
    {
        // Reuse store: build a request without agreement_id
        $r = $request->merge(['agreement_id' => null]);
        return $this->store($r);
    }

    /**
     * Delete a bill (admin or whoever you allow)
     */
    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Bill deleted.');
    }
}