<x-app-layout>
    <x-slot name="header">
        <!-- 🌐 Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer"/>

        <style>
            /* 🌴 Hide default Jetstream header */
            header { display:none; }

            /* 🌅 Body & Background */
            body { background:linear-gradient(180deg,#FFF3E0,#FFFDF8); color:#2C2C2C; font-family:'Figtree',sans-serif; margin:0; padding:0; position:relative; overflow-x:hidden; }
            body::before { content:''; position:absolute; top:0; left:50%; transform:translateX(-50%); width:420px; height:420px; background:url('/path-to-logo.svg') no-repeat center center; background-size:contain; opacity:0.08; pointer-events:none; z-index:0; }

            /* 🏠 Dashboard Container */
            .dashboard-container { padding:24px; max-width:1300px; margin:0 auto; display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:24px; z-index:1; position:relative; }

            /* 📦 Cards */
            .card { background:linear-gradient(145deg,#FFFDFB,#FFF8F0); border-radius:18px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.12); padding:28px 24px; transition:transform .2s ease, box-shadow .2s ease; text-align:center; position:relative; overflow:hidden; }
            .card:hover { transform:translateY(-5px); box-shadow:0 15px 35px rgba(0,0,0,0.16); }

            /* 🏖️ Welcome Section */
            .welcome-card { grid-column:1/-1; padding:50px 30px; background:linear-gradient(135deg,#FFE8C6,#FFF7EB); text-align:center; border:2px solid #E6A574; border-radius:20px; box-shadow:0 12px 30px rgba(0,0,0,0.15); }
            .welcome-card h2 { font-size:42px; font-weight:900; color:#5C3A21; margin-bottom:12px; text-shadow:2px 2px 6px rgba(0,0,0,0.25); }
            .welcome-card h3 { font-size:22px; font-weight:700; color:#5C3A21; margin-bottom:12px; }
            .welcome-card p { font-size:17px; color:#5C3A21; font-weight:500; }

            /* 📊 Section Titles */
            .cube-card h3 { font-size:22px; font-weight:800; color:#5C3A21; margin-bottom:16px; display:flex; align-items:center; justify-content:center; gap:10px; text-transform:uppercase; letter-spacing:0.5px; }

            /* ⚡ Quick Stats */
            .stats-list p { margin:14px 0; font-weight:600; font-size:17px; display:flex; align-items:center; justify-content:space-between; background:#FFF4E3; padding:10px 14px; border-radius:10px; border:1px solid #E6A574; box-shadow:inset 0 1px 3px rgba(0,0,0,0.05); }
            .stats-list p strong { color:#D97A4E; font-size:18px; }
            .stats-list i { color:#D97A4E; }

            /* 🧍 Recent Lists (no hover color now) */
            .recent-list ul { margin:0; padding:0; list-style:none; text-align:left; }
            .recent-list li { background:#FFF9F3; margin-bottom:8px; padding:10px 14px; border:1px solid #E6A574; border-radius:10px; font-weight:500; color:#5C3A21; display:flex; align-items:center; gap:10px; cursor:default; }
            .recent-list li::before { content:"\f0da"; font-family:"Font Awesome 6 Free"; font-weight:900; color:#D97A4E; }

            /* 🔹 Icon Highlights */
            .icon-bg { background:#FFF3E5; padding:10px; border-radius:12px; display:inline-flex; justify-content:center; align-items:center; box-shadow:inset 0 2px 4px rgba(0,0,0,0.05); }

            /* 📱 Responsive */
            @media(max-width:768px){
                .welcome-card h2 { font-size:32px; }
                .welcome-card h3 { font-size:18px; }
                .dashboard-container { grid-template-columns:1fr; }
            }

            /* === 📱 Responsive Enhancements for Maximized/Minimized Screens === */

/* Large screens: max-width >= 1300px, centered dashboard with 3-4 columns */
@media (min-width:1301px) {
    .dashboard-container { 
        grid-template-columns:repeat(4, 1fr);
        gap:32px; 
        padding:32px; 
    }
    .card { padding:32px 28px; }
    .welcome-card { padding:60px 40px; font-size:46px; }
}

/* Medium screens: 1025px - 1300px */
@media (min-width:1025px) and (max-width:1300px) {
    .dashboard-container {
        grid-template-columns:repeat(3, 1fr);
        gap:28px;
        padding:28px;
    }
    .welcome-card { padding:55px 35px; font-size:42px; }
}

/* Small screens already have 1 column at <=768px, add better padding for 769px - 1024px */
@media (min-width:769px) and (max-width:1024px) {
    .dashboard-container { 
        grid-template-columns:repeat(2, 1fr); 
        gap:24px; 
        padding:24px; 
    }
    .welcome-card { padding:50px 30px; font-size:38px; }
}

/* Extra small screens: <=480px */
@media (max-width:480px){
    .dashboard-container { grid-template-columns:1fr; padding:16px; gap:16px; }
    .welcome-card { padding:30px 20px; font-size:28px; }
    .welcome-card h3 { font-size:16px; }
    .card { padding:20px 16px; }
    .stats-list p { font-size:15px; }
}

/* Ensure icons inside cube-card scale nicely */
.cube-card h3 span.icon-bg { width:32px; height:32px; font-size:16px; }

/* Optional: prevent overflow horizontally on minimized screens */
body, .dashboard-container { overflow-x:hidden; }







        </style>
    </x-slot>

    @php
        $rentersCount = \App\Models\Renters::count();
        $roomsCount = \App\Models\Room::count();
        $recentRenters = \App\Models\Renters::latest()->take(5)->get();
        $recentRooms = \App\Models\Room::latest()->take(5)->get();
        $userLoginCount = \App\Models\LoginLog::where('user_id', auth()->id())->count();
        $loginWord = $userLoginCount === 1 ? 'time' : 'times';
    @endphp

    <div class="dashboard-container">

        <!-- 🌞 Welcome -->
        <div class="card welcome-card">
            <h2>Dashboard</h2>
            <h3>{{ $userLoginCount > 1 ? 'Welcome back' : 'Welcome' }}, {{ auth()->user()->name }}!</h3>
            <p>You’ve logged in <strong>{{ $userLoginCount }}</strong> {{ $loginWord }} in total. </p>
        </div>

        <!-- 📈 Quick Stats -->
        <div class="card cube-card stats-list">
            <h3><span class="icon-bg"><i class="fa-solid fa-chart-pie"></i></span> Quick Stats</h3>
            <p><span><i class="fa-solid fa-house"></i> Total Rooms</span> <strong>{{ $roomsCount }}</strong></p>
            <p><span><i class="fa-solid fa-users"></i> Total Renters</span> <strong>{{ $rentersCount }}</strong></p>
        </div>

        <!-- 🧾 Recent Renters -->
        <div class="card cube-card recent-list">
            <h3><span class="icon-bg"><i class="fa-solid fa-user"></i></span> Recent Renters</h3>
            <ul>
                @forelse($recentRenters as $renter)
                    <li><i class="fa-solid fa-id-badge"></i> {{ $renter->full_name }}@if(isset($renter->room)) — Room: {{ $renter->room->room_number }}@endif</li>
                @empty
                    <li><i class="fa-solid fa-circle-exclamation"></i> No renters found.</li>
                @endforelse
            </ul>
        </div>

        <!-- 🏘️ Recent Rooms -->
        <div class="card cube-card recent-list">
            <h3><span class="icon-bg"><i class="fa-solid fa-door-closed"></i></span> Recent Rooms</h3>
            <ul>
                @forelse($recentRooms as $room)
                    <li><i class="fa-solid fa-key"></i> Room {{ $room->room_number }}</li>
                @empty
                    <li><i class="fa-solid fa-circle-exclamation"></i> No rooms found.</li>
                @endforelse
            </ul>
        </div>

    </div>
</x-app-layout>
