<!-- 🌴 Sidebar Layout -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ url('/') }}" class="sidebar-logo-link">
            <img src="{{ asset('images/c5logo.png') }}" alt="C5 Logo" class="sidebar-logo-img full-logo">
            <img src="{{ asset('images/c5logo-small.png') }}" alt="C5 Logo Small" class="sidebar-logo-img small-logo">
        </a>
    </div>

    <div class="sidebar-section sidebar-top">
        <h2>Navigation</h2>
        <ul>
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}">Rooms</a></li>
            <li><a href="{{ route('renters.index') }}" class="{{ request()->routeIs('renters.*') ? 'active' : '' }}">Renters</a></li>
            <li><a href="{{ route('reservation.index') }}" class="{{ request()->routeIs('reservation.*') ? 'active' : '' }}">Reservations</a></li>
            <li><a href="{{ route('agreements.index') }}" class="{{ request()->routeIs('agreements.*') ? 'active' : '' }}">Agreement Registrations</a></li>
            <li><a href="{{ route('bills.index') }}" class="{{ request()->routeIs('bills.*') ? 'active' : '' }}">Billings</a></li>
            <li><a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">Payments</a></li>
        </ul>
    </div>

    <div class="sidebar-section sidebar-bottom">
        @php $currentUser = auth()->user(); @endphp
        @if($currentUser && ($currentUser->is_admin ?? false))
            <p class="sidebar-label">Admin Settings</p>
            <ul>
                <li><a href="{{ route('settings.users') }}" class="{{ request()->routeIs('settings.users') ? 'active' : '' }}">User Management</a></li>
                <li><a href="{{ route('roomtypes.index') }}" class="{{ request()->routeIs('roomtypes.*') ? 'active' : '' }}">Room Type Management</a></li>
            </ul>
        @endif
    </div>

    <!-- 🔘 Toggle Button -->
    <div class="sidebar-footer">
        <button class="sidebar-toggle" id="sidebarToggle"><i id="sidebarIcon" class="fas fa-angle-left"></i></button>
    </div>
</aside>

<style>

/* === 🌅 Base Sidebar Container === */
.sidebar{background:linear-gradient(180deg,#FFF5EC,#F7E1B5);border-right:2px solid #E6A574;/* allow the sidebar to stretch with the page content */height:auto;width:16rem;display:flex;flex-direction:column;justify-content:flex-start;align-items:stretch;align-self:stretch;color:#5C3A21;transition:width .3s ease;box-sizing:border-box;position:relative;overflow:auto;}

/* === 🖼️ Logo Section === */
.sidebar-logo{display:flex;justify-content:center;align-items:center;padding:1.5rem 0;}
.sidebar-logo-link{text-decoration:none;}
.sidebar-logo-img{height:auto;transition:transform .3s ease;}
.full-logo{width:10rem;}
.small-logo{width:2.5rem;display:none;}
.sidebar-logo-img:hover{transform:scale(1.05);filter:drop-shadow(0 2px 6px rgba(230,165,116,.5));}

/* === 📂 Section Styling === */
.sidebar-section{margin:1rem;padding:1rem;border-radius:16px;background:rgba(255,255,255,0.6);box-shadow:0 4px 10px rgba(0,0,0,0.1);backdrop-filter:blur(8px);}
.sidebar-section h2,.sidebar-label{font:1.2rem 'Figtree',sans-serif;font-weight:900;color:#5C3A21;margin-bottom:.75rem;text-align:left;}

/* === 📜 Link List === */
.sidebar ul{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:.6rem;}
.sidebar a:not(.sidebar-logo-link){display:flex;align-items:center;padding:.65rem .9rem;border-radius:10px;color:#5C3A21;text-decoration:none;font-weight:500;background:rgba(255,255,255,0.7);transition:all .2s ease;}
.sidebar a:not(.sidebar-logo-link):hover{background:#F8E0B0;transform:translateX(5px);}
.sidebar a.active{background:linear-gradient(to right,#F4C38C,#E6A574);color:#3A2C1F;font-weight:600;box-shadow:0 2px 6px rgba(0,0,0,.2);}

/* === ⚙️ Footer & Toggle === */
.sidebar-footer{display:flex;justify-content:center;align-items:center;padding:1rem 0;position:relative;}
.sidebar-toggle{background:linear-gradient(135deg,#E6A574,#F4C38C);color:#fff;border:none;border-radius:50%;width:2.6rem;height:2.6rem;cursor:pointer;box-shadow:0 3px 8px rgba(0,0,0,0.25);display:flex;align-items:center;justify-content:center;transition:all .3s ease;}
.sidebar-toggle:hover{background:linear-gradient(135deg,#D97A4E,#E6A574);transform:scale(1.1);box-shadow:0 6px 12px rgba(0,0,0,0.25);}
.sidebar-toggle i{font-size:1.1rem;transition:transform .3s ease;}

/* === 🧭 Collapsed Sidebar (Fixed Logo + Floating Toggle) === */
.sidebar.collapsed{width:4rem;overflow:hidden;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;}
.sidebar.collapsed .sidebar-logo{padding:1rem 0;}
.sidebar.collapsed .full-logo{display:none!important;}
.sidebar.collapsed .small-logo{display:block!important;margin-bottom:.5rem;}
.sidebar.collapsed .sidebar-section{background:transparent!important;box-shadow:none!important;margin:0!important;padding:0!important;}
.sidebar.collapsed .sidebar-section h2,
.sidebar.collapsed .sidebar-label,
.sidebar.collapsed ul{display:none!important;}
.sidebar.collapsed .sidebar-footer{position:relative;padding:.5rem 0;margin-top:.5rem;}
.sidebar.collapsed .sidebar-toggle{width:2.4rem;height:2.4rem;margin-top:.3rem;}

/* === 📱 Responsive Adjustments (Auto Behavior on Minimize) === */
@media(max-width:1024px){
.sidebar{justify-content:flex-start;}
.sidebar-footer{margin-top:auto;position:relative;}
.sidebar:not(.collapsed) .sidebar-footer{margin-top:1rem;align-self:center;}
.sidebar.collapsed .sidebar-footer{margin-top:.5rem;align-self:center;}
.sidebar.auto-collapsed{width:4rem;}
.sidebar.auto-collapsed .full-logo{display:none!important;}
.sidebar.auto-collapsed .small-logo{display:block!important;}
.sidebar.auto-collapsed .sidebar-section h2,
.sidebar.auto-collapsed .sidebar-label,
.sidebar.auto-collapsed ul li a{display:none!important;}
}

/* === 🌐 Enhanced Responsiveness === */

/* Large screens (maximized) */
@media(min-width:1600px){
    .sidebar{width:18rem;}
    .sidebar a:not(.sidebar-logo-link){padding:.8rem 1rem;}
    .sidebar-section{margin:1.2rem;padding:1.2rem;}
    .sidebar-footer{padding:1.2rem 0;}
    .sidebar-toggle{width:2.8rem;height:2.8rem;}
}

/* Medium screens (already partially handled but refined) */
@media(max-width:1200px) and (min-width:1025px){
    .sidebar{width:14rem;}
    .sidebar a:not(.sidebar-logo-link){padding:.6rem .8rem;}
}

/* Minimized / Mobile screens */
@media(max-width:1024px){
    /* Toggle button sits directly under visible sections in uncollapsed state */
    .sidebar:not(.collapsed) .sidebar-footer{margin-top:0.5rem;align-self:center;}

    /* Toggle button sits just under logo in collapsed state */
    .sidebar.collapsed .sidebar-footer{margin-top:.3rem;align-self:center;}

    /* Adjust padding for links and sections */
    .sidebar a:not(.sidebar-logo-link){padding:.5rem .7rem;}
    .sidebar-section{margin:.6rem;padding:.6rem;}
    .sidebar-toggle{width:2.2rem;height:2.2rem;}
}

/* Extra small screens / mobile portrait */
@media(max-width:600px){
    .sidebar{width:100%;flex-direction:row;overflow-x:auto;min-height:auto;}
    .sidebar-logo{padding:.5rem;}
    .sidebar-section{display:flex;flex-direction:row;margin:0;padding:.5rem;overflow-x:auto;gap:.3rem;}
    .sidebar-footer{justify-content:flex-start;padding:.3rem;}
    .sidebar-toggle{width:2rem;height:2rem;margin-top:0;margin-left:.5rem;}
}



</style>

<script>
const sidebar=document.getElementById("sidebar");const toggle=document.getElementById("sidebarToggle");const sidebarIcon=document.getElementById("sidebarIcon");let userToggled=false;
function updateIcon(){sidebarIcon.style.transform=sidebar.classList.contains("collapsed")?"rotate(180deg)":"rotate(0deg)";}
function handleResize(){if(!userToggled){if(window.innerWidth<=1024){sidebar.classList.add("collapsed");updateIcon();}else{sidebar.classList.remove("collapsed");updateIcon();}}}
window.addEventListener("resize",handleResize);handleResize();
toggle.addEventListener("click",function(){sidebar.classList.toggle("collapsed");updateIcon();userToggled=true;});
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
