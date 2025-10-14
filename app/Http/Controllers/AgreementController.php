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
    public function index()
    {
        // Show only active agreements (still valid)
        $agreements = Agreement::with(['renter', 'room.roomType'])
            ->where('is_active', true)
            ->whereDate('end_date', '>=', now()) // ensure not expired
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('agreements.index', compact('agreements'));
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
        ]);

        $room = Room::findOrFail($data['room_id']);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = (clone $start)->addYear()->subSecond();

        // Prevent assigning if active agreement exists for that room
        $conflict = Agreement::where('room_id', $data['room_id'])
            ->where('is_active', true)
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->exists();

        if ($conflict) {
            return back()->withInput()->withErrors(['room_id' => 'This room already has an active agreement.']);
        }

        Agreement::create([
            'renter_id'      => $data['renter_id'],
            'room_id'        => $data['room_id'],
            'agreement_date' => $data['agreement_date'],
            'start_date'     => $start->toDateString(),
            'end_date'       => $end->toDateString(),
            // lock current room price at time of creation
            'monthly_rent'   => $room->room_price,
            'is_active'      => true,
        ]);

        return redirect()->route('agreements.index')->with('success', 'Agreement created and rent locked to current room price.');
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
        $agreement->end_date = Carbon::parse($agreement->end_date)->addYear();
        $agreement->monthly_rent = $room->room_price; // new locked rate
        $agreement->is_active = true;
        $agreement->save();

        return redirect()->route('agreements.edit', $agreement)->with('success', 'Agreement renewed with updated room price.');
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

        return redirect()->route('agreements.index')->with('success', 'Agreement terminated successfully.');
    }

    // Archived agreements view
    public function archived()
    {
        // Agreements that are expired or terminated
        $agreements = Agreement::with(['renter', 'room.roomType'])
            ->where(function ($query) {
                $query->where('is_active', false)
                    ->orWhereDate('end_date', '<', now());
            })
            ->orderBy('end_date', 'desc')
            ->paginate(10);

        return view('agreements.archived', compact('agreements'));
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
