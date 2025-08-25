<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('User Management') }}</h2>
    </x-slot>

    <style>
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
        .success-message { background:#E9F8F1; color:#256D47; border:1px solid #A7DCC0; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:14px; }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif

            <div class="user-table-container">
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
                        @foreach($users as $user)
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
                                <td>
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
