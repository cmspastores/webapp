<!DOCTYPE html>
<html>
<head>
    <title>Logs Table</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

</head>
<body>
    <div class="logs-container">
        <div class="logs-header">
            <h1>Logs Table</h1>
            <form method="POST" action="{{ route('logout') }}">@csrf</form>
        </div>
        <table class="logs-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Logged In</th>
                    <th>Logged Out</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->user_id }}</td>
                    <td>{{ $log->email }}</td>
                    <td title="{{ $log->logged_in_at }}">{{ $log->logged_in_at ? \Carbon\Carbon::parse($log->logged_in_at)->setTimezone(config('app.timezone'))->format('M d, Y g:i A') : '' }}</td>
                    <td title="{{ $log->logged_out_at }}">{{ $log->logged_out_at ? \Carbon\Carbon::parse($log->logged_out_at)->setTimezone(config('app.timezone'))->format('M d, Y g:i A') : '' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="no-logs">No logs yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $logs->links() }}</div>
    </div>
</body>
</html>
