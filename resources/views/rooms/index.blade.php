<x-app-layout>

    <div class="container">

        <!-- 🔹 Header Row (Title + Add Button) -->
        <div class="rooms-header-row">
            <div class="rooms-header">Rooms</div>
        </div>

        <!-- 🔹 Search + Refresh Controls -->
        <div id="search-refresh" class="search-refresh">
            <form method="GET" action="{{ route('rooms.index') }}" class="search-container">
                <input type="text" name="search" placeholder="Search room number..." value="{{ request('search') }}" class="search-input">
                <select name="room_type_id" class="search-filter">
                    <option value="">All Room Types</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ request('room_type_id')==$type->id?'selected':'' }}>{{ $type->name }}</option>
                    @endforeach
                </select>

                <select name="room_order" class="search-filter">
                    <option value="asc" {{ request('room_order')=='asc'?'selected':'' }}>Ascending (1 → 50)</option>
                    <option value="desc" {{ request('room_order')=='desc'?'selected':'' }}>Descending (50 → 1)</option>
                </select>

                <button type="submit" class="btn-search">Search</button>
            </form>

            <div class="refresh-new-container">
                <button id="btn-refresh" class="btn-refresh">Refresh List</button>
                <a href="{{ route('rooms.create') }}" class="btn-new">+ New Room</a>
            </div>


        <!-- 🔹 Room Table -->
        <div class="card table-card">
            <table class="room-table">
                <thead>
                    <tr>
                        <th class="hidden-id">ID</th>
                        <th>Room #</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Occupants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td class="hidden-id">{{ $room->id }}</td>
                            <td>{{ $room->room_number }}</td>
                            <td>{{ $room->roomType->name ?? '-' }}</td>
                            <td>₱{{ number_format($room->room_price, 2) }}</td>
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
/* 🌴 Container */
.container { max-width:960px; margin:0 auto; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); padding:20px; border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:12px; font-family:'Figtree',sans-serif; }

/* 🏷️ Header Row (Title + Button) */
.rooms-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.rooms-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔹 Search + Refresh + New Room toolbar */
.search-refresh { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:16px; gap:10px; }
.search-container { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
.search-input, .search-filter { padding:8px 12px; border-radius:8px; border:1px solid #E6A574; font-size:14px; background:#fff; }
.refresh-new-container { display:flex; gap:6px; }

/* 🔸 Buttons */

.btn-new { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:10px; padding:10px 18px; font-size:15px; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-decoration:none; transition:0.2s; }
.btn-new:hover { background:#D97A4E; color:#fff; }

.btn-refresh, .btn-search { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:600; border:none; border-radius:10px; padding:10px 18px; font-size:15px; cursor:pointer; transition:0.2s; }
.btn-refresh:hover, .btn-search:hover { background:#D97A4E; color:#fff; }

.btn-edit { background:#F4C38C; color:#5C3A21; font-weight:600; border-radius:6px; padding:6px 12px; font-size:13px; border:none; cursor:pointer; transition:background 0.2s; }
.btn-edit:hover { background:#CF8C55; color:#fff; }

.btn-delete { background:#EF4444; color:#fff; font-weight:600; border-radius:6px; padding:6px 12px; font-size:13px; border:none; cursor:pointer; transition:background 0.2s; }
.btn-delete:hover { background:#B91C1C; }

/* 🧾 Table Card */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); padding:16px; border:none; overflow-x:auto; }

.room-table { width:100%; border-collapse:separate; border-spacing:0; text-align:center; table-layout:fixed; background:transparent; border-radius:12px; overflow:hidden; }

/* Table Header */
.room-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; border-radius:12px 12px 0 0; overflow:hidden; }

/* Cells */
.room-table th, .room-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; }

/* Rounded header corners */
.room-table thead tr:first-child th:first-child { border-top-left-radius:12px; }
.room-table thead tr:first-child th:last-child { border-top-right-radius:12px; }

/* Edge cleanup */
.room-table th:first-child, .room-table td:first-child { border-left:none; }
.room-table th:last-child, .room-table td:last-child { border-right:none; }
.room-table tbody tr:last-child td { border-bottom:none; }

/* Row hover effect */
.room-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }


/* 📄 Pagination */
.pagination { margin-top:16px; display:flex; justify-content:flex-end; gap:6px; flex-wrap:wrap; }
.pagination a, .pagination span { padding:6px 10px; border-radius:6px; border:1px solid #D97A4E; text-decoration:none; color:#5C3A21; font-weight:600; }
.pagination a:hover { background:#F4C38C; color:#5C3A21; }
.pagination .active { background:#E6A574; color:#fff; border:none; }

/* 🪶 Misc */
.hidden-id { display:none; }
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
