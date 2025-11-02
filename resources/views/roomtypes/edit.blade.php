<x-app-layout>
    <style>
.rooms-header { font:900 32px 'Figtree',sans-serif; color:#5C3A21; line-height:1.2; text-align:center; margin-bottom:32px; text-transform:uppercase; }
.container { max-width:600px; margin:0 auto; padding:32px; }
.card { background:#FFF8F0; border-radius:16px; border:2px solid #E6A574; padding:32px; font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:24px; }
label { display:block; font-weight:600; color:#5C3A21; margin-bottom:8px; }
input { width:100%; padding:14px 18px; border-radius:10px; border:2px solid #E6A574; background:#FFFDFB; font-family:'Figtree',sans-serif; font-size:15px; margin-bottom:20px; box-sizing:border-box; }
input:focus { border-color:#D97A4E; outline:none; }

.custom-select-wrapper { position:relative; width:100%; user-select:none; }
.custom-select-trigger { padding:14px 18px; border:2px solid #E6A574; border-radius:10px; background:#FFFDFB; color:#5C3A21; font-size:15px; cursor:pointer; }
.custom-options { position:absolute; top:100%; left:0; right:0; background:#FFFDFB; border:2px solid #E6A574; border-radius:10px; display:none; z-index:10; margin-top:4px; overflow:hidden; }
.custom-option { padding:12px 18px; cursor:pointer; }
.custom-option:hover { background:#F4C38C; color:#5C3A21; }
.custom-select-wrapper.open .custom-options { display:block; }

.btn { padding:12px 20px; border:none; border-radius:10px; cursor:pointer; font-weight:700; text-decoration:none; font-family:'Figtree',sans-serif; transition:0.2s; }
.btn-update { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; }
.btn-update:hover { background:linear-gradient(90deg,#F4C38C,#E6A574); }
.btn-cancel { background:#FFF3DF; color:#5C4A32; }
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

                <!-- Custom Dropdown -->
                <div>
                    <label for="is_transient">Type</label>
                    <div class="custom-select-wrapper">
                        <div class="custom-select-trigger">
                            {{ old('is_transient', $roomtype->is_transient) == 1 ? 'Transient (daily)' : 'Dorm (monthly)' }}
                        </div>
                        <div class="custom-options">
                            <div class="custom-option" data-value="0">Dorm (monthly)</div>
                            <div class="custom-option" data-value="1">Transient (daily)</div>
                        </div>
                    </div>
                    <input type="hidden" name="is_transient" id="is_transient" value="{{ old('is_transient', $roomtype->is_transient ?? 0) }}">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-update">Update</button>
                    <a href="{{ route('roomtypes.index') }}" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const options = wrapper.querySelectorAll('.custom-option');
            const hiddenInput = wrapper.nextElementSibling;

            trigger.addEventListener('click', () => wrapper.classList.toggle('open'));
            options.forEach(opt => {
                opt.addEventListener('click', () => {
                    trigger.textContent = opt.textContent;
                    hiddenInput.value = opt.dataset.value;
                    wrapper.classList.remove('open');
                });
            });
        });

        document.addEventListener('click', e => {
            document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
                if(!wrapper.contains(e.target)) wrapper.classList.remove('open');
            });
        });
    </script>
</x-app-layout>
