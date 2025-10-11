<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">Archived Agreements</h2>
            <div class="header-buttons">
                <a href="{{ route('agreements.index') }}" class="btn btn-back">← Back to Active</a>
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
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th>Rent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agreements as $a)
                            <tr>
                                <td>{{ $a->renter->full_name ?? '—' }}</td>
                                <td>{{ $a->room->room_number ?? '—' }}</td>
                                <td>{{ optional($a->start_date)->toDateString() }}</td>
                                <td>{{ optional($a->end_date)->toDateString() }}</td>
                                <td>{{ $a->status }}</td>
                                <td>{{ $a->monthly_rent ? '₱' . number_format($a->monthly_rent,2) : '—' }}</td>
                                <td>
                                    <div style="display:flex;gap:6px;">
                                        <a href="{{ route('agreements.edit', $a) }}" class="btn btn-gray">View</a>
                                        <form method="POST" action="{{ route('agreements.destroy', $a) }}" onsubmit="return confirm('Permanently delete this archived agreement?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-red" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;color:#6B7280;">No archived agreements found.</td>
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
    .container { max-width:1200px; margin:0 auto; padding:16px; }
    .header-container{display:flex;justify-content:flex-end;align-items:center;margin-bottom:16px;position:relative}
    .header-title{font:900 32px 'Figtree',sans-serif;color:#5C3A21;line-height:1.2;text-align:center;text-shadow:2px 2px 6px rgba(0,0,0,0.25);letter-spacing:1.2px;text-transform:uppercase;margin:0;position:absolute;left:50%;transform:translateX(-50%);-webkit-text-stroke:0.5px #5C3A21}
    .header-buttons{display:flex;gap:10px;position:relative;z-index:1}

    .card {background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; padding:16px; margin-bottom:16px; box-shadow:0 8px 20px rgba(0,0,0,0.12); font-family:'Figtree',sans-serif;}
    table th, table td { padding:8px 10px; border-bottom:1px solid #E6A574; text-align:left; font-size:14px; }
    table th { background:#FFF3DF; color:#5C3A21; font-weight:700; }

    /* 🔹 Status badges */
    .status-badge { padding:4px 10px; border-radius:20px; font-weight:600; font-size:13px; display:inline-block; }
    .btn-archive { background: #6B7280; color: white; padding: 6px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; transition: 0.2s; }
    .btn-archive:hover { background: #9CA3AF; }
    .status-badge.active { background:#D1FAE5; color:#065F46; border:1px solid #A7F3D0; }
    .status-badge.terminated { background:#FEE2E2; color:#991B1B; border:1px solid #FCA5A5; }
    .status-badge.expired { background:#E5E7EB; color:#374151; border:1px solid #D1D5DB; }

    /* 🔹 Buttons */
    .btn { padding:5px 12px; border-radius:6px; font-weight:600; cursor:pointer; text-decoration:none; transition:0.2s; border:none; }
    .btn-yellow { background:#E6A574; color:#5C3A21; }
    .btn-yellow:hover { background:#F4C38C; }
    .btn-red { background:#DC2626; color:white; }
    .btn-red:hover { background:#EF4444; }
    .btn-green { background:#059669; color:white; }
    .btn-green:hover { background:#10B981; }
    .btn-gray { background:#6B7280; color:white; }
    .btn-gray:hover { background:#9CA3AF; }
    .btn-new { background:#D97A4E; color:white; }
    .btn-new:hover { background:#F4C38C; color:#5C3A21; }
</style>