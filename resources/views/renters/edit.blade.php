<x-app-layout>


<div class="container">
    <div id="edit-renter-form" class="card form-card">
        <form action="{{ route('renters.update', $renter->renter_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <input type="text" name="first_name" value="{{ $renter->first_name }}" placeholder="First Name" required>
                <input type="text" name="last_name" value="{{ $renter->last_name }}" placeholder="Last Name" required>

                <input type="date" name="dob" value="{{ $renter->dob }}">
                <input type="email" name="email" value="{{ $renter->email }}" placeholder="Email">

                <input type="text" name="phone" value="{{ $renter->phone }}" placeholder="Contact Number">
                <input type="text" name="emergency_contact" value="{{ $renter->emergency_contact }}" placeholder="Emergency Contact">
                <input type="text" name="address" value="{{ $renter->address }}" placeholder="Address" class="full-width">
                <input type="text" name="guardian_name" value="{{ $renter->guardian_name }}" placeholder="Guardian Name">
                <input type="text" name="guardian_phone" value="{{ $renter->guardian_phone }}" placeholder="Guardian Phone">
                <input type="email" name="guardian_email" value="{{ $renter->guardian_email }}" placeholder="Guardian Email">
            </div>

            <!-- Buttons together -->
            <div class="form-buttons">
                <button type="submit" class="btn-confirm">Update</button>
                <a href="{{ route('renters.index') }}" class="btn-back">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.container { max-width:1200px; margin:0 auto; padding:16px; }

.btn-back, .btn-confirm { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; margin-right:8px; }
.btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }
.btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
.btn-confirm:hover { background:#F4C38C; }

.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }

.form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:12px; }
.form-card input { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; }
.full-width { grid-column: span 1; }

/* New style for buttons container */
.form-buttons { display:flex; justify-content:flex-end; gap:10px; margin-top:12px; }
</style>
</x-app-layout>
