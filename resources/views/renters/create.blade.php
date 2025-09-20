<x-app-layout>
<x-slot name="header">
    <div class="header-container">
        <h2 class="header-title">Add New Renter</h2>
        <div class="header-buttons">
            <a href="{{ route('renters.index') }}" class="btn-back">← Back to List</a>
        </div>
    </div>
</x-slot>

<div class="container">
    <div id="new-renter-form" class="card form-card">
        <form action="{{ route('renters.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="date" name="dob" placeholder="Date of Birth">
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="phone" placeholder="Contact Number">
                <input type="text" name="emergency_contact" placeholder="Emergency Contact">
                <input type="text" name="address" placeholder="Address" class="full-width">
                <input type="text" name="guardian_name" placeholder="Guardian Name">
                <input type="text" name="guardian_phone" placeholder="Guardian Phone">
                <input type="email" name="guardian_email" placeholder="Guardian Email">
            </div>
            <button type="submit" class="btn-confirm">Confirm</button>
        </form>
    </div>
</div>

<style>
.container { max-width:1200px; margin:0 auto; padding:16px; }
.header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
.header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; }
.header-buttons { display:flex; gap:10px; }
.btn-back, .btn-confirm { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; }
.btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }
.btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
.btn-confirm:hover { background:#F4C38C; }
.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }
.form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:12px; }
.form-card input { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; }
.full-width { grid-column: span 2; }
</style>

<script>
// No extra JS needed for simple create form
</script>
</x-app-layout>
