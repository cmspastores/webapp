<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Renters;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Show only active agreements (still valid)
        $search = $request->input('search');
        $sort = $request->input('sort', 'asc'); // default ascending

        $agreements = Agreement::with(['renter', 'room.roomType'])
            ->where('is_active', true)
            ->whereDate('end_date', '>=', now())
            ->when($search, function ($query, $search) {
                $query->whereHas('renter', function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%");
                })
                ->orWhereHas('room', function ($q) use ($search) {
                    $q->where('room_number', 'like', "%{$search}%");
                });
            })
            ->orderBy(Room::select('room_number')->whereColumn('rooms.id', 'agreements.room_id'), $sort)
            ->paginate(10)
            ->appends(['search' => $search, 'sort' => $sort]);

        return view('agreements.index', compact('agreements', 'search', 'sort'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $renters = Renters::orderBy('full_name')->get();
        $rooms = Room::with('roomType')->orderBy('room_number')->get();

        return view('agreements.create', compact('renters', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'renter_id'      => 'required|exists:renters,renter_id',
            'room_id'        => 'required|exists:rooms,id',
            'agreement_date' => 'required|date',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date',
            'stay_length'    => 'nullable|integer|min:1', // for transient stays
        ]);

        $room = Room::with('roomType')->findOrFail($data['room_id']);
        $roomType = $room->roomType;

        $start = Carbon::parse($data['start_date'])->startOfDay();

        // 🔹 Transient or Dorm logic
        if ($roomType && $roomType->is_transient) {
            // User can set end date for transient
            $end = isset($data['end_date'])
                ? Carbon::parse($data['end_date'])->startOfDay()
                : $start->copy()->addDays($data['stay_length'] ?? 1);

            $rate = $room->room_price ?? 0;
            $rateUnit = 'daily';
            $monthlyRent = null; // not used
        } else {
            // Dorm auto 1 year
            $end = (clone $start)->addYear();

            $rate = $room->room_price ?? 0;
            $rateUnit = 'monthly';
            $monthlyRent = $rate;
        }

        // 🔹 Capacity check
        $capacity = (int)($room->number_of_occupants ?? 1);
        if ($capacity < 1) $capacity = 1;

        $activeCount = Agreement::where('room_id', $room->id)
            ->where('is_active', true)
            ->count();

        if ($activeCount + 1 > $capacity) {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Room capacity reached. This room allows only ' . $capacity . ' occupant(s).']);
        }

        // 🔹 Create Agreement (aligned with your DB)
        $agreement = Agreement::create([
            'renter_id'      => $data['renter_id'],
            'room_id'        => $data['room_id'],
            'agreement_date' => $data['agreement_date'],
            'start_date'     => $start->toDateString(),
            'end_date'       => $end->toDateString(),
            'monthly_rent'   => $monthlyRent,
            'rate'           => $rate,
            'rate_unit'      => $rateUnit,
            'is_active'      => true,
        ]);

        // --- Auto-create bill for transient agreements (daily billing)
        if (($roomType && $roomType->is_transient) || (($agreement->rate_unit ?? 'monthly') === 'daily')) {
            // Period start (start of day)
            $periodStart = Carbon::parse($agreement->start_date)->startOfDay();

            // Period end (date part) — agreement->end_date is date (keep it as date)
            $periodEndDate = Carbon::parse($agreement->end_date)->startOfDay();

            // inclusive days: diffInDays returns 0 for same day, so +1 to count both endpoints
            $days = $periodStart->diffInDays($periodEndDate) + 1;
            if ($days < 1) $days = 1;

            // daily rate — prefer the agreement->rate (if set by your code), else room price fallback
            $dailyRate = (float) ($agreement->rate ?? $room->room_price ?? 0);

            $amount = round($dailyRate * $days, 2);

            // due_date = expiry day at 12:00 (noon)
            $dueDate = $periodEndDate->copy()->setTime(12, 0, 0);

            \App\Models\Bill::create([
                'agreement_id' => $agreement->agreement_id,
                'renter_id'    => $agreement->renter_id,
                'room_id'      => $agreement->room_id,
                'period_start' => $periodStart->toDateString(),
                'period_end'   => $periodEndDate->toDateString(),
                'due_date'     => $dueDate, // datetime
                'amount_due'   => $amount,
                'balance'      => $amount,
                'status'       => 'unpaid',
                'notes'        => 'Auto-generated bill for transient stay',
            ]);
        }

        // --- Auto-create first bill for dorm agreements (monthly billing)
        if ($roomType && !$roomType->is_transient) {
            $periodStart = Carbon::parse($agreement->start_date)->startOfMonth();
            $periodEnd   = (clone $periodStart)->endOfMonth();
            $dueDate     = $periodEnd->copy()->addDays(7)->endOfDay();

            $baseAmount  = round((float)($agreement->monthly_rent ?? ($agreement->rate ?? 0)), 2);

            \App\Models\Bill::create([
                'agreement_id' => $agreement->agreement_id,
                'renter_id'    => $agreement->renter_id,
                'room_id'      => $agreement->room_id,
                'period_start' => $periodStart->toDateString(),
                'period_end'   => $periodEnd->toDateString(),
                'due_date'     => $dueDate,
                'amount_due'   => $baseAmount,
                'base_amount'  => $baseAmount,
                'balance'      => $baseAmount,
                'status'       => 'unpaid',
                'notes'        => 'Auto-generated first monthly bill for dorm agreement',
            ]);
        }

        // 🔹 Recalculate shared rent only for dorms
        if (!$roomType->is_transient) {
            $this->recalcRoomAgreementRents($room);
        }

        return redirect()
            ->route('agreements.index')
            ->with('success', $roomType->is_transient
                ? 'Transient stay created successfully.'
                : 'Dorm agreement created successfully. Rent adjusted based on occupancy.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agreement $agreement)
    {
        // Get all bills linked to this agreement (most recent first)
        $bills = $agreement->bills()->orderBy('period_start', 'desc')->get();

        // Compute totals for the Statement of Account
        $totalDue = $bills->sum('amount_due');
        $totalBalance = $bills->sum('balance');

        return view('agreements.show', compact('agreement', 'bills', 'totalDue', 'totalBalance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agreement $agreement)
    {
        $renters = Renters::orderBy('full_name')->get();
        $rooms = Room::with('roomType')->orderBy('room_number')->get();

        $bills = $agreement->bills()->orderBy('period_start', 'desc')->get();
        $totalDue = $bills->sum('amount_due');
        $totalBalance = $bills->sum('balance');

        return view('agreements.edit', compact('agreement', 'renters', 'rooms', 'bills', 'totalDue', 'totalBalance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agreement $agreement)
    {
        abort(403, 'Editing agreements is not allowed. Use renew or terminate.');
    }

    /**
     * Renew agreement (admin only)
     */
    public function renew(Agreement $agreement)
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $room = $agreement->room;
        $roomType = $room->roomType;

        if ($roomType && $roomType->is_transient) {
            // EXTEND STAY by 1 day
            $agreement->end_date = Carbon::parse($agreement->end_date)->addDay();
            $agreement->is_active = true;
            $agreement->save();

            $message = 'Transient stay extended by 1 day successfully.';
        } else {
            // DORM: renew for another year
            $agreement->end_date = Carbon::parse($agreement->end_date)->addYear();
            $agreement->is_active = true;
            $agreement->save();

            // Recalculate rents (based on new total active agreements)
            $this->recalcRoomAgreementRents($room);

            $message = 'Dorm agreement renewed successfully. Rent updated based on occupancy.';
        }

        return redirect()
            ->route('agreements.edit', $agreement)
            ->with('success', $message);
    }

    /**
     * Terminate agreement (admin only)
     */
    public function terminate(Agreement $agreement)
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $agreement->is_active = false;
        $agreement->end_date = Carbon::now();
        $agreement->save();

        // Recalculate rent for remaining active agreements in this room
        $this->recalcRoomAgreementRents($agreement->room);

        return redirect()
            ->route('agreements.index')
            ->with('success', 'Agreement terminated successfully and rents recalculated.');
    }

    // Archived agreements view
    public function archived(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'asc');

        // Agreements that are expired or terminated
        $agreements = Agreement::with(['renter', 'room.roomType'])
            ->where(function ($query) {
                $query->where('is_active', false)
                    ->orWhereDate('end_date', '<', now());
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('renter', function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%");
                })
                ->orWhereHas('room', function ($q) use ($search) {
                    $q->where('room_number', 'like', "%{$search}%");
                });
            })
            ->orderBy(Room::select('room_number')->whereColumn('rooms.id', 'agreements.room_id'), $sort)
            ->paginate(10)
            ->appends(['search' => $search, 'sort' => $sort]);

        return view('agreements.archived', compact('agreements', 'search', 'sort'));
    }

    private function recalcRoomAgreementRents($roomOrId)
    {
        $room = $roomOrId instanceof \App\Models\Room ? $roomOrId : \App\Models\Room::find($roomOrId);
        if (!$room) return;

        // capacity (fallback to 1 if not set)
        $capacity = (int) ($room->number_of_occupants ?? 1);
        if ($capacity < 1) $capacity = 1;

        // count active agreements (only currently active ones)
        $activeCount = \App\Models\Agreement::where('room_id', $room->id)
            ->where('is_active', true)
            ->count();

        // divisor is min(activeCount, capacity) but at least 1
        $divisor = max(1, min($activeCount, $capacity));

        // compute per-person monthly rent (decimal)
        $perPerson = $room->room_price / $divisor;

        // update all active agreements for this room to the perPerson
        \App\Models\Agreement::where('room_id', $room->id)
            ->where('is_active', true)
            ->update(['monthly_rent' => $perPerson]);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Agreement $agreement)
    {
        if ($agreement->is_active) {
        return back()->with('error', 'Cannot delete an active agreement. Please terminate it first.');
        }
        $agreement->delete();

        return redirect()->route('agreements.archived')->with('success', 'Archived agreement deleted successfully.');
    }
}
