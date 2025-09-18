<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Room Management</h2>
            <div class="header-buttons">
                <a href="{{ route('rooms.create') }}" class="btn-new">+ New Room</a>
            </div>
        </div>
    </x-slot>

    <div class="container">

        <!-- 🔹 Search & Filter -->
        <form method="GET" action="{{ route('rooms.index') }}" class="search-filter-form">
            <input type="text" name="search" placeholder="Search room number..." value="{{ request('search') }}">
            <select name="room_type_id">
                <option value="">All Room Types</option>
                @foreach($roomTypes as $type)
                    <option value="{{ $type->id }}" {{ request('room_type_id')==$type->id?'selected':'' }}>{{ $type->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-search">Search</button>
        </form>

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
        .header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .header-title { font-weight:900; font-size:24px; color:#5C3A21; }
        .btn-new, .btn-search, .btn-edit, .btn-delete { font-weight:600; border:none; cursor:pointer; transition:0.2s; border-radius:6px; padding:6px 12px; }
        .btn-new { background:#E6A574; color:#5C3A21; }
        .btn-new:hover { background:#F4C38C; }
        .btn-search { background:#E6A574; color:#5C3A21; }
        .btn-search:hover { background:#F4C38C; }
        .btn-edit { background:#E6A574; color:#5C3A21; font-size:13px; }
        .btn-edit:hover { background:#F4C38C; }
        .btn-delete { background:#EF4444; color:#FFF; font-size:13px; margin-left:4px; }
        .btn-delete:hover { opacity:0.9; }
        .inline-form { display:inline; }

        .search-filter-form { display:flex; gap:6px; margin-bottom:12px; align-items:center; }
        .search-filter-form input, .search-filter-form select { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; }

        .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }
        .table-card { overflow-x:auto; }
        .room-table { width:100%; border-collapse:collapse; }
        .room-table th, .room-table td { border:1px solid #D97A4E; padding:6px 10px; text-align:left; }
        .room-table th { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; font-weight:700; }
        .room-table tr:hover { background:#FFF4E1; }

        .pagination { margin-top:16px; display:flex; justify-content:center; flex-wrap:wrap; gap:4px; }
        .pagination a { padding:6px 12px; border-radius:6px; border:1px solid #E6A574; color:#5C3A21; text-decoration:none; font-weight:600; transition:0.2s; }
        .pagination a:hover { background:#F4C38C; }
        .pagination a.active { background:#E6A574; color:#FFF5EC; border-color:#E6A574; }
        .pagination a.disabled { opacity:0.5; pointer-events:none; }
        .text-center { text-align:center; }
    </style>
</x-app-layout>
