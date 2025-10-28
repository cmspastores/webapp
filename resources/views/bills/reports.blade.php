<x-app-layout>
    {{-- Completely suppress the default x-app-layout header and h2 --}}
    <x-slot name="header"></x-slot>

    {{-- External dependencies --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* 🌅 Base styling */
        html, body { height:100%; overflow:hidden; margin:0; padding:0; font-family:'Figtree',sans-serif; color:#2C2C2C; background:linear-gradient(180deg,#FFF3E0,#FFFDF8); }
        body::before { content:''; position:absolute; top:0; left:50%; transform:translateX(-50%); width:400px; height:400px; background:url('/path-to-logo.svg') no-repeat center center; background-size:contain; opacity:0.08; pointer-events:none; }

        /* ⚡ Hide any remaining x-app-layout header wrapper */
        header { display:none !important; }

        /* 📦 Container */
        .reports-container { max-width:900px; margin:0 auto; padding:24px; display:flex; flex-direction:column; align-items:center; text-align:center; }

        /* 🃏 Card styling */
        .card { width:100%; background:linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:18px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); padding:28px; transition:transform .2s, box-shadow .2s; position:relative; }
        .card:hover { transform:translateY(-4px); box-shadow:0 15px 30px rgba(0,0,0,0.18); }

        /* 🔤 Headers */
        h3 { color:#5C3A21; font-weight:900; margin-bottom:14px; display:flex; align-items:center; gap:8px; justify-content:center; }

        /* 📋 Tables */
        table { width:100%; border-collapse:separate; border-spacing:0; border-radius:12px; overflow:hidden; margin-top:16px; }
        th, td { padding:12px 16px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; font-size:14px; }
        th:first-child, td:first-child { border-left:none; }
        th:last-child, td:last-child { border-right:none; }
        thead { background:linear-gradient(to right,#F4C38C,#E6A574); color:#5C3A21; }
        tbody tr:hover { background:#FFF4E1; transition:background 0.2s; }

        /* 📊 Chart container */
        .chart-container { position:relative; height:320px; width:100%; display:flex; justify-content:center; align-items:center; margin-top:24px; }

        /* ⬅️ Back button */
        .btn-back { position:absolute; top:20px; right:20px; font-family:'Figtree',sans-serif; font-weight:600; border:none; cursor:pointer; transition:0.2s; background:#D97A4E; color:#FFF5EC; padding:8px 14px; border-radius:8px; text-decoration:none; display:flex; align-items:center; gap:6px; font-size:14px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
        .btn-back:hover { background:#F4C38C; color:#5C3A21; }

        /* 📱 Responsive adjustments */
        @media(max-width:768px) { .chart-container { height:260px; } .btn-back { top:14px; right:14px; font-size:13px; padding:7px 12px; } }
    </style>

    @php
        $transientSales = $transientSales ?? 0;
        $monthlySales = $monthlySales ?? 0;
        $totalSales = $totalSales ?? 0;
        $totalRevenue = $totalRevenue ?? 0;
        $totalOutstanding = $totalOutstanding ?? 0;
    @endphp

    <div class="reports-container">
        <div class="card">
            {{-- Back button --}}
            <a href="{{ route('bills.index') }}" class="btn-back"></i> Back</a>

            {{-- Reports header --}}
            <h3><i class="fa-solid fa-chart-line"></i> Sales Report Summary</h3>

            {{-- Summary values --}}
            <p><i class="fa-solid fa-money-bill-wave"></i> Total Revenue: ₱{{ number_format($totalRevenue,2) }}</p>
            <p><i class="fa-solid fa-clock"></i> Total Outstanding: ₱{{ number_format($totalOutstanding,2) }}</p>

            {{-- Sales by Room Type Table --}}
            <h3 style="margin-top:24px;"><i class="fa-solid fa-list-check"></i> Sales by Room Type</h3>
            <table>
                <thead>
                    <tr>
                        <th>Room Type</th>
                        <th>Amount (₱)</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $transientPercent = $totalSales > 0 ? round(($transientSales/$totalSales)*100,2) : 0;
                        $monthlyPercent = $totalSales > 0 ? round(($monthlySales/$totalSales)*100,2) : 0;
                    @endphp
                    <tr>
                        <td>Transient</td>
                        <td>₱{{ number_format($transientSales,2) }}</td>
                        <td>{{ $transientPercent }}%</td>
                    </tr>
                    <tr>
                        <td>Monthly</td>
                        <td>₱{{ number_format($monthlySales,2) }}</td>
                        <td>{{ $monthlyPercent }}%</td>
                    </tr>
                </tbody>
            </table>

            {{-- Pie chart --}}
            <div class="chart-container" id="salesChartContainer"
                 data-labels="Transient,Monthly"
                 data-values="{{ $transientSales }},{{ $monthlySales }}">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('salesChartContainer');
        const labels = container.dataset.labels.split(',');
        const values = container.dataset.values.split(',').map(Number);

        if(labels.length && values.length){
            new Chart(document.getElementById('salesChart'), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: ['#E6A574','#F4C38C'],
                        borderColor: '#FFF8F0',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive:true,
                    plugins:{
                        legend: { position:'bottom', labels:{ color:'#5C3A21', font:{ family:'Figtree' } } },
                        tooltip: { callbacks: { label: ctx => `${ctx.label}: ₱${ctx.formattedValue}` } }
                    }
                }
            });
        }
    </script>
</x-app-layout>
