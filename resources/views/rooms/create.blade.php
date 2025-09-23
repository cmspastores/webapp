<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Add New Room</h2>
            <div class="header-buttons">
                <a href="{{ route('rooms.index') }}" class="btn-back">← Back to List</a>
            </div>
        </div>
    </x-slot>

    <div class="container">
        <div class="card form-card">
            <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-grid">
                    {{-- Room Number --}}
                    <div>
                        <label for="room_number">Room #</label>
                        <input type="text" name="room_number" id="room_number" value="{{ old('room_number') }}" required>
                        @error('room_number')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Room Type --}}
                    <div>
                        <label for="room_type_id">Room Type</label>
                        <div class="roomtype-row">
                            <select name="room_type_id" id="room_type_id">
                                <option value="">No Room Type</option>
                                @forelse($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('room_type_id')==$type->id?'selected':'' }}>
                                        {{ $type->name }}
                                    </option>
                                @empty
                                    <option disabled>(No room types)</option>
                                @endforelse
                            </select>

                            {{-- Button beside dropdown --}}
                            @if(auth()->user() && auth()->user()->is_admin)
                                <a href="{{ route('roomtypes.index') }}" class="btn-back">Edit</a>
                            @endif
                        </div>
                        @error('room_type_id')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Room Price --}}
                    <div>
                        <label for="room_price">Price</label>
                        <input type="number" step="0.01" name="room_price" id="room_price" value="{{ old('room_price') }}" required>
                        @error('room_price')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Number of Occupants --}}
                    <div>
                        <label for="number_of_occupants">Occupants</label>
                        <input type="number" name="number_of_occupants" id="number_of_occupants" value="{{ old('number_of_occupants') }}">
                        @error('number_of_occupants')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Room Image (only 1) --}}
                    <div class="full-width">
                        <label>Room Image (optional)</label>
                        <input type="file" name="image" class="file-input" accept="image/*">
                        @error('image')<div class="error">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Form Actions --}}
                <div style="margin-top:12px; display:flex; gap:8px;">
                    <button type="submit" class="btn-confirm">Save</button>
                    <a href="{{ route('rooms.index') }}" class="btn-back">Cancel</a>
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
    </script>
</x-app-layout>

<!-- 🔹 CSS Section -->
<style>
    /* Containers */
    .container { max-width:1200px; margin:0 auto; padding:16px; }
    .header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; }
    .header-buttons { display:flex; gap:10px; }

    .roomtype-row { display:flex; gap:8px; align-items:center; }

    /* Card/Form */
    .card {background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif;}
    .form-card .form-grid {display:grid; grid-template-columns:repeat(2,1fr); gap:12px;}
    .form-card .form-grid > div, .form-card .full-width {display:flex; align-items:center; gap:8px;}
    .form-card label {min-width:100px; font-weight:600; color:#5C3A21;}
    .form-card input, .form-card select {width:200px; padding:6px 10px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif;}
    .form-card .full-width {grid-column:span 2;}
    .form-card .full-width label {min-width:120px;}
    .form-card .full-width input {width:300px; background:#FFF3DF; color:#5C4A32; font-weight:500; cursor:pointer;}


    /* Buttons */
    .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-confirm:hover { background:#F4C38C; }
    .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-back:hover { background:#F4C38C; color:#5C3A21; }

    /* File Inputs */
    .file-input { border:none; background:#FFF3DF; color:#5C4A32; font-weight:500; padding:6px 8px; border-radius:6px; cursor:pointer; }

    /* Error Messages */
    .error { color:#e07b7b; font-size:12px; margin-top:4px; }
</style>

