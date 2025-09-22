<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="rooms-header">Add Room Type</h2>
        </div>
    </x-slot>

    <style>
        .container { max-width:600px; margin:0 auto; padding:16px; }
        .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif; }
        label { display:block; font-weight:600; color:#5C3A21; margin-bottom:6px; }
        input { width:100%; padding:8px 10px; border-radius:6px; border:1px solid #E6A574; background:#FFFDFB; margin-bottom:12px; font-family:'Figtree',sans-serif; }
        input:focus { border-color:#F4C38C; outline:none; box-shadow:0 0 4px rgba(230,165,116,0.4); }
        .btn { padding:6px 14px; border:none; border-radius:6px; cursor:pointer; font-weight:600; text-decoration:none; font-family:'Figtree',sans-serif; transition:0.2s; }
        .btn-save { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; }
        .btn-save:hover { background:linear-gradient(90deg,#F4C38C,#E6A574); transform:translateY(-1px); }
        .btn-cancel { background:#FFF3DF; color:#5C4A32; border:1px solid #E6A574; }
        .btn-cancel:hover { background:#F4C38C; color:#5C3A21; }
        .error { color:#E07B7B; font-size:12px; margin-top:-8px; margin-bottom:8px; }
    </style>

    <div class="container">
        <div class="card">
            <form action="{{ route('roomtypes.store') }}" method="POST">
                @csrf
                <div>
                    <label for="name">Type Name (eg. Dorm - Single)</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-save">Save</button>
                    <a href="{{ route('roomtypes.index') }}" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
