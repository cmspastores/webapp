<x-app-layout>
    <x-slot name="header">
        <!-- 🌐 Load Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-papXq4YQd3z+V9K2xHDQ2FVYQeFQYszgiZhn4QF6iXqFlZTf7/IJ0rkIF0WQfgfp+Xv6q4B7XOmF8N0S9vXMQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <style>
            /* 🌴 Hide default Jetstream header */
            header { display: none; }

            /* 🌅 Body & Background */
            body { background: linear-gradient(180deg,#FFF3E0,#FFFDF8); color:#2C2C2C; font-family:'Figtree',sans-serif; margin:0; padding:0; position:relative; }
            body::before { content:''; position:absolute; top:0; left:50%; transform:translateX(-50%); width:400px; height:400px; background:url('/path-to-logo.svg') no-repeat center center; background-size:contain; opacity:0.08; pointer-events:none; z-index:0; }

            /* 🏠 Dashboard Container */
            .dashboard-container { padding:16px; max-width:1200px; margin:0 auto; display:grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap:16px; z-index:1; }

            /* 📦 Cube-style Cards */
            .card { background: linear-gradient(135deg,#FFFDFB,#FFF8F0); border-radius:16px; border:2px solid #E6A574; box-shadow:0 10px 25px rgba(0,0,0,0.15); padding:24px 20px; transition: transform .2s ease, box-shadow .2s ease; text-align:center; position:relative; overflow:hidden; }
            .card:hover { transform: translateY(-4px); box-shadow:0 15px 30px rgba(0,0,0,0.18); }

            /* 🎯 Welcome + Dashboard Title */
            .welcome-card { grid-column: 1/-1; padding:40px 20px; }
            .welcome-card h2 { font-size:36px; font-weight:900; color:#5C3A21; margin-bottom:12px; text-shadow:2px 2px 6px rgba(0,0,0,0.2); }
            .welcome-card h3 { font-size:20px; font-weight:800; color:#5C3A21; margin-bottom:8px; }

            /* 📊 Stats & Lists Titles same as welcome h3 */
            .cube-card h3 { font-size:20px; font-weight:800; color:#5C3A21; margin-bottom:12px; display:flex; align-items:center; gap:8px; justify-content:center; }

            /* 📊 Stats & Lists Content */
            .stats-list p { margin:8px 0; font-weight:500; text-align:left; display:flex; align-items:center; gap:8px; font-size:16px; }
            .recent-list ul { margin:0; padding-left:20px; text-align:left; list-style:none; }
            .recent-list li { margin-bottom:4px; display:flex; align-items:center; gap:6px; }
            .recent-list li::before { content:"\f0da"; font-family:"Font Awesome 6 Free"; font-weight:900; color:#5C3A21; }

            /* 🔹 Cube layout for stats and recent cards */
            .cube-card { display:flex; flex-direction:column; justify-content:flex-start; height:100%; }
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

        <!-- 🟢 Welcome Card -->
        <div class="card welcome-card">
            <h2>Dashboard</h2>
            <h3>
                {{ $userLoginCount > 1 ? 'Welcome back' : 'Welcome' }}, {{ auth()->user()->name }}!
            </h3>
            <p>You have logged in to this site a total of <strong>{{ $userLoginCount }}</strong> {{ $loginWord }}.</p>
        </div>

        <!-- 📊 Quick Stats Cube -->
        <div class="card cube-card stats-list">
            <h3><i class="fa-solid fa-chart-pie"></i> Quick Stats</h3>
            <p><i class="fa-solid fa-house" style="color:#5C3A21;"></i> Total Rooms: <strong>{{ $roomsCount }}</strong></p>
            <p><i class="fa-solid fa-users" style="color:#5C3A21;"></i> Total Renters: <strong>{{ $rentersCount }}</strong></p>
        </div>

        <!-- 📝 Recent Renters Cube -->
        <div class="card cube-card recent-list">
            <h3><i class="fa-solid fa-user"></i> Recent Renters</h3>
            <ul>
                @forelse($recentRenters as $renter)
                    <li>{{ $renter->full_name }}@if(isset($renter->room)) - Room: {{ $renter->room->room_number }}@endif</li>
                @empty
                    <li>No renters found.</li>
                @endforelse
            </ul>
        </div>

        <!-- 🏘️ Recent Rooms Cube -->
        <div class="card cube-card recent-list">
            <h3><i class="fa-solid fa-door-closed"></i> Recent Rooms</h3>
            <ul>
                @forelse($recentRooms as $room)
                    <li>Room {{ $room->room_number }}</li>
                @empty
                    <li>No rooms found.</li>
                @endforelse
            </ul>
        </div>

    </div>
</x-app-layout>
