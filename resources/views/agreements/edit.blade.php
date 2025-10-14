<x-app-layout>
    <x-slot name="header">
        <div class="header-container">
            <h2 class="header-title">View Agreement</h2>
            <div class="header-buttons">
                <a href="{{ route('agreements.index') }}" class="btn-back">← Back to List</a>
            </div>
        </div>
    </x-slot>

    <div class="container">
        <div class="card form-card">
            <!-- The form stays, but we make fields readonly -->
            <form action="#" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div>
                        <label for="renter_id">Renter</label>
                        <select name="renter_id" id="renter_id" disabled> <!-- {{ (auth()->user() && auth()->user()->is_admin) ? '' : 'disabled' }}> -->
                            <option value="">-- Select Renter --</option>
                            @foreach($renters as $r)
                                <option value="{{ $r->renter_id }}" {{ old('renter_id', $agreement->renter_id) == $r->renter_id ? 'selected' : '' }}>
                                    {{ $r->full_name }} ({{ $r->unique_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('renter_id')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="room_id">Room</label>
                        <select name="room_id" id="room_id" disabled> <!-- {{ (auth()->user() && auth()->user()->is_admin) ? '' : 'disabled' }}> -->
                            <option value="">-- Select Room --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id', $agreement->room_id) == $room->id ? 'selected' : '' }}>
                                    {{ $room->room_number }} {{ $room->roomType->name ? ' - ' . $room->roomType->name : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="agreement_date">Agreement Date</label>
                        <input type="date" name="agreement_date" id="agreement_date" value="{{ old('agreement_date', $agreement->agreement_date?->toDateString()) }}" disabled> <!-- {{ (auth()->user() && auth()->user()->is_admin) ? '' : 'disabled' }}> -->
                        @error('agreement_date')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $agreement->start_date?->toDateString()) }}" disabled> <!-- {{ (auth()->user() && auth()->user()->is_admin) ? '' : 'disabled' }}> -->
                        @error('start_date')<div class="error">{{ $message }}</div>@enderror
                    </div>

                    <div class="full-width">
                        <label for="end_date_preview">End Date (auto)</label>
                        <input type="text" id="end_date_preview" readonly value="{{ old('start_date') ? \Illuminate\Support\Carbon::parse(old('start_date'))->addYear()->toDateString() : $agreement->end_date?->toDateString() }}">
                    </div>

                    <div>
                        <label for="monthly_rent">Monthly Rent</label>
                        <input type="number" name="monthly_rent" id="monthly_rent" step="0.01" value="{{ old('monthly_rent', $agreement->monthly_rent) }}" disabled> <!-- {{ (auth()->user() && auth()->user()->is_admin) ? '' : 'disabled' }}> -->
                        @error('monthly_rent')<div class="error">{{ $message }}</div>@enderror
                    </div>
                    
                    <div>
                        <label for="locked_price">Locked Room Price</label>
                        <input type="text" id="locked_price" value="₱{{ number_format($agreement->monthly_rent, 2) }}" readonly>
                    </div>

                    <div>
                        <label for="is_active">Active</label>
                        <select name="is_active" id="is_active" disabled> <!-- {{ (auth()->user() && auth()->user()->is_admin) ? '' : 'disabled' }}> -->
                            <option value="1" {{ old('is_active', $agreement->is_active) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !old('is_active', $agreement->is_active) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </form>
            
            <!-- 🔹 Statement of Account Section -->
            <div style="margin-top:12px; display:flex; gap:8px;">
                
                @if(isset($bills) && $bills->count())
                    <div class="statement-card" style="margin-top: 24px;">
                        <h3 class="statement-title">Statement of Account</h3>

                        <table class="soa-table">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th>Due Date</th>
                                    <th>Amount Due</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bills as $bill)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($bill->period_start)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($bill->period_end)->format('M d, Y') }}</td>
                                        <td>{{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('M d, Y') : '—' }}</td>
                                        <td>₱{{ number_format($bill->amount_due, 2) }}</td>
                                        <td>₱{{ number_format($bill->balance, 2) }}</td>
                                        <td>
                                            @if($bill->status === 'unpaid')
                                                <span class="status-unpaid">Unpaid</span>
                                            @else
                                                <span class="status-paid">Paid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('bills.show', $bill) }}" class="btn-view-bill">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right" style="font-weight:600;">Totals:</td>
                                    <td>₱{{ number_format($totalDue ?? 0, 2) }}</td>
                                    <td>₱{{ number_format($totalBalance ?? 0, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p style="margin-top:24px; color:#6B6B6B;">No bills have been generated for this agreement yet.</p>
                @endif
            </div>

            <!-- Buttons outside the main form -->
            <div style="margin-top:12px; display:flex; gap:8px;">

                <a href="{{ route('agreements.index') }}" class="btn-back">Back</a>

                @if(auth()->user() && auth()->user()->is_admin)
                    @if($agreement->is_active)
                        <!-- Renewal -->
                        <form action="{{ route('agreements.renew', $agreement) }}" method="POST" onsubmit="return confirm('Renew this agreement for another year?')">
                            @csrf
                            <button type="submit" class="btn-confirm">Renew Agreement</button>
                        </form>

                        <!-- Termination -->
                        <form action="{{ route('agreements.terminate', $agreement) }}" method="POST" onsubmit="return confirm('Are you sure you want to terminate this agreement?')">
                            @csrf
                            <button type="submit" class="btn-cancel">Terminate Agreement</button>
                        </form>
                    @else
                        <p style="color:#b54b4b;font-weight:600;">This agreement has been terminated.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <script>
        function computeEndDate() {
            const start = document.getElementById('start_date').value;
            if (!start) return;
            const d = new Date(start);
            d.setFullYear(d.getFullYear() + 1);
            const yyyy = d.getFullYear();
            const mm = (d.getMonth() + 1).toString().padStart(2, '0');
            const dd = d.getDate().toString().padStart(2, '0');
            document.getElementById('end_date_preview').value = `${yyyy}-${mm}-${dd}`;
        }
        document.addEventListener('DOMContentLoaded', computeEndDate);
        document.getElementById('start_date')?.addEventListener('change', computeEndDate);
    </script>
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

    /* 🔹 Statement of Account Table */
    .statement-card { background: linear-gradient(135deg, #FFFDFB, #FFF8F0); border-radius: 14px; border: 2px solid #E6A574; padding: 16px; margin-top: 24px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .statement-title { font-size: 20px; font-weight: 700; color: #5C3A21; margin-bottom: 12px; }
    .soa-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .soa-table th, .soa-table td { padding: 8px 10px; border-bottom: 1px solid #E6A574; text-align: left; }
    .soa-table th { background: #FFF3DF; color: #5C3A21; font-weight: 600; }
    .status-unpaid { color: #b54b4b; font-weight: 600; }
    .status-paid { color: #198754; font-weight: 600; }
    .btn-view-bill { background: #D97A4E; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; }
    .btn-view-bill:hover { background: #F4C38C; color: #5C3A21; }


    /* Buttons */
    .btn-confirm { background:#E6A574; color:#5C3A21; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-confirm:hover { background:#F4C38C; }
    .btn-back { background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s; }
    .btn-back:hover { background:#F4C38C; color:#5C3A21; }
    .btn-cancel { background:#b54b4b; color:#fff; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; border:none; transition:0.2s;}
    .btn-cancel:hover { background:#d46a6a; }

    /* File Inputs */
    .file-input { border:none; background:#FFF3DF; color:#5C4A32; font-weight:500; padding:6px 8px; border-radius:6px; cursor:pointer; }

    /* Error Messages */
    .error { color:#e07b7b; font-size:12px; margin-top:4px; }
</style>