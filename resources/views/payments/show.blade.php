<x-app-layout>
    <div class="container"><!-- Main container -->

        <!-- Outer Wrapper Card -->
        <div class="outer-card">

            <!-- Page Header -->
            <div class="header-container">
                <h1 class="header-title"><i class="fas fa-credit-card"></i> Payment Details</h1>
            </div>

            <!-- Payer Details Card -->
            <div class="card section-card">
                <h2 class="section-title payer"><i class="fas fa-user"></i> Payer Details</h2>
                <div class="detail-grid">
                    <div class="label"><i class="fas fa-user"></i> Payer:</div>
                    <div class="value">{{ $payment->payer_name }}</div>

                    <div class="label"><i class="fas fa-money-bill-wave"></i> Amount:</div>
                    <div class="value">₱{{ number_format($payment->amount,2) }}</div>

                    <div class="label"><i class="fas fa-exclamation-circle"></i> Unallocated:</div>
                    <div class="value">₱{{ number_format($payment->unallocated_amount ?? 0,2) }}</div>

                    <div class="label"><i class="fas fa-calendar-alt"></i> Date:</div>
                    <div class="value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('F j, Y, h:i A') }}</div>
                </div>
            </div>

            <!-- Allocations Card -->
            <div class="card section-card">
                <h2 class="section-title allocations"><i class="fas fa-list"></i> Allocations</h2>
                <ul class="allocations-list">
                    @foreach($payment->items as $item)
                        <li><i class="fas fa-check-circle"></i> Bill #{{ $item->bill_id }} — ₱{{ number_format($item->amount,2) }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Back Button -->
            <div class="action-container">
                <a href="{{ route('payments.index') }}" class="btn-back"></i> Back</a>
            </div>

        </div><!-- End outer-card -->

    </div><!-- End container -->
</x-app-layout>

<style>
/* Main container */
.container { max-width:900px; margin:0 auto; padding:16px; }

/* Outer Card */
.outer-card { background:linear-gradient(135deg,#FFF8F0,#FFFDFB); border-radius:20px; border:2px solid #E6A574; padding:20px; box-shadow:0 8px 20px rgba(0,0,0,0.12); display:flex; flex-direction:column; gap:20px; }

/* Header */
.header-container { display:flex; justify-content:center; align-items:center; margin-bottom:16px; }
.header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:26px; color:#5C3A21; display:flex; align-items:center; gap:10px; text-align:center; }

/* Cards */
.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:20px; box-shadow:0 6px 16px rgba(0,0,0,0.12); }
.section-card { background:rgba(255,255,255,0.95); border-radius:12px; border:1px solid #E6A574; padding:16px; margin-bottom:16px; }

/* Section Titles */
.section-title { font-family:'Figtree',sans-serif; font-weight:800; font-size:17px; margin-bottom:10px; display:flex; align-items:center; gap:6px; }
.section-title.allocations { color:#5C3A21; } 
.section-title.payer { color:#5C3A21; } 

/* Detail Grid */
.detail-grid { display:grid; grid-template-columns:max-content 1fr; row-gap:6px; column-gap:12px; align-items:center; }
.label { font-weight:900; color:#D97A4E; display:flex; align-items:center; gap:6px; text-align:right; } 
.value { font-weight:500; color:#3A2C1F; word-break:break-word; }

/* Allocations List */
.allocations-list { list-style:none; margin:0; padding:0; }
.allocations-list li { padding:10px 14px; background:rgba(255,255,255,0.95); border-radius:8px; margin-bottom:8px; display:flex; align-items:center; gap:10px; box-shadow:0 2px 4px rgba(0,0,0,0.08); color:#D97A4E; font-weight:700; } 
.allocations-list li i { color:#E6A574; }


/* Action Buttons */
.action-container { display:flex; justify-content:flex-end; margin-top:16px; }
.btn-back { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:flex; align-items:center; gap:6px; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }

/* === Responsive Enhancements === */
@media (max-width:1024px) { .detail-grid { grid-template-columns:1fr; } .label { text-align:left; } }
@media (max-width:768px) { .header-title { font-size:22px; } .section-title { font-size:15px; } .label,.value { font-size:14px; } }
</style>



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
