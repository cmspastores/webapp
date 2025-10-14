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

                <div class="label"><i class="fas fa-user-friends"></i> Emergency Contact:</div>
                <div class="value">{{ $renter->emergency_contact ?? '-' }}</div>

                <div class="label"><i class="fas fa-home"></i> Address:</div>
                <div class="value">{{ $renter->address ?? '-' }}</div>
            </div>
        </div>

        <!-- Guardian Info -->
        <div class="section-card">
            <h2 class="section-title"><i class="fas fa-user-shield"></i> Guardian Information</h2>
            <div class="detail-grid">
                <div class="label"><i class="fas fa-user"></i> Guardian Name:</div>
                <div class="value">{{ $renter->guardian_name ?? '-' }}</div>

                <div class="label"><i class="fas fa-phone"></i> Guardian Phone:</div>
                <div class="value">{{ $renter->guardian_phone ?? '-' }}</div>

                <div class="label"><i class="fas fa-envelope"></i> Guardian Email:</div>
                <div class="value">{{ $renter->guardian_email ?? '-' }}</div>
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
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</x-app-layout>
