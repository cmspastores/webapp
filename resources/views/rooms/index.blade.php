<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Room Management</h2>
        </div>
    </x-slot>

    <div class="container">

        <!-- 🔹 Search + Refresh + New Room Controls -->
        <div id="search-refresh" class="search-refresh">
            <form method="GET" action="{{ route('rooms.index') }}" class="search-container">
                <input type="text" name="search" placeholder="Search room number..." value="{{ request('search') }}" class="search-input">
                <select name="room_type_id" class="search-filter">
                    <option value="">All Room Types</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ request('room_type_id')==$type->id?'selected':'' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-search">Search</button>
            </form>

            <div class="refresh-new-container">
                <button id="btn-refresh" class="btn-refresh">Refresh List</button>
                <a href="{{ route('rooms.create') }}" class="btn-new">+ New Room</a>
            </div>
        </div>

        <!-- 🔹 Room Table -->
        <div class="card table-card">
            <table class="room-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Room#</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Occupants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td>{{ $room->id }}</td>
                            <td>{{ $room->room_number }}</td>
                            <td>{{ $room->roomType->name ?? '-' }}</td>
                            <td>{{ number_format($room->room_price,2) }}</td>
                            <td>{{ $room->number_of_occupants ?? '-' }}</td>
                            <td>
                                <a href="{{ route('rooms.edit',$room) }}" class="btn-edit">Edit</a>
                                <form action="{{ route('rooms.destroy',$room) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete this room?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No rooms found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- 🔹 Custom Pagination -->
            <div class="pagination">
                @if ($rooms->lastPage() > 1)
                    <a href="{{ $rooms->url(1) }}" class="{{ $rooms->currentPage()==1?'disabled':'' }}">« First</a>
                    <a href="{{ $rooms->previousPageUrl() }}" class="{{ $rooms->currentPage()==1?'disabled':'' }}">‹ Prev</a>
                    @for ($i=1;$i<=$rooms->lastPage();$i++)
                        <a href="{{ $rooms->url($i) }}" class="{{ $rooms->currentPage()==$i?'active':'' }}">{{ $i }}</a>
                    @endfor
                    <a href="{{ $rooms->nextPageUrl() }}" class="{{ $rooms->currentPage()==$rooms->lastPage()?'disabled':'' }}">Next ›</a>
                    <a href="{{ $rooms->url($rooms->lastPage()) }}" class="{{ $rooms->currentPage()==$rooms->lastPage()?'disabled':'' }}">Last »</a>
                @endif
            </div>
        </div>
    </div>

    <!-- 🔹 CSS -->
    <style>
        .container { max-width:1200px; margin:0 auto; padding:16px; font-family:'Figtree',sans-serif; }
        .header-container { margin-bottom:16px; }
        .header-title { font-weight:900; font-size:24px; color:#5C3A21; }

        /* Search + Refresh + New Room toolbar */
        .search-refresh { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
        .search-container { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
        .search-input, .search-filter { padding:8px 12px; border-radius:6px; border:1px solid #E6A574; font-size:14px; }
        .search-filter { background:#fff; }
        .refresh-new-container { display:flex; gap:6px; }

        /* Buttons */
        .btn-new, .btn-refresh, .btn-search, .btn-edit, .btn-delete { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; border-radius:6px; padding:8px 16px; }
        .btn-new { background:#E6A574; color:#5C3A21; }
        .btn-new:hover { background:#F4C38C; }
        .btn-refresh { background:#E6A574; color:#5C3A21; }
        .btn-refresh:hover { background:#F4C38C; }
        .btn-search { background:#E6A574; color:#5C3A21; }
        .btn-search:hover { background:#F4C38C; }
        .btn-edit { background:#E6A574; color:#5C3A21; font-size:13px; padding:4px 8px; }
        .btn-edit:hover { background:#F4C38C; }
        .btn-delete { background:#EF4444; color:#FFF; font-size:13px; padding:4px 8px; margin-left:4px; }
        .btn-delete:hover { opacity:0.9; }

        /* Cards & Tables */
        .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); margin-bottom:16px; }
        .table-card { overflow-x:auto; }
        .room-table { width:100%; border-collapse:collapse; }
        .room-table th, .room-table td { border:1px solid #D97A4E; padding:6px 10px; text-align:left; }
        .room-table th { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; font-weight:700; }
        .room-table tr:hover { background:#FFF4E1; }

        /* Pagination */
        .pagination { margin-top:16px; display:flex; justify-content:center; flex-wrap:wrap; gap:4px; }
        .pagination a { padding:6px 12px; border-radius:6px; border:1px solid #E6A574; color:#5C3A21; text-decoration:none; font-weight:600; transition:0.2s; }
        .pagination a:hover { background:#F4C38C; }
        .pagination a.active { background:#E6A574; color:#FFF5EC; border-color:#E6A574; }
        .pagination a.disabled { opacity:0.5; pointer-events:none; }

        /* Misc */
        .inline-form { display:inline; }
        .text-center { text-align:center; }
    </style>

    <!-- 🔹 JS -->
    <script>
        document.getElementById('btn-refresh').addEventListener('click', () => {
            window.location.href = "{{ route('rooms.index') }}";
        });
    </script>

</x-app-layout>
