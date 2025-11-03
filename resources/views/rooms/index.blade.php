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

            <!-- 🔹 Custom Dropdown: Room Type -->
            <div class="custom-dropdown">
                <div class="selected">{{ request('room_type_id') ? $roomTypes->where('id', request('room_type_id'))->first()->name : 'All Room Types' }}</div>
                <div class="dropdown-options">
                    <div class="dropdown-option" data-value="">All Room Types</div>
                    @foreach($roomTypes as $type)
                        <div class="dropdown-option" data-value="{{ $type->id }}">{{ $type->name }}</div>
                    @endforeach
                </div>
                <input type="hidden" name="room_type_id" value="{{ request('room_type_id') }}">
            </div>

            <!-- 🔹 Custom Dropdown: Room Order -->
            <div class="custom-dropdown">
                <div class="selected">{{ request('room_order') ? (request('room_order')=='asc' ? 'Ascending (1 → 50)' : 'Descending (50 → 1)') : 'Ascending (1 → 50)' }}</div>
                <div class="dropdown-options">
                    <div class="dropdown-option" data-value="asc">Ascending (1 → 50)</div>
                    <div class="dropdown-option" data-value="desc">Descending (50 → 1)</div>
                </div>
                <input type="hidden" name="room_order" value="{{ request('room_order','asc') }}">
            </div>

            <button type="submit" class="btn-search">Search</button>
        </form>

        <!-- 🔹 Refresh + New Room Buttons -->
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

        <!-- 🔹 Pagination -->
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
/* 🌴 Base Container */
.container{max-width:1180px;width:95%;margin:0 auto;background:linear-gradient(135deg,#FFFDFB,#FFF8F0);padding:24px;border-radius:16px;border:2px solid #E6A574;box-shadow:0 10px 25px rgba(0,0,0,0.15);display:flex;flex-direction:column;gap:14px;font-family:'Figtree',sans-serif;}
/* 🏷️ Header Row */
.rooms-header-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;}
.rooms-header{font-size:24px;font-weight:900;color:#5C3A21;text-align:left;flex:1;padding-bottom:8px;border-bottom:2px solid #D97A4E;margin-bottom:8px;}
/* 🔎 Search + Toolbar */
.search-refresh{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;margin-bottom:16px;gap:10px;}
.search-container{display:flex;gap:6px;align-items:center;flex-wrap:wrap;}
.search-input{padding:8px 12px;border-radius:8px;border:1px solid #E6A574;font-size:14px;background:#fff;}
.refresh-new-container{display:flex;gap:6px;}
/* 🔸 Buttons */
.btn-new,.btn-refresh,.btn-search{background:linear-gradient(90deg,#E6A574,#F4C38C);color:#5C3A21;font-weight:700;border:none;border-radius:10px;padding:10px 18px;font-size:15px;cursor:pointer;box-shadow:0 4px 10px rgba(0,0,0,0.15);text-decoration:none;transition:0.2s;}
.btn-new:hover,.btn-refresh:hover,.btn-search:hover{background:#D97A4E;color:#fff;}
.btn-edit{background:#F4C38C;color:#5C3A21;font-weight:600;border-radius:6px;padding:6px 12px;font-size:13px;border:none;cursor:pointer;transition:background 0.2s;}
.btn-edit:hover{background:#CF8C55;color:#fff;}
.btn-delete{background:#EF4444;color:#fff;font-weight:600;border-radius:6px;padding:6px 12px;font-size:13px;border:none;cursor:pointer;transition:background 0.2s;}
.btn-delete:hover{background:#B91C1C;}
/* 🧾 Table Card */
.card.table-card{background:linear-gradient(135deg,#FFFDFB,#FFF8F0);border-radius:16px;box-shadow:0 8px 20px rgba(0,0,0,0.12);padding:16px;border:none;overflow-x:auto;}
/* 🌿 Room Table */
.room-table{width:100%;min-width:1100px;table-layout:fixed;border-collapse:separate;border-spacing:0;text-align:center;background:transparent;border-radius:12px;overflow-x:auto;display:block;}
.room-table thead,.room-table tbody,.room-table tr{display:table;width:100%;table-layout:fixed;}
.room-table tbody{display:block;}
.room-table thead{background:linear-gradient(to right,#F4C38C,#E6A574);color:#5C3A21;border-radius:12px 12px 0 0;overflow:hidden;}
.room-table th,.room-table td{padding:12px 16px;font-size:14px;border-bottom:1px solid #D97A4E;border-right:1px solid #D97A4E;}
.room-table th:first-child,.room-table td:first-child{border-left:none;}
.room-table th:last-child,.room-table td:last-child{border-right:none;}
.room-table tbody tr:last-child td{border-bottom:none;}
.room-table tbody tr:hover{background:#FFF4E1;transition:background 0.2s;}
.room-table tbody tr td[colspan]{display:table-cell;width:auto;min-height:50px;line-height:normal;text-align:center;font-weight:400;color:#5C3A21;}
/* 🍊 Tangerine Scrollbar */
.card.table-card::-webkit-scrollbar{height:10px;width:10px;}
.card.table-card::-webkit-scrollbar-track{background:#FFF8F0;border-radius:10px;}
.card.table-card::-webkit-scrollbar-thumb{background:linear-gradient(180deg,#E6A574,#D97A4E);border-radius:10px;border:2px solid #FFF8F0;}
.card.table-card::-webkit-scrollbar-thumb:hover{background:linear-gradient(180deg,#D97A4E,#B8643A);}
.card.table-card{scrollbar-color:#E6A574 #FFF8F0;scrollbar-width:thin;}
/* 📄 Pagination */
.pagination{margin-top:16px;display:flex;justify-content:flex-end;gap:6px;flex-wrap:wrap;}
.pagination a,.pagination span{padding:6px 10px;border-radius:6px;border:1px solid #D97A4E;text-decoration:none;color:#5C3A21;font-weight:600;}
.pagination a:hover{background:#F4C38C;color:#5C3A21;}
.pagination .active{background:#E6A574;color:#fff;border:none;}
/* 🪶 Misc */
.hidden-id{display:none;}
.inline-form{display:inline;}
.text-center{text-align:center;}
/* 🔹 Custom Dropdown */
.custom-dropdown{position:relative;min-width:190px;max-width:240px;cursor:pointer;display:inline-block;margin-right:6px;}
.custom-dropdown .selected{padding:8px 12px;border:1px solid #E6A574;border-radius:8px;background:#fff;color:#5C3A21;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;transition:0.2s;}
.custom-dropdown .selected.active{border-color:#D97A4E;}
.dropdown-options{display:none;position:absolute;top:100%;left:0;width:100%;background:#fff;border:1px solid #E6A574;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.1);z-index:10;}
.custom-dropdown .selected.active+.dropdown-options{display:block;}
.dropdown-option{padding:8px 12px;cursor:pointer;font-weight:500;color:#5C3A21;transition:0.2s;}
.dropdown-option:hover{background:#E6A574;color:#fff;}

/* === 📱 Responsive Enhancements for Rooms Index === */

/* 💻 Large screens (>1200px) */
@media (min-width:1201px) {
  .room-table { table-layout:fixed; width:100%; border-collapse:collapse; word-wrap:break-word; }
  .room-table th, .room-table td { font-size:14px; padding:12px 16px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; }
  .btn-edit, .btn-delete { font-size:13px; padding:6px 12px; min-width:75px; }
  .btn-new, .btn-refresh, .btn-search { font-size:15px; padding:10px 18px; }
  .pagination { justify-content:flex-end; gap:8px; }
  .card.table-card { overflow-x:auto; transition:width 0.3s ease, padding 0.3s ease; }
}

/* 🖥️ Medium screens (769px–1200px) */
@media (min-width:769px) and (max-width:1200px) {
  .container { padding:20px; transition:max-width 0.3s ease; }
  .room-table { table-layout:fixed; width:100%; border-collapse:collapse; }
  .room-table th, .room-table td { font-size:13px; padding:10px 12px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; }
  .btn-edit, .btn-delete { font-size:12px; padding:5px 10px; min-width:70px; }
  .btn-new, .btn-refresh, .btn-search { font-size:14px; padding:8px 14px; }
  .search-container, .refresh-new-container { flex-wrap:wrap; justify-content:center; }
  .pagination { justify-content:center; gap:6px; }
  .card.table-card { overflow-x:auto; transition:width 0.3s ease, padding 0.3s ease; }
}

/* 📱 Small screens / tablets (481px–768px) */
@media (min-width:481px) and (max-width:768px) {
  .container { padding:16px; }
  .rooms-header { font-size:22px; text-align:center; border:none; }
  .search-refresh { flex-direction:column; align-items:center; gap:10px; }
  .search-container, .refresh-new-container { width:100%; justify-content:center; }
  .btn-new, .btn-refresh, .btn-search { width:100%; text-align:center; font-size:13px; padding:8px 12px; }
  .room-table { min-width:100%; display:block; overflow-x:auto; table-layout:auto; }
  .room-table th, .room-table td { font-size:12px; padding:8px 10px; white-space:nowrap; }
  .btn-edit, .btn-delete { font-size:11px; padding:4px 8px; min-width:60px; }
  .pagination { justify-content:center; gap:5px; }
  .card.table-card { overflow-x:auto; scrollbar-width:thin; }
}

/* 📞 Extra small screens / mobile (≤480px) */
@media (max-width:480px) {
  .container { padding:12px; }
  .rooms-header { font-size:20px; text-align:center; }
  .search-refresh { flex-direction:column; align-items:center; gap:8px; }
  .search-container { width:100%; justify-content:center; flex-direction:column; gap:8px; }
  .custom-dropdown, .search-input { width:100%; }
  .btn-new, .btn-refresh, .btn-search { width:100%; font-size:12px; padding:6px 10px; }
  .room-table { min-width:100%; display:block; overflow-x:auto; table-layout:auto; }
  .room-table th, .room-table td { font-size:11px; padding:6px 8px; white-space:nowrap; }
  .btn-edit, .btn-delete { font-size:10px; padding:3px 7px; min-width:55px; }
  .pagination { justify-content:center; flex-wrap:wrap; gap:4px; }
}

/* === 🧩 Sidebar Collapse Compatibility Fix === */
body.sidebar-collapsed .container { max-width:calc(100% - 80px); transition:max-width 0.3s ease; }
body.sidebar-expanded .container { max-width:calc(100% - 240px); transition:max-width 0.3s ease; }

body.sidebar-collapsed .card.table-card,
body.sidebar-expanded .card.table-card {
  overflow-x:auto;
  scrollbar-width:thin;
}

.room-table thead tr,
.room-table tbody tr {
  display:table;
  width:100%;
  table-layout:fixed;
}

@media (max-width:768px) {
  .room-table th, .room-table td { white-space:normal; text-overflow:unset; }
}


</style>

<!-- 🔹 JS Dropdown & Refresh -->
<script>
// 🔁 Refresh button functionality
document.getElementById('btn-refresh').addEventListener('click',()=>{window.location.href="{{ route('rooms.index') }}";});
// 🧭 Custom dropdown toggle logic
document.querySelectorAll('.custom-dropdown').forEach(dd=>{
    const selected=dd.querySelector('.selected');
    const options=dd.querySelectorAll('.dropdown-option');
    const hiddenInput=dd.querySelector('input[type="hidden"]');
    selected.addEventListener('click',()=>{selected.classList.toggle('active');});
    options.forEach(opt=>{
        opt.addEventListener('click',()=>{
            selected.textContent=opt.textContent;
            hiddenInput.value=opt.dataset.value;
            selected.classList.remove('active');
        });
    });
    document.addEventListener('click',e=>{if(!dd.contains(e.target))selected.classList.remove('active');});
});
</script>

</x-app-layout>
