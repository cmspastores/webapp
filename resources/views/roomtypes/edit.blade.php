<x-app-layout>

    <style>
.rooms-header { font:900 32px 'Figtree',sans-serif; color:#5C3A21; line-height:1.2; text-align:center; letter-spacing:1px; text-transform:uppercase; margin-bottom:32px; -webkit-text-stroke:0.5px #5C3A21; }
.container { max-width:600px; margin:0 auto; padding:32px; }
.card { background:#FFF8F0; border-radius:16px; border:2px solid #E6A574; padding:32px; font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:24px; }
label { display:block; font-weight:600; color:#5C3A21; margin-bottom:8px; }
input, select { width:100%; padding:14px 18px; border-radius:10px; border:2px solid #E6A574; background:#FFFDFB; font-family:'Figtree',sans-serif; font-size:15px; box-sizing:border-box; margin-bottom:20px; }
input:focus, select:focus { border-color:#D97A4E; outline:none; }
.btn { padding:12px 20px; border:none; border-radius:10px; cursor:pointer; font-weight:700; text-decoration:none; font-family:'Figtree',sans-serif; transition:0.2s; }
.btn-update { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; }
.btn-update:hover { background:linear-gradient(90deg,#F4C38C,#E6A574); }
.btn-cancel { background:#FFF3DF; color:#5C4A32; border:none; }
.btn-cancel:hover { background:#F4C38C; color:#5C3A21; }
.error { color:#E07B7B; font-size:13px; margin-top:-6px; margin-bottom:16px; }
.form-actions { display:flex; gap:16px; justify-content:flex-end; margin-top:20px; }
    </style>

    <div class="container">
        <div class="card">
            <div class="rooms-header">Edit Room Type</div>
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
                        <option value="0" {{ old('is_transient', $roomtype->is_transient ?? 0) == 0 ? 'selected' : '' }}>Dorm (monthly)</option>
                        <option value="1" {{ old('is_transient', $roomtype->is_transient ?? 0) == 1 ? 'selected' : '' }}>Transient (daily)</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-update">Update</button>
                    <a href="{{ route('roomtypes.index') }}" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>

