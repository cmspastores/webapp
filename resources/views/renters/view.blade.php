<x-app-layout>
<x-slot name="header">
    <div class="header-container">
        <h2 class="header-title">Renter Details</h2>
        <div class="header-buttons">
            <a href="{{ route('renters.index') }}" class="btn-back">← Back to List</a>
        </div>
    </div>
</x-slot>

<div class="container">
    <div id="renter-details" class="card">
        <p><strong>ID:</strong> {{ $renter->unique_id }}</p>
        <p><strong>Name:</strong> {{ $renter->first_name }} {{ $renter->last_name }}</p>
        <p><strong>DOB:</strong> {{ $renter->dob ?? '-' }}</p>
        <p><strong>Email:</strong> {{ $renter->email }}</p>
        <p><strong>Phone:</strong> {{ $renter->phone ?? '-' }}</p>
        <p><strong>Emergency Contact:</strong> {{ $renter->emergency_contact ?? '-' }}</p>
        <p><strong>Address:</strong> {{ $renter->address ?? '-' }}</p>
        <p><strong>Date Created:</strong> {{ $renter->created_at_formatted }}</p>
    </div>
</div>

<style>
.container { max-width:1200px; margin:0 auto; padding:16px; }
.header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
.header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; }
.header-buttons { display:flex; gap:10px; }
.btn-back { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }
.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }
</style>

<script>
// No extra JS needed; view-only
</script>
</x-app-layout>
