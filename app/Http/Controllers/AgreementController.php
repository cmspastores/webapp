<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Renters;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // show latest first, 10 per page
        $agreements = Agreement::with(['renter', 'room.roomType'])
            ->orderBy('agreement_date', 'desc')
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
            'monthly_rent'   => 'nullable|numeric|min:0',
        ]);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = (clone $start)->addYear()->subSecond(); // one year less 1s -> inclusive range

        // conflict if an active agreement exists with overlapping dates:
        $conflict = Agreement::where('room_id', $data['room_id'])
            ->where('is_active', true)
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->exists();

        if ($conflict) {
            return back()->withInput()->withErrors(['room_id' => 'This room is already assigned during that period.']);
        }

        Agreement::create([
            'renter_id'      => $data['renter_id'],
            'room_id'        => $data['room_id'],
            'agreement_date' => $data['agreement_date'],
            'start_date'     => $start->toDateString(),
            'end_date'       => $end->toDateString(),
            'monthly_rent'   => $data['monthly_rent'] ?? null,
            'is_active'      => true,
        ]);

        return redirect()->route('agreements.index')->with('success', 'Agreement created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agreement $agreement)
    {
        $renters = Renters::orderBy('full_name')->get();
        $rooms = Room::with('roomType')->orderBy('room_number')->get();

        return view('agreements.edit', compact('agreement','renters','rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agreement $agreement)
    {
        $data = $request->validate([
            'renter_id'      => 'required|exists:renters,renter_id',
            'room_id'        => 'required|exists:rooms,id',
            'agreement_date' => 'required|date',
            'start_date'     => 'required|date',
            'monthly_rent'   => 'nullable|numeric|min:0',
            'is_active'      => 'nullable|boolean',
        ]);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = (clone $start)->addYear()->subSecond();

        // Check conflicts excluding current agreement
        $conflict = Agreement::where('room_id', $data['room_id'])
            ->where('is_active', true)
            ->where('agreement_id', '<>', $agreement->agreement_id)
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->exists();

        if ($conflict) {
            return back()->withInput()->withErrors(['room_id' => 'This room is already assigned during that period.']);
        }

        $agreement->update([
            'renter_id'      => $data['renter_id'],
            'room_id'        => $data['room_id'],
            'agreement_date' => $data['agreement_date'],
            'start_date'     => $start->toDateString(),
            'end_date'       => $end->toDateString(),
            'monthly_rent'   => $data['monthly_rent'] ?? null,
            'is_active'      => $data['is_active'] ?? true,
        ]);

        return redirect()->route('agreements.index')->with('success', 'Agreement updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agreement $agreement)
    {
        // optionally, you could set is_active=false instead of deleting:
        // $agreement->update(['is_active' => false]);
        $agreement->delete();

        return redirect()->route('agreements.index')->with('success', 'Agreement deleted.');
    }
}
