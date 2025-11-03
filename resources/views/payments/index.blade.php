<x-app-layout>
    <div class="container">

        <!-- 🔹 Header Row -->
        <div class="bills-header-row">
            <div class="bills-header">Payments</div>
        </div>

        <!-- 🔍 Toolbar + Action Buttons -->
        <div class="toolbar-row">
            <!-- Left Toolbar: Search + Filters -->
            <div class="left-toolbar">
                <form method="GET" action="{{ route('payments.index') }}" class="search-toolbar">
                    <input type="text" name="search" placeholder="Search payer name" value="{{ request('search') }}" class="search-input">

                    <!-- 🔹 Status Filter Dropdown -->
                    <div class="custom-dropdown">
                        <div class="selected">{{ request('status') ? ucfirst(request('status')) : 'All Statuses' }}</div>
                        <div class="dropdown-options">
                            <div class="dropdown-option" data-value="">All Statuses</div>
                            <div class="dropdown-option" data-value="allocated">Allocated</div>
                            <div class="dropdown-option" data-value="unallocated">Unallocated</div>
                        </div>
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    </div>

                    <button type="submit" class="btn-search">Search</button>
                    <button type="button" id="btn-refresh" class="btn-search">Refresh</button>
                </form>
            </div>

            <!-- Right Toolbar: Action Buttons -->
            <div class="toolbar-actions">
                <a href="{{ route('payments.create') }}" class="btn-new btn-fullwidth">+ New Payment</a>
            </div>
        </div>

        <!-- 🔹 Payments Table -->
        <div class="card table-card">
            @if($payments->isEmpty())
                <p>No payment records found.</p>
            @else
                <div class="table-wrapper">
                    <table class="bills-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Payer</th>
                                <th>Amount</th>
                                <th>Applied</th>
                                <th>Unallocated</th>
                                <th>Date</th>
                                <th style="white-space:nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $p)
                                <tr>
                                    <td>{{ $p->billing_id }}</td>
                                    <td>{{ $p->payer_name }}</td>
                                    <td>₱{{ number_format($p->amount,2) }}</td>
                                    <td>
                                        @foreach($p->items as $it)
                                            <div>Bill #{{ $it->bill_id }}: ₱{{ number_format($it->amount,2) }}</div>
                                        @endforeach
                                    </td>
                                    <td>₱{{ number_format($p->unallocated_amount,2) }}</td>
                                    <td>{{ $p->payment_date?->format('M d, Y h:i A') }}</td>
                                    <td class="actions-cell">
                                        <div class="actions-buttons">
                                            <a href="{{ route('payments.show', $p) }}" class="btn-view">View</a>
                                            <form method="POST" action="{{ route('payments.destroy', $p) }}" class="inline-form" onsubmit="return confirm('Delete this payment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn-delete" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- 🔹 Pagination -->
            <div class="pagination" style="margin-top:12px;">
                {{ $payments->links() }}
            </div>
        </div>

    </div>

    <!-- 🔹 JS -->
    <script>
        // Refresh button
        document.getElementById('btn-refresh').addEventListener('click', () => {
            window.location.href = "{{ route('payments.index') }}";
        });

        // Custom dropdowns
        document.querySelectorAll('.custom-dropdown').forEach(dd => {
            const selected = dd.querySelector('.selected');
            const options = dd.querySelectorAll('.dropdown-option');
            const hiddenInput = dd.querySelector('input[type="hidden"]');

            selected.addEventListener('click', () => selected.classList.toggle('active'));

            options.forEach(opt => opt.addEventListener('click', () => {
                selected.textContent = opt.textContent;
                hiddenInput.value = opt.dataset.value;
                selected.classList.remove('active');
                options.forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
            }));

            document.addEventListener('click', e => {
                if(!dd.contains(e.target)) selected.classList.remove('active');
            });

            // Set initial active
            options.forEach(o => { if(o.dataset.value === hiddenInput.value) o.classList.add('active'); });
        });
    </script>
</x-app-layout>

<!-- 🔹 CSS -->
<style>
/* 🔹 Container & Header */
.container { max-width:1200px; margin:0 auto; padding:20px; font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:12px; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); align-items:center; }
.bills-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; width:100%; } 
.bills-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔹 Toolbar & Actions */
.toolbar-row { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; margin-bottom:16px; gap:12px; width:100%; }
.left-toolbar, .search-toolbar { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.toolbar-actions { display:flex; flex-direction:column; gap:6px; min-width:160px; }
.btn-fullwidth { display:block; width:100%; text-align:center; }
.search-input { padding:8px 12px; border-radius:8px; border:1px solid #E6A574; font-size:14px; background:#fff; color:#5C3A21; }

/* 🔹 Buttons */
.btn-search, .btn-refresh, .btn-new, .btn-archive, .btn-ribbon { font-weight:600; border-radius:10px; padding:8px 16px; font-size:15px; cursor:pointer; transition:0.2s; }
.btn-search, .btn-refresh, .btn-new, .btn-archive { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; border:none; font-weight:700; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-decoration:none; }
.btn-search:hover, .btn-refresh:hover, .btn-new:hover, .btn-archive:hover, .btn-ribbon.active { background:#D97A4E; color:#fff; }
.btn-ribbon { padding:8px 16px; border-radius:12px; font-weight:700; border:none; cursor:pointer; transition:0.2s; background:#F4C38C; color:#5C3A21; }

/* 🔹 Table Card */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); width:100%; max-width:1100px; }
.table-wrapper { max-height:480px; overflow-y:auto; overflow-x:auto; scrollbar-width:thin; scrollbar-color:#E6A574 #FFF8F0; } 
.table-wrapper::-webkit-scrollbar { height:8px; width:8px; } 
.table-wrapper::-webkit-scrollbar-thumb { background-color:#E6A574; border-radius:8px; } 
.table-wrapper::-webkit-scrollbar-track { background:#FFF8F0; } 
.bills-table { width:100%; border-collapse:separate; border-spacing:0; text-align:center; border-radius:12px; overflow:hidden; } 
.bills-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; } 
.bills-table th, .bills-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; } 
.bills-table th:first-child, .bills-table td:first-child { border-left:none; } 
.bills-table th:last-child, .bills-table td:last-child { border-right:none; } 
.bills-table tbody tr:last-child td { border-bottom:none; } 
.bills-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

/* 🔹 Status Badges */
.status-badge { padding:4px 10px; border-radius:20px; font-weight:600; font-size:13px; display:inline-block; } 
.status-badge.paid { background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; } 
.status-badge.unpaid { background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; } 
.status-badge.pending { background:#E5E7EB; color:#374151; border:1px solid #D1D5DB; }

/* 🔹 Action Buttons */
.actions-buttons { display:flex; gap:6px; justify-content:center; flex-wrap:nowrap; align-items:center; width:100%; }
.inline-form { display:inline-block; margin-left:6px; } 
.btn-view, .btn-delete { padding:6px 12px; border-radius:6px; font-weight:600; font-size:13px; cursor:pointer; border:none; transition:0.2s; text-decoration:none; text-align:center; } 
.btn-view { background:#4C9F70; color:#fff; } 
.btn-view:hover { background:#6FC3A1; } 
.btn-delete { background:#EF4444; color:#fff; } 
.btn-delete:hover { background:#B91C1C; }

/* 🔹 Custom Dropdown */
.custom-dropdown { position:relative; width:150px; cursor:pointer; }
.custom-dropdown .selected { padding:8px 12px; border:1px solid #E6A574; border-radius:8px; background:#fff; color:#5C3A21; transition:0.2s; }
.custom-dropdown .selected.active { border-color:#D97A4E; }
.dropdown-options { display:none; position:absolute; top:100%; left:0; width:100%; background:#fff; border:1px solid #E6A574; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1); z-index:100; }
.custom-dropdown .selected.active + .dropdown-options { display:block; }
.dropdown-option { padding:8px 12px; cursor:pointer; font-weight:500; color:#5C3A21; transition:.2s; }
.dropdown-option:hover, .dropdown-option.active { background:#E6A574; color:#fff; }

/* 📱 Responsive Table Columns */
@media (max-width:768px) { 
  .bills-table th:nth-child(4), .bills-table td:nth-child(4), 
  .bills-table th:nth-child(5), .bills-table td:nth-child(5) { display:none; } 
  .toolbar-row { flex-direction:column; align-items:stretch; gap:8px; }
  .left-toolbar, .search-toolbar { width:100%; justify-content:flex-start; }
  .toolbar-actions { width:100%; flex-direction:row; gap:8px; }
  .btn-fullwidth { width:auto; flex:1; }
}

/* === Responsive Enhancements for Payments Page === */
/* 💻 Large screens (>1200px) */
@media (min-width:1201px) {
  .container { max-width:1180px; padding:24px; align-items:center; }
  .bills-header { font-size:26px; text-align:left; }
  .bills-table th, .bills-table td { font-size:14px; padding:14px 18px; }
  .btn-search, .btn-refresh, .btn-new, .btn-archive, .btn-ribbon { font-size:15px; padding:10px 20px; }
  .toolbar-row { gap:14px; }
  .toolbar-actions { gap:8px; min-width:180px; }
  .table-wrapper { max-height:600px; }
}

/* 🖥️ Medium screens (769px–1200px) */
@media (min-width:769px) and (max-width:1200px) {
  .container { max-width:960px; padding:20px; align-items:center; }
  .bills-header { font-size:24px; text-align:left; }
  .bills-table th, .bills-table td { font-size:13px; padding:12px 14px; }
  .btn-search, .btn-refresh, .btn-new, .btn-archive, .btn-ribbon { font-size:14px; padding:8px 16px; }
  .toolbar-row { gap:12px; flex-wrap:wrap; }
  .toolbar-actions { flex-direction:column; width:auto; min-width:160px; }
  .table-wrapper { max-height:500px; }
}

/* 📱 Small screens / tablets (481px–768px) */
@media (min-width:481px) and (max-width:768px) {
  .container { padding:16px; }
  .bills-header { font-size:22px; text-align:center; border-bottom:none; }
  .toolbar-row { flex-direction:column; align-items:center; gap:10px; }
  .left-toolbar, .toolbar-actions, .search-toolbar { width:100%; justify-content:center; flex-wrap:wrap; }
  .btn-fullwidth { width:100%; }
  .bills-table th, .bills-table td { font-size:12px; padding:8px 10px; }
  .table-wrapper { max-height:400px; }
}

/* 📞 Extra small screens / mobile (≤480px) */
@media (max-width:480px) {
  .container { padding:12px; }
  .bills-header { font-size:20px; text-align:center; }
  .toolbar-row { flex-direction:column; gap:8px; align-items:center; }
  .left-toolbar, .toolbar-actions, .search-toolbar { width:100%; flex-direction:column; gap:6px; }
  .btn-fullwidth { width:100%; }
  .bills-table th, .bills-table td { font-size:11px; padding:6px 8px; }
  .bills-table th:nth-child(3), .bills-table td:nth-child(3),
  .bills-table th:nth-child(4), .bills-table td:nth-child(4),
  .bills-table th:nth-child(5), .bills-table td:nth-child(5) { display:none; }
  .table-wrapper { max-height:300px; }
}

/* === 🧩 Sidebar Collapse Compatibility Fix === */
body.sidebar-collapsed .container { max-width:calc(100% - 80px); transition:max-width 0.3s ease; }
body.sidebar-expanded .container { max-width:calc(100% - 240px); transition:max-width 0.3s ease; }
body.sidebar-collapsed .table-wrapper, body.sidebar-expanded .table-wrapper { overflow-x:auto; scrollbar-width:thin; }
</style>
