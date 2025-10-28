<x-app-layout>

<div class="container">

    <!-- 🔹 Header Row -->
    <div class="renters-header-row">
        <div class="renters-header">Archived Renters</div>
    </div>

    <!-- 🔹 Search + Refresh + Back Controls -->
    <div class="search-refresh">
        <form method="GET" action="{{ route('renters.deleted') }}" class="search-container">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search archive..." class="search-input">
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
            <a href="{{ route('renters.index') }}" class="btn-back">Back</a>
        </div>
    </div>

    <!-- 🔹 Archived Renters Table -->
    <div class="card table-card">
        <div class="table-wrapper">
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
                            <form action="{{ route('renters.restore', $renter->renter_id) }}" method="POST" class="inline-form" onsubmit="return confirm('Restore this renter?');">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn-edit">Restore</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No archived renters found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 🔹 Pagination -->
        <div class="pagination">
            {{ $renters->appends(request()->query())->links() }}
        </div>
    </div>

</div>

<!-- 🔹 CSS -->
<style>
/* 🌴 Container */
.container { max-width:1200px; margin:0 auto; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); padding:24px; border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:12px; font-family:'Figtree',sans-serif; }

/* 🏷 Header */
.renters-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.renters-header { font-size:26px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔹 Search + Controls */
.search-refresh { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:16px; gap:10px; }
.search-container { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
.search-input, .search-filter { padding:8px 12px; border-radius:8px; border:1px solid #E6A574; font-size:14px; background:#fff; }
.refresh-new-container { display:flex; gap:6px; justify-content:flex-end; }

/* 🔹 Buttons */
.btn-back { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:10px; padding:10px 18px; font-size:15px; text-decoration:none; box-shadow:0 4px 10px rgba(0,0,0,0.15); transition:0.2s; }
.btn-back:hover { background:#D97A4E; color:#fff; }
.btn-refresh, .btn-search { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:600; border:none; border-radius:10px; padding:10px 18px; cursor:pointer; transition:0.2s; }
.btn-refresh:hover, .btn-search:hover { background:#D97A4E; color:#fff; }
.btn-edit { background:#F4C38C; color:#5C3A21; font-weight:600; border-radius:6px; padding:6px 12px; border:none; cursor:pointer; transition:0.2s; }
.btn-edit:hover { background:#CF8C55; color:#fff; }

/* 🔹 Card & Table */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); padding:16px; border:none; }
.table-wrapper { overflow-x:auto; border-radius:12px; scrollbar-width:thin; scrollbar-color:#E6A574 #FFF8F0; max-height:480px; }
.table-wrapper::-webkit-scrollbar { height:8px; width:8px; }
.table-wrapper::-webkit-scrollbar-thumb { background-color:#E6A574; border-radius:8px; }
.table-wrapper::-webkit-scrollbar-track { background:#FFF8F0; }
.renter-table { width:1200px; border-collapse:separate; border-spacing:0; text-align:center; table-layout:auto; border-radius:12px; overflow:hidden; }
.renter-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; }
.renter-table th, .renter-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; white-space:nowrap; }
.renter-table th:first-child, .renter-table td:first-child { display:none; }
.renter-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

/* 🔹 Actions */
.actions-cell { display:flex; justify-content:center; gap:6px; }

/* 🔹 Pagination */
.pagination { margin-top:16px; display:flex; justify-content:flex-end; gap:6px; flex-wrap:wrap; }
.pagination a, .pagination span { padding:6px 10px; border-radius:6px; border:1px solid #D97A4E; text-decoration:none; color:#5C3A21; font-weight:600; }
.pagination a:hover { background:#F4C38C; color:#5C3A21; }
.pagination .active { background:#E6A574; color:#fff; border:none; }
.pagination a.disabled { opacity:0.5; pointer-events:none; }

/* 🔹 Misc */
.inline-form { display:inline; }
.text-center { text-align:center; }

/* 🔹 Responsive */
@media (max-width:768px) { .renter-table th, .renter-table td { font-size:12px; padding:8px 10px; } }
@media (max-width:480px) { .renter-table th, .renter-table td { font-size:11px; padding:4px 6px; } }
@media (max-width:360px) { .renter-table th, .renter-table td { font-size:10px; padding:3px 4px; } }

</style>

<script>
document.getElementById('btn-refresh').addEventListener('click', () => {
    window.location.href = "{{ route('renters.deleted') }}";
});
</script>

</x-app-layout>
