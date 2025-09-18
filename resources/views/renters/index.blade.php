<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Renter Management</h2>
            <div class="header-buttons">
                <button id="btn-back-to-list-header" class="btn-back hidden">← Back to List</button>
            </div>
        </div>
    </x-slot>

    <div class="container">

        <!-- Search + Refresh + New Renter Controls -->
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
            </div>
        </div>

        <!-- Renter Table List -->
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
                            <td>
                                <a href="{{ route('renters.show', $renter->renter_id) }}" class="text-link">{{ $renter->unique_id }}</a>
                            </td>
                            <td>{{ $renter->created_at_formatted }}</td>
                            <td class="actions">
                                <a href="{{ route('renters.edit', $renter->renter_id) }}" class="btn-edit">Edit</a>
                                <form action="{{ route('renters.destroy', $renter->renter_id) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this renter?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">No renters found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- ✅ Custom pagination (no Tailwind) -->
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
        .container { max-width:1200px; margin:0 auto; padding:16px; }
        .header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; }
        .header-buttons { display:flex; gap:10px; }
        .btn-new, .btn-refresh, .btn-back, .btn-edit, .btn-delete, .btn-search { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; }
        .btn-new { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
        .btn-new:hover { background:#F4C38C; }
        .btn-refresh { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
        .btn-refresh:hover { background:#F4C38C; }
        .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; }
        .btn-back:hover { background:#F4C38C; color:#5C3A21; }
        .btn-edit { background:#E6A574; color:#5C3A21; padding:4px 8px; border-radius:4px; font-size:13px; }
        .btn-edit:hover { background:#F4C38C; }
        .btn-delete { background:#EF4444; color:white; padding:4px 8px; border-radius:4px; font-size:13px; margin-left:4px; }
        .btn-delete:hover { opacity:0.9; }
        .inline-form { display:inline; }
        .text-link { color:#D97A4E; text-decoration:underline; font-weight:600; cursor:pointer; }
        .search-refresh { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; gap: 12px; }
        .refresh-new-container { display:flex; gap:6px; }
        .search-container { display:flex; gap:6px; align-items:center; }
        .search-input, .search-filter { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; }
        .search-filter { background:#fff; font-family:'Figtree',sans-serif; }
        .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }
        .table-card { overflow-x:auto; }
        .renter-table { width:100%; border-collapse:collapse; }
        .renter-table th, .renter-table td { border:1px solid #D97A4E; padding:6px 10px; text-align:left; }
        .renter-table th { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; font-weight:700; }
        .renter-table tr:hover { background:#FFF4E1; }
        .hidden { display:none; }

        /* Custom pagination */
        .pagination { margin-top:16px; display:flex; justify-content:center; flex-wrap:wrap; gap:4px; }
        .pagination a { padding:6px 12px; border-radius:6px; border:1px solid #E6A574; color:#5C3A21; text-decoration:none; font-weight:600; transition:0.2s; }
        .pagination a:hover { background:#F4C38C; }
        .pagination a.active { background:#E6A574; color:#FFF5EC; border-color:#E6A574; }
        .pagination a.disabled { opacity:0.5; pointer-events:none; }
    </style>

    <!-- 🔹 JS -->
    <script>
        document.getElementById('btn-refresh').addEventListener('click', () => {
            window.location.href = "{{ route('renters.index') }}";
        });
    </script>

</x-app-layout>
