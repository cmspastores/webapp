<x-app-layout>
    <div class="container">

        <!-- 🔹 Header Row -->
        <div class="bills-header-row">
            <div class="bills-header">Billings</div>
        </div>

        <!-- 🔍 Toolbar: Search + Refresh | Buttons Right -->
        <div class="toolbar-row">
            <!-- Left: Search + Refresh -->
            <div class="left-toolbar">
                <form method="GET" action="{{ route('bills.index') }}" class="search-toolbar">
                    <input type="text" name="search" placeholder="Search renter or room" value="{{ request('search') }}" class="search-input">

                    <select name="status" class="search-filter">
                        <option value="">All Statuses</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    <button type="submit" class="btn-search">Search</button>
                </form>
                <button type="button" id="btn-refresh" class="btn-refresh">Refresh List</button>
            </div>

            <!-- Right: Action Buttons -->
            <div class="toolbar-actions">
                <a href="{{ route('bills.create') }}" class="btn-new btn-fullwidth">Generate Monthly Bills</a>
                <a href="{{ route('bills.reports') }}" class="btn-archive btn-fullwidth">View Reports</a>
            </div>
        </div>

        <!-- 🔹 Ribbon Toggle -->
        <div class="ribbon-toggle" style="display:flex; gap:8px; margin-bottom:12px;">
            <button type="button" class="btn-ribbon active" data-target="monthly">Dorm / Monthly Bills</button>
            <button type="button" class="btn-ribbon" data-target="daily">Transient / Daily Bills</button>
        </div>

        <!-- 🔹 Monthly / Dorm Bills Table -->
        <div class="card table-card bill-table" id="monthly-table">
            @if($bills->where('rate_unit','!=','daily')->isEmpty())
                <p>No monthly billing records found.</p>
            @else
                <div class="table-wrapper">
                    <table class="bills-table">
                        <thead>
                            <tr>
                                <th>Renter</th>
                                <th>Room</th>
                                <th>Period</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th style="white-space:nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills->where('rate_unit','!=','daily') as $bill)
                                <tr>
                                    <td>{{ $bill->renter->full_name ?? '—' }}</td>
                                    <td>{{ $bill->room->room_number ?? '—' }}</td>
                                    <td>{{ $bill->period_start->format('M d, Y') }} — {{ $bill->period_end->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($bill->amount_due,2) }}</td>
                                    <td>₱{{ number_format($bill->balance,2) }}</td>
                                    <td><span class="status-badge {{ strtolower($bill->status) }}">{{ ucfirst($bill->status) }}</span></td>
                                    <td class="actions-cell">
                                        <div class="actions-buttons">
                                            <a href="{{ route('bills.show', $bill) }}" class="btn-view">View</a>
                                            <form action="{{ route('bills.destroy', $bill) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete this bill?');">
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
        </div>

        <!-- 🔹 Transient / Daily Bills Table -->
        <div class="card table-card bill-table" id="daily-table" style="display:none;">
            @if($bills->where('rate_unit','daily')->isEmpty())
                <p>No transient billing records found.</p>
            @else
                <div class="table-wrapper">
                    <table class="bills-table">
                        <thead>
                            <tr>
                                <th>Renter</th>
                                <th>Room</th>
                                <th>Period</th>
                                <th>Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th style="white-space:nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills->where('rate_unit','daily') as $bill)
                                <tr>
                                    <td>{{ $bill->renter->full_name ?? '—' }}</td>
                                    <td>{{ $bill->room->room_number ?? '—' }}</td>
                                    <td>{{ $bill->period_start->format('M d, Y') }} — {{ $bill->period_end->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($bill->amount_due,2) }}</td>
                                    <td>₱{{ number_format($bill->balance,2) }}</td>
                                    <td><span class="status-badge {{ strtolower($bill->status) }}">{{ ucfirst($bill->status) }}</span></td>
                                    <td class="actions-cell">
                                        <div class="actions-buttons">
                                            <a href="{{ route('bills.show', $bill) }}" class="btn-view">View</a>
                                            <form action="{{ route('bills.destroy', $bill) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete this bill?');">
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
        </div>

        <!-- 🔹 Pagination (applies to the whole collection) -->
        <div class="pagination" style="margin-top:12px;">
            {{ $bills->appends(request()->query())->links() }}
        </div>

    </div>

    <!-- 🔹 JS for Refresh + Ribbon Toggle -->
    <script>
        document.getElementById('btn-refresh').addEventListener('click', () => {
            window.location.href = "{{ route('bills.index') }}";
        });

        // Ribbon toggle logic
        const buttons = document.querySelectorAll('.btn-ribbon');
        const tables = document.querySelectorAll('.bill-table');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active from all buttons
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Hide all tables
                tables.forEach(t => t.style.display = 'none');

                // Show the selected table
                const target = btn.getAttribute('data-target');
                document.getElementById(`${target}-table`).style.display = 'block';
            });
        });
    </script>
</x-app-layout>

<!-- 🔹 CSS -->
<style>
/* 🌅 Container */ 
.container { max-width:960px; margin:0 auto; padding:20px; font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:12px; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); }

/* 🏷️ Header */ 
.bills-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; } 
.bills-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔧 Toolbar Layout */
.toolbar-row { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; margin-bottom:16px; gap:12px; }
.left-toolbar { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.search-toolbar { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.toolbar-actions { display:flex; flex-direction:column; gap:6px; min-width:160px; }
.btn-fullwidth { display:block; width:100%; text-align:center; }

/* 🔍 Search + Filter */
.search-input, .search-filter { padding:8px 12px; border-radius:8px; border:1px solid #E6A574; font-size:14px; background:#fff; font-family:'Figtree',sans-serif; color:#5C3A21; }
.btn-search, .btn-refresh { min-width:100px; }
.btn-search { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:600; border:none; border-radius:10px; padding:8px 16px; font-size:15px; cursor:pointer; transition:0.2s; }
.btn-search:hover, .btn-refresh:hover { background:#D97A4E; color:#fff; }

/* 🔹 Buttons */
.btn-refresh, .btn-new, .btn-archive { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:10px; padding:10px 18px; font-size:15px; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-decoration:none; transition:0.2s; border:none; cursor:pointer; }
.btn-refresh:hover, .btn-new:hover, .btn-archive:hover { background:#D97A4E; color:#fff; }

/* 📋 Table Card */ 
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }

/* 📑 Table Scroll + Layout */
.table-wrapper { max-height:480px; overflow-y:auto; overflow-x:auto; scrollbar-width:thin; scrollbar-color:#E6A574 #FFF8F0; } 
.table-wrapper::-webkit-scrollbar { height:8px; width:8px; } 
.table-wrapper::-webkit-scrollbar-thumb { background-color:#E6A574; border-radius:8px; } 
.table-wrapper::-webkit-scrollbar-track { background:#FFF8F0; } 

/* 📑 Table Style */ 
.bills-table { width:100%; border-collapse:separate; border-spacing:0; text-align:center; border-radius:12px; overflow:hidden; } 
.bills-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; } 
.bills-table th, .bills-table td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; } 
.bills-table th:first-child, .bills-table td:first-child { border-left:none; } 
.bills-table th:last-child, .bills-table td:last-child { border-right:none; } 
.bills-table tbody tr:last-child td { border-bottom:none; } 
.bills-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

/* 🟩 Status Badges */ 
.status-badge { padding:4px 10px; border-radius:20px; font-weight:600; font-size:13px; display:inline-block; } 
.status-badge.paid { background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; } 
.status-badge.unpaid { background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; } 
.status-badge.pending { background:#E5E7EB; color:#374151; border:1px solid #D1D5DB; }

/* ⚙️ Action Buttons */ 
.actions-buttons { display:flex; gap:6px; justify-content:center; flex-wrap:nowrap; align-items:center; width:100%; }
.inline-form { display:inline-block; margin-left:6px; } 
.btn-view, .btn-delete { padding:6px 12px; border-radius:6px; font-weight:600; font-size:13px; cursor:pointer; border:none; transition:0.2s; text-decoration:none; display:inline-block; text-align:center; } 
.btn-view { background:#4C9F70; color:#fff; } 
.btn-view:hover { background:#6FC3A1; } 
.btn-delete { background:#EF4444; color:#fff; } 
.btn-delete:hover { background:#B91C1C; }

/* 🔹 Ribbon Buttons */
.btn-ribbon { padding:8px 16px; border-radius:12px; font-weight:700; border:none; cursor:pointer; transition:0.2s; background:#F4C38C; color:#5C3A21; }
.btn-ribbon.active { background:#D97A4E; color:#fff; }

/* 📱 Responsive Table Columns */
@media (max-width:768px) { 
  .bills-table th:nth-child(4), .bills-table td:nth-child(4), 
  .bills-table th:nth-child(5), .bills-table td:nth-child(5) { display:none; } 
  .toolbar-row { flex-direction:column; align-items:stretch; gap:8px; }
  .left-toolbar { width:100%; justify-content:flex-start; }
  .toolbar-actions { width:100%; flex-direction:row; gap:8px; }
  .btn-fullwidth { width:auto; flex:1; }
}
</style>
