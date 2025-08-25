<!-- Sidebar -->
<aside class="sidebar">
    <!-- Top Nav -->
    <div class="sidebar-top">
        <h2>Navigation</h2>
        <ul>
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('roomtypes.index') }}" class="{{ request()->routeIs('roomtypes.*') ? 'active' : '' }}">Room Types</a></li>
            <li><a href="{{ route('roomtypes.index') }}" class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}">Room</a></li>
            <li><a href="{{ route('roomtypes.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">Customers</a></li>
            <li><a href="{{ route('roomtypes.index') }}" class="{{ request()->routeIs('agreements.*') ? 'active' : '' }}">Agreement Reg</a></li>
            <li><a href="{{ route('roomtypes.index') }}" class="{{ request()->routeIs('billing.*') ? 'active' : '' }}">Billing</a></li>
            <li><a href="{{ route('roomtypes.index') }}" class="{{ request()->routeIs('charges.*') ? 'active' : '' }}">Charges</a></li>
        </ul>
    </div>

    <!-- Bottom Nav -->
    <div class="sidebar-bottom">
        @if(auth()->user() && auth()->user()->is_admin)
            <p class="sidebar-label">Admin Settings:</p>
            <ul>
                <li><a href="{{ route('settings.users') }}" class="{{ request()->routeIs('settings.users') ? 'active' : '' }}">User Management</a></li>
            </ul>
        @endif
    </div>
</aside>

<!-- Sidebar Styles -->
<style>
/* ============================
   Sidebar 🪴
   ============================ */
.sidebar { background:linear-gradient(180deg,#FFF5EC,#F7E1B5); border-right:2px solid #E6A574; min-height:100vh; display:flex; flex-direction:column; justify-content:space-between; width:16rem; color:#5C3A21; }
.sidebar-top,.sidebar-bottom { padding:1rem; }
.sidebar-top h2 { font-size:1.125rem; font-weight:700; color:#5C3A21; margin-bottom:1rem; }
.sidebar ul { display:flex; flex-direction:column; gap:0.5rem; }
.sidebar a { display:block; padding:0.5rem 1rem; border-radius:0.5rem; color:#5C3A21; text-decoration:none; transition:0.2s; background:transparent; }
.sidebar a:hover { background:rgba(230,165,116,0.25); }
.sidebar a.active { background:linear-gradient(to right,#F4C38C,#E6A574); color:#3A2C1F; font-weight:600; }
.sidebar-label { padding:0.5rem 1rem; color:#8B4A2C; font-weight:600; }


</style>

