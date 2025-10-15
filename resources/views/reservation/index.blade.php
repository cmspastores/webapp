<x-app-layout>
    <div class="container">

        <!-- 🔹 Header Row -->
        <div class="reservations-header-row">
            <div class="reservations-header">Reservations</div>
        </div>

        <!-- 🔹 Create Button -->
        <div class="toolbar-row">
            <a href="{{ route('reservation.create') }}" class="btn-new">+ Create Reservation</a>
        </div>

        <!-- 🔹 Reservations Table Card -->
        <div class="card table-card">
            @if($reservations->isEmpty())
                <p>No reservations found.</p>
            @else
                <div class="table-wrapper">
                    <table class="reservations-table">
                        <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Guest</th>
                                <th>Renter</th>
                                <th>Renter Contact</th>
                                <th>Agreement #</th>
                                <th>Agreement Start</th>
                                <th>Agreement End</th>
                                <th>Type</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->room_id }}</td>

                                    <td>
                                        {{ $reservation->first_name }} {{ $reservation->last_name }}
                                    </td>

                                    <td>
                                        {{-- Access renter via accessor or agreement relation --}}
                                        {{ optional($reservation->renter)->full_name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ optional($reservation->renter)->email ?? '-' }}
                                        @if(optional($reservation->renter)->phone)
                                            <div>{{ optional($reservation->renter)->phone }}</div>
                                        @endif
                                    </td>

                                    <td>{{ optional($reservation->agreement)->agreement_number ?? (optional($reservation->agreement)->agreement_id ? 'Agreement #' . optional($reservation->agreement)->agreement_id : '-') }}</td>

                                    <td>
                                        @if($reservation->agreement && $reservation->agreement->start_date)
                                            {{ $reservation->agreement->start_date->format('Y-m-d') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        @if($reservation->agreement && $reservation->agreement->end_date)
                                            {{ $reservation->agreement->end_date->format('Y-m-d') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>{{ $reservation->reservation_type }}</td>
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

<style>
/* 🌅 Container */
.container { max-width:960px; margin:0 auto; padding:20px; font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:12px; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); }

/* 🏷️ Header */
.reservations-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.reservations-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔹 Toolbar */
.toolbar-row { display:flex; justify-content:flex-start; margin-bottom:16px; }
.btn-new { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:10px; padding:10px 18px; font-size:15px; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-decoration:none; transition:0.2s; border:none; cursor:pointer; }
.btn-new:hover { background:#D97A4E; color:#fff; }

/* 📋 Table Card */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }

/* 📑 Table */
.table-wrapper { overflow-x:auto; }
.reservations-table { width:100%; border-collapse:separate; border-spacing:0; text-align:center; border-radius:12px; overflow:hidden; }
.reservations-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; }
.reservations-table th, .reservations-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; }
.reservations-table th:first-child, .reservations-table td:first-child { border-left:none; }
.reservations-table th:last-child, .reservations-table td:last-child { border-right:none; }
.reservations-table tbody tr:last-child td { border-bottom:none; }
.reservations-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

/* 🟩 Status Badges */
.status-badge { padding:4px 10px; border-radius:20px; font-weight:600; font-size:13px; display:inline-block; }
.status-badge.booked { background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; }
.status-badge.cancelled { background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; }
.status-badge.checkedout { background:#E5E7EB; color:#374151; border:1px solid #D1D5DB; }
</style>
