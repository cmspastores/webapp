<x-app-layout>
<div class="container">

    <!-- Main Card -->
    <div id="bill-details" class="card">

        <!-- Bill Info Section -->
        <div class="section-card">
            <h2 class="section-title"><i class="fas fa-file-invoice"></i> Bill Information</h2>
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

                <div class="label"><i class="fas fa-money-bill-wave"></i> Amount Due:</div>
                <div class="value">₱{{ number_format($bill->amount_due, 2) }}</div>

                <div class="label"><i class="fas fa-wallet"></i> Balance:</div>
                <div class="value">₱{{ number_format($bill->balance, 2) }}</div>

                <div class="label"><i class="fas fa-info-circle"></i> Status:</div>
                <div class="value">{{ ucfirst($bill->status) }}</div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="action-container">
            <a href="{{ route('bills.index') }}" class="btn-back">Back</a>
        </div>

    </div>
</div>

<!-- 🔹 CSS Section (matches Renter View) -->
<style>
.container { max-width:900px; margin:0 auto; padding:16px; }
.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:20px; box-shadow:0 6px 16px rgba(0,0,0,0.12); }
.section-card { background:rgba(255,255,255,0.95); border-radius:12px; border:1px solid #E6A574; padding:16px; margin-bottom:16px; }
.section-title { font-family:'Figtree',sans-serif; font-weight:800; font-size:17px; color:#D97A4E; margin-bottom:10px; display:flex; align-items:center; gap:6px; }
.detail-grid { display:grid; grid-template-columns:max-content 1fr; row-gap:6px; column-gap:12px; align-items:center; }
.label { font-weight:900; color:#5C3A21; display:flex; align-items:center; gap:6px; text-align:right; }
.value { font-weight:500; color:#3A2C1F; word-break:break-word; }
.action-container { display:flex; justify-content:flex-end; margin-top:16px; }
.btn-back { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:flex; align-items:center; gap:6px; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }
@media (max-width:1024px) { .detail-grid { grid-template-columns:1fr; } .label { text-align:left; } }
@media (max-width:768px) { .section-title { font-size:15px; } .label,.value { font-size:14px; } }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</x-app-layout>
