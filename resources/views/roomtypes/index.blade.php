<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Room Types
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow-sm">

                @if(session('success'))
                    <div class="mb-4 px-4 py-2 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex justify-end mb-3">
                    <a href="{{ route('roomtypes.create') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        + Add Room Type
                    </a>
                </div>

                <table class="w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Name</th>
                            <th class="px-4 py-2 border w-40">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roomTypes as $type)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border">{{ $type->id }}</td>
                                <td class="px-4 py-2 border">{{ $type->name }}</td>
                                <td class="px-4 py-2 border">
                                    <div class="flex gap-2">
                                        <a href="{{ route('roomtypes.edit', $type) }}"
                                           class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
                                            Edit
                                        </a>
                                        <form action="{{ route('roomtypes.destroy', $type) }}" method="POST"
                                              onsubmit="return confirm('Delete this room type?')">
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
                                <td colspan="3" class="px-4 py-2 text-center text-gray-500">
                                    No room types found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $roomTypes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>