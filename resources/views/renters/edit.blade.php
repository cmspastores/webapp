<x-app-layout>
<x-slot name="header">
    <div class="header-container">
        <h2 class="header-title">Edit Renter</h2>
        <div class="header-buttons">
            <a href="{{ route('renters.index') }}" class="btn-back">← Back to List</a>
        </div>
    </div>
</x-slot>

<div class="container">
    <div id="edit-renter-form" class="card form-card">
        <form action="{{ route('renters.update', $renter->renter_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <input type="text" name="first_name" value="{{ $renter->first_name }}" placeholder="First Name" required>
                <input type="text" name="last_name" value="{{ $renter->last_name }}" placeholder="Last Name" required>
                <input type="date" name="dob" value="{{ $renter->dob }}">
                <input type="email" name="email" value="{{ $renter->email }}" placeholder="Email" required>
                <input type="text" name="phone" value="{{ $renter->phone }}" placeholder="Contact Number">
                <input type="text" name="emergency_contact" value="{{ $renter->emergency_contact }}" placeholder="Emergency Contact">
                <input type="text" name="address" value="{{ $renter->address }}" placeholder="Address" class="full-width">
                <input type="text" name="guardian_name" value="{{ $renter->guardian_name }}" placeholder="Guardian Name">
                <input type="text" name="guardian_phone" value="{{ $renter->guardian_phone }}" placeholder="Guardian Phone">
                <input type="email" name="guardian_email" value="{{ $renter->guardian_email }}" placeholder="Guardian Email">
            </div>
            <button type="submit" class="btn-confirm">Update</button>
        </form>
    </div>
</div>

<style>
.container { max-width:1200px; margin:0 auto; padding:16px; }
.header-container{display:flex;justify-content:flex-end;align-items:center;margin-bottom:16px;position:relative}
.header-title{font:900 32px 'Figtree',sans-serif;color:#5C3A21;line-height:1.2;text-align:center;text-shadow:2px 2px 6px rgba(0,0,0,0.25);letter-spacing:1.2px;text-transform:uppercase;margin:0;position:absolute;left:50%;transform:translateX(-50%);-webkit-text-stroke:0.5px #5C3A21}
.header-buttons{display:flex;gap:10px;position:relative;z-index:1}

.btn-back, .btn-confirm { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; }
.btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }
.btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
.btn-confirm:hover { background:#F4C38C; }
.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }
.form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:12px; }
.form-card input { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; }
.full-width { grid-column: span 1; }

</style>


</x-app-layout>
