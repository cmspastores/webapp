<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Create Reservation</h2>
        </div>
    </x-slot>

    <div class="container mt-5 max-w-3xl mx-auto">
        <a href="{{ route('reservation.index') }}" class="btn btn-secondary mb-4">← Back to List</a>

        <form method="POST" action="{{ route('reservation.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-4">
                <!-- Agreement -->
                <div>
                    <label for="agreement_id" class="block font-medium text-sm text-gray-700">Agreement</label>
                    <select id="agreement_id" name="agreement_id" class="form-input w-full">
                        <option value="">-- Select Agreement --</option>
                        @isset($agreements)
                            @foreach($agreements as $agreement)
                                <option value="{{ $agreement->agreement_id ?? $agreement->id }}" {{ old('agreement_id') == ($agreement->agreement_id ?? $agreement->id) ? 'selected' : '' }}>
                                    {{ $agreement->agreement_number ?? ('Agreement #' . ($agreement->agreement_id ?? $agreement->id)) }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                    @error('agreement_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Room -->
                <div>
                    <label for="room_id" class="block font-medium text-sm text-gray-700">Room</label>
                    <select id="room_id" name="room_id" class="form-input w-full">
                        <option value="">-- Select Room --</option>
                        @isset($rooms)
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->room_number ?? ('Room ' . $room->id) }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                    @error('room_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Guest names -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block font-medium text-sm text-gray-700">First Name</label>
                        <input id="first_name" name="first_name" value="{{ old('first_name') }}" class="form-input w-full" />
                        @error('first_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block font-medium text-sm text-gray-700">Last Name</label>
                        <input id="last_name" name="last_name" value="{{ old('last_name') }}" class="form-input w-full" />
                        @error('last_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Reservation type -->
                <div>
                    <label for="reservation_type" class="block font-medium text-sm text-gray-700">Reservation Type</label>
                    <input id="reservation_type" name="reservation_type" value="{{ old('reservation_type') }}" class="form-input w-full" />
                    @error('reservation_type') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="check_in_date" class="block font-medium text-sm text-gray-700">Check-in Date</label>
                        <input id="check_in_date" name="check_in_date" type="date" value="{{ old('check_in_date') }}" class="form-input w-full" />
                        @error('check_in_date') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="check_out_date" class="block font-medium text-sm text-gray-700">Check-out Date</label>
                        <input id="check_out_date" name="check_out_date" type="date" value="{{ old('check_out_date') }}" class="form-input w-full" />
                        @error('check_out_date') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block font-medium text-sm text-gray-700">Status</label>
                    <input id="status" name="status" value="{{ old('status', 'booked') }}" class="form-input w-full" />
                    @error('status') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Submit -->
                <div class="flex space-x-2 mt-4">
                    <button type="submit" class="btn btn-primary">Create Reservation</button>
                    <a href="{{ route('reservation.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>