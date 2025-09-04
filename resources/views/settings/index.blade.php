

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Settings</h1>

    @if(auth()->user()->role === 'admin')
        <!-- Admin only: User Management link -->
        <div class="mb-4">
            <a href="{{ route('settings.users') }}" 
               class="text-blue-600 hover:underline">
               Manage Users
            </a>
        </div>
    @else
        <!-- Normal user: no special settings -->
        <p class="text-gray-600">No additional settings available.</p>
    @endif
</div>
@endsection