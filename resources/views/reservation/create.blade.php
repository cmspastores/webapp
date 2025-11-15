<x-app-layout>
    <div class="container">
        <div class="card form-card">
            <form method="POST" action="{{ route('reservation.store') }}">
                @csrf

                <div class="form-grid">

                    <!-- Agreement Section -->
                    <div class="full-width">
                        <label>Agreement (new)</label>
                        <div id="agreement-new" style="margin-top:10px; border-top:1px dashed #E6A574; padding-top:10px;">
                            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:10px;">
                                <div>
                                    <label for="agreement_room_id">Agreement - Room to reserve</label>
                                    <select id="agreement_room_id" name="agreement_room_id">
                                        <option value="">-- Select Room for Agreement --</option>
                                        @isset($rooms)
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}" data-is-transient="{{ $room->roomType->is_transient ?? false ? '1' : '0' }}" {{ old('agreement_room_id') == $room->id ? 'selected' : '' }}>
                                                    {{ $room->room_number ?? ('Room ' . $room->id) }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('agreement_room_id') <div class="error">{{ $message }}</div> @enderror
                                </div>

                                <div>
                                    <label for="agreement_date">Agreement Date</label>
                                    <input id="agreement_date" name="agreement_date" type="date" value="{{ old('agreement_date', now()->toDateString()) }}" />
                                    @error('agreement_date') <div class="error">{{ $message }}</div> @enderror
                                </div>

                                <div>
                                    <label for="start_date">Start Date</label>
                                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date', now()->toDateString()) }}" />
                                    @error('start_date') <div class="error">{{ $message }}</div> @enderror
                                </div>

                                <div>
                                    <label for="end_date">End Date</label>
                                    <input id="end_date" name="end_date" type="date" value="{{ old('end_date', now()->addYear()->toDateString()) }}" />
                                    @error('end_date') <div class="error">{{ $message }}</div> @enderror
                                </div>

                                <div class="full-width">
                                    <div id="auto_one_year_container" style="margin-top:8px; display:none;">
                                        <label>
                                            <input type="checkbox" id="auto_one_year_checkbox" name="auto_one_year" value="1" {{ old('end_date') ? 'checked' : '' }} />
                                            Set end date automatically to +1 year from start date
                                        </label>
                                        <div class="muted">Only available for dorm/monthly room types. For transient rooms, set an explicit end date.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Renter Information Header -->
                    <div class="full-width" style="margin-top:6px; font-weight:700; color:#5C3A21;">Renter information (optional)</div>

                    <!-- Renter Names & DOB -->
                    <div>
                        <label for="first_name">Renter First Name</label>
                        <input id="first_name" name="first_name" value="{{ old('first_name') }}" />
                        @error('first_name') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="last_name">Renter Last Name</label>
                        <input id="last_name" name="last_name" value="{{ old('last_name') }}" />
                        @error('last_name') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="dob">Renter Date of Birth</label>
                        <input id="dob" name="dob" type="date" value="{{ old('dob') }}" />
                        @error('dob') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="email">Renter Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" />
                        @error('email') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Renter Phone + Address side by side -->
                    <div>
                        <label for="phone">Renter Phone</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" />
                        @error('phone') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="address">Renter Address</label>
                        <input id="address" name="address" value="{{ old('address') }}" />
                        @error('address') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Emergency Contact -->
                    <div>
                        <label for="emergency_contact">Renter Emergency Contact</label>
                        <input id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}" />
                        @error('emergency_contact') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_name">Emergency Contact Name</label>
                        <input id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" />
                        @error('emergency_contact_name') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="emergency_contact_email">Emergency Contact Email</label>
                        <input id="emergency_contact_email" name="emergency_contact_email" type="email" value="{{ old('emergency_contact_email') }}" />
                        @error('emergency_contact_email') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Reservation Dates -->
                    <div>
                        <label for="check_in_date">Check-in Date</label>
                        <input id="check_in_date" name="check_in_date" type="date" value="{{ old('check_in_date') }}" />
                        @error('check_in_date') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="check_out_date">Check-out Date</label>
                        <input id="check_out_date" name="check_out_date" type="date" value="{{ old('check_out_date') }}" />
                        @error('check_out_date') <div class="error">{{ $message }}</div> @enderror
                    </div>

                </div>

                <!-- Form Buttons -->
                <div>
                    <button type="submit" class="btn-confirm">Create Reservation</button>
                    <a href="{{ route('reservation.index') }}" class="btn-back">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startEl = document.getElementById('start_date');
    const endEl = document.getElementById('end_date');
    const roomSelect = document.getElementById('agreement_room_id');
    const autoContainer = document.getElementById('auto_one_year_container');
    const autoCheckbox = document.getElementById('auto_one_year_checkbox');

    function computeEndDate() {
        if(!startEl.value) return;
        const d = new Date(startEl.value);
        d.setFullYear(d.getFullYear()+1);
        const yyyy = d.getFullYear();
        const mm = (d.getMonth()+1).toString().padStart(2,'0');
        const dd = d.getDate().toString().padStart(2,'0');
        const val = `${yyyy}-${mm}-${dd}`;

        if(autoCheckbox.checked){
            endEl.value = val;
        }
    }

    function updateAutoVisibility() {
        const opt = roomSelect.options[roomSelect.selectedIndex];
        const isTransient = opt ? opt.dataset.isTransient === '1' : false;

        if(isTransient){
            autoContainer.style.display = 'none';
            autoCheckbox.checked = false;
        } else {
            autoContainer.style.display = 'flex';
        }
    }

    autoContainer.style.display = 'none';
    if(roomSelect){
        roomSelect.addEventListener('change', function(){
            updateAutoVisibility();
            computeEndDate();
        });
        updateAutoVisibility();
    }

    if(startEl){
        startEl.addEventListener('change', computeEndDate);
    }

    if(autoCheckbox){
        autoCheckbox.addEventListener('change', computeEndDate);
    }
});
</script>

<style>
/* === Container === */
.container { max-width:1000px; margin:0 auto; padding:16px; }

/* === Card/Form === */
.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:24px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:16px; }

/* === Form Grid === */
.form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
.form-card .form-grid > div { display:flex; flex-direction:column; width:100%; box-sizing:border-box; }
.form-card label { font-weight:600; color:#5C3A21; margin-bottom:6px; }

/* === Inputs/Selects uniform width === */
.form-card input, .form-card select { width:100%; padding:8px 12px; border-radius:8px; border:1px solid #E6A574; font-size:14px; box-sizing:border-box; transition:0.2s; }
.form-card input:focus, .form-card select:focus { border-color:#D97A4E; outline:none; }

/* === Full-width fields spanning 2 columns === */
.full-width { grid-column: span 2; }

/* === Buttons === */
.btn-confirm, .btn-back { padding:8px 16px; border-radius:8px; font-weight:600; cursor:pointer; border:none; transition:0.2s; min-width:140px; text-align:center; }
.btn-confirm { background:#E6A574; color:#5C3A21; }
.btn-confirm:hover { background:#F4C38C; }
.btn-back { background:#D97A4E; color:#FFF5EC; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }

/* === Error & muted text === */
.error { color:#e07b7b; font-size:12px; margin-top:4px; }
.muted { font-size:12px; color:#6B7280; margin-top:4px; }

/* === Checkbox toggle styling === */
#auto_one_year_container { flex-direction:column; margin-top:8px; display:flex; gap:4px; }
#auto_one_year_container label { display:flex; align-items:center; gap:6px; cursor:pointer; font-size:14px; }
#auto_one_year_checkbox { width:18px; height:18px; accent-color:#E6A574; }

/* === Buttons container === */
form > div:last-child { display:flex; gap:12px; justify-content:flex-start; margin-top:12px; flex-wrap:wrap; }

/* === Responsive tweaks === */
@media (max-width:768px) {
    .form-card .form-grid { grid-template-columns:1fr; gap:12px; }
    .form-card .form-grid > div { max-width:100%; }
    form > div:last-child { flex-direction:column; align-items:stretch; }
}
</style>
