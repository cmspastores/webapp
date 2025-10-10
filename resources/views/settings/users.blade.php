<x-app-layout>


<style>

/* Combined Container for everything (users + logs) */
.management-container { max-width:960px; margin:0 auto; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); padding:20px; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,0.15); }

/* Messages */
.success-message { background:#E9F8F1; color:#256D47; border:1px solid #A7DCC0; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; text-align:center; }
.error-message { background:#FFE5E5; color:#C62828; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; text-align:center; }

/* Table Headers (both tables) */
.user-table-container .table-header, .log-table-container .logs-header { font-size:24px; font-weight:900; color:#5C3A21; text-align:left; padding-bottom:8px; border-bottom:1px solid #D97A4E; margin-bottom:12px; }

/* Filters inside User Table Container only */
.filters { display:flex; flex-wrap:wrap; gap:12px; justify-content:center; margin-bottom:12px; }
.filters input { border:1px solid #D97A4E; border-radius:6px; padding:10px 14px; min-width:160px; flex:1; text-align:center; }
.filters button, .filters a { display:inline-flex; justify-content:center; align-items:center; background:#E6A574; color:#5C3A21; font-weight:700; border-radius:14px; padding:8px 16px; box-shadow:0 5px 12px rgba(0,0,0,0.08); transition:0.3s all; cursor:pointer; font-size:13x; text-decoration:none; border:none; text-align:center; min-width:90px; }
.filters button:hover, .filters a:hover { background:#F4C38C; box-shadow:0 5px 12px rgba(0,0,0,0.1); }

/* User Table Container (includes filters) */
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
.td-actions .admin-label { font-weight:800; color:#FFFFFF; background:#1976D2; border:2px solid #0D47A1; border-radius:6px; padding:2px 8px; font-size:12px; letter-spacing:1px; text-transform:uppercase; box-shadow:0 2px 4px rgba(0,0,0,0.15); }

/* Action Buttons */
.btn-action { display:inline-flex; justify-content:center; align-items:center; font-weight:600; border-radius:6px; padding:4px 10px; font-size:12px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,0.05); cursor:pointer; border:none; min-width:60px; transition:background 0.2s,color 0.2s; }
.btn-block { background:#E53935; color:#FFFFFF; }
.btn-block:hover { background:#B71C1C; color:#FFFFFF; }
.btn-unblock { background:#43A047; color:#FFFFFF; }
.btn-unblock:hover { background:#2E7D32; color:#FFFFFF; }

/* Logs specific tweaks */
.no-logs { text-align:center; color:#5C3A21; padding:12px; }
.pagination { margin-top:16px; display:flex; justify-content:flex-end; }





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
                        <span class="admin-label">ADMIN</span>
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
