<x-app-layout>
<div class="container">
    <div id="edit-renter-form" class="card form-card">
        <form action="{{ route('renters.update', $renter->renter_id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <label>
                    <span class="label-text">First Name</span>
                    <input type="text" name="first_name" value="{{ $renter->first_name }}" required>
                </label>
                <label>
                    <span class="label-text">Last Name</span>
                    <input type="text" name="last_name" value="{{ $renter->last_name }}" required>
                </label>
                <label>
                    <span class="label-text">Date of Birth</span>
                    <input type="date" name="dob" value="{{ $renter->dob }}">
                </label>
                <label>
                    <span class="label-text">Email</span>
                    <input type="email" name="email" value="{{ $renter->email }}">
                </label>
                <label>
                    <span class="label-text">Contact Number</span>
                    <input type="text" name="phone" value="{{ $renter->phone }}">
                </label>
                <label>
                    <span class="label-text">Emergency Contact</span>
                    <input type="text" name="emergency_contact" value="{{ $renter->emergency_contact }}">
                </label>
                <label class="full-width">
                    <span class="label-text">Address</span>
                    <input type="text" name="address" value="{{ $renter->address }}">
                </label>
                <label>
                    <span class="label-text">Emergency Contact Name</span>
                    <input type="text" name="emergency_contact_name" value="{{ $renter->emergency_contact_name }}">
                </label>
                <label>
                    <span class="label-text">Emergency Contact Email</span>
                    <input type="email" name="emergency_contact_email" value="{{ $renter->emergency_contact_email }}">
                </label>
                <label>
                    <span class="label-text">Check-in Date</span>
                    <input type="date" name="check_in_date" value="{{ $renter->check_in_date }}">
                </label>
                <label>
                    <span class="label-text">Check-out Date</span>
                    <input type="date" name="check_out_date" value="{{ $renter->check_out_date }}">
                </label>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-confirm">Update</button>
                <a href="{{ route('renters.index') }}" class="btn-back">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.container { max-width:1200px; margin:0 auto; padding:16px; }
.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }
.form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:12px; }
.form-card input { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif; }
.full-width { grid-column:span 1; }
.btn-back, .btn-confirm { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; }
.btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
.btn-back:hover { background:#F4C38C; color:#5C3A21; }
.btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
.btn-confirm:hover { background:#F4C38C; }
.form-buttons { display:flex; justify-content:flex-end; gap:10px; margin-top:12px; }

label { display:flex; flex-direction:column; gap:4px; }
.label-text { font-weight:600; color:#5C3A21; font-size:13px; }
label input { padding:8px 10px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif; }

/* === 📱 Responsive Enhancements for Renter Edit Form === */

/* 💻 Large screens (>1200px) */
@media (min-width:1201px) {
  .container { padding:24px; }
  .form-card .form-grid { grid-template-columns:repeat(2,1fr); gap:12px; }
  .form-card input { font-size:14px; padding:8px 12px; }
  .btn-confirm, .btn-back { font-size:14px; padding:8px 16px; min-width:100px; }
  .form-buttons { justify-content:flex-end; }
}

/* 🖥️ Medium screens (769px–1200px) */
@media (min-width:769px) and (max-width:1200px) {
  .container { padding:20px; }
  .form-card .form-grid { grid-template-columns:repeat(2,1fr); gap:10px; }
  .form-card input { font-size:13px; padding:6px 10px; }
  .btn-confirm, .btn-back { font-size:13px; padding:6px 14px; }
  .form-buttons { justify-content:flex-end; gap:8px; }
}

/* 📱 Small screens / tablets (481px–768px) */
@media (min-width:481px) and (max-width:768px) {
  .container { padding:16px; }
  .form-card .form-grid { grid-template-columns:1fr; gap:10px; }
  .full-width { grid-column:span 1; }
  .form-card input { font-size:13px; padding:6px 10px; width:100%; }
  .btn-confirm, .btn-back { width:100%; font-size:13px; padding:8px 12px; }
  .form-buttons { flex-direction:column; gap:8px; align-items:center; }
}

/* 📞 Extra small screens / mobile (≤480px) */
@media (max-width:480px) {
  .container { padding:12px; }
  .form-card .form-grid { grid-template-columns:1fr; gap:8px; }
  .full-width { grid-column:span 1; }
  .form-card input { font-size:12px; padding:6px 8px; width:100%; }
  .btn-confirm, .btn-back { width:100%; font-size:12px; padding:6px 10px; }
  .form-buttons { flex-direction:column; gap:6px; align-items:center; }
}

</style>
</x-app-layout>
