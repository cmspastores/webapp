<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Reservation Management</h2>
            <div class="header-buttons">
                <button id="btn-back-to-list-header" class="btn-back hidden">← Back to List</button>
            </div>
        </div>
    </x-slot>
    <div class="container mt-5">
        <h1 class="mb-4">Reservations</h1>
            <div class="header-buttons flex space-x-2">
                <a href="{{ route('reservation.create') }}" class="btn btn-primary">+ Create Reservation</a>
                <button id="btn-back-to-list-header" class="btn-back hidden">← Back to List</button>
            </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($reservations->isEmpty())
            <p>No reservations found.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Room ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Type</th>
                        <th>Check-In Date</th>
                        <th>Check-Out Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->room_id }}</td>
                            <td>{{ $reservation->first_name }}</td>
                            <td>{{ $reservation->last_name }}</td>
                            <td>{{ $reservation->reservation_type }}</td>
                            <td>{{ $reservation->check_in_date->format('Y-m-d') }}</td>
                            <td>{{ $reservation->check_out_date->format('Y-m-d') }}</td>
                            <td>{{ $reservation->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
