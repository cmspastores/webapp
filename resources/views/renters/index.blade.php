<x-app-layout>

<div class="container">

    <!-- 🔹 Header Row (Title) -->
    <div class="renters-header-row">
        <div class="renters-header">Renters</div>
    </div>

    <!-- 🔹 Search + Refresh + New Renter Controls -->
    <div id="search-refresh" class="search-refresh">
        <form method="GET" action="{{ route('renters.index') }}" class="search-container">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search renters..." class="search-input">
            <select name="filter" class="search-filter">
                <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All</option>
                <option value="name" {{ request('filter') == 'name' ? 'selected' : '' }}>Name</option>
                <option value="email" {{ request('filter') == 'email' ? 'selected' : '' }}>Email</option>
                <option value="phone" {{ request('filter') == 'phone' ? 'selected' : '' }}>Phone</option>
            </select>
            <button type="submit" class="btn-search">Search</button>
        </form>

        <div class="refresh-new-container">
            <button id="btn-refresh" class="btn-refresh">Refresh List</button>
            <a href="{{ route('renters.create') }}" class="btn-new">+ New Renter</a>
            <a href="{{ route('renters.deleted') }}" class="btn-new">View Archive</a>
        </div>
    </div>

    <!-- 🔹 Renter Table List -->
    <div class="card table-card">
        <table class="renter-table">
            <thead>
                <tr>
                    <th class="hidden-id">ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($renters as $renter)
                <tr>
                    <td class="hidden-id">{{ $renter->renter_id }}</td>
                    <td>{{ $renter->full_name }}</td>
                    <td>{{ $renter->email ?? '-' }}</td>
                    <td>{{ $renter->phone ?? '-' }}</td>
                    <td>{{ $renter->created_at_formatted }}</td>
                    <td class="actions-cell">
                        <div class="actions-buttons">
                            <a href="{{ route('renters.show', $renter->renter_id) }}" class="btn-view">View</a>
                            <a href="{{ route('renters.edit', $renter->renter_id) }}" class="btn-edit">Edit</a>
                            <form action="{{ route('renters.destroy', $renter->renter_id) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this renter?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No renters found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- 🔹 Pagination -->
        <div class="pagination">
            @if ($renters->lastPage() > 1)
                <a href="{{ $renters->url(1) }}" class="{{ $renters->currentPage()==1?'disabled':'' }}">« First</a>
                <a href="{{ $renters->previousPageUrl() }}" class="{{ $renters->currentPage()==1?'disabled':'' }}">‹ Prev</a>
                @for ($i=1;$i<=$renters->lastPage();$i++)
                    <a href="{{ $renters->url($i) }}" class="{{ $renters->currentPage()==$i?'active':'' }}">{{ $i }}</a>
                @endfor
                <a href="{{ $renters->nextPageUrl() }}" class="{{ $renters->currentPage()==$renters->lastPage()?'disabled':'' }}">Next ›</a>
                <a href="{{ $renters->url($renters->lastPage()) }}" class="{{ $renters->currentPage()==$renters->lastPage()?'disabled':'' }}">Last »</a>
            @endif
        </div>
    </div>
</div>

<!-- 🔹 CSS -->
<style>
/* 🌴 Container */
.container { max-width:960px; margin:0 auto; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); padding:20px; border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:12px; font-family:'Figtree',sans-serif; }

/* 🏷️ Header Row */
.renters-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.renters-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔹 Search + Refresh + New Renter toolbar */
.search-refresh { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:16px; gap:10px; }
.search-container { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
.search-input, .search-filter { padding:8px 12px; border-radius:8px; border:1px solid #E6A574; font-size:14px; background:#fff; }
.refresh-new-container { display:flex; gap:6px; }

/* 🔸 Action Buttons in Table */
.actions-buttons { display:flex; gap:6px; justify-content:center; flex-wrap:nowrap; align-items:center; width:100%; }
.actions-buttons form.inline-form { margin:0; }

/* 🔹 Buttons */
.btn-new { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:10px; padding:10px 18px; font-size:15px; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-decoration:none; transition:0.2s; }
.btn-new:hover { background:#D97A4E; color:#fff; }
.btn-refresh, .btn-search { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:600; border:none; border-radius:10px; padding:10px 18px; font-size:15px; cursor:pointer; transition:0.2s; }
.btn-refresh:hover, .btn-search:hover { background:#D97A4E; color:#fff; }
.btn-edit { background:#F4C38C; color:#5C3A21; font-weight:600; border-radius:6px; padding:6px 12px; font-size:13px; border:none; cursor:pointer; transition:background 0.2s; }
.btn-edit:hover { background:#CF8C55; color:#fff; }
.btn-delete { background:#EF4444; color:#fff; font-weight:600; border-radius:6px; padding:6px 12px; font-size:13px; border:none; cursor:pointer; transition:background 0.2s; }
.btn-delete:hover { background:#B91C1C; }
.btn-view { background:#4C9F70; color:#fff; font-weight:600; border-radius:6px; padding:6px 12px; font-size:13px; text-decoration:none; transition:0.2s; margin-right:4px; }
.btn-view:hover { background:#6FC3A1; }

/* 🧾 Card & Table */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); padding:16px; border:none; overflow-x:auto; }
.renter-table { width:100%; border-collapse:separate; border-spacing:0; text-align:center; table-layout:auto; background:transparent; border-radius:12px; overflow:hidden; }
.renter-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; border-radius:12px 12px 0 0; overflow:hidden; }
.renter-table th, .renter-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; }
.renter-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }
.renter-table thead tr:first-child th:first-child { border-top-left-radius:12px; }
.renter-table thead tr:first-child th:last-child { border-top-right-radius:12px; }
.renter-table th:first-child, .renter-table td:first-child { border-left:none; }
.renter-table th:last-child, .renter-table td:last-child { border-right:none; }
.renter-table tbody tr:last-child td { border-bottom:none; }

/* 🔹 Pagination */
.pagination { margin-top:16px; display:flex; justify-content:flex-end; gap:6px; flex-wrap:wrap; }
.pagination a, .pagination span { padding:6px 10px; border-radius:6px; border:1px solid #D97A4E; text-decoration:none; color:#5C3A21; font-weight:600; }
.pagination a:hover { background:#F4C38C; color:#5C3A21; }
.pagination .active { background:#E6A574; color:#fff; border:none; }
.pagination a.disabled { opacity:0.5; pointer-events:none; }

/* Misc */
.hidden-id { display:none; }
.inline-form { display:inline; }
.text-center { text-align:center; }

/* Responsive tweaks */
@media (max-width:768px) { .renter-table th, .renter-table td { font-size:12px; padding:8px 10px; } }
@media (max-width:480px) { .renter-table th, .renter-table td { font-size:11px; padding:4px 6px; } .renter-table th:nth-child(5), .renter-table td:nth-child(5) { display:none; } }
@media (max-width:360px) { .renter-table th, .renter-table td { font-size:10px; padding:3px 4px; } .renter-table th:nth-child(4), .renter-table td:nth-child(4) { display:none; } }

</style>

<!-- 🔹 JS -->
<script>
    document.getElementById('btn-refresh').addEventListener('click', () => {
        window.location.href = "{{ route('renters.index') }}";
    });
</script>

</x-app-layout>
