<x-app-layout>
    <div class="container">
        <div class="card form-card">
            <form method="POST" action="{{ route('reservation.store') }}">
                @csrf

                <div class="form-grid">

                    <!-- Always create a new agreement (use same fields as agreements.create) -->
                    <div style="grid-column: span 2;">
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
                                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date', now()->toDateString()) }}" onchange="computeEndDate()" />
                                    @error('start_date') <div class="error">{{ $message }}</div> @enderror
                                </div>

                                <div class="full-width" style="grid-column: span 2;">
                                    <label for="end_date_preview">End Date (optional)</label>
                                    <input type="text" id="end_date_preview" readonly value="{{ old('start_date') ? \Illuminate\Support\Carbon::parse(old('start_date'))->addYear()->toDateString() : now()->addYear()->toDateString() }}">
                                    <input type="hidden" id="end_date" name="end_date" value="{{ old('end_date') }}">
                                    <input type="hidden" id="had_old_end_date" value="{{ old('end_date') ? '1' : '0' }}">

                                    <!-- Auto 1-year toggle: only shown for dorm/monthly rooms -->
                                    <div id="auto_one_year_container" style="margin-top:8px; display:none;">
                                        <label style="font-weight:600;">
                                            <input type="checkbox" id="auto_one_year_checkbox" name="auto_one_year" value="1" {{ old('end_date') ? 'checked' : '' }} />
                                            &nbsp;Set end date automatically to +1 year from start date
                                        </label>
                                        <div class="muted">Only available for dorm/monthly room types. For transient rooms, set an explicit end date on confirmation.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Renter fields (use same names as renters.create) -->
                    <div style="grid-column: span 2; margin-top:6px; font-weight:700; color:#5C3A21;">Renter information (optional) — creating a renter will attach it to the created agreement</div>

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

                    <div>
                        <label for="phone">Renter Phone</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}" />
                        @error('phone') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div style="grid-column: span 2;">
                        <label for="address">Renter Address</label>
                        <input id="address" name="address" value="{{ old('address') }}" />
                        @error('address') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div style="grid-column: span 2;">
                        <label for="emergency_contact">Renter Emergency Contact</label>
                        <input id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}" />
                        @error('emergency_contact') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="guardian_name">Guardian Name</label>
                        <input id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" />
                        @error('guardian_name') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="guardian_phone">Guardian Phone</label>
                        <input id="guardian_phone" name="guardian_phone" value="{{ old('guardian_phone') }}" />
                        @error('guardian_phone') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div style="grid-column: span 2;">
                        <label for="guardian_email">Guardian Email</label>
                        <input id="guardian_email" name="guardian_email" type="email" value="{{ old('guardian_email') }}" />
                        @error('guardian_email') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Reservation Dates (reservation_type removed) -->
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

                <div style="margin-top:12px; display:flex; gap:8px;">
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
        const endPreview = document.getElementById('end_date_preview');
        const endHidden = document.getElementById('end_date');
        const roomSelect = document.getElementById('agreement_room_id');
        const autoContainer = document.getElementById('auto_one_year_container');
        const autoCheckbox = document.getElementById('auto_one_year_checkbox');

    const hadOldEndDateEl = document.getElementById('had_old_end_date');
    const hadOldEndDate = hadOldEndDateEl ? hadOldEndDateEl.value === '1' : false;

    function computeEndDate() {
            const start = startEl.value;
            if (!start) return;
            const d = new Date(start);
            d.setFullYear(d.getFullYear() + 1);
            const yyyy = d.getFullYear();
            let mm = (d.getMonth() + 1).toString().padStart(2, '0');
            let dd = d.getDate().toString().padStart(2, '0');
            const val = `${yyyy}-${mm}-${dd}`;
            if (endPreview) endPreview.value = val;

            // Also update reservation check-in/check-out fields when auto end date is enabled
            const checkInElLocal = document.getElementById('check_in_date');
            const checkOutElLocal = document.getElementById('check_out_date');
            if (checkInElLocal && start) {
                // If check-in is empty, default it to the agreement start date
                if (!checkInElLocal.value) checkInElLocal.value = start;
            }

            // Only write to hidden end_date and check-out when the auto checkbox is present and checked.
            // However, if the user has manually edited the check-out field we should not overwrite it.
            if (endHidden) {
                if (autoCheckbox && autoCheckbox.checked) {
                    endHidden.value = val;
                    if (checkOutElLocal && checkOutElLocal.dataset.manual !== 'true') {
                        checkOutElLocal.value = val;
                    }
                } else {
                    // If user hasn't opted into auto end date, don't override explicit old value
                    // Clear only if there was no old value and checkbox is unchecked
                    if (!hadOldEndDate) {
                        endHidden.value = '';
                    }
                }
            }
        }

        function updateAutoVisibility() {
            if (!roomSelect) return;
            const opt = roomSelect.options[roomSelect.selectedIndex];
            const isTransient = opt ? opt.dataset.isTransient === '1' : false;
                if (isTransient) {
                // hide toggle for transient rooms and ensure we don't send an auto end date
                if (autoContainer) autoContainer.style.display = 'none';
                if (autoCheckbox) autoCheckbox.checked = false;
                if (endHidden) {
                    // do not overwrite any explicit end_date sent before; clear if it was only auto
                    if (!hadOldEndDate) endHidden.value = '';
                }
                } else {
                if (autoContainer) autoContainer.style.display = 'block';
                // if there is an old end_date value (from previous submission), keep checkbox checked
                if (autoCheckbox && hadOldEndDate) {
                    autoCheckbox.checked = true;
                }
            }
        }

        if (roomSelect) {
            roomSelect.addEventListener('change', function () {
                updateAutoVisibility();
                computeEndDate();
            });
            // initialize visibility
            updateAutoVisibility();
        }

        // wire start change
        if (startEl) {
            startEl.addEventListener('change', function(){
                // when start changes, reset manual flag so auto can update check-out again
                const co = document.getElementById('check_out_date'); if(co) co.dataset.manual = 'false';
                computeEndDate();
            });
            computeEndDate();
        }

        // allow manual override: if the user edits check-in/check-out, mark check-out as manual
        const checkInEl = document.getElementById('check_in_date');
        const checkOutEl = document.getElementById('check_out_date');
        if(checkInEl){
            checkInEl.addEventListener('input', function(e){
                // user edited check-in: mark manual if they typed a value
                if(this.value && this.value.length) this.dataset.manual = 'true';
            });

            // allow typing a 4-digit year (e.g. "2026") to quickly set the date
            // while typing partial year digits the input will stay blank (buffered) to avoid transient numbers
            checkInEl.addEventListener('input', function(e){
                const raw = this.value.trim();
                // only digits typed (partial year)
                if(/^\d{1,4}$/.test(raw)){
                    // store buffer and keep the visible input blank while typing
                    this.dataset.yearBuffer = raw;
                    this.value = '';
                    if(raw.length === 4){
                        const v = raw;
                        const ref = startEl && startEl.value ? new Date(startEl.value) : new Date();
                        const mm = (ref.getMonth()+1).toString().padStart(2,'0');
                        const dd = (ref.getDate()).toString().padStart(2,'0');
                        this.value = `${v}-${mm}-${dd}`;
                        this.dataset.manual = 'true';
                        delete this.dataset.yearBuffer;
                    }
                    return;
                }
                // if user typed a full date (YYYY-MM-DD) accept it normally
                if(/^\d{4}-\d{2}-\d{2}$/.test(raw)){
                    this.dataset.manual = 'true';
                    delete this.dataset.yearBuffer;
                    return;
                }
                // anything else, clear buffer
                delete this.dataset.yearBuffer;
            });
        }
        if(checkOutEl){
            checkOutEl.addEventListener('input', function(){
                this.dataset.manual = 'true';
            });

            // accept 4-digit year typing for check-out as a convenience: set to year with same month/day as start
            checkOutEl.addEventListener('input', function(e){
                const raw = this.value.trim();
                if(/^\d{1,4}$/.test(raw)){
                    this.dataset.yearBuffer = raw;
                    this.value = '';
                    if(raw.length === 4){
                        const v = raw;
                        const ref = startEl && startEl.value ? new Date(startEl.value) : new Date();
                        const mm = (ref.getMonth()+1).toString().padStart(2,'0');
                        const dd = (ref.getDate()).toString().padStart(2,'0');
                        this.value = `${v}-${mm}-${dd}`;
                        this.dataset.manual = 'true';
                        delete this.dataset.yearBuffer;
                    }
                    return;
                }
                if(/^\d{4}-\d{2}-\d{2}$/.test(raw)){
                    this.dataset.manual = 'true';
                    delete this.dataset.yearBuffer;
                    return;
                }
                delete this.dataset.yearBuffer;
            });
        }

        // if user toggles the auto-checkbox, recompute and when enabling clear manual override so auto applies
        if (autoCheckbox) {
            autoCheckbox.addEventListener('change', function(){
                if(this.checked){
                    // enabling auto should allow auto to set check-out even if previously manual
                    if(checkOutEl) checkOutEl.dataset.manual = 'false';
                }
                computeEndDate();
            });
        }
    });
</script>

<!-- 🔹 CSS Section -->
<style>
    /* Containers */
    .container { max-width:1200px; margin:0 auto; padding:16px; }

    /* Card/Form */
    .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:24px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif; }
    .form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
    .form-card .form-grid > div { display:flex; flex-direction:column; }
    .form-card label { font-weight:600; color:#5C3A21; margin-bottom:6px; }
    .form-card input, .form-card select { width:100%; padding:6px 10px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif; font-size:14px; }

    /* Buttons */
    .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-confirm:hover { background:#F4C38C; }
    .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-back:hover { background:#F4C38C; color:#5C3A21; }

    /* Error Messages */
    .error { color:#e07b7b; font-size:12px; margin-top:4px; }
    .muted { font-size:12px; color:#6B7280; }

    .full-width input { width:100%; }
</style>
