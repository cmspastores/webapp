<!-- 🌴 NAVIGATION BAR -->
<nav class="top-nav">
    <div class="nav-container">
        <div class="nav-right">
            <div class="nav-user-dropdown">
                <button class="nav-user-btn navigation-gear"><i class="fa-solid fa-gear"></i><span>{{ Auth::user()->name }}</span><i class="fa-solid fa-caret-down"></i></button>
                <div class="nav-dropdown-content">
                    <a href="{{ route('profile.edit') }}" class="nav-link">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
                </div>
            </div>
        </div>
        <button class="nav-hamburger" onclick="toggleMobileNav()"><i class="fa-solid fa-bars"></i></button>
    </div>
    <div id="mobile-nav" class="mobile-nav">
        <a href="{{ route('profile.edit') }}" class="mobile-nav-link">Profile</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="mobile-nav-link logout-btn">Log Out</button></form>
    </div>
</nav>

<style>
.top-nav{background:linear-gradient(90deg,#D98348,#E6A574);border-bottom:2px solid #E6A574;font-family:'Figtree',sans-serif;width:100%;}
.nav-container{max-width:1200px;margin:0 auto;display:flex;justify-content:flex-end;align-items:center;padding:0 16px;height:60px;position:relative;}
.nav-user-dropdown{position:relative;}
.nav-user-btn{display:flex;align-items:center;gap:6px;padding:6px 12px;border-radius:6px;border:none;background:#F5E1C8;color:#5C3A21;cursor:pointer;font-weight:600;transition:0.3s all;}
.nav-user-btn:hover{background:linear-gradient(135deg,#F0B78C,#E6A574);transform:scale(1.05);color:#fff;}
.nav-dropdown-content{display:none;position:absolute;top:100%;left:50%;transform:translateX(-50%);background:#FFF8F0;border:1px solid #E6A574;border-radius:8px;min-width:140px;box-shadow:0 4px 10px rgba(0,0,0,0.1);z-index:100;overflow:hidden;}
.nav-dropdown-content a,.nav-dropdown-content button{display:block;width:100%;text-align:left;padding:8px 12px;background:#FFF8F0;border:none;cursor:pointer;color:#5C3A21;text-decoration:none;font-weight:600;transition:color 0.3s ease;}
.nav-dropdown-content a:hover,.nav-dropdown-content button:hover{color:#D98348!important;background:#FFF8F0!important;}
.logout-btn:hover{color:#D98348!important;background:#FFF8F0!important;}
.nav-user-dropdown:hover .nav-dropdown-content{display:block;}
.nav-dropdown-content a:link,.nav-dropdown-content a:visited,.mobile-nav-link:link,.mobile-nav-link:visited{color:#5C3A21;text-decoration:none;}
.nav-hamburger{display:none;border:none;background:transparent;font-size:22px;cursor:pointer;color:#FFF;transition:0.3s ease;}
.nav-hamburger:hover{transform:scale(1.1);color:#FFEBD2;}
.mobile-nav{display:flex;flex-direction:column;background:#FFF5EC;border-top:1px solid #E6A574;max-height:0;overflow:hidden;opacity:0;transition:max-height 0.3s ease,opacity 0.3s ease;}
.mobile-nav.show{max-height:200px;opacity:1;}
.mobile-nav-link{padding:10px 16px;border-bottom:1px solid #E6A574;color:#5C3A21;text-decoration:none;font-weight:600;display:block;text-align:left;transition:color 0.3s ease;}
.mobile-nav-link:hover,.mobile-nav-link.logout-btn:hover{color:#D98348!important;background:#FFF5EC!important;}
@media(max-width:768px){.nav-right{display:none;}.nav-hamburger{display:block;}}
</style>

<script>
function toggleMobileNav(){const mobileNav=document.getElementById('mobile-nav');mobileNav.classList.toggle('show');}
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
