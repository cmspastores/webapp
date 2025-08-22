<aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 min-h-screen hidden sm:block">
    <div class="p-6 text-lg font-semibold text-gray-900 dark:text-white border-b dark:border-gray-700">
        {{ config('app.name', 'Laravel') }}
    </div>
    <nav class="mt-4 space-y-1 px-4">
     <!--   <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
        </x-nav-link> -->

        <x-nav-link :href="route('task.manager')" :active="request()->routeIs('task.manager')">
          {{ __('Task Manager') }}
        </x-nav-link> 



    </nav>
</aside>
