<x-app-layout>
    <div class="container">
        <div class="card form-card">
            {{-- Generate for single agreement --}}
            <form action="{{ route('bills.store') }}" method="POST" style="margin-bottom:12px;">
                @csrf

                {{-- Select Agreement --}}
                <div class="form-grid" style="margin-bottom:16px;">
                    <div>
                        <label for="agreement_id">Select Agreement</label>
                        <select name="agreement_id" id="agreement_id">
                            <option value="">-- Select Agreement --</option>
                            @foreach ($agreements as $agreement)
                                <option value="{{ $agreement->agreement_id }}">
                                    Agreement #{{ $agreement->agreement_id }} —
                                    Renter: {{ $agreement->renter->full_name ?? 'Unknown' }} —
                                    Room: {{ $agreement->room->room_number ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Year and Month --}}
                <div class="form-grid" style="margin-bottom:16px;">
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

                {{-- Buttons --}}
                <div class="form-buttons">
                    <button class="btn-confirm" type="submit">Generate for Selected Agreement</button>
                    <a href="{{ route('bills.index') }}" class="btn-back">Back</a>
                </div>
            </form>

            {{-- Generate bills for all agreements --}}
            <form action="{{ route('bills.generateAll') }}" method="POST" onsubmit="return confirm('Generate bills for ALL active agreements for this month?');">
                @csrf
                <input type="hidden" name="year" value="{{ now()->year }}">
                <input type="hidden" name="month" value="{{ now()->month }}">
                <button type="submit" class="btn-confirm" style="margin-top:20px;">Generate Bills for All Active Dorm Agreements (This Month)</button>
            </form>
        </div>
    </div>
</x-app-layout>

<!-- CSS -->
<style>
    /* Containers */
    .container { max-width:1200px; margin:0 auto; padding:16px; }

    /* Card/Form */
    .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:24px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif; }
    .form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-bottom:16px; } 
    .form-card .form-grid > div { display:flex; flex-direction:column; }
    .form-card label { font-weight:600; color:#5C3A21; margin-bottom:6px; }
    .form-card input, .form-card select { width:100%; padding:6px 10px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif; font-size:14px; box-sizing:border-box; line-height:26px; }

    /* Buttons */
    .form-buttons { margin-top:20px; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
    .btn-confirm { background:#E6A574; color:#5C3A21; padding:8px 16px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; display:inline-block; }
    .btn-confirm:hover { background:#F4C38C; }
    .btn-back { background:#D97A4E; color:#FFF5EC; padding:8px 16px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-back:hover { background:#F4C38C; color:#5C3A21; }

    /* Error Messages */
    .error { color:#e07b7b; font-size:12px; margin-top:4px; }
</style>
