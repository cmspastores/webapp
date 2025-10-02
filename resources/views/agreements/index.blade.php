<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Agreements</h2>
            <div class="header-buttons">
                @if(auth()->user() && auth()->user()->is_admin)
                    <a href="{{ route('agreements.create') }}" class="btn btn-new">+ New Agreement</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="container">
        @if(session('success'))
            <div class="card" style="margin-bottom:12px;padding:8px;background:#D1FAE5;color:#065F46;">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="table-wrapper" style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th>Renter</th>
                            <th>Room</th>
                            <th>Agreement Date</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Rent</th>
                            <th>Status</th>
                            <th style="white-space:nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements as $a)
                            <tr>
                                <td>{{ $a->renter->full_name ?? '—' }}</td>
                                <td>{{ $a->room->room_number ?? '—' }} {{ $a->room->roomType->name ? ' - ' . $a->room->roomType->name : '' }}</td>
                                <td>{{ optional($a->agreement_date)->toDateString() }}</td>
                                <td>{{ optional($a->start_date)->toDateString() }}</td>
                                <td>{{ optional($a->end_date)->toDateString() }}</td>
                                <td>{{ $a->monthly_rent ? '₱' . number_format($a->monthly_rent,2) : '—' }}</td>
                                <td>{{ $a->is_active ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <div style="display:flex;gap:6px;">
                                        @if(auth()->user() && auth()->user()->is_admin)
                                            <a href="{{ route('agreements.edit', $a) }}" class="btn btn-yellow">Edit</a>

                                            <form method="POST" action="{{ route('agreements.destroy', $a) }}" onsubmit="return confirm('Delete this agreement?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-red" type="submit">Delete</button>
                                            </form>
                                        @else
                                            <a href="{{ route('agreements.edit', $a) }}" class="btn btn-gray">View</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;color:#6B7280;">No agreements found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination" style="margin-top:12px;">
                    {{ $agreements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- 🔹 CSS Section -->
<style>
    /* Containers */
    .container { max-width:1200px; margin:0 auto; padding:16px; }
    .header-container{display:flex;justify-content:flex-end;align-items:center;margin-bottom:16px;position:relative}
    .header-title{font:900 32px 'Figtree',sans-serif;color:#5C3A21;line-height:1.2;text-align:center;text-shadow:2px 2px 6px rgba(0,0,0,0.25);letter-spacing:1.2px;text-transform:uppercase;margin:0;position:absolute;left:50%;transform:translateX(-50%);-webkit-text-stroke:0.5px #5C3A21}
    .header-buttons{display:flex;gap:10px;position:relative;z-index:1}

    .roomtype-row { display:flex; gap:8px; align-items:center; }

    /* Card/Form */
    .card {background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif;}
    .form-card .form-grid {display:grid; grid-template-columns:repeat(2,1fr); gap:12px;}
    .form-card .form-grid > div, .form-card .full-width {display:flex; align-items:center; gap:8px;}
    .form-card label {min-width:100px; font-weight:600; color:#5C3A21;}
    .form-card input, .form-card select {width:200px; padding:6px 10px; border-radius:6px; border:1px solid #E6A574; font-family:'Figtree',sans-serif;}
    .form-card .full-width {grid-column:span 2;}
    .form-card .full-width label {min-width:120px;}
    .form-card .full-width input {width:300px; background:#FFF3DF; color:#5C4A32; font-weight:500; cursor:pointer;}


    /* Buttons */
    .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-confirm:hover { background:#F4C38C; }
    .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-back:hover { background:#F4C38C; color:#5C3A21; }

    /* File Inputs */
    .file-input { border:none; background:#FFF3DF; color:#5C4A32; font-weight:500; padding:6px 8px; border-radius:6px; cursor:pointer; }

    /* Error Messages */
    .error { color:#e07b7b; font-size:12px; margin-top:4px; }
</style>