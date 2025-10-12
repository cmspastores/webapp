<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Agreement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReservationController extends Controller
{
    /**
     * Display a listing of the reservations.
     */
    public function index()
    {
        $reservations = Reservation::all();
        return view('reservation.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create()
    {
        $rooms = Room::all();
        $agreements = Agreement::all();
        return view('reservation.create', compact('rooms', 'agreements'));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'agreement_id' => 'required|exists:agreements,agreement_id',
            'room_id' => 'required|exists:rooms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'reservation_type' => 'required|string|max:255',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'status' => 'required|string|max:100',
        ]);

        $reservation = Reservation::create($validated);

        if ($request->wantsJson()) {
            return response()->json($reservation, Response::HTTP_CREATED);
        }

        return redirect()->route('reservation.index')->with('success', 'Reservation created.');
    }

    /**
     * Display the specified reservation.
     */
    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);
        return response()->json($reservation);
    }

    /**
     * Update the specified reservation in storage.
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $validated = $request->validate([
            'agreement_id' => 'sometimes|exists:agreements,agreement_id',
            'room_id' => 'sometimes|exists:rooms,id',
            'reservation_type' => 'sometimes|string|max:255',
            'check_in_date' => 'sometimes|date',
            'check_out_date' => 'sometimes|date|after_or_equal:check_in_date',
            'status' => 'sometimes|string|max:100',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
        ]);

        $reservation->update($validated);

        return response()->json($reservation);
    }

    /**
     * Remove the specified reservation from storage.
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(['message' => 'Reservation deleted successfully.']);
    }
}
