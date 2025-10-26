<x-app-layout>
    <x-slot name="header">
        <!-- 🌐 Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <style>
            /* 🌅 Container & Background */
            body { background: linear-gradient(180deg,#FFF3E0,#FFFDF8); font-family:'Figtree',sans-serif; color:#2C2C2C; margin:0; padding:0; }
            body::before { content:''; position:absolute; top:0; left:50%; transform:translateX(-50%); width:400px; height:400px; background:url('/path-to-logo.svg') no-repeat center center; background-size:contain; opacity:0.08; pointer-events:none; }

            /* 📦 Cards */
            .card { background: linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); padding:20px; text-align:center; transition: transform .2s, box-shadow .2s; }
            .card:hover { transform: translateY(-4px); box-shadow:0 15px 30px rgba(0,0,0,0.18); }

            /* 📊 Dashboard Container */
            .dashboard-container { max-width:1200px; margin:0 auto; padding:20px; display:grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap:16px; }

            /* 📝 Headers */
            h2, h3 { color:#5C3A21; font-weight:900; margin-bottom:12px; }
            h3 { display:flex; align-items:center; gap:8px; justify-content:center; font-weight:800; }

            /* 📋 Summary & Stats */
            .summary p { font-size:16px; font-weight:600; margin:6px 0; display:flex; align-items:center; gap:8px; justify-content:center; }
            .summary i { color:#5C3A21; }

            table { width:100%; border-collapse:separate; border-spacing:0; border-radius:12px; overflow:hidden; margin-top:16px; }
            th, td { padding:12px 16px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; font-size:14px; }
            th:first-child, td:first-child { border-left:none; }
            th:last-child, td:last-child { border-right:none; }
            thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; }
            tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

            /*  Buttons */
            .btn-back { font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; background:#D97A4E; color:#FFF5EC; padding:6px 14px; border-radius:6px; text-decoration:none; display:flex; align-items:center; gap:6px; }
            .btn-back:hover { background:#F4C38C; color:#5C3A21; }

        </style>
    </x-slot>

    @php
        $totalRevenue = $totalRevenue ?? 0;
        $totalOutstanding = $totalOutstanding ?? 0;
        $chargesByType = $chargesByType ?? [];
    @endphp


    <!-- 🔙 Back Button -->
    <div class="action-container" style="margin-bottom:16px; justify-content:flex-start;">
         <a href="{{ route('bills.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

<div class="dashboard-container">
    <!-- Your Revenue Card & Charges Card go here -->


    <div class="dashboard-container">
        <!-- 🟢 Revenue Card -->
        <div class="card summary">
            <h3><i class="fa-solid fa-sack-dollar"></i> Sales Summary</h3>
            <p><i class="fa-solid fa-money-bill-wave"></i> Total Revenue: ₱{{ number_format($totalRevenue,2) }}</p>
            <p><i class="fa-solid fa-clock"></i> Total Outstanding: ₱{{ number_format($totalOutstanding,2) }}</p>
        </div>

        <!-- 📊 Charges Breakdown -->
        <div class="card">
            <h3><i class="fa-solid fa-list-check"></i> Charges by Type</h3>
            <table>
                <thead>
                    <tr>
                        <th>Charge</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chargesByType as $charge)
                        <tr>
                            <td>{{ $charge->name }}</td>
                            <td>₱{{ number_format($charge->total,2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No charges recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
