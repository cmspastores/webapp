<x-app-layout>
    <div class="container">

        <!-- 🔹 Header Row -->
        <div class="agreements-header-row">
            <div class="agreements-header">Archived Agreements</div>
        </div>

        @if(session('success'))
            <div class="card" style="margin-bottom:12px;padding:8px;background:#D1FAE5;color:#065F46;">
                {{ session('success') }}
            </div>
        @endif

        <!-- 🔹 Buttons Only -->
        <div class="search-refresh">
            <div class="refresh-new-container">
                <a href="{{ route('agreements.index') }}" class="btn-archive">Back</a>
            </div>
        </div>

        <!-- 🔹 Archived Agreements Table -->
        <div class="card table-card">
            <div class="table-wrapper" style="overflow-x:auto;">
                <table class="agreements-table">
                    <thead>
                        <tr>
                            <th>Renter</th>
                            <th>Room</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th>Rent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements as $a)
                            <tr>
                                <td>{{ $a->renter->full_name ?? '—' }}</td>
                                <td>{{ $a->room->room_number ?? '—' }}{{ $a->room->roomType->name ? ' - ' . $a->room->roomType->name : '' }}</td>
                                <td>{{ optional($a->start_date)->toDateString() }}</td>
                                <td>{{ optional($a->end_date)->toDateString() }}</td>
                                <td><span class="status-badge {{ strtolower($a->status) }}">{{ ucfirst($a->status) }}</span></td>
                                <td>{{ $a->monthly_rent ? '₱'.number_format($a->monthly_rent,2) : '—' }}</td>
                                <td class="actions-cell">
                                    <div class="actions-buttons">
                                        <a href="{{ route('agreements.edit',$a) }}" class="btn-yellow">View</a>
                                        <form method="POST" action="{{ route('agreements.destroy',$a) }}" onsubmit="return confirm('Permanently delete this archived agreement?');" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-red" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No archived agreements found.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- 🔹 Pagination -->
                <div class="pagination" style="margin-top:12px;">
                    {{ $agreements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- 🔹 CSS -->
<style>
/* Container */
.container{max-width:960px;margin:0 auto;background:linear-gradient(135deg,#FFFDFB,#FFF8F0);padding:20px;border-radius:16px;border:2px solid #E6A574;box-shadow:0 10px 25px rgba(0,0,0,0.15);display:flex;flex-direction:column;gap:12px;font-family:'Figtree',sans-serif;}

/* Header */
.agreements-header-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;}
.agreements-header{font-size:24px;font-weight:900;color:#5C3A21;text-align:left;flex:1;padding-bottom:8px;border-bottom:2px solid #D97A4E;margin-bottom:8px;}

/* Buttons Row */
.search-refresh{display:flex;justify-content:flex-end;align-items:center;flex-wrap:wrap;margin-bottom:16px;gap:10px;}
.refresh-new-container{display:flex;gap:6px;align-items:center;}

/* Table Card */
.card.table-card{background:linear-gradient(135deg,#FFFDFB,#FFF8F0);border-radius:16px;box-shadow:0 8px 20px rgba(0,0,0,0.12);padding:16px;border:none;overflow-x:auto;}

/* Agreements Table */
.agreements-table{width:100%;border-collapse:separate;border-spacing:0;text-align:center;table-layout:auto;background:transparent;border-radius:12px;overflow:hidden;}
.agreements-table thead{background:linear-gradient(to right,#F4C38C,#E6A574);color:#5C3A21;border-radius:12px 12px 0 0;overflow:hidden;}
.agreements-table th,.agreements-table td{padding:12px 16px;font-size:14px;border-bottom:1px solid #D97A4E;border-right:1px solid #D97A4E;text-align:center;}
.agreements-table tbody tr:hover{background:#FFF4E1;transition:background 0.2s;}
.agreements-table th:first-child,.agreements-table td:first-child{border-left:none;}
.agreements-table th:last-child,.agreements-table td:last-child{border-right:none;}
.agreements-table tbody tr:last-child td{border-bottom:none;}

/* Status Badges */
.status-badge{padding:4px 10px;border-radius:20px;font-weight:600;font-size:13px;display:inline-block;}
.status-badge.active{background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;}
.status-badge.terminated{background:#FEE2E2;color:#991B1B;border:1px solid #FCA5A5;}
.status-badge.expired{background:#E5E7EB;color:#374151;border:1px solid #D1D5DB;}

/* Action Buttons */
.actions-buttons .btn-yellow,.actions-buttons .btn-red{padding:6px 12px;border-radius:6px;font-weight:600;font-size:13px;transition:0.2s;border:none;cursor:pointer;}
.actions-buttons .btn-yellow{background:#4C9F70;color:#fff;}
.actions-buttons .btn-yellow:hover{background:#6FC3A1;}
.actions-buttons .btn-red{background:#EF4444;color:#fff;}
.actions-buttons .btn-red:hover{background:#B91C1C;}

/* Navigation Buttons */
.btn-archive{background:linear-gradient(90deg,#E6A574,#F4C38C);color:#5C3A21;font-weight:700;border-radius:10px;padding:10px 18px;font-size:15px;box-shadow:0 4px 10px rgba(0,0,0,0.15);text-decoration:none;transition:0.2s;border:none;cursor:pointer;}
.btn-archive:hover{background:#D97A4E;color:#fff;}

/* Misc */
.actions-buttons{display:flex;gap:6px;justify-content:center;flex-wrap:nowrap;align-items:center;width:100%;}
.inline-form{display:inline;}
.pagination{margin-top:16px;display:flex;justify-content:flex-end;gap:6px;flex-wrap:wrap;}
</style>
