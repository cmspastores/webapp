<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Renter Archive</h2>
            <div class="header-buttons">
                @if(!request()->routeIs('renters.index'))
                    <a href="{{ route('renters.index') }}" class="btn-back">← Back to List</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="container">
        <!-- Search + Refresh Controls -->
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
            </div>
        </div>

        <!-- Renter Table -->
        <div id="renter-list" class="card table-card">
            <table class="renter-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>ID</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($renters as $renter)
                        <tr>
                            <td>{{ $renter->renter_id }}</td>
                            <td>{{ $renter->full_name }}</td>
                            <td>{{ $renter->email }}</td>
                            <td>{{ $renter->phone ?? '-' }}</td>
                            <td>{{ $renter->unique_id }}</a></td>
                            <td>{{ $renter->created_at_formatted }}</td>
                            <td class="actions">
                                <form action="{{ route('renters.restore', $renter->renter_id) }}" method="POST" class="inline-form" onsubmit="return confirm('Restore this renter?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn-edit">Restore</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No renters found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                @if ($renters->lastPage() > 1)
                    <a href="{{ $renters->url(1) }}" class="{{ ($renters->currentPage() == 1) ? 'disabled' : '' }}">« First</a>
                    <a href="{{ $renters->previousPageUrl() }}" class="{{ ($renters->currentPage() == 1) ? 'disabled' : '' }}">‹ Prev</a>
                    @for ($i = 1; $i <= $renters->lastPage(); $i++)
                        <a href="{{ $renters->url($i) }}" class="{{ ($renters->currentPage() == $i) ? 'active' : '' }}">{{ $i }}</a>
                    @endfor
                    <a href="{{ $renters->nextPageUrl() }}" class="{{ ($renters->currentPage() == $renters->lastPage()) ? 'disabled' : '' }}">Next ›</a>
                    <a href="{{ $renters->url($renters->lastPage()) }}" class="{{ ($renters->currentPage() == $renters->lastPage()) ? 'disabled' : '' }}">Last »</a>
                @endif
            </div>
        </div>
    </div>

    <!-- 🔹 CSS -->
   <style>
/* Container & Header */
.container { max-width:1200px; margin:0 auto; padding:16px; }
.header-container{display:flex;justify-content:flex-end;align-items:center;margin-bottom:16px;position:relative}
.header-title{font:900 32px 'Figtree',sans-serif;color:#5C3A21;line-height:1.2;text-align:center;text-shadow:2px 2px 6px rgba(0,0,0,0.25);letter-spacing:1.2px;text-transform:uppercase;margin:0;position:absolute;left:50%;transform:translateX(-50%);-webkit-text-stroke:0.5px #5C3A21}
.header-buttons{display:flex;gap:10px;position:relative;z-index:1}

/* Buttons */
.btn-back { background:#D97A4E; color:#FFF5EC; font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; border-radius:6px; padding:6px 14px; text-decoration:none; display:inline-flex; align-items:center; transition:0.2s; } 
.btn-back:hover { background:#F4C38C; color:#5C3A21; } 
.btn-refresh { background:#E6A574; color:#5C3A21; font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; border-radius:6px; padding:8px 16px; transition:0.2s; } 
.btn-refresh:hover { background:#F4C38C; } 
.btn-search { background:#E6A574; color:#5C3A21; font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; border-radius:6px; padding:8px 16px; transition:0.2s; } 
.btn-search:hover { background:#F4C38C; } 
.btn-edit { background:#E6A574; color:#5C3A21; font-family:'Figtree',sans-serif; font-weight:600; font-size:12px; padding:3px 6px; border:none; cursor:pointer; border-radius:6px; transition:0.2s; } 
.btn-edit:hover { background:#F4C38C; } 
.btn-archive { background:#E6A574; color:#5C3A21; font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; border-radius:6px; padding:8px 16px; transition:0.2s; } 
.btn-archive:hover { background:#F4C38C; } 
.btn-delete { background:#EF4444; color:white; font-family:'Figtree',sans-serif; font-weight:600; font-size:13px; padding:4px 8px; border:none; cursor:pointer; border-radius:6px; transition:0.2s; margin-left:4px; } 
.btn-delete:hover { opacity:0.9; } 

/* Search + Filter Form */
.search-refresh { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px; } 
.search-container { display:flex; gap:6px; align-items:center; flex-wrap:wrap; } 
.search-input, .search-filter { padding:8px 12px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif; font-size:14px; } 
.search-filter { background:#fff; } 
.refresh-new-container { display:flex; gap:6px; } 

/* Cards & Tables */
.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); margin-bottom:16px; } 
.table-card { overflow-x:auto; } 
.renter-table { width:100%; border-collapse:collapse; min-width:700px; table-layout:auto; } 
.renter-table th, .renter-table td { border:1px solid #D97A4E; padding:6px 10px; text-align:left; } 
.renter-table th { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; font-weight:700; } 
.renter-table tr:hover { background:#FFF4E1; } 

/* Pagination */
.pagination { margin-top:16px; display:flex; justify-content:center; flex-wrap:wrap; gap:4px; } 
.pagination a { padding:6px 12px; border-radius:6px; border:1px solid #E6A574; color:#5C3A21; text-decoration:none; font-weight:600; transition:0.2s; } 
.pagination a:hover { background:#F4C38C; } 
.pagination a.active { background:#E6A574; color:#FFF5EC; border-color:#E6A574; } 
.pagination a.disabled { opacity:0.5; pointer-events:none; } 

/* Misc */
.inline-form { display:inline; } 
.text-link { color:#D97A4E; text-decoration:underline; font-weight:600; cursor:pointer; } 
.text-center { text-align:center; } 

/* Responsive Table */
@media (max-width:768px) { .renter-table th, .renter-table td { font-size:12px; padding:4px 6px; } } 
@media (max-width:600px) { .renter-table th, .renter-table td { font-size:11px; padding:3px 5px; } } 
@media (max-width:480px) { .renter-table th, .renter-table td { font-size:10px; padding:2px 4px; } .renter-table th:nth-child(5), .renter-table td:nth-child(5) { display:none; } } 
@media (max-width:360px) { .renter-table th, .renter-table td { font-size:9px; padding:2px 3px; } .renter-table th:nth-child(4), .renter-table td:nth-child(4) { display:none; } }
</style>


    <!-- 🔹 JS -->
    <script>
        document.getElementById('btn-refresh').addEventListener('click', () => { window.location.href = "{{ route('renters.index') }}"; });
    </script>
</x-app-layout>
