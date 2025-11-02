<x-app-layout>

<style>

/* Combined Container for everything (users + logs) */
.management-container { max-width:960px; margin:0 auto; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); padding:20px; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,0.15); }

/* Messages */
.success-message { background:#E9F8F1; color:#256D47; border:1px solid #A7DCC0; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; text-align:center; }
.error-message { background:#FFE5E5; color:#C62828; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; text-align:center; }

/* Table Headers (both tables) */
.user-table-container .table-header, .log-table-container .logs-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; padding-bottom:8px; border-bottom:1px solid #D97A4E; margin-bottom:12px; }

/* Filters */
.filters { display:flex; flex-wrap:wrap; gap:12px; justify-content:center; margin-bottom:12px; }
.filters input { border:1px solid #D97A4E; border-radius:6px; padding:10px 14px; flex:1; text-align:center; }
.filters button, .filters a { display:inline-flex; justify-content:center; align-items:center; background:#E6A574; color:#5C3A21; font-weight:700; border-radius:14px; padding:8px 16px; box-shadow:0 5px 12px rgba(0,0,0,0.08); transition:0.3s all; cursor:pointer; font-size:13px; text-decoration:none; border:none; text-align:center; min-width:90px; }
.filters button:hover, .filters a:hover { background:#F4C38C; box-shadow:0 5px 12px rgba(0,0,0,0.1); }

/* User Table Container */
.user-table-container { overflow-x:auto; border:1px solid #D97A4E; border-radius:12px; background:#FFFFFF; box-shadow:0 10px 25px rgba(0,0,0,0.15); padding:16px; margin-bottom:24px; display:flex; flex-direction:column; gap:12px; }

/* Log Table Container */
.log-table-container { overflow-x:auto; border:1px solid #D97A4E; border-radius:12px; background:#FFFFFF; box-shadow:0 10px 25px rgba(0,0,0,0.15); padding:16px; margin-bottom:24px; display:flex; flex-direction:column; gap:12px; }

/* Tables */
.user-table, .log-table, table.logs-table { width:100%; border-collapse:separate; border-spacing:0; background:transparent; table-layout:fixed; text-align:center; border:none; border-radius:0; margin-bottom:0; }
.user-table thead, .log-table thead, table.logs-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; display:table-header-group; }
.user-table tbody, .log-table tbody, table.logs-table tbody { display:table-row-group; }
.user-table th, .user-table td, .log-table th, .log-table td, table.logs-table th, table.logs-table td { padding:12px 16px; font-size:14px; vertical-align:middle; text-align:center; word-wrap:break-word; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; }
.user-table th, .log-table th, table.logs-table th { font-weight:600; }
.user-table th:first-child, .log-table th:first-child, table.logs-table th:first-child { border-left:none; border-top-left-radius:12px; }
.user-table th:last-child, .log-table th:last-child, table.logs-table th:last-child { border-right:none; border-top-right-radius:12px; }
.user-table td:first-child, .log-table td:first-child, table.logs-table td:first-child { border-left:none; }
.user-table td:last-child, .log-table td:last-child, table.logs-table td:last-child { border-right:none; }
.user-table tbody tr:last-child td, .log-table tbody tr:last-child td, table.logs-table tbody tr:last-child td { border-bottom:none; }
.user-table tbody tr:hover, .log-table tbody tr:hover, table.logs-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

/* Status */
.status-online { color:#2E7D32; font-weight:600; }
.status-offline { color:#757575; font-weight:600; }
.status-blocked { color:#C62828; font-weight:600; }

/* Admin label */
.td-actions .admin-label { font-weight:800; color:#FFFFFF; background:#1976D2; border:2px solid #0D47A1; border-radius:6px; padding:2px 8px; font-size:12px; letter-spacing:1px; text-transform:uppercase; box-shadow:0 2px 4px rgba(0,0,0,0.15); display:inline-flex; justify-content:center; align-items:center; }

/* Admin badge variants */
.admin-label .admin-full { display:inline-block; }
.admin-label .admin-mini { display:none; }

/* Action Buttons */
.btn-action { display:inline-flex; justify-content:center; align-items:center; font-weight:600; border-radius:6px; padding:4px 10px; font-size:12px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,0.05); cursor:pointer; border:none; min-width:60px; transition:background 0.2s,color 0.2s; }
.btn-block { background:#E53935; color:#FFFFFF; }
.btn-block:hover { background:#B71C1C; color:#FFFFFF; }
.btn-unblock { background:#43A047; color:#FFFFFF; }
.btn-unblock:hover { background:#2E7D32; color:#FFFFFF; }

/* Logs specific tweaks */
.no-logs { text-align:center; color:#5C3A21; padding:12px; }
.pagination { margin-top:16px; display:flex; justify-content:flex-end; }

/* === 📱 Responsive Enhancements === */

/* Large screens (>1200px) */
@media(min-width:1201px) {
.management-container { max-width:1200px; padding:28px; }
.filters input { min-width:200px; font-size:15px; }
.filters button, .filters a { font-size:14px; padding:10px 18px; min-width:100px; }
.user-table th, .user-table td, .log-table th, .log-table td, table.logs-table th, table.logs-table td { font-size:15px; padding:14px 18px; }
.td-actions .admin-label { font-size:14px; padding:3px 10px; }
.btn-action { font-size:13px; padding:5px 12px; min-width:65px; }
.admin-label .admin-full { display:inline-block; }
.admin-label .admin-mini { display:none; }
}

/* Medium screens (769px - 1200px) */
@media(min-width:769px) and (max-width:1200px) {
.management-container { max-width:1000px; padding:24px; }
.filters { justify-content:flex-start; flex-wrap:wrap; gap:10px; }
.filters input { min-width:150px; flex:1; }
.filters button, .filters a { font-size:13px; padding:8px 14px; }
.user-table th, .user-table td, .log-table th, .log-table td, table.logs-table th, table.logs-table td { font-size:14px; padding:12px 14px; }
.td-actions .admin-label { font-size:13px; padding:3px 8px; }
.btn-action { font-size:12px; padding:4px 10px; min-width:60px; }
.admin-label .admin-full { display:inline-block; }
.admin-label .admin-mini { display:none; }
}

/* Small screens / tablets (481px - 768px) */
@media(min-width:481px) and (max-width:768px) {
.management-container { max-width:100%; padding:20px; }
.filters { flex-direction:column; gap:10px; align-items:stretch; }
.filters input { min-width:100%; }
.filters button, .filters a { width:100%; justify-content:center; font-size:12px; padding:6px 10px; }
.user-table-container, .log-table-container { overflow-x:auto; }
.user-table th, .user-table td, .log-table th, .log-table td, table.logs-table th, table.logs-table td { font-size:13px; padding:10px; }
.td-actions .admin-label { font-size:12px; padding:2px 6px; }
.btn-action { font-size:11px; padding:3px 8px; min-width:55px; }
.admin-label .admin-full { display:none; }
.admin-label .admin-mini { display:inline-block; }
}

/* Extra small screens / mobile (≤480px) */
@media(max-width:480px) {
.management-container { padding:12px; }
.filters { flex-direction:column; gap:8px; align-items:stretch; margin-bottom:16px; }
.filters input { min-width:100%; padding:8px 12px; font-size:13px; }
.filters button, .filters a { width:100%; font-size:12px; padding:6px 10px; }
.user-table-container, .log-table-container { padding:12px; }
.user-table th, .user-table td, .log-table th, .log-table td, table.logs-table th, table.logs-table td { font-size:12px; padding:8px 10px; }
.table-header, .logs-header { font-size:20px; text-align:center; }
.td-actions .admin-label { font-size:11px; padding:2px 5px; }
.btn-action { font-size:11px; padding:3px 6px; min-width:50px; }
.admin-label .admin-full { display:none; }
.admin-label .admin-mini { display:inline-block; }
.pagination { justify-content:center; margin-top:12px; }
}

</style>

<div class="management-container">

<!-- ✅ Success & error messages -->
@if(session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="error-message">{{ session('error') }}</div>
@endif

<!-- 📦 User Table Container (includes filters + title) -->
<div class="user-table-container">
    <div class="table-header"><h2>Users Table</h2></div>
    <form method="GET" action="{{ route('settings.users') }}" class="filters">
        <input type="text" name="name" value="{{ request('name') }}" placeholder="Filter by name">
        <input type="text" name="email" value="{{ request('email') }}" placeholder="Filter by email">
        <button type="submit">Filter</button>
        <a href="{{ route('settings.users') }}">Reset</a>
    </form>
    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->is_blocked)
                        <span class="status-blocked">Blocked</span>
                    @else
                        @if($user->id === Auth::id())
                            <span class="status-online">Online</span>
                        @else
                            <span class="status-offline">Offline</span>
                        @endif
                    @endif
                </td>
                <td class="td-actions">
                    @if($user->is_admin)
                        <span class="admin-label">
                            <span class="admin-full">ADMIN</span>
                            <span class="admin-mini">A</span>
                        </span>
                    @else
                        @if(!$user->is_blocked)
                            <form action="{{ url('/settings/users/'.$user->id.'/block') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-block">Block</button>
                            </form>
                        @else
                            <form action="{{ url('/settings/users/'.$user->id.'/unblock') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-action btn-unblock">Unblock</button>
                            </form>
                        @endif
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4 text-[#5C3A21]">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 📄 User Table Pagination -->
    <div class="pagination">
        {{ $users->withQueryString()->links() }}
    </div>
</div>

<!-- 📝 Login Logs Table -->
<div class="log-table-container">
    @include('logstable')
</div>

</div> <!-- End management-container -->

</x-app-layout>
