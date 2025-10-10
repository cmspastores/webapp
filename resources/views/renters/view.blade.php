<x-app-layout>


<div class="container">
    <div id="renter-details" class="card">
        <!-- 🔹 Personal Info -->
        <div class="detail-group">
            <p><strong>Full Name:</strong> {{ $renter->first_name }} {{ $renter->last_name }}</p>
            <p><strong>DOB:</strong> {{ $renter->dob ?? '-' }}</p>
        </div>

        <!-- 🔹 Contact Info -->
        <div class="detail-group">
            <p><strong>Email:</strong> {{ $renter->email }}</p>
            <p><strong>Phone:</strong> {{ $renter->phone ?? '-' }}</p>
            <p><strong>Emergency Contact:</strong> {{ $renter->emergency_contact ?? '-' }}</p>
            <p><strong>Address:</strong> {{ $renter->address ?? '-' }}</p>
        </div>

        <!-- 🔹 Guardian Info -->
        <div class="detail-group">
            <p><strong>Guardian Name:</strong> {{ $renter->guardian_name ?? '-' }}</p>
            <p><strong>Guardian Phone:</strong> {{ $renter->guardian_phone ?? '-' }}</p>
            <p><strong>Guardian Email:</strong> {{ $renter->guardian_email ?? '-' }}</p>
        </div>

        <!-- 🔹 Meta Info -->
        <div class="detail-group">
            <p><strong>Date Created:</strong> {{ $renter->created_at_formatted }}</p>
        </div>

        <!-- 🔹 Back Button -->
        <div class="action-container">
            <a href="{{ route('renters.index') }}" class="btn-back">Back</a>
        </div>
    </div>
</div>

<style>
.container { max-width:1200px; margin:0 auto; padding:16px; }
.header-container { display:flex; justify-content:center; align-items:center; margin-bottom:16px; }
.header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; }

.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }

/* 🔹 Detail spacing */
.detail-group { margin-bottom:16px; }
.detail-group p { margin:4px 0; }

/* 🔹 Back Button Styling */
.action-container { display:flex; justify-content:flex-end; margin-top:20px; }
.btn-back { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }
</style>

</x-app-layout>
