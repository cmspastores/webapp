<x-app-layout>

<style>
.rooms-header { font:900 32px 'Figtree',sans-serif; color:#5C3A21; line-height:1.2; text-align:center; text-shadow:2px 2px 6px rgba(0,0,0,0.25); letter-spacing:1.2px; text-transform:uppercase; margin-bottom:20px; position:relative; -webkit-text-stroke:0.5px #5C3A21; }
.container { max-width:620px; margin:0 auto; padding:24px; }
.card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:28px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:18px; }
label { display:block; font-weight:700; color:#5C3A21; margin-bottom:8px; }
input, select { width:100%; padding:12px 14px; border-radius:8px; border:1.5px solid #E6A574; background:#fff; margin-bottom:18px; font-family:'Figtree',sans-serif; font-size:15px; transition:0.2s; box-sizing:border-box; }
input:focus, select:focus { border-color:#D97A4E; outline:none; box-shadow:0 0 6px rgba(230,165,116,0.45); }
.btn { padding:10px 18px; border:none; border-radius:8px; cursor:pointer; font-weight:700; text-decoration:none; font-family:'Figtree',sans-serif; transition:0.2s; }
.btn-update { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; }
.btn-update:hover { background:linear-gradient(90deg,#D97A4E,#E6A574); transform:translateY(-1px); }
.btn-cancel { background:#FFF3DF; color:#5C4A32; border:none; }
.btn-cancel:hover { background:#F4C38C; color:#5C3A21; border:none; }
.error { color:#E07B7B; font-size:13px; margin-top:-10px; margin-bottom:8px; }
.form-actions { display:flex; gap:10px; justify-content:center; align-items:center; margin-top:8px; }

/* === 📱 Responsive Enhancements === */

/* 🖥️ Large screens (>1200px) */
@media(min-width:1201px) {
    .container { max-width:700px; padding:40px; }
    .card { padding:40px; gap:28px; }
    .rooms-header { font-size:36px; }
    input, select { font-size:16px; padding:16px 20px; }
    .btn { font-size:16px; padding:14px 22px; }
    .form-actions { gap:20px; }
}

/* 💻 Medium screens (769px - 1200px) */
@media(min-width:769px) and (max-width:1200px) {
    .container { max-width:650px; padding:36px; }
    .card { padding:36px; gap:26px; }
    .rooms-header { font-size:32px; }
    input, select { font-size:15px; padding:14px 18px; }
    .btn { font-size:15px; padding:12px 20px; }
    .form-actions { gap:18px; }
}

/* 📱 Small screens / tablets (481px - 768px) */
@media(min-width:481px) and (max-width:768px) {
    .container { max-width:100%; padding:28px; }
    .card { padding:28px; gap:22px; }
    .rooms-header { font-size:28px; }
    input, select { font-size:14px; padding:12px 16px; }
    .btn { font-size:14px; padding:10px 16px; width:100%; text-align:center; }
    .form-actions { flex-direction:column; gap:12px; align-items:center; justify-content:center; }
}

/* 📞 Extra small screens / mobile (≤480px) */
@media(max-width:480px) {
    .container { padding:16px; }
    .card { padding:16px; gap:16px; }
    .rooms-header { font-size:24px; }
    input, select { font-size:13px; padding:10px 12px; }
    .btn { font-size:13px; padding:8px 12px; width:100%; text-align:center; }
    .form-actions { flex-direction:column; gap:10px; align-items:center; justify-content:center; }
}
</style>

    <div class="container">
        <div class="card">
            <form action="{{ route('roomtypes.update', $roomtype) }}" method="POST">
                @csrf
                @method('PUT')
                <div>
                    <label for="name">Type Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $roomtype->name) }}" required>
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="is_transient">Type</label>
                    <select name="is_transient" id="is_transient">
                        <option value="0" {{ old('is_transient', $roomtype->is_transient) == 0 ? 'selected' : '' }}>Dorm (monthly)</option>
                        <option value="1" {{ old('is_transient', $roomtype->is_transient) == 1 ? 'selected' : '' }}>Transient (daily)</option>
                    </select>
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-update">Update</button>
                    <a href="{{ route('roomtypes.index') }}" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
