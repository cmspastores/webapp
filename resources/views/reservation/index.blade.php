<x-app-layout>
    <div class="container">

        <!-- 🔹 Header Row -->
        <div class="reservations-header-row">
            <div class="reservations-header">Reservations</div>
        </div>

        <!-- 🔹 Create Button -->
        <div class="toolbar-row">
            <a href="{{ route('reservation.create') }}" class="btn-new">+ Create Reservation</a>
            <a href="{{ route('reservation.archived') }}" class="btn-archive">View Archives</a>
        </div>

        {{-- Pending Reservations --}}
        <div class="card table-card" style="margin-bottom:18px;">
            <h3 style="margin:0 0 12px 0; color:#5C3A21;">Pending Reservations</h3>
            @if(empty($pendingReservations) || $pendingReservations->isEmpty())
                <p>No pending reservations.</p>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingReservations as $reservation)
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
                                    <td class="actions-cell">
                                        <div class="actions-buttons">
                                        {{-- Confirm button --}}
                                        <form method="POST" action="{{ route('reservation.confirm', $reservation) }}" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn-confirm" title="Confirm reservation">Confirm</button>
                                        </form>

                                        {{-- Archive pending reservation (available to all authenticated users) --}}
                                        <form method="POST" action="{{ route('reservation.archive', $reservation) }}" style="display:inline" onsubmit="return confirm('Archive this pending reservation? You can view archived items from the Archive page.');">
                                            @csrf
                                            <button type="submit" class="btn-delete" title="Archive pending reservation">Archive</button>
                                        </form>

                                        {{-- Delete pending reservation --}}
                                        <form method="POST" action="{{ route('reservation.destroy', $reservation) }}" style="display:inline" onsubmit="return confirm('Delete this pending reservation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="Delete pending reservation">Delete</button>
                                        </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">No pending reservations.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for pending reservations -->
                <div class="pagination">
                    @if ($pendingReservations->lastPage() > 1)
                        <a href="{{ $pendingReservations->url(1) }}" class="{{ $pendingReservations->currentPage()==1?'disabled':'' }}">« First</a>
                        <a href="{{ $pendingReservations->previousPageUrl() }}" class="{{ $pendingReservations->currentPage()==1?'disabled':'' }}">‹ Prev</a>
                        @for ($i=1;$i<=$pendingReservations->lastPage();$i++)
                            <a href="{{ $pendingReservations->url($i) }}" class="{{ $pendingReservations->currentPage()==$i?'active':'' }}">{{ $i }}</a>
                        @endfor
                        <a href="{{ $pendingReservations->nextPageUrl() }}" class="{{ $pendingReservations->currentPage()==$pendingReservations->lastPage()?'disabled':'' }}">Next ›</a>
                        <a href="{{ $pendingReservations->url($pendingReservations->lastPage()) }}" class="{{ $pendingReservations->currentPage()==$pendingReservations->lastPage()?'disabled':'' }}">Last »</a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Confirmed Reservations --}}
        <div class="card table-card">
            <h3 style="margin:0 0 12px 0; color:#5C3A21;">Confirmed Reservations</h3>
            {{-- Search & Filter (confirmed only) --}}
            <div class="confirmed-controls" style="display:flex; gap:8px; align-items:center; margin:10px 0 12px 0;">
                <input type="text" id="confirmedSearchInput" placeholder="Search confirmed reservations..." style="padding:8px 10px; border-radius:8px; border:1px solid #E6A574; min-width:260px;" />
                <select id="confirmedFilterSelect" style="padding:8px 10px; border-radius:8px; border:1px solid #E6A574; background:#FFF9F3;">
                    <option value="all">All fields</option>
                    <option value="room">Room ID</option>
                    <option value="guest">Guest</option>
                    <option value="renter">Renter</option>
                    <option value="agreement">Agreement #</option>
                    <option value="status">Status</option>
                </select>
                <button type="button" id="confirmedSearchBtn" class="btn-new" style="padding:8px 14px; font-size:14px;">Search</button>
                <button type="button" id="confirmedRefreshBtn" class="btn-refresh" style="padding:8px 12px; font-size:14px;">Refresh Table</button>
            </div>
            @if(empty($confirmedReservations) || $confirmedReservations->isEmpty())
                <p>No confirmed reservations.</p>
            @else
                <div class="table-wrapper">
                    <table id="confirmedReservationsTable" class="reservations-table">
                        <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Guest</th>
                                <th>Renter</th>
                                <th>Renter Contact</th>
                                <th>Agreement #</th>
                                <th>Agreement Start</th>
                                <th>Agreement End</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($confirmedReservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->room_id }}</td>
                                    <td>{{ $reservation->first_name }} {{ $reservation->last_name }}</td>
                                    <td>{{ optional($reservation->renter)->full_name ?? '-' }}</td>
                                    <td>
                                        {{ optional($reservation->renter)->email ?? '-' }}
                                        @if(optional($reservation->renter)->phone)
                                            <div>{{ optional($reservation->renter)->phone }}</div>
                                        @endif
                                    </td>
                                    <td>{{ optional($reservation->agreement)->agreement_number ?? (optional($reservation->agreement)->agreement_id ? 'Agreement #' . optional($reservation->agreement)->agreement_id : '-') }}</td>
                                    <td>{{ $reservation->agreement && $reservation->agreement->start_date ? \Carbon\Carbon::parse($reservation->agreement->start_date)->format('M d, Y') : '-' }}</td>
                                    <td>{{ $reservation->agreement && $reservation->agreement->end_date ? \Carbon\Carbon::parse($reservation->agreement->end_date)->format('M d, Y') : '-' }}</td>
                                    <td>{{ $reservation->check_in_date ? \Carbon\Carbon::parse($reservation->check_in_date)->format('M d, Y') : '-' }}</td>
                                    <td>{{ $reservation->check_out_date ? \Carbon\Carbon::parse($reservation->check_out_date)->format('M d, Y') : '-' }}</td>

                                    <td><span class="status-badge {{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span></td>

                                    <td class="actions-cell">
                                        <div class="actions-buttons">
                                        {{-- Only admins can delete confirmed reservations --}}
                                        @if(auth()->user() && (auth()->user()->is_admin ?? false))
                                            <form method="POST" action="{{ route('reservation.destroy', $reservation) }}" style="display:inline" onsubmit="return confirm('Delete this confirmed reservation? This will permanently remove the reservation and its link to the agreement.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-delete" title="Delete confirmed reservation">Delete</button>
                                            </form>
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="text-center">No confirmed reservations.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (match other index pages) -->
                <div class="pagination">
                    @if ($confirmedReservations->lastPage() > 1)
                        <a href="{{ $confirmedReservations->url(1) }}" class="{{ $confirmedReservations->currentPage()==1?'disabled':'' }}">« First</a>
                        <a href="{{ $confirmedReservations->previousPageUrl() }}" class="{{ $confirmedReservations->currentPage()==1?'disabled':'' }}">‹ Prev</a>
                        @for ($i=1;$i<=$confirmedReservations->lastPage();$i++)
                            <a href="{{ $confirmedReservations->url($i) }}" class="{{ $confirmedReservations->currentPage()==$i?'active':'' }}">{{ $i }}</a>
                        @endfor
                        <a href="{{ $confirmedReservations->nextPageUrl() }}" class="{{ $confirmedReservations->currentPage()==$confirmedReservations->lastPage()?'disabled':'' }}">Next ›</a>
                        <a href="{{ $confirmedReservations->url($confirmedReservations->lastPage()) }}" class="{{ $confirmedReservations->currentPage()==$confirmedReservations->lastPage()?'disabled':'' }}">Last »</a>
                    @endif
                </div>
            @endif
        </div>

    </div>
</x-app-layout>

<style>
/* 🌅 Container */
.container { max-width:1100px; margin:0 auto; padding:20px; font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:12px; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); }

/* 🏷️ Header */
.reservations-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.reservations-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔹 Toolbar */
.toolbar-row { display:flex; justify-content:flex-start; margin-bottom:16px; gap:8px; }
.btn-new { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:10px; padding:10px 18px; font-size:15px; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-decoration:none; transition:0.2s; border:none; cursor:pointer; }
.btn-new:hover { background:#D97A4E; color:#fff; }

/* Archive button (subtle secondary) */
.btn-archive {
    background: #F3F4F6;
    color: #374151;
    border-radius:10px;
    padding:10px 14px;
    font-weight:700;
    text-decoration:none;
    border:1px solid #E5E7EB;
    display:inline-flex;
    align-items:center;
    justify-content:center;
}
.btn-archive:hover { background:#EDEFF2; color:#1F2937; }

/* 📋 Table Card */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }

/* 📑 Table */
.table-wrapper { overflow-x:auto; scrollbar-width:thin; scrollbar-color:#E6A574 #FFF8F0; }
.table-wrapper::-webkit-scrollbar { height:8px; width:8px; }
.table-wrapper::-webkit-scrollbar-thumb { background-color: #E6A574; border-radius:8px; border:2px solid #FFF8F0; }
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
.status-badge.verified { background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; }   /* confirmed */
.status-badge.unverified { background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; } /* pending/unverified */
.status-badge.cancelled { background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; }
.status-badge.checkedout { background:#E5E7EB; color:#374151; border:1px solid #D1D5DB; }

/* 🔘 Confirm & Delete Buttons */
.btn-confirm { background:#4CAF50; color:#fff; border:none; border-radius:8px; padding:8px 16px; font-size:14px; font-weight:600; cursor:pointer; transition:background 0.2s, transform 0.2s; }
.btn-confirm:hover { background:#45A049; transform:translateY(-1px); }
.btn-confirm:active { transform:translateY(1px); }

.btn-delete {
    display: inline-flex;           /* ensure button box */
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #E53E3E;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    line-height: 1;
    transition: background 0.15s ease, transform 0.08s ease;
    -webkit-appearance: none;       /* prevent UA button reset */
    appearance: none;
}

/* stronger hover/focus styles */
.btn-delete:hover { background: #C53030; transform: translateY(-1px); }
.btn-delete:active { transform: translateY(1px); }
.btn-delete:focus { outline: 2px solid rgba(229,62,62,0.25); outline-offset: 2px; }

/* fallback in case something targets anchor.btn-delete */
a.btn-delete { display: inline-flex; align-items:center; justify-content:center; }

/* ensure the button inside form inline layout keeps spacing */
td form { display: inline-block; margin: 0 4px; }

/* Actions wrapper for reservation tables to avoid using display:flex on TD */
.actions-buttons { display:flex; gap:6px; align-items:center; justify-content:center; flex-wrap:wrap; }
.actions-cell { white-space:nowrap; }

/* 📄 Pagination */
.pagination{margin-top:16px;display:flex;justify-content:flex-end;gap:6px;flex-wrap:wrap;}
.pagination a,.pagination span{padding:6px 10px;border-radius:6px;border:1px solid #D97A4E;text-decoration:none;color:#5C3A21;font-weight:600;}
.pagination a:hover{background:#F4C38C;color:#5C3A21;}
.pagination .active{background:#E6A574;color:#fff;border:none;}
</style>

<script>
    (function(){
        const input = document.getElementById('confirmedSearchInput');
        const filter = document.getElementById('confirmedFilterSelect');
        const btn = document.getElementById('confirmedSearchBtn');
    const refreshBtn = document.getElementById('confirmedRefreshBtn');
        const table = document.getElementById('confirmedReservationsTable');
        if(!table || !input || !filter || !btn) return;

        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        function matchesCellText(cell, term){
            if(!cell) return false;
            return cell.textContent.trim().toLowerCase().indexOf(term) !== -1;
        }

        function filterConfirmed(){
            const term = (input.value || '').trim().toLowerCase();
            const mode = filter.value || 'all';
            let visibleCount = 0;

            rows.forEach(row=>{
                const cells = row.querySelectorAll('td');
                let show = false;
                if(term === ''){
                    show = true;
                } else {
                    switch(mode){
                        case 'room':
                            show = matchesCellText(cells[0], term);
                            break;
                        case 'guest':
                            show = matchesCellText(cells[1], term);
                            break;
                        case 'renter':
                            show = matchesCellText(cells[2], term);
                            break;
                        case 'agreement':
                            show = matchesCellText(cells[4], term);
                            break;
                        case 'status':
                            show = matchesCellText(cells[9], term);
                            break;
                        default:
                            // all fields: check first 9 columns (exclude actions)
                            for(let i=0;i<=9;i++){
                                if(matchesCellText(cells[i], term)) { show = true; break; }
                            }
                    }
                }
                row.style.display = show ? '' : 'none';
                if(show) visibleCount++;
            });

            // show/hide a no-results row
            let noResults = document.getElementById('confirmedNoResults');
            if(!noResults){
                noResults = document.createElement('p');
                noResults.id = 'confirmedNoResults';
                noResults.style.textAlign = 'center';
                noResults.style.color = '#5C3A21';
                noResults.style.marginTop = '8px';
                noResults.textContent = 'No confirmed reservations match your search.';
                table.parentNode.appendChild(noResults);
            }
            noResults.style.display = visibleCount ? 'none' : '';
        }

    btn.addEventListener('click', filterConfirmed);
    refreshBtn && refreshBtn.addEventListener('click', ()=>{ window.location.href = "{{ route('reservation.index') }}"; });

        // trigger on Enter
        input.addEventListener('keyup', (e)=>{ if(e.key === 'Enter') filterConfirmed(); });

        // keep initial state (show all)
        document.addEventListener('DOMContentLoaded', ()=>filterConfirmed());
    })();
</script>
