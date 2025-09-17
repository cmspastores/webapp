<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="rooms-header">Room Types</h2>
            <div class="header-buttons">
                <a href="{{ route('roomtypes.create') }}" class="btn-new">+ Add Room Type</a>
            </div>
        </div>
    </x-slot>

    <!-- 🔹 CSS Section -->
    <style>
        .container { max-width:1200px; margin:0 auto; padding:16px; }
        .header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .rooms-header { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; line-height:1.2; }
        .header-buttons { display:flex; gap:10px; }
        .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif; }
        .table-wrapper { overflow-x:auto; }
        table { width:100%; min-width:400px; border-collapse:collapse; font-size:14px; }
        th, td { border:1px solid #D97A4E; padding:6px 10px; text-align:left; white-space:nowrap; }
        th { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; font-weight:700; }
        td.ellipsis { max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        tr:hover { background:#FFF4E1; }
        .btn { padding:6px 14px; border:none; border-radius:8px; cursor:pointer; font-weight:600; text-decoration:none; transition:0.2s; font-family:'Figtree',sans-serif; }
        
        /* 🔹 Updated Add Room Type button */
        .btn-new { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:12px; padding:8px 18px; box-shadow:0 4px 10px rgba(0,0,0,0.15); transition:0.3s all; display:inline-block; }
        .btn-new:hover { background:linear-gradient(90deg,#F4C38C,#E6A574); transform:translateY(-2px); box-shadow:0 6px 14px rgba(0,0,0,0.2); color:#5C3A21; }

        .btn-new,.btn-refresh,.btn-search { background:#E6A574; color:#5C3A21; border-radius:8px; }
        .btn-new:hover,.btn-refresh:hover,.btn-search:hover { background:#F4C38C; }
        .btn-edit { background:#F4C38C; color:#5C3A21; padding:4px 8px; border-radius:6px; font-size:13px; }
        .btn-edit:hover { opacity:0.9; }
        .btn-delete { background:#EF4444; color:white; padding:4px 8px; border-radius:6px; font-size:13px; }
        .btn-delete:hover { opacity:0.9; }

        .pagination { margin-top:12px; display:flex; gap:4px; justify-content:center; flex-wrap:wrap; }
        .pagination a, .pagination span { padding:4px 8px; border-radius:6px; border:1px solid #E6A574; text-decoration:none; color:#5C3A21; font-family:'Figtree',sans-serif; }
        .pagination a:hover { background:#F4C38C; color:#5C3A21; }
        .pagination .active { background:#E6A574; color:#FFF; border:none; }
    </style>

    <div class="container">
        <div class="card">
            @if(session('success'))
                <div style="margin-bottom:12px;padding:6px 10px;background:#D1FAE5;color:#065F46;border-radius:6px;">{{ session('success') }}</div>
            @endif

            <div class="table-wrapper">
                <table>
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
                                <td class="ellipsis">{{ $type->name }}</td>
                                <td style="white-space:nowrap;">
                                    <div style="display:flex;gap:6px;">
                                        <a href="{{ route('roomtypes.edit', $type) }}" class="btn btn-edit">Edit</a>
                                        <form action="{{ route('roomtypes.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Delete this room type?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;color:#6B7280;">No room types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination">
                    {{ $roomTypes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
