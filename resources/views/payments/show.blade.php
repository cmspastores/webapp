<x-app-layout>
    <div class="container">
        <div class="card">
            <h3>Payment #{{ $payment->billing_id ?? $payment->id }}</h3>
            <p>Payer: {{ $payment->payer_name }}</p>
            <p>Amount: ₱{{ number_format($payment->amount,2) }}</p>
            <p>Unallocated: ₱{{ number_format($payment->unallocated_amount ?? 0,2) }}</p>
            <p>Date: {{ $payment->payment_date }}</p>

            <h4>Allocations</h4>
            <ul>
                @foreach($payment->items as $item)
                    <li>Bill #{{ $item->bill_id }} — ₱{{ number_format($item->amount,2) }}</li>
                @endforeach
            </ul>

            <a href="{{ route('payments.index') }}">Back</a>
        </div>
    </div>
</x-app-layout>