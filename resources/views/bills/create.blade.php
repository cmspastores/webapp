<x-app-layout>
    <x-slot name="header">
        <h2>Generate Bill</h2>
    </x-slot>

    <div class="p-6">
        <div class="card">
            {{-- Generate for single agreement --}}
            <form action="{{ route('bills.store') }}" method="POST" style="margin-bottom:12px;">
                @csrf

                {{-- Select Agreement --}}
                <div class="mb-3">
                    <label for="agreement_id" class="form-label">Select Agreement</label>
                    <select name="agreement_id" id="agreement_id" class="form-control">
                        <option value="">-- Choose Agreement (or leave blank to generate all) --</option>
                        @foreach ($agreements as $agreement)
                            <option value="{{ $agreement->agreement_id }}">
                                Agreement #{{ $agreement->agreement_id }} —
                                Renter: {{ $agreement->renter->full_name ?? 'Unknown' }} —
                                Room: {{ $agreement->room->room_number ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Year and Month --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label>Year</label>
                        <input type="number" name="year" value="{{ old('year', now()->year) }}" required>
                    </div>

                    <div>
                        <label>Month</label>
                        <select name="month" required>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(2000, $m, 1)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <button class="btn-confirm" type="submit">Generate for Selected Agreement</button>
                    <a href="{{ route('bills.index') }}" class="btn-back">Back</a>
                </div>
            </form>

            {{-- Generate bills for all agreements --}}
            <form action="{{ route('bills.generateAll') }}" method="POST" onsubmit="return confirm('Generate bills for ALL active agreements for this month?');">
                @csrf
                <input type="hidden" name="year" value="{{ now()->year }}">
                <input type="hidden" name="month" value="{{ now()->month }}">
                <button type="submit" class="btn btn-new">Generate Bills for All Active Agreements (This Month)</button>
            </form>
        </div>
    </div>
</x-app-layout>

<!-- 🔹 CSS Section -->
<style>
    /* 🔹 Layout */
    .container { max-width: 900px; margin: 0 auto; padding: 16px; font-family: 'Figtree', sans-serif; }
    .card { background: linear-gradient(135deg, #FFFDFB, #FFF8F0); border-radius: 16px; border: 2px solid #E6A574; padding: 24px; box-shadow: 0 8px 20px rgba(0,0,0,0.12); font-family: 'Figtree', sans-serif; }

    /* 🔹 Form */
    form label { font-weight: 600; color: #5C3A21; display: block; margin-bottom: 6px; }
    form input, form select { width: 100%; padding: 8px 10px; border-radius: 6px; border: 1px solid #E6A574; font-family: 'Figtree', sans-serif; font-size: 14px; }
    .mb-3 { margin-bottom: 16px; }
    .grid { display: grid; gap: 12px; }
    .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }

    /* 🔹 Buttons */
    .btn { padding: 8px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; transition: 0.2s; border: none; display: inline-block; }
    .btn-confirm { background: #E6A574; color: #5C3A21; }
    .btn-confirm:hover { background: #F4C38C; }
    .btn-back { background: #D97A4E; color: #FFF5EC; margin-left: 8px; }
    .btn-back:hover { background: #F4C38C; color: #5C3A21; }
    .btn-new { background: #b86536; color: #fff; margin-top: 16px; }
    .btn-new:hover { background: #F4C38C; color: #5C3A21; }

    /* 🔹 Misc */
    h2 { color: #5C3A21; font-size: 24px; font-weight: 800; text-align: center; margin-bottom: 20px; text-transform: uppercase; }
</style>