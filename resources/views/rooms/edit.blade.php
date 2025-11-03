<x-app-layout>
 
  

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
                        <div class="roomtype-row">
                            <select name="room_type_id" id="room_type_id">
                                <option value="">No Room Type</option>
                                @forelse($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ (old('room_type_id', $room->room_type_id) == $type->id)?'selected':'' }}>
                                        {{ $type->name }}
                                    </option>
                                @empty
                                    <option disabled>(No room types)</option>
                                @endforelse
                            </select>

                            <!-- Button beside dropdown -->
                            @if(auth()->user() && auth()->user()->is_admin)
                                <a href="{{ route('roomtypes.index') }}" class="btn-back">Edit</a>
                            @endif
                        </div>
                        @error('room_type_id')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Room Price -->
                    <div>
                        <label for="room_price">Price</label>
                        <input type="number" step="0.01" name="room_price" id="room_price" value="{{ old('room_price', $room->room_price) }}" required min="0.01">

                        @error('room_price')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Number of Occupants -->
                    <div>
                        <label for="number_of_occupants">Occupants</label>
                        <input type="number" name="number_of_occupants" id="number_of_occupants" value="{{ old('number_of_occupants') ?? 1 }}" required min="1">

                        @error('number_of_occupants')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Image -->
                    <div class="full-width">
                        <label>Room Image</label>
                        <div class="img-wrapper">
                            <!-- Existing Image -->
                            @if($room->image)
                                <div id="current_image_wrapper" class="img-wrapper">
                                    <img id="current_image" src="{{ asset('storage/'.$room->image) }}" class="img-preview">
                                    <button type="button" onclick="removeExistingImage('image')" class="btn-back small-btn">Remove</button>
                                </div>
                            @endif

                            <!-- New File Input -->
                            <div id="file_input_wrapper" @if($room->image) style="display:none;" @endif>
                                <input type="file" name="image" class="file-input" accept="image/*" onchange="previewImage(this,'preview_image')">
                            </div>

                            <!-- Preview of Newly Chosen File -->
                            <div id="preview_image_wrapper" class="img-wrapper" style="display:none;">
                                <img id="preview_image" class="img-preview">
                                <button type="button" onclick="removeNewImage('image','preview_image_wrapper')" class="btn-back small-btn">Remove</button>
                            </div>

                            <!-- Hidden Input -->
                            <input type="hidden" name="remove_image" id="remove_image" value="0">
                            @error('image')<div class="error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    @if(auth()->user() && auth()->user()->is_admin)
                        <button type="submit" class="btn-confirm">Save Changes</button>
                    @endif
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
            // Show file input again
            document.getElementById('file_input_wrapper').style.display = 'block';
        }

        function removeExistingImage(field) {
            document.getElementById('remove_'+field).value = '1';
            const currentWrapper = document.getElementById('current_'+field+'_wrapper');
            if(currentWrapper) currentWrapper.remove();
            // Show file input
            document.getElementById('file_input_wrapper').style.display = 'block';
        }
    </script>
</x-app-layout>

<!-- 🔹 CSS Section -->
<style>
    .container { max-width:1200px; margin:0 auto; padding:16px; }
    .header-container{display:flex;justify-content:flex-end;align-items:center;margin-bottom:16px;position:relative}
    .header-title{font:900 32px 'Figtree',sans-serif;color:#5C3A21;line-height:1.2;text-align:center;text-shadow:2px 2px 6px rgba(0,0,0,0.25);letter-spacing:1.2px;text-transform:uppercase;margin:0;position:absolute;left:50%;transform:translateX(-50%);-webkit-text-stroke:0.5px #5C3A21}
    .header-buttons{display:flex;gap:10px;position:relative;z-index:1}
    
    .roomtype-row { display: flex; gap: 8px; align-items: center;}

    .card {background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif;}
    .form-card .form-grid {display:grid; grid-template-columns:repeat(2,1fr); gap:12px;}
    .form-card .form-grid > div, .form-card .full-width {display:flex; align-items:center; gap:8px;}
    .form-card label {min-width:100px; font-weight:600; color:#5C3A21;}
    .form-card input, .form-card select {width:200px; padding:6px 10px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif;}
    .form-card .full-width {grid-column:span 2;}
    .form-card .full-width label {min-width:120px;}
    .form-card .full-width input {width:300px; background:#FFF3DF; color:#5C4A32; font-weight:500; cursor:pointer;}

    .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-confirm:hover { background:#F4C38C; }

    .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    .btn-back:hover { background:#F4C38C; color:#5C3A21; }
    .btn-back.small-btn { padding:2px 6px; font-size:12px; }

    .file-input { border:none; background:#FFF3DF; color:#5C4A32; font-weight:500; padding:6px 8px; border-radius:6px; cursor:pointer; }
    .img-preview { width:200px; height:200px; object-fit:cover; border-radius:10px; margin-right:8px; border: 2px solid #E6A574}
    .img-wrapper { display:flex; align-items:center; gap:4px; margin-bottom:4px; }
    .error { color:#e07b7b; font-size:12px; margin-top:4px; }

    .form-actions { margin-top:12px; display:flex; gap:8px; }
</style>