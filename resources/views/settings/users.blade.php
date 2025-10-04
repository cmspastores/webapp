<x-app-layout>

    <x-slot name="header">
        <h1 class="user-management-title">User Management</h1>
    </x-slot>

<style>
/* User Management Title */  
.user-management-title { font:900 32px 'Figtree',sans-serif; color:#5C3A21; line-height:1.2; text-align:center; text-shadow:2px 2px 6px rgba(0,0,0,0.25); letter-spacing:1.2px; text-transform:uppercase; margin-bottom:16px; position:relative; -webkit-text-stroke:0.5px #5C3A21; }

/* Container */
.user-container { max-width:960px; margin:0 auto; background:#FFFFFF; padding:16px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }

/* Messages */
.success-message { background:#E9F8F1; color:#256D47; border:1px solid #A7DCC0; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; text-align:center; }
.error-message { background:#FFE5E5; color:#C62828; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; text-align:center; }

/* Filters */
.filters { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:16px; justify-content:center; }
.filters input { border:1px solid #D97A4E; border-radius:6px; padding:10px 14px; min-width:160px; flex:1; text-align:center; }
.filters button, .filters a { display:inline-flex; justify-content:center; align-items:center; background:#E6A574; color:#5C3A21; font-weight:700; border-radius:14px; padding:10px 20px; box-shadow:0 5px 12px rgba(0,0,0,0.08); transition:0.3s all; cursor:pointer; font-size:14px; text-decoration:none; border:none; text-align:center; min-width:100px; }
.filters button:hover, .filters a:hover { background:#F4C38C; box-shadow:0 5px 12px rgba(0,0,0,0.1); }

/* Table Container */
.user-table-container { overflow-x:auto; }

/* Table */
.user-table { width:100%; border-collapse:separate; border-spacing:0; background:#FFFFFF; border:1px solid #D97A4E; border-radius:12px; table-layout:fixed; text-align:center; }
.user-table thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; display:table-header-group; }
.user-table tbody { display:table-row-group; }
.user-table th, .user-table td { padding:12px 16px; border-bottom:1px solid #D97A4E; font-size:14px; vertical-align:middle; text-align:center; word-wrap:break-word; }
.user-table th { font-weight:600; border-right:1px solid #D97A4E; }
.user-table td { border-right:1px solid #D97A4E; }
.user-table th:last-child, .user-table td:last-child { border-right:none; }
.user-table tbody tr:last-child td { border-bottom:none; }

/* Hover */
.user-table tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

/* Status */
.status-online { color:#2E7D32; font-weight:600; }
.status-offline { color:#757575; font-weight:600; }
.status-blocked { color:#C62828; font-weight:600; }

/* Admin label */
.td-actions .admin-label { font-weight:800; color:#FFFFFF; background:#1976D2; border:2px solid #0D47A1; border-radius:6px; padding:4px 12px; font-size:13px; letter-spacing:1px; text-transform:uppercase; box-shadow:0 2px 4px rgba(0,0,0,0.15); }

/* Action Buttons */
.btn-action { display:inline-flex; justify-content:center; align-items:center; font-weight:600; border-radius:6px; padding:6px 14px; font-size:13px; text-align:center; box-shadow:0 2px 4px rgba(0,0,0,0.05); cursor:pointer; border:none; min-width:70px; transition:background 0.2s,color 0.2s; }

/* Default filter buttons */
.filters button, .filters a { border-radius:6px; padding:8px 16px; font-size:13px; font-weight:600; min-width:90px; }

/* Block button */
.btn-block { background:#E53935; color:#FFFFFF; }
.btn-block:hover { background:#B71C1C; color:#FFFFFF; }

/* Unblock button */
.btn-unblock { background:#43A047; color:#FFFFFF; }
.btn-unblock:hover { background:#2E7D32; color:#FFFFFF; }
</style>

<div class="user-container">

    <!-- ✅ Success message -->
    @if(session('success'))
    <div class="success-message">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="error-message">{{ session('error') }}</div>
    @endif

    <!-- 🧩 Filters -->
    <form method="GET" action="{{ route('settings.users') }}" class="filters">
        <input type="text" name="name" value="{{ request('name') }}" placeholder="Filter by name">
        <input type="text" name="email" value="{{ request('email') }}" placeholder="Filter by email">
        <button type="submit">Filter</button>
        <a href="{{ route('settings.users') }}">Reset</a>
    </form>

    <!-- 📦 User table -->
    <div class="user-table-container">
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
    </div>

    <!-- 📄 Pagination -->
    <div class="mt-4">
        {{ $users->withQueryString()->links() }}
    </div>

</div>

</x-app-layout>
