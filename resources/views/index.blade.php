<!DOCTYPE html>
<html>
<head>
    <title>Log Table</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto bg-white p-4 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">User Table Logs</h1>   

        <table class="min-w-full bg-white border border-blue-200">
            <thead class="bg-blue-200">
                <tr>
                    <th class="py-2 px-4 border-b">User ID</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Logged In At</th>
                    <th class="py-2 px-4 border-b">Logged Out At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td class="py-2 px-4 border-b">{{ $log->user_id }}</td>
                        <td class="py-2 px-4 border-b">{{ $log->email }}</td>
                        <td class="py-2 px-4 border-b">{{ $log->logged_in_at }}</td>
                        <td class="py-2 px-4 border-b">{{ $log->logged_out_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-2 px-4 text-center">No logs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</body>
</html>
