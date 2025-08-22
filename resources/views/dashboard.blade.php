<x-app-layout>
    <x-slot name="header">
        <style>
           
            body {
                background-color: #302810; 
                color: #f0f0f5;             
                font-family: 'Figtree', sans-serif;
                margin: 0;
                padding: 0;
            }

            .header-bar {
                background-color: #1d00c1; 
                color: #ffffff;
                text-align: center;
                font-size: 26px;
                font-weight: bold;
                padding: 24px 0;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
                border-bottom: 3px solid #4781ff;
            }

            .dashboard-container {
                padding: 40px 20px;
                max-width: 1200px;
                margin: 0 auto;
            }

            .card {
                background-color: #4781ff; 
                color: #ffffff;
                padding: 24px;
                border-radius: 14px;
                border: 3px solid #1d00c1;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
                font-size: 18px;
                margin-bottom: 36px;
            }

            .log-table-container {
                background-color: #dbeafe;
                color: #111827; 
                padding: 24px;
                border-radius: 12px;
                border: 2px solid #4781ff;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                font-size: 16px;
            }

        </style>

        <div class="header-bar">
            {{ __('Welcome To Your Dashboard! : D') }}
        </div>
    </x-slot>

    <div class="dashboard-container">

 
        <!-- Log Table -->
        <div class="log-table-container">
            @include('index')
        </div>

    </div>
</x-app-layout>
