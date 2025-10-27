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
                                                <option value="{{ $room->id }}" {{ old('agreement_room_id') == $room->id ? 'selected' : '' }}>
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
                                    <label for="end_date_preview">End Date (auto 1 year - optional)</label>
                                    <input type="text" id="end_date_preview" readonly value="{{ old('start_date') ? \Illuminate\Support\Carbon::parse(old('start_date'))->addYear()->toDateString() : now()->addYear()->toDateString() }}">
                                    <input type="hidden" id="end_date" name="end_date" value="{{ old('end_date') }}">
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
            if (endHidden) endHidden.value = val;
        }

        if (startEl) {
            startEl.addEventListener('change', computeEndDate);
            computeEndDate();
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
