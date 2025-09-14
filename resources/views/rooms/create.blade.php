<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add New Room
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number</label>
                        <input type="text" name="room_number" id="room_number"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                      @error('room_number') border-red-500 @enderror"
                               value="{{ old('room_number') }}" required>
                        @error('room_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="room_type_id" class="block text-sm font-medium text-gray-700">Room Type</label>
                        <select name="room_type_id" id="room_type_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                       focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                       @error('room_type_id') border-red-500 @enderror">
                            <option value="">No Room Type</option>
                            @forelse($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @empty
                                <option value="" disabled>(No room types available)</option>
                            @endforelse
                        </select>
                        @error('room_type_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="room_price" class="block text-sm font-medium text-gray-700">Room Price</label>
                        <input type="number" step="0.01" name="room_price" id="room_price"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                      @error('room_price') border-red-500 @enderror"
                               value="{{ old('room_price') }}" required>
                        @error('room_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NEW FIELD --}}
                    <div>
                        <label for="number_of_occupants" class="block text-sm font-medium text-gray-700">Number of Occupants</label>
                        <input type="number" name="number_of_occupants" id="number_of_occupants"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                      focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                      @error('number_of_occupants') border-red-500 @enderror"
                               value="{{ old('number_of_occupants') }}">
                        @error('number_of_occupants')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- IMAGES --}}
                    <div>
                        <label for="image1" class="block text-sm font-medium text-gray-700">Image 1 (optional)</label>
                        <input type="file" name="image1" id="image1"
                            class="mt-1 block w-full text-sm text-gray-700
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100" accept="image/*">
                        @error('image1')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="image2" class="block text-sm font-medium text-gray-700">Image 2 (optional)</label>
                        <input type="file" name="image2" id="image2"
                            class="mt-1 block w-full text-sm text-gray-700
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100" accept="image/*">
                        @error('image2')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="image3" class="block text-sm font-medium text-gray-700">Image 3 (optional)</label>
                        <input type="file" name="image3" id="image3"
                            class="mt-1 block w-full text-sm text-gray-700
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100" accept="image/*">
                        @error('image3')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Save
                        </button>
                        <a href="{{ route('rooms.index') }}"
                           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>