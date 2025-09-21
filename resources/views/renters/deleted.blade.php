<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Renter Management</h2>
            <div class="header-buttons">
                <button id="btn-back-to-list-header" class="btn-back hidden">← Back to List</button>
            </div>
        </div>
    </x-slot>

    <div class="container">

        <!-- Search + Refresh + New Renter Controls -->
        <div id="search-refresh" class="search-refresh">
            <form method="GET" action="{{ route('renters.index') }}" class="search-container">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search renters..." class="search-input">
                <select name="filter" class="search-filter">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="name" {{ request('filter') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="email" {{ request('filter') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="phone" {{ request('filter') == 'phone' ? 'selected' : '' }}>Phone</option>
                </select>
                <button type="submit" class="btn-search">Search</button>
            </form>

            <div class="refresh-new-container">
                <a href="{{ route('renters.index') }}" class="btn-new" style="background:#6B7280; color:white;">← Back to Active</a>
                <button id="btn-refresh" class="btn-refresh">Refresh List</button>
            </div>
        </div>

        <!-- Renter Table List -->
        <div id="renter-list" class="card table-card">
            <table class="renter-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>ID</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($renters as $renter)
                        <tr>
                            <td>{{ $renter->renter_id }}</td>
                            <td>{{ $renter->full_name }}</td>
                            <td>{{ $renter->email }}</td>
                            <td>{{ $renter->phone ?? '-' }}</td>
                            <td>
                                <a href="{{ route('renters.show', $renter->renter_id) }}" class="text-link">{{ $renter->unique_id }}</a>
                            </td>
                            <td>{{ $renter->created_at_formatted }}</td>
                            <td class="actions">
                                <form action="{{ route('renters.restore', $renter->renter_id) }}" method="POST" class="inline-form" onsubmit="return confirm('Restore this renter?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn-edit">Restore</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No renters found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Custom pagination -->
            <div class="pagination">
                @if ($renters->lastPage() > 1)
                    <a href="{{ $renters->url(1) }}" class="{{ ($renters->currentPage() == 1) ? 'disabled' : '' }}">« First</a>
                    <a href="{{ $renters->previousPageUrl() }}" class="{{ ($renters->currentPage() == 1) ? 'disabled' : '' }}">‹ Prev</a>

                    @for ($i = 1; $i <= $renters->lastPage(); $i++)
                        <a href="{{ $renters->url($i) }}" class="{{ ($renters->currentPage() == $i) ? 'active' : '' }}">{{ $i }}</a>
                    @endfor

                    <a href="{{ $renters->nextPageUrl() }}" class="{{ ($renters->currentPage() == $renters->lastPage()) ? 'disabled' : '' }}">Next ›</a>
                    <a href="{{ $renters->url($renters->lastPage()) }}" class="{{ ($renters->currentPage() == $renters->lastPage()) ? 'disabled' : '' }}">Last »</a>
                @endif
            </div>
        </div>

    </div>

</x-app-layout>