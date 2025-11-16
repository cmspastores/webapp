<x-app-layout>

<div class="container">

    <!-- Main Card -->
    <div id="renter-details" class="card">

        <!-- Header inside card -->
        <div class="header-container">
           
        </div>

        <!-- Personal & Contact Info -->
        <div class="section-card">
            <h2 class="section-title"><i class="fas fa-id-card"></i> Personal & Contact Information</h2>
            <div class="detail-grid">
                <div class="label"><i class="fas fa-user"></i> Full Name:</div>
                <div class="value">{{ $renter->first_name ?? '-' }} {{ $renter->last_name ?? '-' }}</div>

                <div class="label"><i class="fas fa-calendar-alt"></i> DOB:</div>
                <div class="value">{{ $renter->dob ?? '-' }}</div>

                <div class="label"><i class="fas fa-envelope"></i> Email:</div>
                <div class="value">{{ $renter->email ?? '-' }}</div>

                <div class="label"><i class="fas fa-phone"></i> Phone:</div>
                <div class="value">{{ $renter->phone ?? '-' }}</div>

                <div class="label"><i class="fas fa-home"></i> Address:</div>
                <div class="value">{{ $renter->address ?? '-' }}</div>
            </div>
        </div>

        <!-- Emergency Contact Info -->
        <div class="section-card">
            <h2 class="section-title"><i class="fas fa-user-shield"></i> Emergency Contact Information</h2>
            <div class="detail-grid">
                <div class="label"><i class="fas fa-user"></i> Emergency Contact Name:</div>
                <div class="value">{{ $renter->emergency_contact_name ?? '-' }}</div>

                <div class="label"><i class="fas fa-user-friends"></i> Emergency Contact Number:</div>
                <div class="value">{{ $renter->emergency_contact ?? '-' }}</div>

                <div class="label"><i class="fas fa-envelope"></i> Emergency Contact Email:</div>
                <div class="value">{{ $renter->emergency_contact_email ?? '-' }}</div>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="section-card">
           
            <div class="detail-grid">
                <div class="label"><i class="fas fa-calendar-plus"></i> Date Created:</div>
                <div class="value">{{ $renter->created_at_formatted ?? '-' }}</div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="action-container">
            <a href="{{ route('renters.index') }}" class="btn-back">Back</a>
        </div>

    </div>
</div>

<style>
.container { max-width:900px; margin:0 auto; padding:16px; }
.header-container { display:flex; justify-content:center; align-items:center; margin-bottom:16px; }
.header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:26px; color:#5C3A21; display:flex; align-items:center; gap:10px; text-align:center; }
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
@media (max-width:768px) { .header-title { font-size:22px; } .section-title { font-size:15px; } .label,.value { font-size:14px; } }

/* === 📱 Responsive Enhancements for Renter Details View === */

/* 💻 Large screens (>1200px) */
@media (min-width:1201px) {
  .container { max-width:1200px; padding:24px; }
  .header-title { font-size:32px; }
  .section-title { font-size:20px; }
  .label { font-size:16px; }
  .value { font-size:15px; }
  .action-container { justify-content:flex-end; }
  .btn-back { font-size:14px; padding:8px 18px; }
}

/* 🖥️ Medium screens (1025px–1200px) */
@media (min-width:1025px) and (max-width:1200px) {
  .container { padding:20px; max-width:1100px; }
  .header-title { font-size:28px; }
  .section-title { font-size:18px; }
  .label { font-size:15px; }
  .value { font-size:14px; }
  .btn-back { font-size:13px; padding:7px 16px; }
}

/* 📱 Small screens / tablets (769px–1024px) */
@media (min-width:769px) and (max-width:1024px) {
  .container { padding:18px; max-width:1000px; }
  .header-title { font-size:26px; }
  .section-title { font-size:17px; }
  .label, .value { font-size:14px; }
  .detail-grid { grid-template-columns:1fr; }
  .label { text-align:left; }
  .btn-back { font-size:13px; padding:6px 14px; }
}

/* 📱 Small screens / tablets (481px–768px) */
@media (min-width:481px) and (max-width:768px) {
  .container { padding:16px; }
  .header-title { font-size:24px; text-align:center; }
  .section-title { font-size:16px; }
  .label, .value { font-size:13px; }
  .detail-grid { grid-template-columns:1fr; row-gap:8px; column-gap:8px; }
  .action-container { justify-content:center; }
  .btn-back { font-size:12px; padding:6px 12px; width:100%; justify-content:center; }
}

/* 📞 Extra small screens / mobile (≤480px) */
@media (max-width:480px) {
  .container { padding:12px; }
  .header-title { font-size:20px; text-align:center; }
  .section-title { font-size:15px; }
  .label, .value { font-size:12px; }
  .detail-grid { grid-template-columns:1fr; row-gap:6px; column-gap:6px; }
  .action-container { flex-direction:column; gap:6px; align-items:center; }
  .btn-back { width:100%; font-size:12px; padding:5px 10px; }
}


</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</x-app-layout>
