<!-- Sidebar -->
<aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ url('/') }}" class="sidebar-logo-link">
            <img src="{{ asset('images/c5logo.png') }}" alt="C5 Logo" class="sidebar-logo-img full-logo">
            <img src="{{ asset('images/c5logo-small.png') }}" alt="C5 Logo Small" class="sidebar-logo-img small-logo">
        </a>
    </div>

    <!-- Collapse Toggle BELOW LOGO -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i id="sidebarIcon" class="fas fa-angle-left"></i>
    </button>

    <!-- Top Nav -->
    <div class="sidebar-top">
        <h2>Navigation</h2>
        <ul>
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}">Rooms</a></li>
            <li><a href="{{ route('renters.index') }}" class="{{ request()->routeIs('renters.*') ? 'active' : '' }}">Renters</a></li>
            <li><a href="{{ route('reservation.index') }}" class="{{ request()->routeIs('reservation.*') ? 'active' : '' }}">Reservations</a></li>
            <li><a href="{{ route('agreements.index') }}" class="{{ request()->routeIs('agreements.*') ? 'active' : '' }}">Agreement Registration</a></li>
            <li><a href="{{ route('bills.index') }}" class="{{ request()->routeIs('billing.*') ? 'active' : '' }}">Billing</a></li>
        </ul>
    </div>

    <!-- Bottom Nav / Admin Links -->
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
/* Sidebar */
.sidebar { background: linear-gradient(180deg,#FFF5EC,#F7E1B5); border-right:2px solid #E6A574; min-height:100vh; width:16rem; display:flex; flex-direction:column; color:#5C3A21; box-sizing:border-box; transition:width .3s ease, backdrop-filter .3s ease; }

/* Logo */
.sidebar-logo { display:flex; justify-content:center; align-items:center; padding:1rem 0; }
.sidebar-logo-link { display:inline-block; cursor:pointer; text-decoration:none; line-height:0; }
.sidebar-logo-img { display:block; height:auto; transition:transform .3s ease, box-shadow .3s ease, opacity .3s ease; }
.full-logo { width:10rem; }
.small-logo { width:2.5rem; display:none; }
.sidebar-logo-img:hover { transform:scale(1.05); filter:drop-shadow(0 2px 6px rgba(230,165,116,.5)); }

/* Sidebar Toggle below Logo */
.sidebar-toggle { margin:.5rem 1rem; background:#D98348; color:#FFF; border:none; border-radius:6px; padding:.5rem; cursor:pointer; box-shadow:0 2px 6px rgba(0,0,0,.2); width:calc(100% - 2rem); display:flex; justify-content:center; align-items:center; transition:.2s; }
.sidebar-toggle:hover { background:#C46A32; }
.sidebar-toggle i { font-size:1.2rem; font-weight:bold; }

/* Top & Bottom Nav with Pop Effect */
.sidebar-top, .sidebar-bottom { padding:1rem; margin:.5rem; border-radius:12px; background:rgba(255,255,255,.4); box-shadow:0 4px 8px rgba(0,0,0,.1); display:flex; flex-direction:column; gap:.5rem; }
.sidebar-top h2, .sidebar-label { font:1.25rem 'Figtree',sans-serif; font-weight:900; color:#5C3A21; margin-bottom:.5rem; text-align:left; padding-left:.5rem; }

/* Navigation Links */
.sidebar ul { display:flex; flex-direction:column; gap:.5rem; padding:0; margin:0; list-style:none; }
.sidebar a:not(.sidebar-logo-link) { display:flex; align-items:center; padding:.5rem .75rem; border-radius:8px; color:#5C3A21; text-decoration:none; font-weight:500; background:#FFF9F2; box-shadow:inset 0 1px 3px rgba(0,0,0,.08); transition:all .2s ease; }
.sidebar a:not(.sidebar-logo-link):hover { background:linear-gradient(to right,#F7E1B5,#F4C38C); transform:translateX(4px); box-shadow:0 2px 6px rgba(0,0,0,.15); }
.sidebar a.active { background:linear-gradient(to right,#F4C38C,#E6A574); color:#3A2C1F; font-weight:600; box-shadow:0 2px 6px rgba(0,0,0,.2); }

/* Push Bottom Nav lower so it doesn't look empty */
.sidebar-bottom { margin-top:auto; padding-bottom:1.5rem; }

/* Collapsed Mode */
.sidebar.collapsed { width:4rem; backdrop-filter:blur(6px); }
.sidebar.collapsed .full-logo { display:none; }
.sidebar.collapsed .small-logo { display:block; }
.sidebar.collapsed .sidebar-top h2, .sidebar.collapsed .sidebar-label, .sidebar.collapsed ul li a { display:none; }
.sidebar.collapsed .sidebar-top, .sidebar.collapsed .sidebar-bottom { padding:0 .25rem; }
.sidebar.collapsed .sidebar-toggle { width:80%; margin:.5rem auto; display:flex; justify-content:center; }

/* Responsive */
@media (max-width:1024px) { 
  .sidebar:not(.collapsed) { width:16rem; } 
  .sidebar.auto-collapsed { width:4rem; } 
  .sidebar.auto-collapsed .full-logo { display:none !important; } 
  .sidebar.auto-collapsed .small-logo { display:block !important; } 
  .sidebar.auto-collapsed .sidebar-top h2, .sidebar.auto-collapsed .sidebar-label, .sidebar.auto-collapsed ul li a { display:none !important; } 
  .sidebar-toggle { width:80%; margin:.5rem auto; } 
}
@media (max-width:480px) { .sidebar-toggle { width:80%; margin:.5rem auto; } }
</style>

<script>
const sidebar = document.getElementById("sidebar");
const toggle = document.getElementById("sidebarToggle");
const sidebarIcon = document.getElementById("sidebarIcon");
let userToggled = false;
function handleResize() { 
    if(!userToggled){ 
        if(window.innerWidth<=1024){ 
            sidebar.classList.add("collapsed"); 
            sidebarIcon.className="fas fa-angle-right"; 
        }else{ 
            sidebar.classList.remove("collapsed"); 
            sidebarIcon.className="fas fa-angle-left"; 
        } 
    } 
}
window.addEventListener("resize",handleResize); 
handleResize();
toggle.addEventListener("click",function(){ 
    sidebar.classList.toggle("collapsed"); 
    sidebarIcon.className=sidebar.classList.contains("collapsed")?"fas fa-angle-right":"fas fa-angle-left"; 
    userToggled=true; 
});
</script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
