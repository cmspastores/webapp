<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="py-6 px-4 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="GET" action="{{ route('index') }}" class="mb-6 flex gap-4">
            <input type="text" name="name" placeholder="Name" value="{{ request('name') }}"
                   class="border px-3 py-2 rounded-md">
            <input type="text" name="email" placeholder="Email" value="{{ request('email') }}"
                   class="border px-3 py-2 rounded-md">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Filter</button>
        </form>

        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                        <td class="border px-4 py-2">{{ $user->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>