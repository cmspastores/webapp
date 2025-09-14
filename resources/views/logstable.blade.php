<!DOCTYPE html>
<html>
<head>
    <title>Logs Table</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-[#FFF5EC] p-6">
    <div class="max-w-6xl mx-auto bg-white p-4 rounded shadow">

        <!-- Header with title and logout button -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-[#5C3A21]">Logs Table</h1>
            
            <!-- Logout form (POST) -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
               
            </form>
        </div>

        <!-- Logs Table -->
        <table class="min-w-full bg-white border border-[#D97A4E] rounded-lg overflow-hidden">
            <thead class="text-[#5C3A21]" style="background:linear-gradient(to right,#F4C38C,#E6A574);">
                <tr>
                    <th class="py-2 px-4 border-b border-[#D97A4E] text-left">User ID</th>
                    <th class="py-2 px-4 border-b border-[#D97A4E] text-left">Email</th>
                    <th class="py-2 px-4 border-b border-[#D97A4E] text-left">Logged In</th>
                    <th class="py-2 px-4 border-b border-[#D97A4E] text-left">Logged Out</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="hover:bg-[#FFF4E1] transition">
                        <td class="py-2 px-4 border-b border-[#D97A4E]">{{ $log->user_id }}</td>
                        <td class="py-2 px-4 border-b border-[#D97A4E]">{{ $log->email }}</td>

                        <!-- NEW: formatted with Carbon for timestap -->
                        <td class="py-2 px-4 border-b border-[#D97A4E]" title="{{ $log->logged_in_at }}">
                            {{ $log->logged_in_at ? \Carbon\Carbon::parse($log->logged_in_at)->setTimezone(config('app.timezone'))->format('M d, Y g:i A') : '' }}


                        </td>

                        
                        <!-- NEW: formatted with Carbon for timestap-->
                        <td class="py-2 px-4 border-b border-[#D97A4E]" title="{{ $log->logged_out_at }}">
                           {{ $log->logged_out_at ? \Carbon\Carbon::parse($log->logged_out_at)->timezone(config('app.timezone'))->format('M d, Y g:i A') : '' }}


                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-2 px-4 text-center text-[#5C3A21]">No logs yet.</td>
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
