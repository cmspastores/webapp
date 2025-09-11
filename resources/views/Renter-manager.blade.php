<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Renter Manager</h2>
            <div class="header-buttons">
                <button id="btn-back-to-list-header" class="btn-back hidden">← Back to List</button>
                <button id="btn-new-renter" class="btn-new">+ New Renter</button>
            </div>
        </div>
    </x-slot>

    <div class="container">
        {{-- Search / Refresh --}}
        <div id="search-refresh" class="search-refresh">
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
                        <th>Created</th>
                        <th>Updated</th>
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
                            <td>{{ $renter->created_at->format('M d, Y h:i A') }}</td>
                            <td>{{ $renter->updated_at->format('M d, Y h:i A') }}</td>
                            <td class="actions">
                                <button class="btn-edit" data-id="{{ $renter->renter_id }}" 
                                        data-first="{{ $renter->first_name }}"
                                        data-last="{{ $renter->last_name }}" 
                                        data-dob="{{ $renter->dob }}" 
                                        data-email="{{ $renter->email }}"
                                        data-phone="{{ $renter->phone }}" 
                                        data-emergency="{{ $renter->emergency_contact }}"
                                        data-address="{{ $renter->address }}">Edit</button>
                                <form action="{{ route('renters.destroy', $renter->renter_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-delete" onclick="return confirm('Delete this renter?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center">No renters found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination">{{ $renters->links() }}</div>
        </div>

        {{-- New / Edit Forms --}}
        <div id="new-renter-form" class="card form-card hidden">
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

        <div id="edit-renter-form" class="card form-card hidden">
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
        .header-buttons { display:flex; gap:10px; }
        .btn-new, .btn-refresh, .btn-confirm, .btn-back, .btn-edit, .btn-delete { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; }
        .btn-new, .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
        .btn-new:hover, .btn-confirm:hover { background:#F4C38C; }
        .btn-refresh { background:#F4C38C; color:#5C3A21; padding:6px 14px; border-radius:6px; }
        .btn-refresh:hover { background:#E6A574; }
        .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; }
        .btn-back:hover { background:#F4C38C; color:#5C3A21; }
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
        const btnBackHeader = document.getElementById('btn-back-to-list-header');
        const listDiv = document.getElementById('renter-list');
        const newFormDiv = document.getElementById('new-renter-form');
        const editFormDiv = document.getElementById('edit-renter-form');
        const searchDiv = document.getElementById('search-refresh');

        function showForm(formDiv) {
            listDiv.classList.add('hidden');
            newFormDiv.classList.add('hidden');
            editFormDiv.classList.add('hidden');
            searchDiv.classList.add('hidden');
            formDiv.classList.remove('hidden');
            btnBackHeader.classList.remove('hidden');
            btnNew.classList.add('hidden');
        }

        function showList() {
            listDiv.classList.remove('hidden');
            searchDiv.classList.remove('hidden');
            newFormDiv.classList.add('hidden');
            editFormDiv.classList.add('hidden');
            btnBackHeader.classList.add('hidden');
            btnNew.classList.remove('hidden');
        }

        btnNew.addEventListener('click', () => showForm(newFormDiv));
        btnBackHeader.addEventListener('click', showList);

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                showForm(editFormDiv);
                document.getElementById('edit-first_name').value = btn.dataset.first;
                document.getElementById('edit-last_name').value = btn.dataset.last;
                document.getElementById('edit-dob').value = btn.dataset.dob;
                document.getElementById('edit-email').value = btn.dataset.email;
                document.getElementById('edit-phone').value = btn.dataset.phone;
                document.getElementById('edit-emergency_contact').value = btn.dataset.emergency;
                document.getElementById('edit-address').value = btn.dataset.address;
                document.getElementById('form-edit-renter').action = `/renters/${id}`;
            });
        });

        document.getElementById('btn-refresh').addEventListener('click', () => {
            location.reload();
        });
    </script>
</x-app-layout>
