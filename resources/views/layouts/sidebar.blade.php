<!-- Sidebar -->
<aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ url('/') }}" class="sidebar-logo-link">
            <img src="{{ asset('images/c5logo.png') }}" alt="C5 Logo" class="sidebar-logo-img full-logo">
            <img src="{{ asset('images/c5logo-small.png') }}" alt="C5 Logo Small" class="sidebar-logo-img small-logo">
        </a>
    </div>

    <!-- Top Nav -->
    <div class="sidebar-top">
        <h2>Navigation</h2>
        <ul>
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}">Rooms</a></li>
            <li><a href="{{ route('renters.index') }}" class="{{ request()->routeIs('renters.*') ? 'active' : '' }}">Renters</a></li>
            <li><a href="{{ route('agreements.index') }}" class="{{ request()->routeIs('agreements.*') ? 'active' : '' }}">Agreement Registration</a></li>
            <li><a href="{{ route('reservation.index') }}" class="{{ request()->routeIs('reservation.*') ? 'active' : '' }}">Reservations</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('billing.*') ? 'active' : '' }}">Billing</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('charges.*') ? 'active' : '' }}">Charges</a></li>
        </ul>
    </div>

    <!-- Collapse Toggle BUTTON BELOW NAV -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i id="sidebarIcon" class="fas fa-angle-left"></i>
    </button>

    <!-- Bottom Nav -->
    <div class="sidebar-bottom">
        @if(auth()->user() && auth()->user()->is_admin)
            <p class="sidebar-label">Admin Settings</p>
            <ul>
                <li><a href="{{ route('settings.users') }}" class="{{ request()->routeIs('settings.users') ? 'active' : '' }}">User Management</a></li>
                <li><a href="{{ route('roomtypes.index') }}" class="{{ request()->routeIs('roomtypes.*') ? 'active' : '' }}">Room Type Management</a></li>
            </ul>
        @endif
    </div>

</aside>


<style>
/* Sidebar Styles */
.sidebar { background: linear-gradient(180deg,#FFF5EC,#F7E1B5); border-right: 2px solid #E6A574; min-height: 100vh; display: flex; flex-direction: column; justify-content: flex-start; width: 16rem; color: #5C3A21; box-sizing: border-box; transition: width .3s ease, backdrop-filter .3s ease; position: relative; overflow: visible; }

/* Logo */
.sidebar-logo { display: flex; justify-content: center; align-items: center; padding: 1rem 0; }
.sidebar-logo-link { display: inline-block; cursor: pointer; text-decoration: none; border: none; background: none; padding: 0; margin: 0; line-height: 0; }
.sidebar-logo-link:focus { outline: none; box-shadow: none; }
.sidebar-logo-link:active { outline: none; box-shadow: none; }
.sidebar-logo-img { display: block; height: auto; transition: transform .3s ease, box-shadow .3s ease, opacity .3s ease; }
.full-logo { width: 10rem; }
.small-logo { width: 2.5rem; display: none; }
.sidebar-logo-img:hover { transform: scale(1.1); filter: drop-shadow(0 0 6px rgba(230,165,116,.7)); }

/* Sidebar Top & Bottom */
.sidebar-top { padding: 0 1rem; margin-bottom: 1rem; }
.sidebar-bottom { padding: 0 1rem; margin-top: auto; margin-bottom: 1.5rem; }

/* Top and Bottom Titles */
.sidebar-top h2, .sidebar-label { font:1.25rem 'Figtree',sans-serif; font-weight:900; color:#5C3A21; margin-bottom:.75rem; text-shadow:0 0 1px #5C3A21,0 0 1px #5C3A21; text-align:center; padding:.5rem 0; }

/* Navigation Links */
.sidebar ul { display: flex; flex-direction: column; gap: .5rem; padding: 0; margin: 0; list-style: none; }
.sidebar a:not(.sidebar-logo-link) { display: flex; align-items: center; padding: .5rem 1rem; border-radius: .6rem; color: #5C3A21; text-decoration: none; transition: .2s; background: rgba(255,255,255,.35); font-weight: 500; box-shadow: inset 0 1px 2px rgba(0,0,0,.05); }
.sidebar a:not(.sidebar-logo-link):hover { background: linear-gradient(to right,#F7E1B5,#F4C38C); transform: translateX(4px); box-shadow: 0 2px 6px rgba(0,0,0,.15); }
.sidebar a:not(.sidebar-logo-link).active { background: linear-gradient(to right,#F4C38C,#E6A574); color: #3A2C1F; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,.2); }

/* Sidebar Toggle */
.sidebar-toggle { margin: 0.75rem 1rem; background: #D98348; color: #FFF; border: none; border-radius: 6px; padding: .5rem; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,.2); transition: .2s; width: calc(100% - 2rem); }
.sidebar-toggle:hover { background: #C46A32; }
.sidebar-toggle i { font-size: 1.2rem; font-weight: bold; }

/* Collapsed Mode (Manual) */
.sidebar.collapsed { width: 4rem; backdrop-filter: blur(6px); }
.sidebar.collapsed .full-logo { display: none; }
.sidebar.collapsed .small-logo { display: block; }
.sidebar.collapsed .sidebar-top h2, .sidebar.collapsed .sidebar-label, .sidebar.collapsed ul li a { display: none; }
.sidebar.collapsed .sidebar-top, .sidebar.collapsed .sidebar-bottom { padding: 0 .25rem; }
.sidebar.collapsed .sidebar-toggle { width: 80%; margin: .5rem auto; display: block; }

/* Auto-collapse on small screens  */
@media (max-width: 1024px) {
    .sidebar:not(.collapsed) { width: 16rem; }
    .sidebar.auto-collapsed { width: 4rem; }
    .sidebar.auto-collapsed .full-logo { display: none !important; }
    .sidebar.auto-collapsed .small-logo { display: block !important; }
    .sidebar.auto-collapsed .sidebar-top h2, .sidebar.auto-collapsed .sidebar-label, .sidebar.auto-collapsed ul li a { display: none !important; }
    .sidebar-toggle { width: 80%; margin: .5rem auto; display: block; }
}

/* Very small screens */
@media (max-width: 480px) {
    .sidebar-toggle { width: 80%; margin: .5rem auto; display: block; }
}
</style>


<!-- Sidebar Script -->
<script>
const sidebar = document.getElementById("sidebar");
const toggle = document.getElementById("sidebarToggle");
const sidebarIcon = document.getElementById("sidebarIcon");

let userToggled = false; // track if user clicked the button

function handleResize() {
    if (!userToggled) {
        if (window.innerWidth <= 1024) {
            sidebar.classList.add("collapsed");
            sidebarIcon.className = "fas fa-angle-right";
        } else {
            sidebar.classList.remove("collapsed");
            sidebarIcon.className = "fas fa-angle-left";
        }
    }
}

window.addEventListener("resize", handleResize);
handleResize();

toggle.addEventListener("click", function() {
    sidebar.classList.toggle("collapsed");
    sidebarIcon.className = sidebar.classList.contains("collapsed")
        ? "fas fa-angle-right"
        : "fas fa-angle-left";
    userToggled = true; // user has overridden auto behavior
});
</script>

<!-- Add Font Awesome (once in your <head>) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
