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
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the reservations.
     */
    public function index()
    {
        // Pending reservations: not yet linked to an agreement and have pending payload
        $pendingReservations = Reservation::whereNull('agreement_id')
            ->whereNotNull('pending_payload')
            ->orderBy('created_at', 'desc')
            ->get();

        // Confirmed / linked reservations
        $confirmedReservations = Reservation::whereNotNull('agreement_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reservation.index', compact('pendingReservations', 'confirmedReservations'));
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
     * Store reservation first (pending). Do NOT create renter/agreement yet.
     * Save renter+agreement form data to pending_payload for later confirmation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // agreement fields (from create view)
            'agreement_room_id'      => 'required|exists:rooms,id',
            'agreement_date'         => 'nullable|date',
            'start_date'             => 'required|date',
            'end_date'               => 'nullable|date|after_or_equal:start_date',

            // renter fields (optional) — do NOT enforce unique email here (pending)
            'first_name'             => 'nullable|string|max:255',
            'last_name'              => 'nullable|string|max:255',
            'dob'                    => 'nullable|date',
            'email'                  => 'nullable|email',
            'phone'                  => 'nullable|string|max:50',
            'address'                => 'nullable|string|max:1000',
            'emergency_contact'      => 'nullable|string|max:1000',
            'guardian_name'          => 'nullable|string|max:255',
            'guardian_phone'         => 'nullable|string|max:50',
            'guardian_email'         => 'nullable|email',

            // reservation dates
            'check_in_date'          => 'required|date',
            'check_out_date'         => 'required|date|after_or_equal:check_in_date',
        ]);

        // build pending payload (agreement + renter data)
        $pending = [
            'agreement' => [
                'room_id'       => $request->input('agreement_room_id'),
                'agreement_date'=> $request->input('agreement_date'),
                'start_date'    => $request->input('start_date'),
                'end_date'      => $request->input('end_date'),
            ],
            'renter' => $request->only([
                'first_name','last_name','dob','email','phone',
                'address','emergency_contact','guardian_name','guardian_phone','guardian_email'
            ]),
        ];

        // create pending/unverified reservation (no agreement created yet)
        $reservation = Reservation::create([
            'agreement_id'     => null,
            'room_id'          => $request->input('agreement_room_id'),
            'first_name'       => $request->input('first_name', null),
            'last_name'        => $request->input('last_name', null),
            'check_in_date'    => $request->input('check_in_date'),
            'check_out_date'   => $request->input('check_out_date'),
            'status'           => 'unverified',
            'pending_payload'  => $pending,
        ]);

        if ($request->wantsJson()) {
            return response()->json($reservation, Response::HTTP_CREATED);
        }

        return redirect()->route('reservation.index')->with('success', 'Reservation saved as unverified (pending).');
    }

    /**
     * Confirm a pending reservation: create renter (if data present) and agreement,
     * then link them to the reservation.
     */
    public function confirm(Request $request, Reservation $reservation)
    {
        if ($reservation->agreement_id) {
            return redirect()->back()->with('error', 'Reservation already confirmed.');
        }

        $payload = $reservation->pending_payload ?? [];

        if (empty($payload) || empty($payload['agreement'])) {
            return redirect()->back()->with('error', 'No pending data to confirm.');
        }

        try {
            DB::transaction(function () use ($reservation, $payload) {
                $renterId = null;

                $renterData = $payload['renter'] ?? [];

                // Create renter if at least one name or email present
                if (!empty($renterData['first_name']) || !empty($renterData['last_name']) || !empty($renterData['email'])) {
                    // prevent duplicate email violations
                    if (!empty($renterData['email']) && Renters::where('email', $renterData['email'])->exists()) {
                        throw new \Exception('duplicate_renter_email');
                    }

                    $r = Renters::create(array_merge($renterData, [
                        // ensure full_name/unique_id handled by model boot if not provided
                    ]));
                    $renterId = $r->renter_id;
                }

                $agr = $payload['agreement'];
                // compute end_date if missing (auto 1 year)
                $start = $agr['start_date'] ?? now()->toDateString();
                $end = $agr['end_date'] ?? Carbon::parse($start)->addYear()->toDateString();

                $agreement = Agreement::create([
                    'renter_id'     => $renterId,
                    'room_id'       => $agr['room_id'],
                    'agreement_date'=> $agr['agreement_date'] ?? now()->toDateString(),
                    'start_date'    => $start,
                    'end_date'      => $end,
                    'monthly_rent'  => ($reservation->reservation_type === 'dorm') ? ($agr['monthly_rent'] ?? null) : null,
                    'is_active'     => true,
                ]);

                // Link agreement to reservation
                $reservation->agreement_id = $agreement->agreement_id;
                $reservation->room_id = $agreement->room_id;
                // mark as verified once confirmed
                $reservation->status = 'verified';
                $reservation->pending_payload = null;
                $reservation->save();
            });
        } catch (QueryException $e) {
            // handle db integrity (e.g. duplicate email) gracefully
            if ($e->getCode() === '23000') {
                return redirect()->back()->with('error', 'Database integrity error while confirming reservation.');
            }
            throw $e;
        } catch (\Exception $e) {
            if ($e->getMessage() === 'duplicate_renter_email') {
                return redirect()->back()->with('error', 'Renter email already exists. Please edit the reservation and provide a different email.');
            }
            throw $e;
        }

        return redirect()->route('reservation.index')->with('success', 'Reservation confirmed and agreement/renter created.');
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
            'last_name' => 'sometimes|string|max:255',
        ]);

        $reservation->update($validated);

        return response()->json($reservation);
    }

    /**
     * Remove the specified reservation from storage.
     */
    public function destroy(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        // If the client expects JSON (API/AJAX), return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Reservation deleted successfully.']);
        }

        // Normal web request -> redirect back to index with flash message
        return redirect()->route('reservation.index')->with('success', 'Reservation deleted successfully.');
    }
}
