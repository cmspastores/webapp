<x-app-layout>
    <div class="container">
        <div class="card form-card">
            <form action="{{ route('agreements.store') }}" method="POST">
                @csrf

                <div class="form-grid">
                    <!-- Renter -->
                    <div>
                        <label for="renter_id">Renter</label>
                        <select name="renter_id" id="renter_id" required>
                            <option value="">-- Select Renter --</option>
                            @foreach($renters as $r)
                                <option value="{{ $r->renter_id }}" {{ old('renter_id') == $r->renter_id ? 'selected' : '' }}>
                                    {{ $r->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('renter_id')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Room -->
                    <div>
                        <label for="room_id">Room</label>
                        <select name="room_id" id="room_id" required onchange="handleRoomChange()">
                            <option value="">-- Select Room --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}"
                                    data-transient="{{ optional($room->roomType)->is_transient ? '1' : '0' }}"
                                    data-type-name="{{ optional($room->roomType)->name }}"
                                    {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->room_number }}
                                    {{ optional($room->roomType)->name ? ' - ' . optional($room->roomType)->name : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Agreement Date -->
                    <div>
                        <label for="agreement_date">Agreement Date</label>
                        <input type="date" name="agreement_date" id="agreement_date"
                               value="{{ old('agreement_date', now()->toDateString()) }}" required>
                        @error('agreement_date')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date"
                               value="{{ old('start_date', now()->toDateString()) }}" required onchange="computeEndDate()">
                        @error('start_date')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- 🔹 Length of Stay (Transient only) -->
                    <div id="stay_length_field" style="display:none;">
                        <label for="stay_length">Length of Stay (Days)</label>
                        <input type="number" name="stay_length" id="stay_length" min="1"
                               value="{{ old('stay_length', 1) }}" onchange="computeEndDate()">
                        @error('stay_length')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <!-- End Date -->
                    <div class="full-width">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" required>
                        <small id="end_date_note" style="color:#777;"></small>
                    </div>

                    <!-- Rent Info (Auto-filled) -->
                    <div id="rent_info" class="full-width" style="margin-top:8px; font-weight:bold;"></div>
                </div>

                <div style="margin-top:12px; display:flex; gap:8px;">
                    <button type="submit" class="btn-confirm">Save Agreement</button>
                    <a href="{{ route('agreements.index') }}" class="btn-back">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function computeEndDate() {
            const start = document.getElementById('start_date').value;
            if (!start) return;

            const selectedRoom = document.getElementById('room_id').selectedOptions[0];
            if (!selectedRoom) return;

            const isTransient = selectedRoom.dataset.transient === '1';
            const stayDays = parseInt(document.getElementById('stay_length')?.value || 1);

            const d = new Date(start);

            if (isTransient) {
                // Transient — compute default based on stay length
                d.setDate(d.getDate() + stayDays);
            } else {
                // Dorm — fixed 1 year after start
                d.setFullYear(d.getFullYear() + 1);
            }

            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            const formatted = `${yyyy}-${mm}-${dd}`;

            // Update end_date field
            const endInput = document.getElementById('end_date');
            endInput.value = formatted;
        }

        function updateRentInfo() {
            const selectedRoom = document.getElementById('room_id').selectedOptions[0];
            const rentInfo = document.getElementById('rent_info');

            if (!selectedRoom) {
                rentInfo.textContent = '';
                return;
            }

            const isTransient = selectedRoom.dataset.transient === '1';
            const typeName = selectedRoom.dataset.typeName || '';
            rentInfo.textContent = isTransient
                ? `Room Type: ${typeName} — Daily Rate`
                : `Room Type: ${typeName} — Monthly Rate`;
        }

        function handleRoomChange() {
            const select = document.getElementById('room_id');
            const selected = select.selectedOptions[0];
            const stayField = document.getElementById('stay_length_field');
            const endDateField = document.getElementById('end_date');
            const endNote = document.getElementById('end_date_note');

            if (!selected) {
                stayField.style.display = 'none';
                endDateField.readOnly = true;
                endNote.textContent = '';
                return;
            }

            const isTransient = selected.dataset.transient === '1';

            if (isTransient) {
                // Transient: show stay length + editable end date
                stayField.style.display = 'block';
                endDateField.readOnly = false;
                endNote.textContent = 'Transient: You can manually adjust the end date.';
            } else {
                // Dorm: hide stay length + auto compute + lock end date
                stayField.style.display = 'none';
                endDateField.readOnly = true;
                endNote.textContent = 'Dorm: End date auto-set 1 year after start date.';
            }

            updateRentInfo();
            computeEndDate();
        }

        document.addEventListener('DOMContentLoaded', () => {
            handleRoomChange();
            computeEndDate();
        });
    </script>
</x-app-layout>

<!-- 🔹 CSS Section -->
<style>
    /* Containers */
    .container { max-width:1200px; margin:0 auto; padding:16px; }
    .header-container{display:flex;justify-content:flex-end;align-items:center;margin-bottom:16px;position:relative}
    .header-title{font:900 32px 'Figtree',sans-serif;color:#5C3A21;line-height:1.2;text-align:center;text-shadow:2px 2px 6px rgba(0,0,0,0.25);letter-spacing:1.2px;text-transform:uppercase;margin:0;position:absolute;left:50%;transform:translateX(-50%);-webkit-text-stroke:0.5px #5C3A21}
    .header-buttons{display:flex;gap:10px;position:relative;z-index:1}

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