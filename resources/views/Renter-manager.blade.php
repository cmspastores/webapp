<x-app-layout>

    <!-- 🔹 HTML SECTION: Renters Manager Page Layout -->
    
    <!-- 🔸 Header Section -->
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Renter Manager</h2>
            <div class="header-buttons">
                <!-- Back button: hidden by default, shown when viewing/creating/editing -->
                <button id="btn-back-to-list-header" class="btn-back hidden">← Back to List</button>
                <!-- Opens the "New Renter" form -->
                <button id="btn-new-renter" class="btn-new">+ New Renter</button>
            </div>
        </div>
    </x-slot>

    <div class="container">

        <!-- 🔸 Search + Refresh Controls -->
        <div id="search-refresh" class="search-refresh">
            <form method="GET" action="{{ route('renters.index') }}" class="search-container">
                <!-- Dropdown filter -->
                <select name="filter" class="search-filter">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="name" {{ request('filter') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="email" {{ request('filter') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="phone" {{ request('filter') == 'phone' ? 'selected' : '' }}>Phone</option>
                </select>
                <!-- Search input -->
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search renters..." class="search-input">
                <button type="submit" class="btn-search">Search</button>
            </form>
            <!-- Refresh button -->
            <button id="btn-refresh" class="btn-refresh">Refresh List</button>
        </div>

        <!-- 🔸 Renter Table List -->
        <div id="renter-list" class="card table-card">
            <table class="renter-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>ID</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($renters as $renter)
                        <tr>
                            <!-- Display renter info -->
                            <td>{{ $renter->renter_id }}</td>
                            <td>{{ $renter->full_name }}</td>
                            <td>{{ $renter->email }}</td>
                            <td>{{ $renter->phone ?? '-' }}</td>

                            <!-- Unique ID clickable for detail view -->
                            <td>
                                <a href="#" class="text-link btn-view"
                                   data-id="{{ $renter->renter_id }}"
                                   data-first="{{ $renter->first_name }}"
                                   data-last="{{ $renter->last_name }}"
                                   data-dob="{{ $renter->dob }}"
                                   data-email="{{ $renter->email }}"
                                   data-phone="{{ $renter->phone }}"
                                   data-emergency="{{ $renter->emergency_contact }}"
                                   data-address="{{ $renter->address }}"
                                   data-created="{{ $renter->created_at_formatted }}"
                                   data-updated="{{ $renter->updated_at_formatted }}">
                                   {{ $renter->unique_id }}
                                </a>
                            </td>

                            <!-- Timestamps -->
                            <td>{{ $renter->created_at_formatted }}</td>
                            <td>{{ $renter->updated_at_formatted }}</td>

                            <!-- Edit button with data attributes -->
                            <td class="actions">
                                <button class="btn-edit" data-id="{{ $renter->renter_id }}" 
                                        data-first="{{ $renter->first_name }}"
                                        data-last="{{ $renter->last_name }}" 
                                        data-dob="{{ $renter->dob }}" 
                                        data-email="{{ $renter->email }}"
                                        data-phone="{{ $renter->phone }}" 
                                        data-emergency="{{ $renter->emergency_contact }}"
                                        data-address="{{ $renter->address }}">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <!-- No renters -->
                        <tr><td colspan="8" class="text-center">No renters found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="pagination">{{ $renters->links() }}</div>
        </div>

        <!-- 🔸 Renter Details Card -->
        <div id="renter-details" class="card hidden">
            <h3 class="header-title">Renter Details</h3>
            <p><strong>ID:</strong> <span id="detail-id"></span></p>
            <p><strong>Name:</strong> <span id="detail-name"></span></p>
            <p><strong>DOB:</strong> <span id="detail-dob"></span></p>
            <p><strong>Email:</strong> <span id="detail-email"></span></p>
            <p><strong>Phone:</strong> <span id="detail-phone"></span></p>
            <p><strong>Emergency Contact:</strong> <span id="detail-emergency"></span></p>
            <p><strong>Address:</strong> <span id="detail-address"></span></p>
            <p><strong>Created:</strong> <span id="detail-created"></span></p>
            <p><strong>Updated:</strong> <span id="detail-updated"></span></p>
        </div>

        <!-- 🔸 New Renter Form -->
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

        <!-- 🔸 Edit Renter Form -->
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

    <!-- 🔹 CSS SECTION: Renters Manager Page Styling -->
    <style>
        /* Container & Header */
        .container { max-width:1200px; margin:0 auto; padding:16px; }
        .header-container { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .header-title { font-family:'Figtree',sans-serif; font-weight:900; font-size:24px; color:#5C3A21; }
        .header-buttons { display:flex; gap:10px; }

        /* Buttons */
        .btn-new, .btn-refresh, .btn-confirm, .btn-back, .btn-edit { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; }
        .btn-new, .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
        .btn-new:hover, .btn-confirm:hover { background:#F4C38C; }
        .btn-refresh { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; }
        .btn-refresh:hover { background:#F4C38C; }
        .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; }
        .btn-back:hover { background:#F4C38C; color:#5C3A21; }
        .btn-edit { background:#E6A574; color:#5C3A21; padding:4px 8px; border-radius:4px; font-size:13px; }
        .btn-edit:hover { background:#F4C38C; }
        .text-link { color:#D97A4E; text-decoration:underline; font-weight:600; cursor:pointer; }

        /* Search bar */
        .search-refresh { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .search-container { display:flex; gap:6px; align-items:center; }
        .search-input, .search-filter { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; }
        .search-filter { background:#fff; font-family:'Figtree',sans-serif; }
        .btn-search { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; border:none; font-family:'Figtree',sans-serif; font-weight:600; cursor:pointer; transition:0.2s; }
        .btn-search:hover { background:#F4C38C; }

        /* Cards & Tables */
        .card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }
        .table-card { overflow-x:auto; }
        .renter-table { width:100%; border-collapse:collapse; }
        .renter-table th, .renter-table td { border:1px solid #D97A4E; padding:6px 10px; text-align:left; }
        .renter-table th { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; font-weight:700; }
        .renter-table tr:hover { background:#FFF4E1; }

        /* Forms */
        .form-card .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:8px; margin-bottom:12px; }
        .form-card input { padding:6px 10px; border-radius:6px; border:1px solid #E6A574; }
        .full-width { grid-column: span 2; }

        /* Utilities */
        .hidden { display:none; }
        .pagination { margin-top:12px; }
    </style>

    <!-- 🔹 JAVASCRIPT SECTION: Renters Manager Page Interactions -->
    <script>
        // Element references
        const btnNew = document.getElementById('btn-new-renter');
        const btnBackHeader = document.getElementById('btn-back-to-list-header');
        const listDiv = document.getElementById('renter-list');
        const newFormDiv = document.getElementById('new-renter-form');
        const editFormDiv = document.getElementById('edit-renter-form');
        const detailDiv = document.getElementById('renter-details');
        const searchDiv = document.getElementById('search-refresh');

        // Show specific form
        function showForm(formDiv) {
            listDiv.classList.add('hidden');
            newFormDiv.classList.add('hidden');
            editFormDiv.classList.add('hidden');
            detailDiv.classList.add('hidden');
            searchDiv.classList.add('hidden');

            formDiv.classList.remove('hidden');
            btnBackHeader.classList.remove('hidden');
            btnNew.classList.add('hidden');
        }

        // Show renter list
        function showList() {
            listDiv.classList.remove('hidden');
            searchDiv.classList.remove('hidden');

            newFormDiv.classList.add('hidden');
            editFormDiv.classList.add('hidden');
            detailDiv.classList.add('hidden');

            btnBackHeader.classList.add('hidden');
            btnNew.classList.remove('hidden');
        }

        // New renter button
        btnNew.addEventListener('click', () => showForm(newFormDiv));
        // Back button
        btnBackHeader.addEventListener('click', showList);

        // Edit renter buttons
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

        // View renter detail buttons
        document.querySelectorAll('.btn-view').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                showForm(detailDiv);

                document.getElementById('detail-id').textContent = link.dataset.id;
                document.getElementById('detail-name').textContent = link.dataset.first + " " + link.dataset.last;
                document.getElementById('detail-dob').textContent = link.dataset.dob || "-";
                document.getElementById('detail-email').textContent = link.dataset.email;
                document.getElementById('detail-phone').textContent = link.dataset.phone || "-";
                document.getElementById('detail-emergency').textContent = link.dataset.emergency?.trim() || "-";
                document.getElementById('detail-address').textContent = link.dataset.address || "-";
                document.getElementById('detail-created').textContent = link.dataset.created;
                document.getElementById('detail-updated').textContent = link.dataset.updated;
            });
        });

        // Refresh button reloads renter list
        document.getElementById('btn-refresh').addEventListener('click', () => {
            window.location.href = "{{ route('renters.index') }}";
        });
    </script>

</x-app-layout>
