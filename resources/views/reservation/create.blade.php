<x-app-layout>
    <div class="container">
        <div class="card form-card">
            <form method="POST" action="{{ route('reservation.store') }}">
                @csrf

                <div class="form-grid">

                    <!-- Agreement -->
                    <div>
                        <label for="agreement_id">Agreement</label>
                        <select id="agreement_id" name="agreement_id">
                            <option value="">-- Select Agreement --</option>
                            @isset($agreements)
                                @foreach($agreements as $agreement)
                                    <option value="{{ $agreement->agreement_id ?? $agreement->id }}" {{ old('agreement_id') == ($agreement->agreement_id ?? $agreement->id) ? 'selected' : '' }}>
                                        {{ $agreement->agreement_number ?? ('Agreement #' . ($agreement->agreement_id ?? $agreement->id)) }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('agreement_id') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Room -->
                    <div>
                        <label for="room_id">Room</label>
                        <select id="room_id" name="room_id">
                            <option value="">-- Select Room --</option>
                            @isset($rooms)
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_number ?? ('Room ' . $room->id) }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('room_id') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Guest Names -->
                    <div>
                        <label for="first_name">First Name</label>
                        <input id="first_name" name="first_name" value="{{ old('first_name') }}" />
                        @error('first_name') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="last_name">Last Name</label>
                        <input id="last_name" name="last_name" value="{{ old('last_name') }}" />
                        @error('last_name') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Reservation Type -->
                    <div>
                        <label for="reservation_type">Reservation Type</label>
                        <input id="reservation_type" name="reservation_type" value="{{ old('reservation_type') }}" />
                        @error('reservation_type') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <!-- Dates -->
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

                    <!-- Status -->
                    <div>
                        <label for="status">Status</label>
                        <input id="status" name="status" value="{{ old('status', 'booked') }}" />
                        @error('status') <div class="error">{{ $message }}</div> @enderror
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
</style>
