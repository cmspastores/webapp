<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Agreement;
use App\Models\Renters;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

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
        return view('reservation.create', compact('rooms'));
    }

    /**
     * Store a newly created reservation in storage.
     *
     * Creates a renter (if renter fields provided), an agreement, then the reservation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // reservation
            'reservation_type' => 'required|in:transient,dorm',
            'check_in_date'    => 'required|date',
            'check_out_date'   => 'required|date|after_or_equal:check_in_date',
            'status'           => 'required|string|max:100',

            // agreement
            'agreement_room_id'        => 'required|exists:rooms,id',
            'agreement_date'           => 'nullable|date',
            'start_date'               => 'required|date',
            'end_date'                 => 'nullable|date|after_or_equal:start_date',
            'agreement_monthly_rent'   => 'nullable|numeric',

            // renter (optional) - add unique rule for email to prevent duplicate entry
            'first_name'        => 'sometimes|required_with:last_name|string|max:255',
            'last_name'         => 'sometimes|required_with:first_name|string|max:255',
            'dob'               => 'nullable|date',
            'email'             => 'nullable|email|unique:renters,email',
            'phone'             => 'nullable|string|max:50',
            'address'           => 'nullable|string|max:1000',
            'emergency_contact' => 'nullable|string|max:1000',
            'guardian_name'     => 'nullable|string|max:255',
            'guardian_phone'    => 'nullable|string|max:50',
            'guardian_email'    => 'nullable|email',
        ]);

        $reservation = null;

        try {
            DB::transaction(function () use ($request, &$reservation) {
                // Create renter if provided
                $renterId = null;
                if ($request->filled('first_name') || $request->filled('last_name')) {
                    $renter = Renters::create([
                        'first_name'       => $request->input('first_name'),
                        'last_name'        => $request->input('last_name'),
                        'dob'              => $request->input('dob'),
                        'email'            => $request->input('email'),
                        'phone'            => $request->input('phone'),
                        'address'          => $request->input('address'),
                        'emergency_contact'=> $request->input('emergency_contact'),
                        'guardian_name'    => $request->input('guardian_name'),
                        'guardian_phone'   => $request->input('guardian_phone'),
                        'guardian_email'   => $request->input('guardian_email'),
                    ]);
                    $renterId = $renter->renter_id;
                }

                // Create agreement
                $agreement = Agreement::create([
                    'renter_id'     => $renterId,
                    'room_id'       => $request->input('agreement_room_id'),
                    'agreement_date'=> $request->input('agreement_date') ?: now()->toDateString(),
                    'start_date'    => $request->input('start_date'),
                    'end_date'      => $request->input('end_date'), // Agreement model may auto-fill
                    'monthly_rent'  => $request->input('reservation_type') === 'dorm' ? $request->input('agreement_monthly_rent') : null,
                    'is_active'     => true,
                ]);

                // Create reservation linked to agreement
                $reservation = Reservation::create([
                    'agreement_id'     => $agreement->agreement_id,
                    'room_id'          => $agreement->room_id,
                    'first_name'       => $request->input('first_name', null),
                    'last_name'        => $request->input('last_name', null),
                    'reservation_type' => $request->input('reservation_type'),
                    'check_in_date'    => $request->input('check_in_date'),
                    'check_out_date'   => $request->input('check_out_date'),
                    'status'           => $request->input('status'),
                ]);
            });
        } catch (QueryException $e) {
            // Handle duplicate email unique constraint or other integrity violations gracefully
            $msg = $e->getMessage();

            // common SQLSTATE for integrity constraint violation
            if ($e->getCode() === '23000' && str_contains($msg, 'renters_email_unique')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['email' => 'That email is already used by another renter.']);
            }

            // fallback: if unique index name not matched, still try to detect duplicate entry for email
            if ($e->getCode() === '23000' && str_contains($msg, 'Duplicate entry') && str_contains($msg, '@')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['email' => 'Email already exists.']);
            }

            // rethrow for other cases so developers see unexpected errors
            throw $e;
        }

        if ($request->wantsJson()) {
            return response()->json($reservation, Response::HTTP_CREATED);
        }

        return redirect()->route('reservation.index')->with('success', 'Reservation, agreement and renter (if provided) created.');
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
