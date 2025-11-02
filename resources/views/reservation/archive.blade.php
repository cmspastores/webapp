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
                                    <td>{{ $reservation->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $reservation->room_id }}</td>
                                    <td>{{ $reservation->pending_payload['renter']['first_name'] ?? '-' }} {{ $reservation->pending_payload['renter']['last_name'] ?? '' }}</td>
                                    <td>
                                        {{ $reservation->pending_payload['renter']['email'] ?? '-' }}
                                        @if(!empty($reservation->pending_payload['renter']['phone']))
                                            <div>{{ $reservation->pending_payload['renter']['phone'] }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $reservation->check_in_date ? $reservation->check_in_date->format('Y-m-d') : '-' }}</td>
                                    <td>{{ $reservation->check_out_date ? $reservation->check_out_date->format('Y-m-d') : '-' }}</td>
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