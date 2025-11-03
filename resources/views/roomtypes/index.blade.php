<x-app-layout>

<style>

/* Room Types Container */
.room-types-container { max-width:960px; margin:0 auto; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); padding:20px; border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); display:flex; flex-direction:column; gap:12px; font-family:'Figtree',sans-serif; }

/* Header and Add Button Layout */
.room-types-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }
.add-roomtype-wrapper { display:flex; justify-content:flex-end; margin-bottom:12px; }

/* Table Styling */
table.room-types-table { width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; text-align:center; background:transparent; border:none; margin-bottom:0; }
table.room-types-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; }
table.room-types-table th, table.room-types-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; }
table.room-types-table th:first-child { border-left:none; border-top-left-radius:12px; }
table.room-types-table th:last-child { border-right:none; border-top-right-radius:12px; }
table.room-types-table td:first-child { border-left:none; }
table.room-types-table td:last-child { border-right:none; }
table.room-types-table tbody tr:last-child td { border-bottom:none; }
table.room-types-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

/* Add Button */
.btn-new { background:#F4C38C; color:#5C3A21; font-weight:700; border-radius:8px; padding:8px 14px; font-size:14px; box-shadow:0 3px 8px rgba(0,0,0,0.12); text-decoration:none; border:none; }
.btn-new:hover { background:#D97A4E; color:#fff; }


/* Action Buttons (Equal Size) */
.btn-action { display:inline-flex; justify-content:center; align-items:center; font-weight:600; border-radius:6px; padding:6px 12px; font-size:13px; cursor:pointer; border:none; text-decoration:none; min-width:70px; }
.btn-edit { background:#EAB97E; color:#5C3A21; }
.btn-edit:hover { background:#CF8C55; color:#fff; }
.btn-delete { background:#EF4444; color:#FFF8F0; }
.btn-delete:hover { background:#B91C1C; color:#fff; }

/* Success Message */
.success-message { background:#E9F8F1; color:#256D47; border:1px solid #A7DCC0; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; text-align:center; }

/* Pagination */
.pagination { margin-top:16px; display:flex; justify-content:flex-end; gap:6px; flex-wrap:wrap; }
.pagination a, .pagination span { padding:6px 10px; border-radius:6px; border:1px solid #D97A4E; text-decoration:none; color:#5C3A21; font-weight:600; }
.pagination a:hover { background:#F4C38C; color:#5C3A21; }
.pagination .active { background:#E6A574; color:#fff; border:none; }

/* === 📱 Responsive Enhancements (Updated for button shrink) === */

/* Large screens (>1200px) */
@media(min-width:1201px) {
    .room-types-container { max-width:1200px; padding:28px; }
    .room-types-header { font-size:28px; }
    .btn-new { font-size:16px; padding:10px 18px; }
    table.room-types-table th, table.room-types-table td { font-size:15px; padding:14px 18px; }
    .btn-action { font-size:14px; padding:6px 14px; min-width:75px; }
    .pagination { justify-content:flex-end; gap:8px; }
}

/* Medium screens (769px - 1200px) */
@media(min-width:769px) and (max-width:1200px) {
    .room-types-container { max-width:1000px; padding:24px; }
    .room-types-header { font-size:26px; }
    .btn-new { font-size:15px; padding:9px 16px; }
    table.room-types-table th, table.room-types-table td { font-size:14px; padding:12px 16px; }
    .btn-action { font-size:13px; padding:5px 12px; min-width:70px; }
    .pagination { justify-content:flex-end; gap:6px; }
}

/* Small screens / tablets (481px - 768px) */
@media(min-width:481px) and (max-width:768px) {
    .room-types-container { max-width:100%; padding:20px; }
    .room-types-header { font-size:22px; text-align:center; }
    .add-roomtype-wrapper { justify-content:center; margin-bottom:10px; }
    .btn-new { width:100%; text-align:center; padding:8px 12px; font-size:14px; min-width:auto; }
    table.room-types-table th, table.room-types-table td { font-size:13px; padding:10px 12px; }
    .btn-action { font-size:12px; padding:4px 8px; min-width:auto; flex:1; }
    table.room-types-table td { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    table.room-types-table { table-layout:fixed; width:100%; }
    .pagination { justify-content:center; flex-wrap:wrap; gap:6px; }
}

/* Extra small screens / mobile (≤480px) */
@media(max-width:480px) {
    .room-types-container { padding:12px; }
    .room-types-header { font-size:20px; text-align:center; }
    .add-roomtype-wrapper { flex-direction:column; align-items:center; margin-bottom:8px; }
    .btn-new { width:100%; font-size:13px; padding:6px 10px; min-width:auto; }
    table.room-types-table th, table.room-types-table td { font-size:12px; padding:8px 10px; }
    .btn-action { font-size:11px; padding:3px 6px; min-width:auto; flex:1; }
    table.room-types-table td { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    table.room-types-table { table-layout:fixed; width:100%; }
    .pagination { justify-content:center; margin-top:10px; gap:4px; }
}


</style>

<div class="room-types-container">

    <!-- Header -->
    <div class="room-types-header">Room Types</div>

    <!-- Add Button under title -->
    <div class="add-roomtype-wrapper">
        <a href="{{ route('roomtypes.create') }}" class="btn-new">+ New Room Type</a>
    </div>

    @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    <!-- Table -->
    <table class="room-types-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roomTypes as $type)
            <tr>
                <td>{{ $type->id }}</td>
                <td>{{ $type->name }}</td>
                <td style="display:flex; gap:6px; justify-content:center;">
                    <a href="{{ route('roomtypes.edit', $type) }}" class="btn-action btn-edit">Edit</a>
                    <form action="{{ route('roomtypes.destroy', $type) }}" method="POST" onsubmit="return confirm('Delete this room type?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align:center;color:#5C3A21;">No room types found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        {{ $roomTypes->links() }}
    </div>
</div>

</x-app-layout>