<x-app-layout>
    <div class="container">

        <!-- 🔹 Header Row -->
        <div class="agreements-header-row">
            <div class="agreements-header">Agreements</div>
        </div>

        @if(session('success'))
            <div class="card" style="margin-bottom:12px;padding:8px;background:#D1FAE5;color:#065F46;">
                {{ session('success') }}
            </div>
        @endif

        <!-- 🔍 Search + Sort Left | Buttons Right -->
        <div class="toolbar-row">
            <form method="GET" action="{{ route('agreements.index') }}" class="search-toolbar">
                <input type="text" name="search" placeholder="Search renter or room" value="{{ request('search') }}" class="search-input">
                <select name="sort" class="search-filter">
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Room Number ↑ Ascending</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Room Number ↓ Descending</option>
                </select>
                <button type="submit" class="btn-search">Search</button>
            </form>

            <div class="toolbar-actions">
                <button type="button" id="btn-refresh" class="btn-refresh">Refresh List</button>
                @if(auth()->user() && auth()->user()->is_admin)
                    <a href="{{ route('agreements.create') }}" class="btn-new">+ New Agreement</a>
                @endif
                <a href="{{ route('agreements.archived') }}" class="btn-archive">View Archive</a>
            </div>
        </div>

        <!-- 🔹 Toggle Buttons -->
        <div class="toggle-buttons" style="display:flex; gap:10px; margin-bottom:16px;">
            <button type="button" id="btn-dorm" class="btn-toggle active">Dorm / Monthly</button>
            <button type="button" id="btn-transient" class="btn-toggle">Transient / Daily</button>
        </div>

        <!-- 🔹 Dorm / Monthly Agreements Table -->
        <div class="card table-card" id="table-dorm">
            <div class="table-wrapper">
                <h3 style="text-align:left;margin-bottom:8px;color:#5C3A21;">Dorm / Monthly Agreements</h3>
                <table class="agreements-table">
                    <thead>
                        <tr>
                            <th>Renter</th>
                            <th>Room</th>
                            <th>Agreement Date</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Rent</th>
                            <th>Status</th>
                            <th style="white-space:nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements->where('rate_unit','!=','daily') as $a)
                            @php
                                $now = \Carbon\Carbon::now();
                                if ($a->end_date && \Carbon\Carbon::parse($a->end_date)->isPast()) {
                                    $status = 'Expired';
                                } elseif ($a->is_active) {
                                    $status = 'Active';
                                } else {
                                    $status = 'Terminated';
                                }
                            @endphp
                            <tr>
                                <td>{{ $a->renter->full_name ?? '—' }}</td>
                                <td>{{ $a->room->room_number ?? '—' }}{{ optional($a->room->roomType)->name ? ' - ' . optional($a->room->roomType)->name : '' }}</td>
                                <td>{{ optional($a->agreement_date)->format('M d, Y') }}</td>
                                <td>{{ optional($a->start_date)->format('M d, Y') }}</td>
                                <td>{{ optional($a->end_date)->format('M d, Y') }}</td>
                                <td>₱{{ number_format($a->monthly_rent ?? $a->rate, 2) }} /month</td>
                                <td><span class="status-badge {{ strtolower($status) }}">{{ $status }}</span></td>
                                <td class="actions-cell">
                                    <div class="actions-buttons">
                                        <a href="{{ route('agreements.edit',$a) }}" class="btn-yellow">View</a>
                                        @if(auth()->user() && auth()->user()->is_admin)
                                            @if($a->is_active)
                                                <form method="POST" action="{{ route('agreements.terminate',$a) }}" onsubmit="return confirm('Terminate this agreement?');" class="inline-form">@csrf<button class="btn-red" type="submit">Terminate</button></form>
                                            @else
                                                <form method="POST" action="{{ route('agreements.renew',$a) }}" onsubmit="return confirm('Renew this agreement for another year?');" class="inline-form">@csrf<button class="btn-green" type="submit">Renew</button></form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No dorm agreements found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 🔹 Transient / Daily Agreements Table -->
        <div class="card table-card" id="table-transient" style="display:none;">
            <div class="table-wrapper">
                <h3 style="text-align:left;margin-bottom:8px;color:#5C3A21;">Transient / Daily Agreements</h3>
                <table class="agreements-table">
                    <thead>
                        <tr>
                            <th>Renter</th>
                            <th>Room</th>
                            <th>Agreement Date</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Rent</th>
                            <th>Status</th>
                            <th style="white-space:nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements->where('rate_unit','daily') as $a)
                            @php
                                $now = \Carbon\Carbon::now();
                                if ($a->end_date && \Carbon\Carbon::parse($a->end_date)->isPast()) {
                                    $status = 'Expired';
                                } elseif ($a->is_active) {
                                    $status = 'Active';
                                } else {
                                    $status = 'Terminated';
                                }
                            @endphp
                            <tr>
                                <td>{{ $a->renter->full_name ?? '—' }}</td>
                                <td>{{ $a->room->room_number ?? '—' }}{{ optional($a->room->roomType)->name ? ' - ' . optional($a->room->roomType)->name : '' }}</td>
                                <td>{{ optional($a->agreement_date)->format('M d, Y') }}</td>
                                <td>{{ optional($a->start_date)->format('M d, Y') }}</td>
                                <td>{{ optional($a->end_date)->format('M d, Y') }}</td>
                                <td>₱{{ number_format($a->rate, 2) }} /day</td>
                                <td><span class="status-badge {{ strtolower($status) }}">{{ $status }}</span></td>
                                <td class="actions-cell">
                                    <div class="actions-buttons">
                                        <a href="{{ route('agreements.edit',$a) }}" class="btn-yellow">View</a>
                                        @if(auth()->user() && auth()->user()->is_admin)
                                            @if($a->is_active)
                                                <form method="POST" action="{{ route('agreements.terminate',$a) }}" onsubmit="return confirm('Terminate this agreement?');" class="inline-form">@csrf<button class="btn-red" type="submit">Terminate</button></form>
                                            @else
                                                <form method="POST" action="{{ route('agreements.renew',$a) }}" onsubmit="return confirm('Renew this agreement for another year?');" class="inline-form">@csrf<button class="btn-green" type="submit">Renew</button></form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">No transient agreements found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 🔹 Pagination -->
        <div class="pagination" style="margin-top:12px;">
            {{ $agreements->appends(request()->query())->links() }}
        </div>

    </div>

    <!-- 🔹 JS for Table Toggle & Refresh -->
    <script>
        const btnDorm = document.getElementById('btn-dorm');
        const btnTransient = document.getElementById('btn-transient');
        const tableDorm = document.getElementById('table-dorm');
        const tableTransient = document.getElementById('table-transient');

        btnDorm.addEventListener('click', () => {
            tableDorm.style.display = 'block';
            tableTransient.style.display = 'none';
            btnDorm.classList.add('active');
            btnTransient.classList.remove('active');
        });

        btnTransient.addEventListener('click', () => {
            tableDorm.style.display = 'none';
            tableTransient.style.display = 'block';
            btnDorm.classList.remove('active');
            btnTransient.classList.add('active');
        });

        document.getElementById('btn-refresh').addEventListener('click', () => {
            window.location.href="{{ route('agreements.index') }}";
        });
    </script>

</x-app-layout>

<style>
/* 🌅 Container */
.container { max-width:1100px; margin:0 auto; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); padding:20px; border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:12px; font-family:'Figtree',sans-serif; }

/* 🏷️ Header */
.agreements-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.agreements-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔧 Toolbar Layout */
.toolbar-row { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:16px; gap:10px; }
.search-toolbar { display:flex; align-items:center; gap:8px; flex:0 1 auto; }
.toolbar-actions { display:flex; align-items:center; gap:6px; margin-left:auto; flex:0 0 auto; }

/* 🔍 Search + Filter */
.search-input, .search-filter { padding:8px 12px; border-radius:8px; border:1px solid #E6A574; font-size:14px; background:#fff; font-family:'Figtree',sans-serif; color:#5C3A21; }
.btn-search { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:600; border:none; border-radius:10px; padding:8px 16px; font-size:15px; cursor:pointer; transition:0.2s; }
.btn-search:hover { background:#D97A4E; color:#fff; }

/* 🔹 Buttons */
.btn-refresh, .btn-new, .btn-archive { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:10px; padding:10px 18px; font-size:15px; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-decoration:none; transition:0.2s; border:none; cursor:pointer; }
.btn-refresh:hover, .btn-new:hover, .btn-archive:hover { background:#D97A4E; color:#fff; }

/* 🔹 Toggle Buttons */
.btn-toggle { padding:8px 16px; border-radius:10px; font-weight:700; cursor:pointer; border:none; background:#E6A574; color:#5C3A21; transition:0.2s; }
.btn-toggle.active { background:#D97A4E; color:#fff; }
.btn-toggle:hover { opacity:0.85; }

/* 📋 Table Card */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); padding:16px; border:none; overflow:hidden; }
.table-wrapper { width:100%; overflow-x:auto; overflow-y:hidden; max-width:100%; scrollbar-color:#E6A574 #FFF8F0; scrollbar-width:thin; }
.table-wrapper::-webkit-scrollbar { height:8px; }
.table-wrapper::-webkit-scrollbar-thumb { background:#E6A574; border-radius:10px; }
.table-wrapper::-webkit-scrollbar-track { background:#FFF8F0; border-radius:10px; }

/* 📑 Agreements Table */
.agreements-table { width:1200px; min-width:100%; border-collapse:separate; border-spacing:0; text-align:center; table-layout:auto; background:transparent; border-radius:12px; overflow:hidden; }
.agreements-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; border-radius:12px 12px 0 0; overflow:hidden; }
.agreements-table th, .agreements-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; text-align:center; white-space:nowrap; }
.agreements-table th:first-child, .agreements-table td:first-child { border-left:none; }
.agreements-table th:last-child, .agreements-table td:last-child { border-right:none; }
.agreements-table tbody tr:last-child td { border-bottom:none; }
.agreements-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

/* 🟩 Status Badges */
.status-badge { padding:4px 10px; border-radius:20px; font-weight:600; font-size:13px; display:inline-block; }
.status-badge.active { background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; }
.status-badge.terminated { background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; }
.status-badge.expired { background:#E5E7EB; color:#374151; border:1px solid #D1D5DB; }

/* ⚙️ Action Buttons */
.actions-buttons { display:flex; gap:6px; justify-content:center; flex-wrap:nowrap; align-items:center; width:100%; }
.actions-buttons .btn-yellow, .actions-buttons .btn-red, .actions-buttons .btn-green { padding:6px 12px; border-radius:6px; font-weight:600; font-size:13px; transition:0.2s; border:none; cursor:pointer; }
.actions-buttons .btn-yellow { background:#4C9F70; color:#fff; }
.actions-buttons .btn-yellow:hover { background:#6FC3A1; }
.actions-buttons .btn-red { background:#EF4444; color:#fff; }
.actions-buttons .btn-red:hover { background:#B91C1C; }
.actions-buttons .btn-green { background:#F4C38C; color:#5C3A21; }
.actions-buttons .btn-green:hover { background:#CF8C55; }

/* 🧾 Misc */
.inline-form { display:inline; }
.pagination { margin-top:16px; display:flex; justify-content:flex-end; gap:6px; flex-wrap:wrap; }
</style>
