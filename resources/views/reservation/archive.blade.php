<x-app-layout>
    <div class="container">
        <div class="reservations-header-row">
            <div class="reservations-header">Archived Pending Reservations</div>
            <div><a href="{{ route('reservation.index') }}" class="btn-new">Back to Reservations</a></div>
        </div>

        <div class="card table-card">
            @if(empty($archivedReservations) || $archivedReservations->isEmpty())
                <p>No archived pending reservations.</p>
            @else
                <div class="table-wrapper">
                    <table class="reservations-table">
                        <thead>
                            <tr>
                                <th>Created</th>
                                <th>Room (preview)</th>
                                <th>Renter (pending)</th>
                                <th>Contact</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($archivedReservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->created_at->format('M d, Y g:i A') }}</td>
                                    <td>{{ $reservation->room_id }}</td>
                                    <td>{{ $reservation->pending_payload['renter']['first_name'] ?? '-' }} {{ $reservation->pending_payload['renter']['last_name'] ?? '' }}</td>
                                    <td>
                                        {{ $reservation->pending_payload['renter']['email'] ?? '-' }}
                                        @if(!empty($reservation->pending_payload['renter']['phone']))
                                            <div>{{ $reservation->pending_payload['renter']['phone'] }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $reservation->check_in_date ? \Carbon\Carbon::parse($reservation->check_in_date)->format('M d, Y') : '-' }}</td>
                                    <td>{{ $reservation->check_out_date ? \Carbon\Carbon::parse($reservation->check_out_date)->format('M d, Y') : '-' }}</td>
                                    <td><span class="status-badge {{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<style>
/* Ensure archive uses same reservations table styling */
.container { max-width:1100px; margin:0 auto; padding:20px; font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:12px; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); }
.table-wrapper { overflow-x:auto; }
.reservations-table { width:100%; min-width:1100px; border-collapse:separate; border-spacing:0; text-align:center; border-radius:12px; overflow:hidden; }
.reservations-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; }
.reservations-table th, .reservations-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; }
.reservations-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }
.actions-buttons { display:flex; gap:6px; align-items:center; justify-content:center; }
.actions-cell { white-space:nowrap; }
</style>