<x-app-layout>
<div class="container">

    <!-- Main Card -->
    <div id="bill-details" class="card">

        <!-- Bill Info Section -->
        <div class="section-card">
            <h2 class="section-title"><i class="fas fa-file-invoice"></i> Bill Information</h2>
            <div class="detail-grid">
                <div class="label"><i class="fas fa-file-contract"></i> Agreement:</div>
                <div class="value">{{ $bill->agreement->agreement_id ?? '-' }}</div>

                <div class="label"><i class="fas fa-user"></i> Renter:</div>
                <div class="value">{{ $bill->renter->full_name ?? '-' }}</div>

                <div class="label"><i class="fas fa-door-closed"></i> Room:</div>
                <div class="value">{{ $bill->room->room_number ?? '-' }}</div>

                <div class="label"><i class="fas fa-calendar-alt"></i> Billing Period:</div>
                <div class="value">{{ $bill->period_start->format('M d, Y') }} — {{ $bill->period_end->format('M d, Y') }}</div>

                <div class="label"><i class="fas fa-calendar-day"></i> Due Date:</div>
                <div class="value">{{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') : '-' }}</div>

                <div class="label"><i class="fas fa-money-bill-wave"></i> Amount Due:</div>
                <div class="value">₱{{ number_format($bill->amount_due, 2) }}</div>

                <div class="label"><i class="fas fa-wallet"></i> Balance:</div>
                <div class="value">₱{{ number_format($bill->balance, 2) }}</div>

                <div class="label"><i class="fas fa-info-circle"></i> Status:</div>
                <div class="value">{{ ucfirst($bill->status) }}</div>
            </div>
        </div>

        <!-- Charges list -->
        <div class="card charges-card">
            <h3 class="charges-title"><i class="fas fa-list-check"></i> Charges</h3>

            <table class="charges-table">
                <thead>
                    <tr>
                        <th>Name</th><th>Description</th><th>Amount</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->charges as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->description ?? '—' }}</td>
                            <td>₱{{ number_format($c->amount,2) }}</td>
                            <td>
                                <form action="{{ route('bills.charges.destroy', [$bill, $c]) }}" method="POST" onsubmit="return confirm('Remove charge?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-red">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($bill->charges->isEmpty())
                        <tr><td colspan="4">No charges yet.</td></tr>
                    @endif
                </tbody>
            </table>

            <!-- Add charge form -->
            <form action="{{ route('bills.charges.store', $bill) }}" method="POST" class="add-charge-form">
                @csrf
                <div class="form-grid">
                    <input type="text" name="name" placeholder="Charge name" required>
                    <input type="text" name="description" placeholder="Description (optional)">
                    <input type="number" name="amount" step="0.01" placeholder="Amount" required>
                    <button class="btn-confirm" type="submit">Add Charge</button>
                </div>
            </form>
        </div>

        <!-- Totals display with icons and centered -->
        <div class="totals-display">
            <div><i class="fas fa-house"></i> Base rent: ₱{{ number_format($bill->base_amount ?? 0,2) }}</div>
            <div><i class="fas fa-hand-holding-dollar"></i> Charges: ₱{{ number_format($bill->total_charges ?? 0,2) }}</div>
            <div><i class="fas fa-money-bill-wave"></i> Total due: ₱{{ number_format($bill->amount_due, 2) }}</div>

            <!-- Payments -->
            <a href="{{ route('payments.create', ['bill_id' => $bill->id]) }}" class="btn-pay">Make Payment</a>
        </div>

        <!-- Back Button -->
        <div class="action-container">
            <a href="{{ route('bills.index') }}" class="btn-back">Back</a>
        </div>

    </div>
</div>

<style>
/* Container wrapper */
.container{max-width:900px;margin:0 auto;padding:16px;}

/* Main card styling */
.card{background:linear-gradient(135deg,#FFFDFB,#FFF8F0);border-radius:16px;border:2px solid #E6A574;padding:20px;box-shadow:0 6px 16px rgba(0,0,0,0.12);}

/* Section card (Bill Info) */
.section-card{background:rgba(255,255,255,0.95);border-radius:12px;border:1px solid #E6A574;padding:16px;margin-bottom:16px;}

/* Section title */
.section-title{font-family:'Figtree',sans-serif;font-weight:800;font-size:17px;color:#D97A4E;margin-bottom:10px;display:flex;align-items:center;gap:6px;}

/* Grid for labels & values */
.detail-grid{display:grid;grid-template-columns:max-content 1fr;row-gap:6px;column-gap:12px;align-items:center;}
.label{font-weight:900;color:#5C3A21;display:flex;align-items:center;gap:6px;text-align:right;}
.value{font-weight:500;color:#3A2C1F;word-break:break-word;}

/* Charges card wrapper */
.charges-card{border-radius:16px;border:2px solid #E6A574;padding:16px;background:linear-gradient(135deg,#FFFDFB,#FFF8F0);margin-top:12px;box-shadow:0 6px 16px rgba(0,0,0,0.1);}

/* Charges title */
.charges-title{font-family:'Figtree',sans-serif;font-weight:800;font-size:16px;color:#D97A4E;margin-bottom:12px;display:flex;align-items:center;gap:6px;}

/* Charges table styling */
.charges-table{width:100%;border-collapse:separate;border-spacing:0;border-radius:12px;overflow:hidden;margin-bottom:12px;}
.charges-table th,.charges-table td{padding:10px 12px;border-bottom:1px solid #E6A574;border-right:1px solid #E6A574;text-align:left;}
.charges-table th:first-child,.charges-table td:first-child{border-left:none;}
.charges-table th:last-child,.charges-table td:last-child{border-right:none;}
.charges-table thead{background:linear-gradient(to right,#F4C38C,#E6A574);color:#5C3A21;font-weight:600;}
.charges-table tbody tr:hover{background:#FFF4E1;transition:0.2s;}

/* Add charge form */
.add-charge-form .form-grid{display:flex;flex-wrap:wrap;gap:8px;align-items:center;}
.add-charge-form input{padding:6px 10px;border-radius:6px;border:1px solid #E6A574;font-family:'Figtree',sans-serif;background:#FFFDFB;color:#000;width:auto;flex:1 1 150px;min-width:120px;}
.add-charge-form .btn-confirm{background:#E6A574;color:#5C3A21;padding:6px 14px;border-radius:6px;font-weight:600;cursor:pointer;border:none;transition:0.2s;}
.add-charge-form .btn-confirm:hover{background:#F4C38C;}

/* Delete button */
.btn-red{background:#b54b4b;color:#fff;padding:5px 12px;border-radius:6px;font-weight:600;border:none;cursor:pointer;transition:0.2s;}
.btn-red:hover{background:#d46a6a;}

/* Totals display with centered content */
.totals-display{margin-top:12px;font-weight:700;color:#3A2C1F;text-align:center;display:flex;flex-direction:column;gap:6px;}
.totals-display i{margin-right:6px;}

/* Action container */
.action-container{display:flex;justify-content:flex-end;margin-top:16px;}

/* Back button */
.btn-back{font-family:'Figtree',sans-serif;font-weight:600;border:none;cursor:pointer;transition:0.2s;background:#D97A4E;color:#FFF5EC;padding:6px 14px;border-radius:6px;text-decoration:none;display:flex;align-items:center;gap:6px;}
.btn-back:hover{background:#F4C38C;color:#5C3A21;}

/* Responsive */
@media(max-width:1024px){.detail-grid{grid-template-columns:1fr;}.label{text-align:left;}}
@media(max-width:768px){.section-title{font-size:15px;}.label,.value{font-size:14px;}.add-charge-form input{flex:1 1 100%;}}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</x-app-layout>
