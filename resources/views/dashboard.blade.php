<x-app-layout>
    <x-slot name="header">
        <style>
            body{background:linear-gradient(180deg,#FFF3E0,#FFFDF8);color:#2C2C2C;font-family:'Figtree',sans-serif;margin:0;padding:0;position:relative;}
            body::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:400px;height:400px;background:url('/path-to-logo.svg') no-repeat center center;background-size:contain;opacity:0.08;pointer-events:none;z-index:0;}
            
            .dashboard-title{font:900 32px 'Figtree',sans-serif;color:#5C3A21;line-height:1.2;text-align:center;text-shadow:2px 2px 6px rgba(0,0,0,0.25);letter-spacing:1.2px;text-transform:uppercase;margin-bottom:16px;position:relative;-webkit-text-stroke:0.5px #5C3A21}


            .dashboard-container{padding:16px;max-width:1200px;margin:0 auto;position:relative;z-index:1}

            .dashboard-container{padding:16px;max-width:1200px;margin:0 auto;position:relative;z-index:1;}
            
            .log-table-container{background:linear-gradient(135deg,#FFFDFB,#FFF8F0);border-radius:16px;border:2px solid #E6A574;box-shadow:0 10px 25px rgba(0,0,0,0.15);padding:20px;margin-bottom:20px;transition:transform .2s ease,box-shadow .2s ease;}
            
            .card{background:linear-gradient(135deg,#FFFDFB,#FFF8F0);border-radius:16px;border:2px solid #E6A574;box-shadow:0 10px 25px rgba(0,0,0,0.15);padding:24px 20px;transition:transform .2s ease,box-shadow .2s ease;}
            .card:hover,.log-table-container:hover{transform:translateY(-4px);box-shadow:0 15px 30px rgba(0,0,0,0.18);}

        </style>

        <h2 class="dashboard-title">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="dashboard-container">
        <div class="log-table-container">
            @include('logstable')
        </div>

        <div class="card">
            <h3 style="font-weight:800;font-size:18px;margin-bottom:8px;">Welcome back!</h3>
            <p style="margin:0;color:#5C3A21;">You have logged in to this site a total of <strong>{{ $logs->total() }}</strong> Times.</p>
        </div>
    </div>
</x-app-layout>
