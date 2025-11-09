<x-app-layout>
    <div class="container">
        <div class="card form-card">
            {{-- Flash & validation messages --}}
            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom:12px;">{!! session('success') !!}</div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning" style="margin-bottom:12px;">{!! session('warning') !!}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom:12px;">
                    <ul style="margin:0 0 0 16px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                                <option value="{{ $agreement->agreement_id }}" {{ old('agreement_id') == $agreement->agreement_id ? 'selected' : '' }}>
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

    /* === 📱 Responsive Enhancements for Bills Create Form === */

/* 💻 Large screens (>1200px) */
@media (min-width:1201px) {
  .container { padding:24px; }
  .form-card .form-grid { grid-template-columns:repeat(2,1fr); gap:16px; }
  .form-card input, .form-card select { font-size:14px; padding:8px 12px; }
  .btn-confirm, .btn-back { font-size:14px; padding:8px 18px; min-width:140px; }
}

/* 🖥️ Medium screens (769px–1200px) */
@media (min-width:769px) and (max-width:1200px) {
  .container { padding:20px; }
  .form-card .form-grid { grid-template-columns:repeat(2,1fr); gap:14px; }
  .form-card input, .form-card select { font-size:13px; padding:6px 10px; }
  .btn-confirm, .btn-back { font-size:13px; padding:6px 16px; }
}

/* 📱 Small screens / tablets (481px–768px) */
@media (min-width:481px) and (max-width:768px) {
  .container { padding:16px; }
  .form-card .form-grid { grid-template-columns:1fr; gap:12px; }
  .form-card .form-grid > div { width:100%; }
  .form-card input, .form-card select { width:100%; font-size:13px; padding:6px 10px; }
  .btn-confirm, .btn-back { width:100%; font-size:13px; padding:8px 12px; }
}

/* 📞 Extra small screens / mobile (≤480px) */
@media (max-width:480px) {
  .container { padding:12px; }
  .form-card .form-grid { grid-template-columns:1fr; gap:10px; }
  .form-card .form-grid > div { width:100%; }
  .form-card input, .form-card select { width:100%; font-size:12px; padding:6px 8px; }
  .btn-confirm, .btn-back { width:100%; font-size:12px; padding:6px 10px; }
}


</style>
