<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Bill;

use App\Models\BillCharge; // 🔹 ADDED THIS LINE for querying charges

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class BillController extends Controller
{
    // list bills (paginated)
    public function index(Request $request)
    {
        $bills = Bill::with(['renter', 'room', 'agreement'])
            ->orderBy('period_start', 'desc')
            ->paginate(15);

        return view('bills.index', compact('bills'));
    }

    // show single bill / statement
    public function show(Bill $bill)
    {
        // eager load charges if any (for show view)
        $bill->load('charges', 'agreement', 'room', 'renter');
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

        // periodStart = first day of month 00:00, periodEnd = last day of month 23:59:59
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();

        $created = 0;
        $skipped = 0;

        // Helper to check whether DB has base_amount column on bills
        $hasBaseAmountColumn = Schema::hasColumn('bills', 'base_amount');

        // Helper closure to create a bill for one agreement and period
        $createBill = function ($agreement, $periodStart, $periodEnd) use (&$created, &$skipped, $hasBaseAmountColumn) {
            // Ensure room relation is loaded
            $room = $agreement->room ?? $agreement->load('room')->room;
            $roomType = $room->roomType ?? null;

            // consider agreement rate_unit or roomtype flag. Use agreement->rate_unit primarily.
            $isTransient = (($agreement->rate_unit ?? null) === 'daily') || ($roomType && ($roomType->is_transient ?? false));

            // check if a bill already exists for this exact period and agreement
            $exists = Bill::where('agreement_id', $agreement->agreement_id)
                ->where('period_start', $periodStart->toDateString())
                ->where('period_end', $periodEnd->toDateString())
                ->exists();

            if ($exists) {
                $skipped++;
                return;
            }

            // compute base amount (rent portion) depending on transient or dorm
            if ($isTransient) {
                // transient: daily. days inclusive (e.g. 1 to 1 = 1 day)
                $days = $periodEnd->copy()->startOfDay()->diffInDays($periodStart->copy()->startOfDay()) + 1;

                // prefer agreement->rate (daily), fallback to room->room_price or monthly_rent / 30 as last resort
                $dailyRate = $agreement->rate ?? $room->room_price ?? ($agreement->monthly_rent ? ($agreement->monthly_rent / 30) : 0);

                $base = round((float)$dailyRate * (float)$days, 2);

                // due date: noon of the period end day (as datetime)
                $dueDate = $periodEnd->copy()->setTime(12, 0, 0);
            } else {
                // dorm monthly bill -> use agreement's monthly_rent (locked), fallback to agreement->rate if monthly
                $base = round((float)($agreement->monthly_rent ?? ($agreement->rate ?? 0)), 2);

                // due date: 7 days after period end (as date)
                $dueDate = $periodEnd->copy()->addDays(7)->endOfDay();
            }

            $billData = [
                'agreement_id' => $agreement->agreement_id,
                'renter_id' => $agreement->renter_id,
                'room_id' => $agreement->room_id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'due_date' => $dueDate,     // Carbon instance; Eloquent will cast if Bill::$casts has datetime
                'amount_due' => $base,
                'balance' => $base,
                'status' => 'unpaid',
            ];

            // include base_amount column if present so we preserve original rent before charges
            if ($hasBaseAmountColumn) {
                $billData['base_amount'] = $base;
            }

            Bill::create($billData);

            $created++;
        };

        // If an agreement was passed — create only for that agreement
        if (!empty($data['agreement_id'])) {
            $agreement = Agreement::findOrFail($data['agreement_id']);

            if (! $agreement->is_active ||
                $agreement->start_date > $periodEnd->toDateString() ||
                $agreement->end_date < $periodStart->toDateString()
            ) {
                return back()->withInput()->withErrors(['agreement_id' => 'Selected agreement is not active for that period.']);
            }

            $createBill($agreement, $periodStart, $periodEnd);
        } else {
            // No agreement specified -> generate for all active agreements that overlap the period
            $agreements = Agreement::where('is_active', true)
                ->whereDate('start_date', '<=', $periodEnd->toDateString())
                ->whereDate('end_date', '>=', $periodStart->toDateString())
                ->get();

            foreach ($agreements as $agreement) {
                $createBill($agreement, $periodStart, $periodEnd);
            }
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
        // If you also want to delete associated charges, do so here:
        // $bill->charges()->delete();

        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Bill deleted.');
    }

    // 🔹 Sales/Reports method
public function reports(Request $request)
{
    // Optional filters for month/year
    $month = $request->input('month');
    $year = $request->input('year');

    // Start with all bills
    $query = Bill::with('charges', 'renter', 'room');

    // Apply filters if provided
    if ($month && $year) {
        $query->whereYear('period_start', $year)
              ->whereMonth('period_start', $month);
    }

    $bills = $query->get();

    // Aggregate totals
    $totalRevenue = $bills->sum('amount_due');
    $totalOutstanding = $bills->where('status', 'unpaid')->sum('balance');

    // Aggregate charges by type
    $chargesByType = BillCharge::selectRaw('name, SUM(amount) as total')
                        ->groupBy('name')
                        ->get();

    return view('bills.reports', compact('bills', 'totalRevenue', 'totalOutstanding', 'chargesByType'));
}

}

