<x-app-layout>
    <x-slot name="header">
        <h2>Bill Details</h2>
    </x-slot>

    <div class="p-6">
        <div class="card">
            <h3>Bill</h3>

            <table class="table-auto w-full text-sm mt-3">
                <tr>
                    <th class="text-left w-1/3">Agreement</th>
                    <td>{{ $bill->agreement->agreement_id ?? '—' }}</td>
                </tr>
                <tr>
                    <th class="text-left">Renter</th>
                    <td>{{ $bill->renter->full_name ?? '—' }}</td>
                </tr>
                <tr>
                    <th class="text-left">Room</th>
                    <td>{{ $bill->room->room_number ?? '—' }}</td>
                </tr>
                <tr>
                    <th class="text-left">Billing Period</th>
                    <td>{{ $bill->period_start->format('M d, Y') }} — {{ $bill->period_end->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <th class="text-left">Due Date</th>
                    <td>{{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') : '—' }}</td>
                </tr>
                <tr>
                    <th class="text-left">Amount Due</th>
                    <td>₱{{ number_format($bill->amount_due, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left">Balance</th>
                    <td>₱{{ number_format($bill->balance, 2) }}</td>
                </tr>
                <tr>
                    <th class="text-left">Status</th>
                    <td>{{ ucfirst($bill->status) }}</td>
                </tr>
            </table>

            <div class="mt-6">
                <a href="{{ route('bills.index') }}" class="btn btn-gray">← Back to Bills</a>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- 🔹 CSS Section -->
<style>
    .card { background: linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius: 16px; border: 2px solid #E6A574;
        padding: 16px; margin-bottom: 16px; box-shadow: 0 8px 20px rgba(0,0,0,0.12); font-family: 'Figtree', sans-serif; }
    th { color: #5C3A21; padding: 6px 8px; font-weight: 600; vertical-align: top; }
    td { padding: 6px 8px; color: #3B3B3B; }
    .btn-gray { background:#6B7280; color:white; padding:8px 14px; border-radius:6px; font-weight:600; }
    .btn-gray:hover { background:#9CA3AF; }
</style>