<x-app-layout>
    <div class="container">
        <div class="card form-card">
            <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-grid">
                    <!-- Room Number -->
                    <div>
                        <label for="room_number">Room #</label>
                        <input type="text" name="room_number" id="room_number" value="{{ old('room_number') }}" required>
                        @error('room_number')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Room Type Custom Dropdown -->
                    <div>
                        <label for="room_type_id">Room Type</label>
                        <div class="roomtype-row">
                            <div class="custom-select-wrapper">
                                <div class="custom-select-trigger">
                                    {{ old('room_type_id') ? $roomTypes->firstWhere('id', old('room_type_id'))->name : 'No Room Type' }}
                                </div>
                                <div class="custom-options">
                                    <div class="custom-option" data-value="">No Room Type</div>
                                    @foreach($roomTypes as $type)
                                        <div class="custom-option" data-value="{{ $type->id }}">
                                            {{ $type->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Edit button beside dropdown -->
                            @if(auth()->user() && auth()->user()->is_admin)
                                <a href="{{ route('roomtypes.index') }}" class="btn-back" style="margin-left:4px;">Edit</a>
                            @endif
                        </div>
                        @error('room_type_id')<div class="error">{{ $message }}</div>@enderror
                        <!-- Hidden input to store selected value -->
                        <input type="hidden" name="room_type_id" id="room_type_id" value="{{ old('room_type_id') }}">
                    </div>

                    <!-- Room Price -->
                    <div>
                        <label for="room_price">Price</label>
                        <input type="number" step="0.01" name="room_price" id="room_price" value="{{ old('room_price') }}" required min="0.01">
                        @error('room_price')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Number of Occupants -->
                    <div>
                        <label for="number_of_occupants">Occupants</label>
                        <input type="number" name="number_of_occupants" id="number_of_occupants" value="{{ old('number_of_occupants') ?? 1 }}" required min="1">
                        @error('number_of_occupants')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Room Image -->
                    <div class="full-width">
                        <label>Room Image (optional)</label>
                        <input type="file" name="image" class="file-input" accept="image/*">
                        @error('image')<div class="error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-confirm">Save</button>
                    <a href="{{ route('rooms.index') }}" class="btn-back">Cancel</a>
                </div>

            </form>
        </div>
    </div>

    <!-- 🔹 JS Section -->
    <script>
        // Custom dropdown logic
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const options = wrapper.querySelectorAll('.custom-option');
            const hiddenInput = wrapper.nextElementSibling; // hidden input

            trigger.addEventListener('click', () => wrapper.classList.toggle('open'));

            options.forEach(opt => {
                opt.addEventListener('click', () => {
                    trigger.textContent = opt.textContent;
                    hiddenInput.value = opt.getAttribute('data-value');
                    wrapper.classList.remove('open');
                });
            });
        });

        document.addEventListener('click', e => {
            document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
                if(!wrapper.contains(e.target)) wrapper.classList.remove('open');
            });
        });

        function previewImage(input, id) {
            const f = input.files[0];
            if(f) {
                document.getElementById(id).src = URL.createObjectURL(f);
                document.getElementById(id+'_wrapper').style.display = 'flex';
            }
        }
    </script>
</x-app-layout>

<!-- 🔹 CSS Section -->
<style>
    .container { max-width:1200px; margin:0 auto; padding:16px; }
    .card {background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif;}
    .form-card .form-grid {display:grid; grid-template-columns:repeat(2,1fr); gap:12px;}
    .form-card .form-grid > div, .form-card .full-width {display:flex; align-items:center; gap:8px;}
    .form-card label {min-width:100px; font-weight:600; color:#5C3A21;}
    .form-card input {width:200px; padding:6px 10px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif;}
    .form-card .full-width {grid-column:span 2;}
    .form-card .full-width input {width:300px; background:#FFF3DF; color:#5C4A32; font-weight:500; cursor:pointer;}

    .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-confirm:hover { background:#F4C38C; }
    .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
    .btn-back:hover { background:#F4C38C; color:#5C3A21; }
    .file-input { border:none; background:#FFF3DF; color:#5C4A32; font-weight:500; padding:6px 8px; border-radius:6px; cursor:pointer; }
    .error { color:#e07b7b; font-size:12px; margin-top:4px; }

    /* 🔹 Custom Dropdown */
    .custom-select-wrapper { position: relative; width:200px; user-select:none; }
    .custom-select-trigger { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; background:#FFFDFB; cursor:pointer; color:#5C3A21; }
    .custom-options { position:absolute; top:100%; left:0; right:0; background:#FFFDFB; border:1px solid #E6A574; border-radius:6px; max-height:200px; overflow-y:auto; display:none; z-index:10; }
    .custom-option { padding:6px 10px; cursor:pointer; }
    .custom-option:hover { background:#F4C38C; color:#5C3A21; }
    .custom-select-wrapper.open .custom-options { display:block; }

    /* 🔹 Room Type row fix to keep button next to dropdown */
    .roomtype-row { display:flex; gap:4px; align-items:center; }
</style>
