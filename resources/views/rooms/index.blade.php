<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="rooms-header">Rooms Management</h2>
            <div class="header-buttons">
                <!-- Back button if needed in the future -->
                <button id="btn-back-to-list-header" class="btn-back hidden">← Back to List</button>
            </div>
        </div>
    </x-slot>


    <style>
        /* Container & Header */
        .container { max-width:1200px; margin:0 auto; padding:16px; }
        .header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }

        .rooms-header { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; line-height:1.2; }

        .header-buttons { display:flex; gap:10px; }


        /* Card */
        .card{background:linear-gradient(135deg,#FFFDFB,#FFF8F0);border-radius:16px;border:2px solid #E6A574;padding:16px;box-shadow:0 8px 20px rgba(0,0,0,0.12);font-family:'Figtree',sans-serif;}

        /* Buttons */
        .btn{padding:6px 14px;border:none;border-radius:6px;cursor:pointer;font-weight:600;text-decoration:none;transition:0.2s;}
        .btn-new,.btn-refresh,.btn-search{background:#E6A574;color:#5C3A21;}
        .btn-new:hover,.btn-refresh:hover,.btn-search:hover{background:#F4C38C;}
        .btn-gray{background:#FFF3DF;color:#5C4A32;border:1px solid #E6A574;}
        .btn-gray:hover{background:#F4C38C;color:#5C4A32;}
        .btn-yellow{background:linear-gradient(90deg,#F4C38C,#E6A574);color:#5C3A21;}
        .btn-yellow:hover{opacity:0.9;}
        .btn-red{background:linear-gradient(90deg,#F87171,#EF4444);color:white;}
        .btn-red:hover{opacity:0.9;}

        /* Search + Refresh Container */
        .search-refresh { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .search-container { display:flex; gap:6px; align-items:center; }
        .search-input, .search-filter { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; background:#FFF; font-family:'Figtree',sans-serif; color:#5C3A21; }
        .search-input::placeholder { color:#A67C52; }

        /* Table */
        .table-wrapper{overflow-x:auto;}
        table{width:100%;min-width:900px;border-collapse:collapse;font-size:14px;}
        th,td{border:1px solid #D97A4E;padding:6px 10px;text-align:left;white-space:nowrap;}
        th{background:linear-gradient(to right,#F4C38C,#E6A574);color:#5C3A21;font-weight:700;}
        td.ellipsis{max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
        tr:hover{background:#FFF4E1;}
        form.inline{display:inline;}
        .pagination { margin-top:12px; display:flex; gap:4px; justify-content:center; flex-wrap:wrap; }
        .pagination a, .pagination span { padding:4px 8px; border-radius:6px; border:1px solid #E6A574; text-decoration:none; color:#5C3A21; font-family:'Figtree',sans-serif; }
        .pagination a:hover { background:#F4C38C; color:#5C3A21; }
        .pagination .active { background:#E6A574; color:#FFF; border:none; }
    </style>

    <div class="container">
        {{-- 🔎 Search + Filter + Refresh --}}
        <div class="search-refresh">
            <div class="search-container">
                <form method="GET" action="{{ route('rooms.index') }}" class="search-form">
                    <input type="text" name="search" class="search-input" placeholder="Search by Room #" value="{{ $search ?? '' }}">
                    <select name="room_type_id" class="search-filter">
                        <option value="">-- All Room Types --</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}" {{ ($selectedRoomType ?? '') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-search">Search</button>
                </form>
            </div>
            <div class="header-buttons">
                <a href="{{ route('rooms.index') }}" class="btn btn-refresh">Refresh List</a>
                <a href="{{ route('rooms.create') }}" class="btn btn-new">+ Add Room</a>
            </div>
        </div>
        {{-- End Search + Filter + Refresh --}}

        {{-- Card Table --}}
        <div class="card">
            {{-- Success Message --}}
            @if(session('success'))
                <div style="margin-bottom:12px;padding:6px 10px;background:#D1FAE5;color:#065F46;border-radius:6px;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Room #</th>
                            <th>Room Type</th>
                            <th>Price</th>
                            <th># Occupants</th>
                            <th>Occupant</th>
                            <th>Start Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr>
                                <td class="ellipsis">{{ $room->room_number }}</td>
                                <td class="ellipsis">{{ $room->roomType->name ?? '—' }}</td>
                                <td>₱{{ number_format($room->room_price,2) }}</td>
                                <td>{{ $room->number_of_occupants ?? '—' }}</td>
                                <td class="ellipsis">{{ $room->occupant_name ?? '—' }}</td>
                                <td>{{ $room->start_date ?? '—' }}</td>
                                <td style="white-space:nowrap;">
                                    <div style="display:flex;gap:6px;">
                                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-yellow" style="font-size:13px;padding:4px 8px;">View</a>
                                        <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this room?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-red" style="font-size:13px;padding:4px 8px;">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;color:#6B7280;">No rooms available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="pagination">
                    {{ $rooms->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
        {{-- End Card Table --}}
    </div>
</x-app-layout>
