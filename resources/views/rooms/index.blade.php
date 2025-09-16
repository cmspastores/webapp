<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rooms
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex justify-end mb-3">
                    <a href="{{ route('rooms.create') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        + Add Room
                    </a>
                </div>

                {{-- 🔎 Search + Filter Form --}}
                <form method="GET" action="{{ route('rooms.index') }}" class="mb-4 flex flex-wrap gap-2">
                    <input type="text"
                           name="search"
                           class="border border-gray-300 rounded px-3 py-2 w-48"
                           placeholder="Search by Room #"
                           value="{{ $search ?? '' }}">

                    <select name="room_type_id" class="border border-gray-300 rounded px-3 py-2">
                        <option value="">-- All Room Types --</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ ($selectedRoomType ?? '') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Search
                    </button>
                    <a href="{{ route('rooms.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Reset
                    </a>
                </form>
                {{-- 🔎 End Search + Filter Form --}}

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 border">Room #</th>
                                <th class="px-4 py-2 border">Room Type</th>
                                <th class="px-4 py-2 border">Price</th>
                                <th class="px-4 py-2 border"># Occupants</th> {{-- NEW --}}
                                <th class="px-4 py-2 border">Occupant</th>
                                <th class="px-4 py-2 border">Start Date</th>
                                <th class="px-4 py-2 border w-48">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $room->room_number }}</td>
                                    <td class="px-4 py-2 border">{{ $room->roomType->name ?? '—' }}</td>
                                    <td class="px-4 py-2 border">₱{{ number_format($room->room_price, 2) }}</td>
                                    <td class="px-4 py-2 border">{{ $room->number_of_occupants ?? '—' }}</td> {{-- NEW --}}
                                    <td class="px-4 py-2 border">{{ $room->occupant_name ?? '—' }}</td>
                                    <td class="px-4 py-2 border">{{ $room->start_date ?? '—' }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex gap-2">
                                            <a href="{{ route('rooms.edit', $room) }}"
                                               class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
                                                Edit
                                            </a>
                                            <form action="{{ route('rooms.destroy', $room) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this room?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-2 text-center text-gray-500">
                                        No rooms available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>