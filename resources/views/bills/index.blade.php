<x-app-layout>
    <div class="container">

        <!-- 🔹 Header Row -->
        <div class="bills-header-row">
            <div class="bills-header">Bills</div>
        </div>

        <!-- 🔹 Create Button -->
        <div class="toolbar-row">
            <a href="{{ route('bills.create') }}" class="btn-new">Generate Month Bills</a>
        </div>

        <!-- 🔹 Bills Table Card -->
        <div class="card table-card">
            @if($bills->isEmpty())
                <p>No bills yet.</p>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills as $bill)
                                <tr>
                                    <td>{{ $bill->renter->full_name ?? '—' }}</td>
                                    <td>{{ $bill->room->room_number ?? '—' }}</td>
                                    <td>{{ $bill->period_start->toDateString() }} — {{ $bill->period_end->toDateString() }}</td>
                                    <td>₱{{ number_format($bill->amount_due,2) }}</td>
                                    <td>₱{{ number_format($bill->balance,2) }}</td>
                                    <td><span class="status-badge {{ strtolower($bill->status) }}">{{ ucfirst($bill->status) }}</span></td>
                                    <td class="actions-cell">
                                        <a href="{{ route('bills.show', $bill) }}" class="btn-view">View</a>
                                        <form action="{{ route('bills.destroy', $bill) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete this bill?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-delete" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<style>
/* 🌅 Container */
.container { max-width:960px; margin:0 auto; padding:20px; font-family:'Figtree',sans-serif; display:flex; flex-direction:column; gap:12px; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); }

/* 🏷️ Header */
.bills-header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
.bills-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; flex:1; padding-bottom:8px; border-bottom:2px solid #D97A4E; margin-bottom:8px; }

/* 🔹 Toolbar */
.toolbar-row { display:flex; justify-content:flex-start; margin-bottom:16px; }
.btn-new { background:linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; border-radius:10px; padding:10px 18px; font-size:15px; box-shadow:0 4px 10px rgba(0,0,0,0.15); text-decoration:none; transition:0.2s; border:none; cursor:pointer; }
.btn-new:hover { background:#D97A4E; color:#fff; }

/* 📋 Table Card */
.card.table-card { background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; padding:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); }

/* 📑 Table */
.table-wrapper { overflow-x:auto; }
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
.inline-form { display:inline-block; margin-left:6px; }
.btn-view, .btn-delete { padding:6px 12px; border-radius:6px; font-weight:600; font-size:13px; cursor:pointer; border:none; transition:0.2s; text-decoration:none; display:inline-block; text-align:center; }
.btn-view { background:#4C9F70; color:#fff; }        /* ✅ Green matching Renters */
.btn-view:hover { background:#6FC3A1; }
.btn-delete { background:#EF4444; color:#fff; }
.btn-delete:hover { background:#B91C1C; }
</style>
