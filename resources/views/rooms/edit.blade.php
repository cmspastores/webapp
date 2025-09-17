<x-app-layout>
    <!-- 🔹 Header Section -->
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Edit Room</h2>
            <div class="header-buttons">
                <a href="{{ route('rooms.index') }}" class="btn-back">← Back to List</a>
            </div>
        </div>
    </x-slot>

    <div class="container">
        <!-- 🔹 Edit Room Card -->
        <div class="card form-card">
            <form action="{{ route('rooms.update', $room) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <!-- Room Number -->
                    <div>
                        <label for="room_number">Room#</label>
                        <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $room->room_number) }}" required>
                        @error('room_number')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Room Type -->
                    <div>
                        <label for="room_type_id">Room Type</label>
                        <select name="room_type_id" id="room_type_id">
                            <option value="">No Room Type</option>
                            @forelse($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ (old('room_type_id', $room->room_type_id) == $type->id)?'selected':'' }}>{{ $type->name }}</option>
                            @empty
                                <option disabled>(No room types)</option>
                            @endforelse
                        </select>
                        @error('room_type_id')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Room Price -->
                    <div>
                        <label for="room_price">Price</label>
                        <input type="number" step="0.01" name="room_price" id="room_price" value="{{ old('room_price', $room->room_price) }}" required>
                        @error('room_price')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Number of Occupants -->
                    <div>
                        <label for="number_of_occupants">Occupants</label>
                        <input type="number" name="number_of_occupants" id="number_of_occupants" value="{{ old('number_of_occupants', $room->number_of_occupants) }}">
                        @error('number_of_occupants')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Images -->
                    @foreach(['image1','image2','image3'] as $img)
                        <div class="full-width">
                            <label>{{ ucfirst($img) }} (optional)</label>
                            <div class="img-wrapper">
                                <!-- Existing Image -->
                                @if($room->$img)
                                    <img id="current_{{ $img }}" src="{{ asset('storage/'.$room->$img) }}" class="img-preview">
                                    <button type="button" onclick="removeExistingImage('{{ $img }}')" class="btn-back small-btn">Remove</button>
                                @endif

                                <!-- New File Input -->
                                <input type="file" name="{{ $img }}" class="file-input" accept="image/*" onchange="previewImage(this,'preview_{{ $img }}')">

                                <!-- Preview of Newly Chosen File -->
                                <div id="preview_{{ $img }}_wrapper" class="img-wrapper" style="display:none;">
                                    <img id="preview_{{ $img }}" class="img-preview">
                                    <button type="button" onclick="removeNewImage('{{ $img }}','preview_{{ $img }}_wrapper')" class="btn-back small-btn">Remove</button>
                                </div>

                                <!-- Hidden Input -->
                                <input type="hidden" name="remove_{{ $img }}" id="remove_{{ $img }}" value="0">
                                @error($img)<div class="error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-confirm">Save Changes</button>
                    <a href="{{ route('rooms.index') }}" class="btn-back">Back</a>
                </div>
            </form>
        </div>
    </div>

    <!-- 🔹 JS Section -->
    <script>
        function previewImage(input, id) {
            const f = input.files[0];
            if(f) {
                document.getElementById(id).src = URL.createObjectURL(f);
                document.getElementById(id+'_wrapper').style.display = 'flex';
            }
        }
        function removeNewImage(inputId, wrapperId) {
            document.getElementsByName(inputId)[0].value = '';
            document.getElementById(wrapperId).style.display = 'none';
        }
        function removeExistingImage(field) {
            document.getElementById('remove_'+field).value = '1';
            const current = document.getElementById('current_'+field);
            if(current) current.remove();
        }
    </script>
</x-app-layout>

<!-- 🔹 CSS Section -->
<style>
    .container { max-width:1200px; margin:0 auto; padding:16px; }
    .header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; }
    .header-buttons { display:flex; gap:10px; }

    .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif; }
    .form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
    .form-card input, .form-card select { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; width:100%; }
    .full-width { grid-column: span 2; }

    .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-confirm:hover { background:#F4C38C; }

    .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    .btn-back:hover { background:#F4C38C; color:#5C3A21; }
    .btn-back.small-btn { padding:2px 6px; font-size:12px; }

    .file-input { border:none; background:#FFF3DF; color:#5C4A32; font-weight:500; padding:6px 8px; border-radius:6px; cursor:pointer; }
    .img-preview { width:80px; height:80px; object-fit:cover; border-radius:6px; margin-right:8px; }
    .img-wrapper { display:flex; align-items:center; gap:4px; margin-bottom:4px; }
    .error { color:#e07b7b; font-size:12px; margin-top:4px; }

    .form-actions { margin-top:12px; display:flex; gap:8px; }
</style>
