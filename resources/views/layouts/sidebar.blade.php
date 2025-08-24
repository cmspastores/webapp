<!-- Sidebar -->
<aside class="w-64 bg-white dark:bg-gray-900 min-h-screen border-r border-gray-200 dark:border-gray-700 flex flex-col justify-between">
    <!-- Top Nav -->
    <div class="p-4">
        <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">Navigation</h2>
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-black
                   {{ request()->routeIs('dashboard') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                   Dashboard
                </a>
            </li>

            <!-- Task Manager -->
            <li>
                <a href="{{ route('tasks.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-black
                   {{ request()->routeIs('tasks.*') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                   Task Manager
                </a>
            </li>
        </ul>
    </div>

    <!-- Bottom Nav -->
    <div class="p-4">
        <ul>
            <!-- Settings (admins only, text only) -->
            @if(auth()->user() && auth()->user()->is_admin)
                <li class="px-4 py-2 text-gray-600 dark:text-gray-300 font-semibold">
                    Admin Settings:
                </li>
                <!-- User Management (admins only) -->
                <li class="ml-4">
                    <a href="{{ route('settings.users') }}"
                        class="block px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-black
                        {{ request()->routeIs('settings.users') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                        User Management
                    </a>
                </li>
            @endif
        </ul>
    </div>
</aside>