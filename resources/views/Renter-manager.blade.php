<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Renter Manager</h2>
            <button id="btn-new-renter" class="btn-new">+ New Renter</button>
        </div>
    </x-slot>

    <div class="container">

        {{-- Search / Refresh --}}
        <div class="search-refresh">
            <input type="text" id="renter-search" placeholder="Search renters..." class="search-input">
            <button id="btn-refresh" class="btn-refresh">Refresh List</button>
        </div>

        {{-- Renter Table --}}
        <div id="renter-list" class="card table-card">
            <table class="renter-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>DOB</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Emergency Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($renters as $renter)
                    <tr>
                        <td>{{ $renter->renter_id }}</td>
                        <td>{{ $renter->first_name }}</td>
                        <td>{{ $renter->last_name }}</td>
                        <td>{{ $renter->dob ?? '-' }}</td>
                        <td>{{ $renter->email }}</td>
                        <td>{{ $renter->phone }}</td>
                        <td>{{ $renter->emergency_contact ?? '-' }}</td>
                        <td class="actions">
                            <button class="btn-edit" data-id="{{ $renter->renter_id }}" data-first="{{ $renter->first_name }}"
                                data-last="{{ $renter->last_name }}" data-dob="{{ $renter->dob }}" data-email="{{ $renter->email }}"
                                data-phone="{{ $renter->phone }}" data-emergency="{{ $renter->emergency_contact }}"
                                data-address="{{ $renter->address }}">Edit</button>
                            <form action="{{ route('renters.destroy', $renter->renter_id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn-delete" onclick="return confirm('Delete this renter?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No renters found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination">{{ $renters->links() }}</div>
        </div>

        {{-- New Renter Form --}}
        <div id="new-renter-form" class="card form-card hidden">
            <div class="form-header">
                <h3>Add New Renter</h3>
                <button id="btn-back-to-list" class="btn-back">Back to List</button>
            </div>
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
                </div>
                <button type="submit" class="btn-confirm">Confirm</button>
            </form>
        </div>

        {{-- Edit Renter Form --}}
        <div id="edit-renter-form" class="card form-card hidden">
            <div class="form-header">
                <h3>Edit Renter</h3>
                <button id="btn-back-to-list-edit" class="btn-back">Back to List</button>
            </div>
            <form id="form-edit-renter" method="POST">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <input type="text" name="first_name" id="edit-first_name" required>
                    <input type="text" name="last_name" id="edit-last_name" required>
                    <input type="date" name="dob" id="edit-dob">
                    <input type="email" name="email" id="edit-email" required>
                    <input type="text" name="phone" id="edit-phone">
                    <input type="text" name="emergency_contact" id="edit-emergency_contact">
                    <input type="text" name="address" id="edit-address" class="full-width">
                </div>
                <button type="submit" class="btn-confirm">Update</button>
            </form>
        </div>

    </div>

    <style>
        .container { max-width:1200px; margin:0 auto; padding:16px; }
        .header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; }
        .btn-new, .btn-refresh, .btn-confirm, .btn-back, .btn-edit, .btn-delete { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; }
        .btn-new, .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
        .btn-new:hover, .btn-confirm:hover { background:#F4C38C; }
        .btn-refresh { background:#F4C38C; color:#5C3A21; padding:6px 14px; border-radius:6px; }
        .btn-refresh:hover { background:#E6A574; }
        .btn-back { background:transparent; color:#5C3A21; }
        .btn-back:hover { color:#E6A574; }
        .btn-edit { background:#E6A574; color:#5C3A21; padding:4px 8px; border-radius:4px; font-size:13px; }
        .btn-edit:hover { background:#F4C38C; }
        .btn-delete { background:#D97A4E; color:#FFF5EC; padding:4px 8px; border-radius:4px; font-size:13px; }
        .btn-delete:hover { background:#F4C38C; color:#5C3A21; }

        .search-refresh { display:flex; justify-content:space-between; margin-bottom:16px; }
        .search-input { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; width:30%; }

        .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }
        .table-card { overflow-x:auto; }
        .renter-table { width:100%; border-collapse:collapse; }
        .renter-table th, .renter-table td { border:1px solid #D97A4E; padding:6px 10px; text-align:left; }
        .renter-table th { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; font-weight:700; }
        .renter-table tr:hover { background:#FFF4E1; }

        .form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:12px; }
        .form-card input { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; }
        .full-width { grid-column: span 2; }

        .hidden { display:none; }
        .pagination { margin-top:12px; }
    </style>

    <script>
        const btnNew = document.getElementById('btn-new-renter');
        const btnBack = document.getElementById('btn-back-to-list');
        const listDiv = document.getElementById('renter-list');
        const formDiv = document.getElementById('new-renter-form');
        const editFormDiv = document.getElementById('edit-renter-form');
        const btnBackEdit = document.getElementById('btn-back-to-list-edit');

        // Show new renter form
        btnNew.addEventListener('click', ()=>{
            listDiv.classList.add('hidden');
            formDiv.classList.remove('hidden');
            editFormDiv.classList.add('hidden');
        });

        // Back to list from new renter
        btnBack.addEventListener('click', ()=>{
            formDiv.classList.add('hidden');
            listDiv.classList.remove('hidden');
        });

        // Back to list from edit renter
        btnBackEdit.addEventListener('click', ()=>{
            editFormDiv.classList.add('hidden');
            listDiv.classList.remove('hidden');
        });

        // Refresh button
        document.getElementById('btn-refresh').addEventListener('click', ()=>{ location.reload(); });

        // Edit buttons
        document.querySelectorAll('.btn-edit').forEach(btn=>{
            btn.addEventListener('click', ()=>{
                const id = btn.dataset.id;
                editFormDiv.classList.remove('hidden');
                listDiv.classList.add('hidden');
                formDiv.classList.add('hidden');

                // Fill edit form
                document.getElementById('edit-first_name').value = btn.dataset.first;
                document.getElementById('edit-last_name').value = btn.dataset.last;
                document.getElementById('edit-dob').value = btn.dataset.dob;
                document.getElementById('edit-email').value = btn.dataset.email;
                document.getElementById('edit-phone').value = btn.dataset.phone;
                document.getElementById('edit-emergency_contact').value = btn.dataset.emergency;
                document.getElementById('edit-address').value = btn.dataset.address;

                // Update form action
                document.getElementById('form-edit-renter').action = `/renters/${id}`;
            });
        });
    </script>
</x-app-layout>
