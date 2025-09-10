<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer Manager') }}
        </h2>
    </x-slot>
<div class="container">
    <h1>Customer Manager</h1>

    {{-- Add Customer Form --}}
    <div class="card mb-4">
        <div class="card-header">Add New Customer</div>
        <div class="card-body">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="row mb-2">
                    <div class="col">
                        <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                    </div>
                    <div class="col">
                        <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col">
                        <input type="text" name="phone" class="form-control" placeholder="Phone">
                    </div>
                </div>
                <div class="mb-2">
                    <input type="text" name="address" class="form-control" placeholder="Address">
                </div>
                <button type="submit" class="btn btn-success">Add Customer</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($customers as $customer)
            <tr>
                <td>{{ $customer->customer_id }}</td>
                <td>
                    {{-- Show Customer Form --}}
                    <form action="{{ route('customers.show', $customer->customer_id) }}" method="GET" style="display:inline;">
                        <button type="submit" class="btn btn-link p-0">{{ $customer->first_name }}</button>
                    </form>
                </td>
                <td>{{ $customer->last_name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-warning btn-sm">Edit</a>
                    {{-- Delete Customer Form --}}
                    <form action="{{ route('customers.destroy', $customer->customer_id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this customer?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center">No customers found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    {{ $customers->links() }}
</div>
</x-app-layout>