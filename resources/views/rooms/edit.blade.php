<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Room
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('rooms.update', $room) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number</label>
                        <input type="text" name="room_number" id="room_number"
                               value="{{ old('room_number', $room->room_number) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('room_number') border-red-500 @enderror"
                               required>
                        @error('room_number')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="room_type_id" class="block text-sm font-medium text-gray-700">Room Type</label>
                        <select name="room_type_id" id="room_type_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                    focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm
                                    @error('room_type_id') border-red-500 @enderror">
                            <option value="">No Room Type</option>
                            @forelse($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ (old('room_type_id', $room->room_type_id) == $type->id) ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @empty
                                <option value="" disabled>(No room types available)</option>
                            @endforelse
                        </select>
                        @error('room_type_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="room_price" class="block text-sm font-medium text-gray-700">Room Price</label>
                        <input type="number" step="0.01" name="room_price" id="room_price"
                               value="{{ old('room_price', $room->room_price) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('room_price') border-red-500 @enderror"
                               required>
                        @error('room_price')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NEW FIELD --}}
                    <div class="mb-4">
                        <label for="number_of_occupants" class="block text-sm font-medium text-gray-700">Number of Occupants</label>
                        <input type="number" name="number_of_occupants" id="number_of_occupants"
                               value="{{ old('number_of_occupants', $room->number_of_occupants) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('number_of_occupants') border-red-500 @enderror">
                        @error('number_of_occupants')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- IMAGE 1 --}}
                    <div class="mb-4">
                        <label for="image1" class="block text-sm font-medium text-gray-700">Image 1 (optional)</label>

                        {{-- Existing image --}}
                        <div id="current_image1_wrapper" class="mb-2">
                            @if($room->image1)
                                <img id="current_image1" src="{{ asset('storage/' . $room->image1) }}" 
                                    alt="Room Image 1" class="w-32 h-32 object-cover rounded">
                                <button type="button" onclick="removeExistingImage('image1')" 
                                        class="mt-2 text-red-600 text-sm hover:underline">
                                    Remove Image
                                </button>
                            @endif
                        </div>

                        {{-- File input with preview --}}
                        <input type="file" name="image1" id="image1"
                            class="mt-1 block w-full text-sm text-gray-700
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100"
                            accept="image/*" onchange="previewImage(this, 'preview_image1')">

                        {{-- Preview for new selection --}}
                        <div id="preview_image1_wrapper" class="mt-2 hidden">
                            <img id="preview_image1" class="w-32 h-32 object-cover rounded">
                            <button type="button" onclick="removeNewImage('image1', 'preview_image1_wrapper')" 
                                    class="mt-2 text-red-600 text-sm hover:underline">
                                Remove Selection
                            </button>
                        </div>

                        {{-- Hidden input to mark removal of old image --}}
                        <input type="hidden" name="remove_image1" id="remove_image1" value="0">
                    </div>

                    {{-- IMAGE 2 --}}
                    <div class="mb-4">
                        <label for="image2" class="block text-sm font-medium text-gray-700">Image 2 (optional)</label>

                        <div id="current_image2_wrapper" class="mb-2">
                            @if($room->image2)
                                <img id="current_image2" src="{{ asset('storage/' . $room->image2) }}" 
                                    alt="Room Image 2" class="w-32 h-32 object-cover rounded">
                                <button type="button" onclick="removeExistingImage('image2')" 
                                        class="mt-2 text-red-600 text-sm hover:underline">
                                    Remove Image
                                </button>
                            @endif
                        </div>

                        <input type="file" name="image2" id="image2"
                            class="mt-1 block w-full text-sm text-gray-700
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100"
                            accept="image/*" onchange="previewImage(this, 'preview_image2')">

                        <div id="preview_image2_wrapper" class="mt-2 hidden">
                            <img id="preview_image2" class="w-32 h-32 object-cover rounded">
                            <button type="button" onclick="removeNewImage('image2', 'preview_image2_wrapper')" 
                                    class="mt-2 text-red-600 text-sm hover:underline">
                                Remove Selection
                            </button>
                        </div>

                        <input type="hidden" name="remove_image2" id="remove_image2" value="0">
                    </div>

                    {{-- IMAGE 3 --}}
                    <div class="mb-4">
                        <label for="image3" class="block text-sm font-medium text-gray-700">Image 3 (optional)</label>

                        <div id="current_image3_wrapper" class="mb-2">
                            @if($room->image3)
                                <img id="current_image3" src="{{ asset('storage/' . $room->image3) }}" 
                                    alt="Room Image 3" class="w-32 h-32 object-cover rounded">
                                <button type="button" onclick="removeExistingImage('image3')" 
                                        class="mt-2 text-red-600 text-sm hover:underline">
                                    Remove Image
                                </button>
                            @endif
                        </div>

                        <input type="file" name="image3" id="image3"
                            class="mt-1 block w-full text-sm text-gray-700
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100"
                            accept="image/*" onchange="previewImage(this, 'preview_image3')">

                        <div id="preview_image3_wrapper" class="mt-2 hidden">
                            <img id="preview_image3" class="w-32 h-32 object-cover rounded">
                            <button type="button" onclick="removeNewImage('image3', 'preview_image3_wrapper')" 
                                    class="mt-2 text-red-600 text-sm hover:underline">
                                Remove Selection
                            </button>
                        </div>

                        <input type="hidden" name="remove_image3" id="remove_image3" value="0">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update
                        </button>
                        <a href="{{ route('rooms.index') }}" 
                           class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Show preview of newly chosen file
    function previewImage(input, previewId) {
        const file = input.files[0];
        if (file) {
            const previewWrapper = document.getElementById(previewId + '_wrapper');
            const previewImg = document.getElementById(previewId);
            previewImg.src = URL.createObjectURL(file);
            previewWrapper.classList.remove('hidden');
        }
    }

    // Remove newly chosen file before saving
    function removeNewImage(inputId, wrapperId) {
        const input = document.getElementById(inputId);
        input.value = ''; // clear input
        document.getElementById(wrapperId).classList.add('hidden');
    }

    // Remove existing saved image
    function removeExistingImage(imageField) {
        // hide existing preview
        document.getElementById('current_' + imageField + '_wrapper').innerHTML = '';
        // set hidden input flag so backend knows to delete
        document.getElementById('remove_' + imageField).value = '1';
    }
</script>