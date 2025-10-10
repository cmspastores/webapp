<x-app-layout>

<div class="container">
    <div id="new-renter-form" class="card form-card">
        <form action="{{ route('renters.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="date" name="dob" placeholder="Date of Birth">
                <input type="email" name="email" placeholder="Email">

                <input type="text" name="phone" placeholder="Contact Number">
                <input type="text" name="emergency_contact" placeholder="Emergency Contact">
                <input type="text" name="address" placeholder="Address" class="full-width">
                <input type="text" name="guardian_name" placeholder="Guardian Name">
                <input type="text" name="guardian_phone" placeholder="Guardian Phone">
                <input type="email" name="guardian_email" placeholder="Guardian Email">
            </div>

            {{-- Form Actions --}}
            <div style="margin-top:12px; display:flex; gap:8px;">
                <button type="submit" class="btn-confirm">Confirm</button>
                <a href="{{ route('renters.index') }}" class="btn-back">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.container { max-width:1200px; margin:0 auto; padding:16px; }
.header-container { display:flex; justify-content:center; align-items:center; margin-bottom:16px; position:relative; }
.header-title { font:900 32px 'Figtree',sans-serif; color:#5C3A21; line-height:1.2; text-align:center; text-shadow:2px 2px 6px rgba(0,0,0,0.25); letter-spacing:1.2px; text-transform:uppercase; margin:0; -webkit-text-stroke:0.5px #5C3A21; }

.btn-back, .btn-confirm { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; }
.btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }
.btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
.btn-confirm:hover { background:#F4C38C; }

.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }
.form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:12px; }
.form-card input { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; }
.full-width { grid-column:span 1; }
</style>
</x-app-layout>
