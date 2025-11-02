<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- 🌐 External Dependencies --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
/* 🌅 Base & Container */
html, body { height:100%; margin:0; padding:0; font-family:'Figtree',sans-serif; color:#2C2C2C; background:#FFF8F2; }
body::before { content:''; position:absolute; top:0; left:50%; transform:translateX(-50%); width:400px; height:400px; background:url('/path-to-logo.svg') no-repeat center center; background-size:contain; opacity:0.08; pointer-events:none; }
header { display:none!important; }

.reports-container { max-width:950px; margin:0 auto; padding:24px; display:flex; flex-direction:column; align-items:center; text-align:center; }

/* 📦 Card */
.card { width:100%; background:#FFFDFB; border-radius:18px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); padding:28px; transition: transform .2s, box-shadow .2s; position:relative; }
.card:hover { transform:translateY(-4px); box-shadow:0 15px 30px rgba(0,0,0,0.18); }

/* 💰 Headings & Totals */
h3 { color:#5C3A21; font-weight:900; margin-bottom:14px; display:flex; align-items:center; gap:8px; justify-content:center; }
.total-unpaid { font-size:18px; font-weight:900; color:#5C3A21; margin-top:16px; margin-bottom:10px; display:flex; align-items:center; justify-content:center; gap:8px; background:#FFF3E5; padding:10px 16px; border-radius:8px; border:2px solid #E6A574; box-shadow:0 3px 10px rgba(0,0,0,0.08); }

/* 📋 Table Styling */
table { width:100%; border-collapse:separate; border-spacing:0; border-radius:12px; overflow:hidden; margin-top:16px; color:#5C3A21; }
th, td { padding:12px 16px; font-size:14px; border-bottom:1px solid #D97A4E; border-right:1px solid #D97A4E; color:#5C3A21; }
th:first-child, td:first-child { border-left:none; }
th:last-child, td:last-child { border-right:none; }
thead { background: linear-gradient(90deg,#E6A574,#F4C38C); color:#5C3A21; font-weight:700; }
tbody tr:hover { background:#FFF4E1; transition: background .2s; }

/* Table Scrollbar */
.table-wrapper { max-height:480px; overflow-x:auto; overflow-y:auto; scrollbar-width:thin; scrollbar-color:#E6A574 #FFF8F0; }
.table-wrapper::-webkit-scrollbar { width:8px; height:8px; }
.table-wrapper::-webkit-scrollbar-track { background:#FFF8F0; }
.table-wrapper::-webkit-scrollbar-thumb { background:#E6A574; border-radius:8px; }
.table-wrapper::-webkit-scrollbar-thumb:hover { background:#D97A4E; }

/* 📊 Chart Container */
.chart-container { position:relative; height:320px; width:100%; display:flex; justify-content:center; align-items:center; margin-top:24px; }

/* 🔹 Buttons */
/* 🔹 Buttons unified colors */
.btn-back{font-family:'Figtree',sans-serif;font-weight:600;border:none;cursor:pointer;transition:0.2s;background:#D97A4E;color:#FFF5EC;padding:6px 14px;border-radius:6px;text-decoration:none;display:flex;align-items:center;gap:6px;justify-content:center;box-shadow:0 4px 10px rgba(0,0,0,0.1);position:absolute;top:20px;right:20px;}
.btn-back:hover{background:#F4C38C;color:#5C3A21;}
.btn-refresh{font-family:'Figtree',sans-serif;font-weight:600;border:none;cursor:pointer;transition:0.2s;background:#D97A4E;color:#FFF5EC;padding:6px 14px;border-radius:6px;text-decoration:none;display:flex;align-items:center;gap:6px;justify-content:center;box-shadow:0 4px 10px rgba(0,0,0,0.1);position:absolute;top:20px;right:110px;}
.btn-refresh:hover{background:#F4C38C;color:#5C3A21;}
.filter-form button{font-family:'Figtree',sans-serif;font-weight:700;border:none;cursor:pointer;transition:0.2s;background:#D97A4E;color:#FFF5EC;padding:10px 18px;border-radius:6px;text-decoration:none;display:flex;align-items:center;gap:6px;justify-content:center;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
.filter-form button:hover{background:#F4C38C;color:#5C3A21;}




/* 🎨 Filter Form & Custom Dropdowns */
.filter-form { display:flex; justify-content:center; align-items:center; gap:16px; flex-wrap:wrap; margin:28px 0 30px; padding:12px 16px; background:#FFF9F3; border:1px solid #E6A574; border-radius:14px; color:#5C3A21; }
.filter-form label { font-weight:700; color:#5C3A21; font-size:15px; }
.filter-form select { font-family:'Figtree',sans-serif; font-size:15px; padding:10px 14px; border-radius:8px; border:2px solid #E6A574; background:#FFF9F3; color:#5C3A21; font-weight:500; min-width:120px; transition:.2s; }
.filter-form select:disabled { opacity:0.6; cursor:not-allowed; }

/* Custom Dropdown */
.custom-dropdown { position:relative; min-width:130px; }
.dropdown-selected { background:#FFF9F3; border:2px solid #E6A574; border-radius:8px; padding:10px 14px; cursor:pointer; font-weight:600; color:#5C3A21; transition:.2s; }
.dropdown-selected:hover { background:#FFE7CA; border-color:#D97A4E; }
.dropdown-options { display:none; position:absolute; z-index:10; top:110%; left:0; width:100%; max-height:150px; overflow-y:auto; background:#FFF; border:2px solid #E6A574; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); color:#5C3A21; scrollbar-width:thin; scrollbar-color:#E6A574 #FFF; }
.dropdown-option { padding:8px 12px; cursor:pointer; font-weight:500; color:#5C3A21; transition:.2s; }
.dropdown-option:hover, .dropdown-option.active { background:#E6A574; color:#FFF; }

/* Dropdown Scrollbar */
.dropdown-options::-webkit-scrollbar { width:8px; }
.dropdown-options::-webkit-scrollbar-track { background:#FFF9F3; border-radius:8px; }
.dropdown-options::-webkit-scrollbar-thumb { background:#E6A574; border-radius:8px; }
.dropdown-options::-webkit-scrollbar-thumb:hover { background:#D97A4E; }

/* ⚙️ Responsive */
@media(max-width:768px) {
    .chart-container { height:260px; }
    .btn-back { top:14px; right:14px; font-size:13px; padding:7px 12px; }
    .btn-refresh { top:14px; right:100px; font-size:13px; padding:7px 12px; }
    .filter-form { gap:10px; flex-direction:column; }
    .custom-dropdown, .filter-form button, .filter-form select { width:100%; }
}




    </style>

    @php
        $transientOutstanding = $transientOutstanding ?? 0;
        $monthlyOutstanding = $monthlyOutstanding ?? 0;
        $totalOutstandingCombined = $totalOutstandingCombined ?? 0;
        $periodType = $periodType ?? 'monthly';
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;
    @endphp

    <div class="reports-container">
        <div class="card">
            <a href="{{ route('bills.index') }}" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back</a>
            <a href="{{ route('bills.reports') }}" class="btn-refresh"><i class="fa-solid fa-rotate-right"></i> Refresh</a>
            <h3><i class="fa-solid fa-chart-line"></i> Unpaid Bills Report</h3>

            <form method="GET" action="{{ route('bills.reports') }}" class="filter-form">
                {{-- View Dropdown --}}
                <label>View:</label>
                <div class="custom-dropdown" id="viewDropdown">
                    <div class="dropdown-selected">{{ ucfirst($periodType) }}</div>
                    <div class="dropdown-options">
                        <div class="dropdown-option {{ $periodType === 'monthly' ? 'active' : '' }}" data-value="monthly">Monthly</div>
                        <div class="dropdown-option {{ $periodType === 'annual' ? 'active' : '' }}" data-value="annual">Annual</div>
                    </div>
                    <input type="hidden" name="period_type" value="{{ $periodType }}">
                </div>

                {{-- Month Dropdown --}}
                <label>Month:</label>
                <div class="custom-dropdown" id="monthDropdown">
                    <div class="dropdown-selected">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</div>
                    <div class="dropdown-options">
                        @for($m = 1; $m <= 12; $m++)
                            <div class="dropdown-option {{ $month == $m ? 'active' : '' }}" data-value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</div>
                        @endfor
                    </div>
                    <input type="hidden" name="month" value="{{ $month }}">
                </div>

                {{-- Year Dropdown --}}
                <label>Year:</label>
                <div class="custom-dropdown" id="yearDropdown">
                    <div class="dropdown-selected">{{ $year }}</div>
                    <div class="dropdown-options">
                        @for($y = 2035; $y >= 2015; $y--)
                            <div class="dropdown-option {{ $year == $y ? 'active' : '' }}" data-value="{{ $y }}">{{ $y }}</div>
                        @endfor
                    </div>
                    <input type="hidden" name="year" value="{{ $year }}">
                </div>

                <button type="submit"><i class="fa-solid fa-filter"></i> Apply</button>
            </form>

            <p class="total-unpaid"><i class="fa-solid fa-money-bill-wave"></i> Total Unpaid: ₱{{ number_format($totalOutstandingCombined,2) }}</p>
            <h3 style="margin-top:24px;"><i class="fa-solid fa-list-check"></i> Outstanding Balance Breakdown</h3>
            <table>
                <thead><tr><th>Category</th><th>Amount (₱)</th><th>Percentage</th></tr></thead>
                <tbody>
                    @php
                        $transientPercent = $totalOutstandingCombined > 0 ? round(($transientOutstanding / $totalOutstandingCombined) * 100, 2) : 0;
                        $monthlyPercent = $totalOutstandingCombined > 0 ? round(($monthlyOutstanding / $totalOutstandingCombined) * 100, 2) : 0;
                    @endphp
                    <tr><td>Transient/Daily</td><td>₱{{ number_format($transientOutstanding,2) }}</td><td>{{ $transientPercent }}%</td></tr>
                    <tr><td>Dorm/Monthly</td><td>₱{{ number_format($monthlyOutstanding,2) }}</td><td>{{ $monthlyPercent }}%</td></tr>
                </tbody>
            </table>

            <div class="chart-container" id="salesChartContainer" data-labels="Transient/Daily,Dorm/Monthly" data-values="{{ $transientOutstanding }},{{ $monthlyOutstanding }}">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Chart Initialization
        const container=document.getElementById('salesChartContainer');
        const labels=container.dataset.labels.split(',');
        const values=container.dataset.values.split(',').map(Number);
        if(labels.length&&values.length){
            new Chart(document.getElementById('salesChart'),{
                type:'pie',
                data:{labels:labels,datasets:[{data:values,backgroundColor:['#E6A574','#F4C38C'],borderColor:'#FFF8F0',borderWidth:2}]},
                options:{responsive:true,plugins:{legend:{position:'bottom',labels:{color:'#5C3A21',font:{family:'Figtree'}}},tooltip:{callbacks:{label:ctx=>`${ctx.label}: ₱${ctx.formattedValue}`}}}}
            });
        }

        // Custom Dropdown Logic
        document.querySelectorAll('.custom-dropdown').forEach(dropdown=>{
            const selected=dropdown.querySelector('.dropdown-selected');
            const options=dropdown.querySelector('.dropdown-options');
            const input=dropdown.querySelector('input[type="hidden"]');
            selected.addEventListener('click',()=>options.style.display=options.style.display==='block'?'none':'block');
            options.querySelectorAll('.dropdown-option').forEach(option=>{
                option.addEventListener('click',()=>{
                    selected.textContent=option.textContent;
                    input.value=option.dataset.value;
                    options.querySelectorAll('.dropdown-option').forEach(o=>o.classList.remove('active'));
                    option.classList.add('active');
                    options.style.display='none';
                });
            });
            document.addEventListener('click',e=>{if(!dropdown.contains(e.target))options.style.display='none';});
        });
    </script>
</x-app-layout>
