<x-app-layout>
    <div class="container">

        <!-- 🔹 Header Row -->
        <div class="agreements-header-row">
            <div class="agreements-header">Archived Agreements</div>
        </div>

        @if(session('success'))
            <div class="card" style="margin-bottom:12px;padding:8px;background:#D1FAE5;color:#065F46;">
                {{ session('success') }}
            </div>
        @endif

        <!-- 🔍 Search + Sort Left | Ribbon Buttons Right -->
        <div class="toolbar-row">
            <form method="GET" action="{{ route('agreements.archived') }}" class="search-toolbar">
                <input type="text" name="search" placeholder="Search renter or room" value="{{ request('search') }}" class="search-input">

                <!-- 🔹 Custom Dropdown for Sort -->
                <div class="custom-dropdown">
                    <div class="selected">{{ request('sort') == 'desc' ? 'Room Number ↓ Descending' : 'Room Number ↑ Ascending' }}</div>
                    <div class="dropdown-options">
                        <div class="dropdown-option" data-value="asc">Room Number ↑ Ascending</div>
                        <div class="dropdown-option" data-value="desc">Room Number ↓ Descending</div>
                    </div>
                    <input type="hidden" name="sort" value="{{ request('sort', 'asc') }}">
                </div>

                <button type="submit" class="btn-search">Search</button>
            </form>

            <div class="toolbar-actions">
                <button type="button" id="btn-refresh" class="btn-ribbon">Refresh List</button>
                <a href="{{ route('agreements.index') }}" class="btn-ribbon">Back</a>
            </div>
        </div>

        <!-- 🔹 Toggle Buttons -->
        <div class="toggle-buttons">
            <button type="button" id="btn-dorm" class="btn-toggle active">Dorm / Monthly</button>
            <button type="button" id="btn-transient" class="btn-toggle">Transient / Daily</button>
        </div>

        <!-- 🔹 Dorm / Monthly Table -->
        <div class="card table-card" id="table-dorm">
            <div class="table-wrapper">
                <h3 class="table-title">Dorm / Monthly Agreements</h3>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements->where('rate_unit','!=','daily') as $a)
                            @php
                                $status = ($a->end_date && \Carbon\Carbon::parse($a->end_date)->isPast()) ? 'Expired' : ($a->is_active ? 'Active' : 'Terminated');
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
                                        <form method="POST" action="{{ route('agreements.destroy',$a) }}" onsubmit="return confirm('Permanently delete this archived agreement?');" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-red" type="submit">Delete</button>
                                        </form>
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

        <!-- 🔹 Transient / Daily Table -->
        <div class="card table-card" id="table-transient" style="display:none;">
            <div class="table-wrapper">
                <h3 class="table-title">Transient / Daily Agreements</h3>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements->where('rate_unit','daily') as $a)
                            @php
                                $status = ($a->end_date && \Carbon\Carbon::parse($a->end_date)->isPast()) ? 'Expired' : ($a->is_active ? 'Active' : 'Terminated');
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
                                        <form method="POST" action="{{ route('agreements.destroy',$a) }}" onsubmit="return confirm('Permanently delete this archived agreement?');" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-red" type="submit">Delete</button>
                                        </form>
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
        <div class="pagination">
            {{ $agreements->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- 🔹 JS for Table Toggle, Refresh & Dropdown -->
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
            window.location.href="{{ route('agreements.archived') }}";
        });

        // 🔹 Custom dropdown logic
        document.querySelectorAll('.custom-dropdown').forEach(dd => {
            const selected = dd.querySelector('.selected');
            const options = dd.querySelectorAll('.dropdown-option');
            const hiddenInput = dd.querySelector('input[type="hidden"]');

            selected.addEventListener('click', () => {
                selected.classList.toggle('active');
            });

            options.forEach(opt => {
                opt.addEventListener('click', () => {
                    selected.textContent = opt.textContent;
                    hiddenInput.value = opt.dataset.value;
                    selected.classList.remove('active');
                });
            });

            document.addEventListener('click', e => {
                if (!dd.contains(e.target)) selected.classList.remove('active');
            });
        });
    </script>
</x-app-layout>

<!-- 🔹 CSS -->
<style>
/* 🌅 Container */
.container { max-width:1100px; margin:0 auto; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); padding:20px; border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:12px; font-family:'Figtree',sans-serif; }

/* 🏷️ Header */
.agreements-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.agreements-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔧 Toolbar */
.toolbar-row { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:16px; gap:10px; }
.search-toolbar { display:flex; align-items:center; gap:8px; flex:0 1 auto; }
.toolbar-actions { display:flex; align-items:center; gap:6px; margin-left:auto; flex:0 0 auto; }

/* 🔍 Search */
.search-input { padding:8px 12px; border-radius:8px; border:1px solid #E6A574; font-size:14px; background:#fff; color:#5C3A21; font-family:'Figtree',sans-serif; }

/* 🔹 Custom Dropdown */
.custom-dropdown { position: relative; width: auto; min-width: 220px; cursor: pointer; }
.custom-dropdown .selected { padding: 8px 12px; border: 1px solid #E6A574; border-radius: 8px; background: #fff; color: #5C3A21; transition: 0.2s; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.custom-dropdown .selected.active { border-color: #D97A4E; }
.dropdown-options { display: none; position: absolute; top: 100%; left: 0; width: 100%; background: #fff; border: 1px solid #E6A574; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); z-index: 10; }
.custom-dropdown .selected.active + .dropdown-options { display: block; }
.dropdown-option { padding: 8px 12px; cursor: pointer; font-weight: 500; color: #5C3A21; transition: 0.2s; white-space: nowrap; }
.dropdown-option:hover { background: #E6A574; color: #fff; }

/* 🔹 Buttons */
.btn-search { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:600; border:none; border-radius:10px; padding:8px 16px; font-size:15px; cursor:pointer; transition:0.2s; }
.btn-search:hover { background:#D97A4E; color:#fff; }

/* 🔹 Ribbon Buttons */
.btn-ribbon { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:12px; padding:10px 18px; font-size:15px; box-shadow:0 4px 12px rgba(0,0,0,0.2); text-decoration:none; transition:0.3s; border:none; cursor:pointer; }
.btn-ribbon:hover { background:#D97A4E; color:#fff; transform:translateY(-2px); }

/* 🔹 Toggle Buttons */
.btn-toggle { padding:10px 20px; border-radius:12px; font-weight:700; font-size:15px; cursor:pointer; border:none; background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; box-shadow:0 4px 10px rgba(0,0,0,0.15); transition:0.3s; }
.btn-toggle.active { background:#D97A4E; color:#fff; box-shadow:0 6px 15px rgba(0,0,0,0.25); }
.btn-toggle:hover { opacity:0.9; transform:translateY(-2px); }
.btn-toggle + .btn-toggle { margin-left:8px; }

/* 📋 Table Card */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); padding:16px; border:none; overflow:hidden; }
.table-wrapper { width:100%; overflow-x:auto; max-width:100%; scrollbar-color:#E6A574 #FFF8F0; scrollbar-width:thin; }
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
.actions-buttons .btn-yellow, .actions-buttons .btn-red { padding:6px 12px; border-radius:6px; font-weight:600; font-size:13px; transition:0.2s; border:none; cursor:pointer; }
.actions-buttons .btn-yellow { background:#4C9F70; color:#fff; }
.actions-buttons .btn-yellow:hover { background:#6FC3A1; }
.actions-buttons .btn-red { background:#EF4444; color:#fff; }
.actions-buttons .btn-red:hover { background:#B91C1C; }

/* Inline Form */
.inline-form { display:inline; }

/* Pagination */
.pagination { margin-top:16px; display:flex; justify-content:flex-end; gap:6px; flex-wrap:wrap; }


/* === 📱 Responsive Enhancements for Agreements Page (Sidebar intact + container/table responsive) === */

/* 🖥️ Large screens (>1200px) */
@media(min-width:1201px) {
    body { display:flex; }
    .sidebar { width:250px; flex-shrink:0; }
    .container { flex:1; max-width:1100px; padding:20px; }
    .table-wrapper { overflow-x: visible; }
    .agreements-table { min-width:1200px; width:auto; }
}

/* 💻 Medium screens (769px - 1200px) */
@media(min-width:769px) and (max-width:1200px) {
    body { display:flex; }
    .sidebar { width:220px; flex-shrink:0; }
    .container { flex:1; width:100%; padding:16px; }
    .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .agreements-table { min-width:900px; width:auto; }
    .search-toolbar, .toolbar-actions { flex-wrap: wrap; gap:6px; }
}

/* 📱 Small screens / tablets (481px - 768px) */
@media(min-width:481px) and (max-width:768px) {
    body { display:flex; flex-direction:row; }
    .sidebar { width:180px; flex-shrink:0; }
    .container { flex:1; width:100%; padding:12px; }
    .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .agreements-table { min-width:700px; font-size:13px; width:auto; }

    /* Hide less-important columns */
    .agreements-table thead th:nth-child(4), 
    .agreements-table thead th:nth-child(5), 
    .agreements-table thead th:nth-child(6),
    .agreements-table tbody td:nth-child(4),
    .agreements-table tbody td:nth-child(5),
    .agreements-table tbody td:nth-child(6) {
        display: none;
    }
}

/* 📞 Extra small / mobile (≤480px) */
@media(max-width:480px) {
    body { display:flex; flex-direction:row; }
    .sidebar { width:150px; flex-shrink:0; }
    .container { flex:1; width:100%; padding:8px; }
    .agreements-header { font-size:16px; }
    .btn-search, .btn-refresh, .btn-new, .btn-archive, .btn-toggle { font-size:12px; padding:6px 6px; }

    /* Table fully scrollable and shrunk */
    .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; width:100%; }
    .agreements-table { min-width:600px; width:auto; font-size:12px; }

    /* Hide additional less-important columns */
    .agreements-table thead th:nth-child(3),
    .agreements-table thead th:nth-child(7),
    .agreements-table tbody td:nth-child(3),
    .agreements-table tbody td:nth-child(7) {
        display: none;
    }
}



</style>
