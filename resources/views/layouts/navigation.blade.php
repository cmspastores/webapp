<!-- NAVIGATION BAR -->
<nav class="top-nav">
    <div class="nav-container">
        <!-- Right side: Only user dropdown -->
        <div class="nav-right">
            <div class="nav-user-dropdown">
                <button class="nav-user-btn navigation-gear">
                    <i class="fa-solid fa-gear"></i>
                    <span>{{ Auth::user()->name }}</span>
                    <i class="fa-solid fa-caret-down"></i>
                </button>
                <div class="nav-dropdown-content">
                    <!-- Profile link -->
                    <a href="{{ route('profile.edit') }}">Profile</a>
                    <!-- Logout form -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">Log Out</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hamburger for mobile (not really needed but included for completeness) -->
        <button class="nav-hamburger hidden" onclick="toggleMobileNav()">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <!-- Mobile menu (hidden, included for completeness) -->
    <div id="mobile-nav" class="mobile-nav hidden">
        <a href="{{ route('profile.edit') }}" class="mobile-nav-link">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="mobile-nav-link logout-btn">Log Out</button>
        </form>
    </div>
</nav>

<!-- NAVIGATION CSS -->
<style>
/* Base nav */
.top-nav { background:#FFF5EC; border-bottom:2px solid #E6A574; font-family:'Figtree',sans-serif; width:100%; }
.nav-container { max-width:1200px; margin:0 auto; display:flex; justify-content:flex-end; align-items:center; padding:0 16px; height:60px; position:relative; }

/* User dropdown */
.nav-user-dropdown { position:relative; }

.nav-user-btn { display:flex; align-items:center; gap:6px; padding:6px 12px; border-radius:6px; border:none; background:linear-gradient(135deg,#E6955A,#F0A67C); color:#5C3A21; cursor:pointer; font-weight:600; transition:0.3s background,0.3s transform,0.3s color; }

.nav-user-btn:hover { background:linear-gradient(135deg,#F0B078,#F5C191); transform:scale(1.05); color:#3E2C1B; }



.nav-dropdown-content { display:none; position:absolute; top:100%; left:50%; transform:translateX(-50%); background:#FFF8F0; border:1px solid #E6A574; border-radius:8px; min-width:140px; box-shadow:0 4px 10px rgba(0,0,0,0.1); z-index:100; }

.nav-dropdown-content a, .nav-dropdown-content button { display:block; width:100%; text-align:left; padding:8px 12px; background:transparent; border:none; cursor:pointer; color:#5C3A21; text-decoration:none; font-weight:500; }
.nav-dropdown-content a:hover, .nav-dropdown-content button:hover { background:#E6A574; color:#FFF5EC; }
.nav-user-dropdown:hover .nav-dropdown-content { display:block; }

/* Hamburger (mobile) */
.nav-hamburger { display:none; border:none; background:transparent; font-size:20px; cursor:pointer; color:#5C3A21; }

/* Mobile menu */
.mobile-nav { display:flex; flex-direction:column; background:#FFF5EC; border-top:1px solid #E6A574; }
.mobile-nav-link { padding:10px 16px; border-bottom:1px solid #E6A574; color:#5C3A21; text-decoration:none; font-weight:600; }
.mobile-nav-link:hover, .mobile-nav-link.active { background:#E6A574; color:#FFF5EC; }

/* Hidden class */
.hidden { display:none; }

/* Responsive */
@media (max-width:768px) { .nav-right { display:none; } .nav-hamburger { display:block; } }
</style>

<!-- NAVIGATION JS -->
<script>
/* Toggle mobile menu */
function toggleMobileNav() {
    const mobileNav = document.getElementById('mobile-nav');
    mobileNav.classList.toggle('hidden');
}
</script>
