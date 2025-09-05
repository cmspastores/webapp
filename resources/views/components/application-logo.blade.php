


<style>
.logo-container{text-align:center;margin-bottom:24px;}

.welcome-logo{width:200px;height:auto;transition:transform 0.3s ease, filter 0.3s ease;cursor:pointer;}
.welcome-logo:hover{transform:scale(1.1) rotate(3deg);filter:brightness(1.1);}
.welcome-logo:active{transform:scale(0.95) rotate(-3deg);filter:brightness(0.9);}

.navigation-logo{width:75px;height:auto;cursor:pointer;transition:transform 0.3s ease, filter 0.3s ease;padding-top:25px;}
.navigation-logo:hover{transform:scale(1.1) rotate(3deg);filter:brightness(1.1);}
.navigation-logo:active{transform:scale(0.95) rotate(-3deg);filter:brightness(0.9);}

</style>


<div class="logo-container">
    @if(request()->is('dashboard*') || request()->is('settings/users*') || request()->is('roomtypes*'))
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/c5logo.png') }}" alt="C5 Dormitel Logo" class="navigation-logo">
        </a>
    @else
        <img src="{{ asset('images/c5logo.png') }}" alt="C5 Dormitel Logo" class="welcome-logo">
    @endif
</div>
