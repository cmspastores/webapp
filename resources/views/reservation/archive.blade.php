<x-app-layout>
    <div class="container">
        <!-- 🔹 Header -->
        <div class="reservations-header-row">
            <div class="reservations-header">Archived Pending Reservations</div>
        </div>

        <!-- 🔹 Back Button Below Header, Right-Aligned -->
        <div class="toolbar-row">
            <a href="{{ route('reservation.index') }}" class="btn-new">Back</a>
        </div>

        <!-- 🔹 Table Card -->
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
/* 🌅 Container */
.container { max-width:1100px; margin:0 auto; padding:20px; font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:12px; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); }

/* 🏷️ Header */
.reservations-header-row { display:flex; justify-content:flex-start; align-items:center; margin-bottom:8px; }
.reservations-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; }

/* 🔹 Toolbar Below Header, Right-Aligned */
.toolbar-row { display:flex; justify-content:flex-end; margin-bottom:16px; gap:8px; }

/* 🔹 Buttons */
.btn-new { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:10px; padding:10px 18px; font-size:15px; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-decoration:none; transition:0.2s; border:none; cursor:pointer; }
.btn-new:hover { background:#D97A4E; color:#fff; }

/* 🗄️ Archive button same as Renters */
.btn-archive { background:linear-gradient(90deg,#E6A574,#F4C38C) !important; color:#5C3A21 !important; font-weight:700; border-radius:10px; padding:10px 14px; text-decoration:none; border:none; display:inline-flex; align-items:center; justify-content:center; transition:0.2s; cursor:pointer; }
.btn-archive:hover { background:#D97A4E !important; color:#fff !important; }

/* 📋 Table Card */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }

/* 📑 Table */
.table-wrapper { overflow-x:auto; border-radius:12px; scrollbar-width:thin; scrollbar-color:#E6A574 #FFF8F0; }
.table-wrapper::-webkit-scrollbar { height:8px; width:8px; }
.table-wrapper::-webkit-scrollbar-thumb { background-color:#E6A574; border-radius:8px; }
.table-wrapper::-webkit-scrollbar-track { background:#FFF8F0; }

.reservations-table { width:100%; min-width:1100px; border-collapse:separate; border-spacing:0; text-align:center; border-radius:12px; overflow:hidden; }
.reservations-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; }
.reservations-table th, .reservations-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; }
.reservations-table th:first-child, .reservations-table td:first-child { border-left:none; }
.reservations-table th:last-child, .reservations-table td:last-child { border-right:none; }
.reservations-table tbody tr:last-child td { border-bottom:none; }
.reservations-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

/* 🟩 Status Badges */
.status-badge { padding:4px 10px; border-radius:20px; font-weight:600; font-size:13px; display:inline-block; }
.status-badge.verified { background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; }
.status-badge.unverified { background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; }
.status-badge.cancelled { background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; }
.status-badge.checkedout { background:#E5E7EB; color:#374151; border:1px solid #D1D5DB; }

/* 🔘 Confirm & Delete Buttons */
.btn-confirm { background:#4CAF50; color:#fff; border:none; border-radius:8px; padding:8px 16px; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.2s, transform 0.2s; }
.btn-confirm:hover { background:#45A049; transform:translateY(-1px); }
.btn-confirm:active { transform:translateY(1px); }

.btn-delete { background:#E53E3E; color:#fff; font-weight:600; border-radius:8px; padding:8px 12px; font-size:14px; display:inline-flex; align-items:center; justify-content:center; gap:8px; border:none; cursor:pointer; text-decoration:none; line-height:1; transition:background 0.15s ease, transform 0.08s ease; -webkit-appearance:none; appearance:none; }
.btn-delete:hover { background:#C53030; transform:translateY(-1px); }
.btn-delete:active { transform:translateY(1px); }
.btn-delete:focus { outline:2px solid rgba(229,62,62,0.25); outline-offset:2px; }
a.btn-delete { display:inline-flex; align-items:center; justify-content:center; }
td form { display:inline-block; margin:0 4px; }

/* Actions wrapper */
.actions-buttons { display:flex; gap:6px; align-items:center; justify-content:center; flex-wrap:wrap; }
.actions-cell { white-space:nowrap; }
</style>
