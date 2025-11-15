<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Agreement;
use App\Models\Renters;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        // Paginate pending reservations (separate paginator param to avoid conflicts with confirmed)
        $pendingReservations = Reservation::whereNull('agreement_id')
            ->whereNotNull('pending_payload')
            ->where('is_archived', false)
            ->latest()
            ->paginate(10, ['*'], 'pending_page')
            ->withQueryString();

        $confirmedReservations = Reservation::whereNotNull('agreement_id')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('reservation.index', compact('pendingReservations', 'confirmedReservations'));
    }

    public function archive(Request $request, Reservation $reservation)
    {
        if ($reservation->agreement_id) {
            return back()->with('error', 'Only pending reservations can be archived.');
        }

        $reservation->update(['is_archived' => true]);
        return back()->with('success', 'Reservation archived successfully.');
    }

    public function archived()
    {
        $archivedReservations = Reservation::whereNull('agreement_id')
            ->where('is_archived', true)
            ->latest()
            ->get();

        return view('reservation.archive', compact('archivedReservations'));
    }

    public function create()
    {
        $rooms = Room::all();
        return view('reservation.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agreement_room_id'      => 'required|exists:rooms,id',
            'agreement_date'         => 'nullable|date',
            'start_date'             => 'required|date',
            'end_date'               => 'nullable|date|after_or_equal:start_date',
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
            'check_in_date'          => 'required|date',
            'check_out_date'         => 'required|date|after_or_equal:check_in_date',
        ]);

        $room = Room::find($validated['agreement_room_id']);
        $reservationType = ($room && ($room->roomType->is_transient ?? false)) ? 'transient' : 'dorm';

        $pending = [
            'agreement' => [
                'room_id'       => $room->id,
                'agreement_date'=> $request->input('agreement_date'),
                'start_date'    => $request->input('start_date'),
                'end_date'      => $request->input('end_date'),
            ],
            'renter' => $request->only([
                'first_name','last_name','dob','email','phone',
                'address','emergency_contact','guardian_name','guardian_phone','guardian_email'
            ]),
        ];

        $reservation = Reservation::create([
            'agreement_id'     => null,
            'room_id'          => $room->id,
            'first_name'       => $request->input('first_name'),
            'last_name'        => $request->input('last_name'),
            'reservation_type' => $reservationType,
            'check_in_date'    => $request->input('check_in_date'),
            'check_out_date'   => $request->input('check_out_date'),
            'status'           => 'unverified',
            'pending_payload'  => $pending,
        ]);

        if ($request->wantsJson()) {
            return response()->json($reservation, Response::HTTP_CREATED);
        }

        return redirect()->route('reservation.index')->with('success', 'Reservation saved as pending.');
    }

    public function confirm(Request $request, Reservation $reservation)
    {
        if ($reservation->agreement_id) {
            return back()->with('error', 'Reservation already confirmed.');
        }

        $payload = $reservation->pending_payload ?? [];
        if (empty($payload['agreement']) || empty($payload['renter'])) {
            return back()->with('error', 'Incomplete pending data.');
        }

        try {
            DB::transaction(function () use ($reservation, $payload) {
                // 🧍 Create or get Renter
                $renterData = $payload['renter'];
                $renter = Renters::firstOrCreate(
                    ['email' => $renterData['email'] ?? null],
                    $renterData
                );

                // 📄 Create Agreement
                $agreementData = $payload['agreement'];
                $room = Room::find($agreementData['room_id']);
                $isTransient = ($room->roomType->is_transient ?? false) || $reservation->reservation_type === 'transient';

                // Determine end_date: prefer explicit agreement payload; for transient use reservation check_out_date fallback,
                // otherwise default to +1 year for dorm agreements.
                $endDate = !empty($agreementData['end_date'])
                    ? $agreementData['end_date']
                    : ($isTransient ? ($reservation->check_out_date ?? Carbon::now()->toDateString()) : Carbon::now()->addYear()->toDateString());

                $agreement = Agreement::create([
                    'renter_id'      => $renter->renter_id,
                    'room_id'        => $room->id,
                    'agreement_date' => $agreementData['agreement_date'] ?? now()->toDateString(),
                    'start_date'     => $agreementData['start_date'] ?? now()->toDateString(),
                    'end_date'       => $endDate,
                    'rate'           => $room->room_price ?? 0,
                    'rate_unit'      => $isTransient ? 'daily' : 'monthly',
                    'monthly_rent'   => $isTransient ? null : $room->room_price,
                    'is_active'      => true,
                ]);

                // 💰 Create Bills
                if ($isTransient) {
                    $this->createTransientBill($agreement);
                } else {
                    $this->createDormBill($agreement);
                    $this->recalcRoomAgreementRents($room);
                }

                // 🔗 Update Reservation
                $reservation->update([
                    'agreement_id'    => $agreement->agreement_id,
                    'room_id'         => $agreement->room_id,
                    'status'          => 'verified',
                    'pending_payload' => null,
                ]);
            });

        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Database integrity error while confirming reservation.');
            }
            throw $e;
        }

        return redirect()->route('reservation.index')->with('success', 'Reservation confirmed with Agreement and Bills.');
    }

    private function createTransientBill(Agreement $agreement)
    {
        $periodStart = Carbon::parse($agreement->start_date)->startOfDay();
        $periodEnd = Carbon::parse($agreement->end_date)->startOfDay();
        $days = $periodStart->diffInDays($periodEnd) + 1;
        $amount = round(($agreement->rate ?? 0) * $days, 2);
        $dueDate = $periodEnd->copy()->setTime(12, 0, 0);

        Bill::create([
            'agreement_id' => $agreement->agreement_id,
            'renter_id'    => $agreement->renter_id,
            'room_id'      => $agreement->room_id,
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
            'due_date'     => $dueDate,
            'amount_due'   => $amount,
            'balance'      => $amount,
            'status'       => 'unpaid',
            'notes'        => 'Auto-generated bill for transient stay',
        ]);
    }

    private function createDormBill(Agreement $agreement)
    {
        $periodStart = Carbon::parse($agreement->start_date)->startOfMonth();
        $periodEnd = (clone $periodStart)->endOfMonth();
        $dueDate = (clone $periodEnd)->addDays(7)->endOfDay();
        $baseAmount = round((float)($agreement->monthly_rent ?? $agreement->rate ?? 0), 2);

        Bill::create([
            'agreement_id' => $agreement->agreement_id,
            'renter_id'    => $agreement->renter_id,
            'room_id'      => $agreement->room_id,
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
            'due_date'     => $dueDate,
            'amount_due'   => $baseAmount,
            'base_amount'  => $baseAmount,
            'balance'      => $baseAmount,
            'status'       => 'unpaid',
            'notes'        => 'Auto-generated first monthly bill for dorm agreement',
        ]);
    }

    private function recalcRoomAgreementRents($roomOrId)
    {
        $room = $roomOrId instanceof Room ? $roomOrId : Room::find($roomOrId);
        if (!$room) return;

        $capacity = max(1, (int)($room->number_of_occupants ?? 1));
        $activeCount = Agreement::where('room_id', $room->id)->where('is_active', true)->count();

        $divisor = max(1, min($activeCount, $capacity));
        $perPerson = $room->room_price / $divisor;

        Agreement::where('room_id', $room->id)
            ->where('is_active', true)
            ->update(['monthly_rent' => $perPerson]);
    }

    public function destroy(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->agreement_id && !optional($request->user())->is_admin) {
            return back()->with('error', 'Only admins can delete confirmed reservations.');
        }

        $reservation->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Reservation deleted successfully.']);
        }

        return redirect()->route('reservation.index')->with('success', 'Reservation deleted successfully.');
    }
}
