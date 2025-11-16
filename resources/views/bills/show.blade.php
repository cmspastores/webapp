<x-app-layout>
<div class="container">

    <!-- Main Card -->
    <div id="bill-details" class="card">

        <!-- 🔹 Bill Overview + Monthly Fees -->
        <div class="flex-grid">
            <!-- Bill Info -->
            <div class="section-card flex-item">
                <h2 class="section-title"><i class="fas fa-file-invoice"></i> Bill Overview</h2>
                <div class="detail-grid">
                    <div class="label"><i class="fas fa-file-contract"></i> Agreement:</div>
                    <div class="value">{{ $bill->agreement->agreement_id ?? '-' }}</div>

                    <div class="label"><i class="fas fa-user"></i> Renter:</div>
                    <div class="value">{{ $bill->renter->full_name ?? '-' }}</div>

                    <div class="label"><i class="fas fa-door-closed"></i> Room:</div>
                    <div class="value">{{ $bill->room->room_number ?? '-' }}</div>

                    <div class="label"><i class="fas fa-calendar-alt"></i> Billing Period:</div>
                    <div class="value">{{ $bill->period_start->format('M d, Y') }} — {{ $bill->period_end->format('M d, Y') }}</div>

                    <div class="label"><i class="fas fa-calendar-day"></i> Due Date:</div>
                    <div class="value">{{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') : '-' }}</div>

                    <div class="label"><i class="fas fa-circle-check"></i> Status:</div>
                    <div class="value">{{ ucfirst($bill->status) }}</div>
                </div>
            </div>

            <!-- Monthly Fees -->
            <div class="section-card flex-item">
                <h2 class="section-title"><i class="fas fa-list-check"></i> Monthly Fees</h2>
                <div class="detail-grid">
                    <div class="label"><i class="fas fa-house"></i> Base Rent:</div>
                    <div class="value">₱{{ number_format($bill->base_amount ?? 0,2) }}</div>

                    @if($bill->charges->isNotEmpty())
                        @foreach($bill->charges as $c)
                            <div class="label"><i class="fas fa-circle-notch"></i> {{ $c->name }}:</div>
                            <div class="value">₱{{ number_format($c->amount,2) }}</div>
                        @endforeach
                    @else
                        <div class="label">Additional Charges:</div>
                        <div class="value">₱0.00</div>
                    @endif

                    <div class="label" style="font-weight:900;">Total for this Month:</div>
                    <div class="value" style="font-weight:900;">₱{{ number_format($bill->amount_due,2) }}</div>
                </div>
            </div>
        </div>

        <!-- 🔹 Charges Table -->
        <div class="card charges-card">
            <h3 class="charges-title"><i class="fas fa-list-check"></i> Charges</h3>
            <table class="charges-table">
                <thead>
                    <tr>
                        <th>Name</th><th>Description</th><th>Amount</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bill->charges as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->description ?? '—' }}</td>
                            <td>₱{{ number_format($c->amount,2) }}</td>
                            <td>
                                <form action="{{ route('bills.charges.destroy', [$bill, $c]) }}" method="POST" onsubmit="return confirm('Remove charge?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-red">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No charges yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Add charge form -->
            <form action="{{ route('bills.charges.store', $bill) }}" method="POST" class="add-charge-form">
                @csrf
                <div class="form-grid">
                    <input type="text" name="name" placeholder="Charge name" required>
                    <input type="text" name="description" placeholder="Description (optional)">
                    <input type="number" name="amount" step="0.01" placeholder="Amount" required>
                    <button class="btn-confirm" type="submit">Add Charge</button>
                </div>
            </form>
        </div>

        <!-- 🔹 Payments & Balance Section -->
        <div class="section-card payments-section">
            <h2 class="section-title"><i class="fas fa-wallet"></i> Payments & Balance</h2>

            <div class="detail-grid" style="grid-template-columns:1fr; row-gap:6px;">
                <!-- Total for this Month -->     
            <div class="label" style="grid-column:1; display:block; text-align:left;">
            Total fee for this month: ₱{{ number_format($bill->amount_due,2) }}
           </div>
           <div style="display:none;"></div>



                <!-- List of Payments -->
                <div class="label">Payments:</div>
                <div class="value" style="display:flex;flex-direction:column;gap:2px;">
                    @forelse($bill->payments as $index => $payment)
                        <span>({{ $index + 1 }}) {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}: ₱{{ number_format($payment->amount,2) }}</span>
                    @empty
                        <span>No payments yet</span>
                    @endforelse
                </div>

                <!-- Total Amount Paid -->
                <div class="label">_________________</div>
                <div class="value">Total Amount Paid: ₱{{ number_format($totalPaid ?? 0,2) }}</div>

                <!-- Remaining Balance -->
                <div class="label">_________________</div>
                <div class="value">Remaining Balance: ₱{{ number_format($bill->balance,2) }}</div>
            </div>

            <div class="button-group">
                <a href="{{ route('payments.create', ['bill_id' => $bill->id]) }}" class="btn-pay">Make Payment</a>
                @if(auth()->user() && auth()->user()->is_admin && strtolower($bill->status) === 'paid')
                    <form action="{{ route('bills.refund', $bill) }}" method="POST" onsubmit="return confirm('Mark this bill as refunded?')">
                        @csrf
                        <button class="btn-cancel" type="submit">Refund</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Back Button -->
        <div class="action-container">
            <a href="{{ route('bills.index') }}" class="btn-back">Back</a>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* Flex wrapper for Bill Overview + Monthly Fees */
.flex-grid{display:flex;gap:16px;margin-bottom:16px;flex-wrap:wrap;}
.flex-item{flex:1 1 48%;}

/* Container */
.container{max-width:900px;margin:0 auto;padding:16px;}

/* Card base */
.card, .section-card, .charges-card {
    background: linear-gradient(135deg,#FFFDFB,#FFF8F0);
    border-radius:16px;
    border:2px solid #E6A574;
    padding:20px;
    box-shadow:0 6px 16px rgba(0,0,0,0.12);
}

/* Section cards specifics */
.section-card{margin-bottom:16px;}

/* Payments spacing */
.section-card.payments-section{margin-top:20px;}

/* Section Titles */
.section-title{font-family:'Figtree',sans-serif;font-weight:800;font-size:17px;color:#D97A4E;margin-bottom:10px;display:flex;align-items:center;gap:6px;}

/* Detail Grid */
.detail-grid{display:grid;grid-template-columns:max-content 1fr;row-gap:6px;column-gap:12px;align-items:center;}
.label{font-weight:900;color:#5C3A21;display:flex;align-items:center;gap:6px;text-align:right;}
.value{font-weight:500;color:#3A2C1F;word-break:break-word;}

/* Payments button group: left aligned, same height buttons */
.button-group{display:flex;gap:10px;align-items:center;justify-content:flex-start;margin-top:12px;}
.button-group .btn-pay, .button-group .btn-cancel{height:38px;line-height:1.2; padding:0 20px; display:flex;align-items:center;justify-content:center; font-weight:700; font-family:'Figtree',sans-serif; border-radius:6px; border:none; cursor:pointer; transition:0.2s;}
.button-group .btn-pay{background:#D97A4E;color:#FFF5EC; text-decoration:none;}
.button-group .btn-pay:hover{background:#F4C38C; color:#5C3A21;}
.button-group .btn-cancel{background:#D97A4E;color:#FFF5EC;}
.button-group .btn-cancel:hover{background:#F4C38C; color:#5C3A21;}

/* Charges table */
.charges-title{font-family:'Figtree',sans-serif;font-weight:800;font-size:16px;color:#D97A4E;margin-bottom:12px;display:flex;align-items:center;gap:6px;}
.charges-table{width:100%;border-collapse:separate;border-spacing:0;border-radius:12px;overflow:hidden;margin-bottom:12px;}
.charges-table th,.charges-table td{padding:10px 12px;border-bottom:1px solid #E6A574;border-right:1px solid #E6A574;text-align:left;}
.charges-table th:first-child,.charges-table td:first-child{border-left:none;}
.charges-table th:last-child,.charges-table td:last-child{border-right:none;}
.charges-table thead{background:linear-gradient(to right,#F4C38C,#E6A574);color:#5C3A21;font-weight:600;}
.charges-table tbody tr:hover{background:#FFF4E1;transition:0.2s;}

/* Add charge form */
.add-charge-form .form-grid{display:flex;flex-wrap:wrap;gap:8px;align-items:center;}
.add-charge-form input{padding:6px 10px;border-radius:6px;border:1px solid #E6A574;font-family:'Figtree',sans-serif;background:#FFFDFB;color:#000;width:auto;flex:1 1 150px;min-width:120px;}
.add-charge-form .btn-confirm{background:#E6A574;color:#5C3A21;padding:6px 14px;border-radius:6px;font-weight:600;cursor:pointer;border:none;transition:0.2s;}
.add-charge-form .btn-confirm:hover{background:#F4C38C;}

/* Delete button */
.btn-red{background:#b54b4b;color:#fff;padding:5px 12px;border-radius:6px;font-weight:600;border:none;cursor:pointer;transition:0.2s;}
.btn-red:hover{background:#d46a6a;}

/* Action container */
.action-container{display:flex;justify-content:flex-end;margin-top:16px;}
.btn-back{font-family:'Figtree',sans-serif;font-weight:600;border:none;cursor:pointer;transition:0.2s;background:#D97A4E;color:#FFF5EC;padding:6px 14px;border-radius:6px;text-decoration:none;display:flex;align-items:center;gap:6px;}
.btn-back:hover{background:#F4C38C;color:#5C3A21;}

/* Responsive */
@media(max-width:1024px){.detail-grid{grid-template-columns:1fr;}.label{text-align:left;} }
@media(max-width:768px){.section-title{font-size:15px;}.label,.value{font-size:14px;}.add-charge-form input{flex:1 1 100%;} }
@media(max-width:480px){.container{padding:12px;}.section-title{font-size:14px;}.label,.value{font-size:13px;}.add-charge-form input{flex:1 1 100%; min-width:0; font-size:12px;}.btn-back,.btn-confirm,.btn-red,.btn-pay,.btn-cancel{width:100%;font-size:12px;padding:6px 10px;} }

/* Sidebar collapse fix */
body.sidebar-collapsed .container { max-width:calc(100% - 80px); transition:max-width 0.3s ease; }
body.sidebar-expanded .container { max-width:calc(100% - 240px); transition:max-width 0.3s ease; }
body.sidebar-collapsed .table-wrapper, body.sidebar-expanded .table-wrapper { overflow-x:auto; scrollbar-width:thin; }










/* === 📱 Responsive Enhancements for Bills Show Blade === */

/* 💻 Large screens (>1200px) */
@media (min-width:1201px) {
  .container { padding:24px; max-width:1400px; }
  .card { padding:24px; }
  .section-card, .charges-card { padding:20px; }
  .section-title, .charges-title { font-size:18px; }
  .detail-grid { column-gap:16px; row-gap:8px; }
  .add-charge-form input { padding:8px 12px; font-size:14px; min-width:160px; }
  .btn-back, .btn-confirm, .btn-red { padding:8px 16px; font-size:14px; }
  .totals-display { font-size:16px; gap:8px; }
}

/* 🖥️ Medium screens (769px–1200px) */
@media (min-width:769px) and (max-width:1200px) {
  .container { padding:20px; }
  .card { padding:20px; }
  .section-card, .charges-card { padding:18px; }
  .section-title, .charges-title { font-size:17px; }
  .detail-grid { column-gap:14px; row-gap:7px; }
  .add-charge-form input { padding:6px 10px; font-size:13px; min-width:150px; }
  .btn-back, .btn-confirm, .btn-red { padding:7px 14px; font-size:13px; }
  .totals-display { font-size:15px; gap:7px; }
}

/* 📱 Small screens / tablets (481px–768px) */
@media (min-width:481px) and (max-width:768px) {
  .container { padding:16px; }
  .detail-grid { grid-template-columns:1fr; }
  .label { text-align:left; }
  .section-title, .charges-title { font-size:15px; }
  .label, .value { font-size:14px; }
  .add-charge-form input { flex:1 1 100%; min-width:0; font-size:13px; }
  .btn-back, .btn-confirm, .btn-red { width:100%; font-size:13px; padding:8px 12px; }
  .totals-display { font-size:14px; }
}

/* 📞 Extra small screens / mobile (≤480px) */
@media (max-width:480px) {
  .container { padding:12px; }
  .detail-grid { grid-template-columns:1fr; }
  .label { text-align:left; }
  .section-title, .charges-title { font-size:14px; }
  .label, .value { font-size:13px; }
  .add-charge-form input { flex:1 1 100%; min-width:0; font-size:12px; }
  .btn-back, .btn-confirm, .btn-red { width:100%; font-size:12px; padding:6px 10px; }
  .totals-display { font-size:13px; gap:5px; }
  .charges-table th, .charges-table td { padding:6px 8px; font-size:12px; }
}


</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</x-app-layout>