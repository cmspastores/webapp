<x-app-layout>
    <div class="container">
        <div class="card form-card">
            @if($errors->any())
                <div style="margin-bottom:12px;padding:8px;background:#fee2e2;color:#991b1b;">
                    <ul style="margin:0;padding-left:16px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('payments.store') }}" method="POST">
                @csrf

                <div class="form-grid">
                    <!-- Agreement select -->
                    <div>
                        <label for="agreement_id">Agreement</label>
                        <select id="agreement_id" name="agreement_id" onchange="onAgreementChange()">
                            <option value="">-- Select Agreement (optional) --</option>
                            @foreach($agreements as $ag)
                                @php
                                    // prepare bills JSON for JS; use only id, balance, period_start, maybe period_end
                                    // Use anonymous function + ternaries to avoid PHP 8-only syntax (fn and nullsafe) which
                                    // can cause "decorators not valid here / expression expected" in some environments.
                                    $billsJson = $ag->bills->map(function($b) {
                                        return [
                                            'id' => $b->id,
                                            'balance' => (float) $b->balance,
                                            'period_start' => $b->period_start ? $b->period_start->toDateString() : null,
                                            'period_end' => $b->period_end ? $b->period_end->toDateString() : null,
                                        ];
                                    })->toJson(JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
                                @endphp
                                <option value="{{ $ag->agreement_id }}"
                                    data-bills='{{ $billsJson }}'
                                    {{ (old('agreement_id') == $ag->agreement_id) ? 'selected' : '' }}
                                    {{ (isset($selectedBill) && $selectedBill->agreement_id == $ag->agreement_id) ? 'selected' : '' }}
                                >
                                    {{ $ag->agreement_id }} — {{ $ag->renter->full_name ?? '—' }} — {{ $ag->room->room_number ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bill dropdown -->
                    <div>
                        <label for="bill_id">Bill (from agreement)</label>
                        <select id="bill_id" name="bill_id" onchange="onBillChange()">
                            <option value="">-- Select bill --</option>
                            {{-- JS will populate. We'll also pre-populate if selectedBill provided --}}
                        </select>
                    </div>

                    <div>
                        <label for="payer_name">Payer Name</label>
                        <input type="text" name="payer_name" id="payer_name" value="{{ old('payer_name') }}">
                    </div>

                    <div>
                        <label for="amount">Amount</label>
                        <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', isset($selectedBill) ? number_format((float)$selectedBill->balance,2,'.','') : '') }}">
                    </div>

                    <div>
                        <label for="exact_payment">Exact payment</label>
                        <!-- hidden default (ensures a value is always sent) -->
                        <input type="hidden" name="exact_payment" value="0">

                        <!-- checkbox sends 1 when checked -->
                        <input type="checkbox" name="exact_payment" id="exact_payment" value="1" onchange="onExactToggle()">
                    </div>

                    <div class="full-width">
                        <label for="notes">Notes</label>
                        <input type="text" name="notes" id="notes" value="{{ old('notes') }}">
                    </div>
                </div>

                <div style="margin-top:12px; display:flex; gap:8px;">
                    <button type="submit" class="btn-confirm">Save Payment</button>
                    <a href="{{ route('payments.index') }}" class="btn-back">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<script>
    // server-provided small payload (avoids Blade @ directives inside JS which some editors/linters flag)
</script>
<div id="payments-data" style="display:none"
     data-old-bill='@json(old("bill_id"))'
     data-preselected='@json($bill_id ?? null)'
></div>
<script>
    // helper to add options to bills select
    function fillBills(bills) {
        const billsSelect = document.getElementById('bill_id');
        billsSelect.innerHTML = '<option value="">-- Select bill --</option>';
        bills.forEach(b=>{
            const o = document.createElement('option');
            o.value = b.id;
            o.textContent = `${b.period_start}${b.period_end ? ' - '+b.period_end : ''} — Balance: ₱${parseFloat(b.balance).toFixed(2)}`;
            o.dataset.balance = b.balance;
            billsSelect.appendChild(o);
        });
    }

    function onBillChange(){
        const sel = document.getElementById('bill_id');
        const opt = sel.selectedOptions[0];
        const balance = opt ? parseFloat(opt.dataset.balance || 0) : 0;
        // if exact is checked, set amount to balance
        if(document.getElementById('exact_payment').checked){
            document.getElementById('amount').value = balance.toFixed(2);
        }
    }

    function onExactToggle(){
        const checked = document.getElementById('exact_payment').checked;
        const amountEl = document.getElementById('amount');
        if(checked){
            const sel = document.getElementById('bill_id');
            const opt = sel.selectedOptions[0];
            const balance = opt ? parseFloat(opt.dataset.balance || 0) : 0;
            amountEl.value = balance.toFixed(2);
            amountEl.readOnly = true;
        }else{
            amountEl.readOnly = false;
        }
    }

    function onAgreementChange(){
        const sel = document.getElementById('agreement_id');
        const opt = sel.selectedOptions[0];
        const billsSelect = document.getElementById('bill_id');
        billsSelect.innerHTML = '<option value="">-- Select bill --</option>';
        if(!opt) {
            // clear
            onBillChange();
            return;
        }
        const bills = JSON.parse(opt.dataset.bills || '[]');
        fillBills(bills);

        // optionally auto-select first unpaid bill if old() not present
        const dataEl = document.getElementById('payments-data');
        let oldBill = null, preselected = null;
        if (dataEl) {
            try {
                oldBill = dataEl.dataset.oldBill ? JSON.parse(dataEl.dataset.oldBill) : null;
            } catch(e) { oldBill = dataEl.dataset.oldBill || null; }
            try {
                preselected = dataEl.dataset.preselected ? JSON.parse(dataEl.dataset.preselected) : null;
            } catch(e) { preselected = dataEl.dataset.preselected || null; }
        }

        if (oldBill) {
            billsSelect.value = oldBill;
        } else if (preselected) {
            // if selectedBill passed, select it
            billsSelect.value = preselected;
        } else if (bills.length) {
            billsSelect.selectedIndex = 1;
        }

        onBillChange();
    }

    document.addEventListener('DOMContentLoaded', () => {
        // If blade has a selected bill from controller, set agreement & bills now:
        onAgreementChange();
        onExactToggle();
    });
</script>
</x-app-layout>


<style>
    /* Containers */
    .container { max-width:1200px; margin:0 auto; padding:16px; }

    /* Card/Form */
    .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:24px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif; }
    .form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-bottom:16px; } 
    .form-card .form-grid > div { display:flex; flex-direction:column; }
    .form-card label { font-weight:600; color:#5C3A21; margin-bottom:6px; }
    .form-card input, .form-card select { width:100%; padding:6px 10px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif; font-size:14px; box-sizing:border-box; line-height:26px; }


    /* Exact Payment Checkbox */
    #exact_payment { width:36px; height:36px; border-radius:50%; border:2px solid #E6A574; background:#fff; cursor:pointer; appearance:none; display:inline-block; vertical-align:middle; position:relative; transition:0.2s; box-shadow:0 2px 5px rgba(0,0,0,0.15); }
    #exact_payment:checked { background:#E6A574; box-shadow:0 2px 8px rgba(0,0,0,0.25); }
    #exact_payment:checked::after { content:'✔'; color:#fff; font-size:18px; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); }
    #exact_payment:hover { box-shadow:0 4px 10px rgba(0,0,0,0.2); }
    #exact_payment + label { font-size:14px; font-weight:500; color:#5C3A21; cursor:pointer; margin-left:8px; vertical-align:middle; transition:0.2s; }
    #exact_payment:checked + label { font-weight:700; color:#fff; }




    /* Buttons */
    .form-buttons { margin-top:20px; display:flex; justify-content:flex-start; gap:12px; flex-wrap:wrap; }
    .btn-confirm { background:#E6A574; color:#5C3A21; padding:8px 16px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; display:inline-block; }
    .btn-confirm:hover { background:#F4C38C; }
    .btn-back { background:#D97A4E; color:#FFF5EC; padding:8px 16px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; display:flex; align-items:center; justify-content:center; text-align:center; }
    .btn-back:hover { background:#F4C38C; color:#5C3A21; }


    /* Error Messages */
    .error { color:#e07b7b; font-size:12px; margin-top:4px; }

    /* Responsive */
    @media(max-width:1024px) { .form-card .form-grid { grid-template-columns:1fr; gap:10px; } }
    @media(max-width:768px) { .form-card input, .form-card select { font-size:13px; padding:5px 8px; } #exact_payment { margin-top:8px; } }

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
