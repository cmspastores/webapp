
<!-- Sidebar -->
<aside class="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ url('/') }}" class="sidebar-logo-link">
            <img src="{{ asset('images/c5logo.png') }}" alt="C5 Logo" class="sidebar-logo-img">
        </a>
    </div>

    <!-- Top Nav -->
    <div class="sidebar-top">
        <h2>Navigation</h2>
        <ul>
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}">Rooms</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">Customers</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('agreements.*') ? 'active' : '' }}">Agreement Registration</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('billing.*') ? 'active' : '' }}">Billing</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('charges.*') ? 'active' : '' }}">Charges</a></li>
        </ul>
    </div>

    <!-- Bottom Nav -->
    <div class="sidebar-bottom">
        @if(auth()->user() && auth()->user()->is_admin)
            <p class="sidebar-label">Admin Settings:</p>
            <ul>
                <li><a href="{{ route('settings.users') }}" class="{{ request()->routeIs('settings.users') ? 'active' : '' }}">User Management</a></li>
                <li><a href="{{ route('roomtypes.index') }}" class="{{ request()->routeIs('roomtypes.*') ? 'active' : '' }}">Room Type Management</a></li>
            </ul>
        @endif
    </div>

</aside>

<!-- Sidebar Styles -->
<style>


.sidebar{background:linear-gradient(180deg,#FFF5EC,#F7E1B5);border-right:2px solid #E6A574;min-height:100vh;display:flex;flex-direction:column;justify-content:space-between;width:16rem;color:#5C3A21;box-sizing:border-box;}
.sidebar-logo{display:flex;justify-content:center;align-items:center;padding:1rem 0;}
.sidebar-logo-link{display:inline-block;cursor:pointer;text-decoration:none;border:none;background:none;padding:0;margin:0;line-height:0;}
.sidebar-logo-link:focus,.sidebar-logo-link:active{outline:none;box-shadow:none;}
.sidebar-logo-img{display:block;width:10rem;height:auto;transition:transform .3s ease,box-shadow .3s ease;}
.sidebar-logo-img:hover { transform: scale(1.1); filter: drop-shadow(0 0 6px rgba(230,165,116,.7)); }


.sidebar-top,.sidebar-bottom{padding:0 1rem;}
.sidebar-top h2 { font: 1.25rem 'Figtree', sans-serif; font-weight: 900; color: #5C3A21; margin-bottom: .75rem; text-shadow: 0 0 1px #5C3A21, 0 0 1px #5C3A21; }

.sidebar ul{display:flex;flex-direction:column;gap:.5rem;padding:0;margin:0;list-style:none;}
.sidebar a:not(.sidebar-logo-link){display:flex;align-items:center;padding:.5rem 1rem;border-radius:.6rem;color:#5C3A21;text-decoration:none;transition:.2s;background:rgba(255,255,255,.35);font-weight:500;box-shadow:inset 0 1px 2px rgba(0,0,0,.05);}
.sidebar a:not(.sidebar-logo-link):hover{background:linear-gradient(to right,#F7E1B5,#F4C38C);transform:translateX(4px);box-shadow:0 2px 6px rgba(0,0,0,.15);}
.sidebar a:not(.sidebar-logo-link).active{background:linear-gradient(to right,#F4C38C,#E6A574);color:#3A2C1F;font-weight:600;box-shadow:0 2px 6px rgba(0,0,0,.2);}
.sidebar-label{padding:.5rem 0;color:#8B4A2C;font-weight:600;}
.sidebar-bottom{margin-top:auto;padding:1rem;}




</style>