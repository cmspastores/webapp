

<x-app-layout>


    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Management</h2>
    </x-slot>

    <style>
        /*  User Table */
        .user-table-container { background:#FFF5EC; border:1px solid #E6CBB3; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); overflow:hidden; }
        .user-table { width:100%; border-collapse:collapse; background:#FFFDF9; color:#5C3A21; }
        .user-table thead { background:linear-gradient(to right,#A65E3F,#70432C); color:#FFF5EC; }
        .user-table th,.user-table td { padding:12px 16px; border:1px solid #E6CBB3; text-align:left; font-size:14px; }
        .user-table th { font-weight:600; }
        .status-active { color:#2E7D32; font-weight:600; }
        .status-blocked { color:#C62828; font-weight:600; }
        .btn-action { padding:6px 12px; border:none; border-radius:6px; font-size:13px; font-weight:500; cursor:pointer; transition:background 0.2s ease; }
        .btn-block { background:#C62828; color:#FFF; }
        .btn-block:hover { background:#A62323; }
        .btn-unblock { background:#2E7D32; color:#FFF; }
        .btn-unblock:hover { background:#255D27; }
        .btn-delete { background:#8B0000; color:#FFF; }
        .btn-delete:hover { background:#660000; }
        .success-message { background:#E9F8F1; color:#256D47; border:1px solid #A7DCC0; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; }
        /* Filters styling */
        .filters { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:16px; }
        .filters input { border:1px solid #E6CBB3; border-radius:6px; padding:8px 12px; min-width:150px; flex:1; }
        .filters button, .filters a { padding:8px 16px; border-radius:6px; font-weight:600; text-decoration:none; }
        .filters button { background:linear-gradient(90deg,#D98348,#E6A574); color:#FFF; border:none; cursor:pointer; transition:0.2s; }
        .filters button:hover { background:linear-gradient(90deg,#C46A32,#D98348); }
        .filters a { background:#FFF5EC; border:1px solid #E6CBB3; color:#5C3A21; }
    </style>

    <div class="py-6 px-4 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- ✅ Success message -->
        @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 text-red-800 p-2 rounded mb-3">{{ session('error') }}</div>
        @endif

        <!-- 🧩 Filters -->
        <form method="GET" action="{{ route('settings.users') }}" class="filters">
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Filter by name">
            <input type="text" name="email" value="{{ request('email') }}" placeholder="Filter by email">
            <input type="date" name="from" value="{{ request('from') }}">
            <input type="date" name="to" value="{{ request('to') }}">
            <button type="submit">Filter</button>
            <a href="{{ route('settings.users') }}">Reset</a>
        </form>

        <!-- 📦 User table -->
        <div class="user-table-container overflow-x-auto">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
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
                                <span class="status-active">Active</span>
                            @endif
                        </td>
                        <td style="display:flex; gap:6px; flex-wrap:wrap;">
                            @if($user->is_admin)
                                <span class="text-gray-500">Admin</span>
                            @else
                                @if(!$user->is_blocked)
                                    <form action="{{ url('/settings/users/'.$user->id.'/block') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn-action btn-block">Block</button>
                                    </form>
                                @else
                                    <form action="{{ url('/settings/users/'.$user->id.'/unblock') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn-action btn-unblock">Unblock</button>
                                    </form>
                                @endif

                         
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">No users found.</td>
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
