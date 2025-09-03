<x-app-layout>
    <x-slot name="header">
        <style>
            body{background:linear-gradient(180deg,#FFF3E0,#FFFDF8);color:#2C2C2C;font-family:'Figtree',sans-serif;margin:0;padding:0;position:relative;}
            body::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:400px;height:400px;background:url('/path-to-logo.svg') no-repeat center center;background-size:contain;opacity:0.08;pointer-events:none;z-index:0;}
            .dashboard-title{font-weight:800;font-size:26px;color:#5C3A21;line-height:1.3;margin-bottom:24px;text-align:center;text-shadow:1px 1px 2px rgba(0,0,0,0.15);}
            .dashboard-container{padding:16px 16px; max-width:1200px; margin:0 auto; position:relative; z-index:1;}
            
            .card{background:linear-gradient(135deg,#FFFDFB,#FFF9F5);color:#2C2C2C;padding:20px;border-radius:14px;border:2px solid #E6A574;box-shadow:0 8px 20px rgba(0,0,0,0.12);font-size:16px;margin-bottom:32px;}
            .log-table-container{background:#FFF9F5;color:#2C2C2C;padding:20px;border-radius:12px;border:2px solid #E6A574;box-shadow:0 6px 20px rgba(0,0,0,0.12);font-size:16px;}
        </style>

        <h2 class="dashboard-title">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="dashboard-container">
        <!-- Logs Table -->
        <div class="log-table-container">
            @include('index')
        </div>
    </div>
</x-app-layout>
