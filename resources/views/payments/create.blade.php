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
                                    $billsJson = $ag->bills->map(fn($b) => [
                                        'id' => $b->id,
                                        'balance' => (float) $b->balance,
                                        'period_start' => $b->period_start?->toDateString(),
                                        'period_end' => $b->period_end?->toDateString()
                                    ])->toJson(JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
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
        const oldBill = @json(old('bill_id'));
        const preselected = @json($bill_id ?? null);

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
