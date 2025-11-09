<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'C5 Dormitel') }}</title>

  <!-- Favicons -->
   <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon.png') }}">
   <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon.png') }}">
   <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
   <meta name="theme-color" content="#E6A574">



  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Custom Styles -->
  <style>
    body { font-family:'Figtree',sans-serif; background:linear-gradient(to bottom,#FFF4E1,#FFE0B2); color:#5C3A21; margin:0; padding:0; display:flex; flex-direction:column; min-height:100vh; }
    header { background:linear-gradient(to right,#FFF4E1,#FFE0B2); box-shadow:0 2px 4px rgba(0,0,0,0.05); }
    header>div { max-width:90%; margin:0 auto; padding:1.5rem 1rem; }
    main { flex:1; padding:2rem; background:transparent; }
    .sidebar { background:#FFD9B3; }
    .top-nav { background:linear-gradient(to right,#A65E3F,#70432C); border-bottom:1px solid #8B4A2C; color:#FFF5EC; box-shadow:0 2px 8px rgba(0,0,0,0.25); }
    .top-nav .text-cream,.top-nav svg,.top-nav .fill-current { color:#FFF5EC; fill:#FFF5EC; }
    .nav-user-btn { background:transparent; border:none; color:#FFF5EC; font-weight:500; display:inline-flex; align-items:center; padding:0.5rem 0.75rem; border-radius:0.5rem; transition:background 0.2s ease,color 0.2s ease; }
    .nav-user-btn:hover { background:rgba(255,245,236,0.12); color:#F4C38C; }
    .nav-dropdown { background:#5C3A21; border:1px solid #4A2C1A; border-radius:0.5rem; box-shadow:0 4px 10px rgba(0,0,0,0.25); }
    .nav-dropdown a { display:block; padding:0.5rem 1rem; color:#FFF5EC; transition:background 0.2s ease; }
    .nav-dropdown a:hover { background:rgba(244,195,140,0.15); color:#F4C38C; }
    .mobile-nav { background:linear-gradient(to bottom,#8B4A2C,#5C3A21); border-top:1px solid #4A2C1A; }
    .mobile-nav a { color:#FFF5EC; padding:0.75rem 1rem; display:block; transition:background 0.2s ease; }
    .mobile-nav a:hover { background:rgba(244,195,140,0.15); color:#F4C38C; }
    .nav-hamburger { background:transparent; border:none; color:#FFF5EC; padding:0.5rem; border-radius:0.375rem; transition:background 0.2s ease,color 0.2s ease; }
    .nav-hamburger:hover { background:rgba(255,245,236,0.12); color:#F4C38C; }
    
    footer { background:linear-gradient(90deg,#D98348,#E6A574); border-top:1px solid #E6A574; text-align:center; padding:1rem; font-size:14px; font-weight:500; font-family:'Figtree',sans-serif; line-height:1.4; color:#fff; box-shadow:0 -2px 6px rgba(0,0,0,0.1); }

  </style>
</head>
<body class="font-sans antialiased">
  <div class="min-h-screen flex">
    
    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Main content --}}
    <div class="flex-1 flex flex-col">
      {{-- Top bar / user menu --}}
      @include('layouts.navigation')

      {{-- Page heading --}}
      @isset($header)
        <header><div>{{ $header }}</div></header>
      @endisset

      {{-- Page content --}}
      <main class="flex-1">
        @yield('content') {{-- For RoomsController pages --}}
        {{ $slot ?? '' }} {{-- For Jetstream/Breeze dashboard --}}
      </main>

      {{-- Footer --}}
      <footer>© {{ date('Y') }} {{ config('app.name', 'C5 Dormitel') }}. All rights reserved.</footer>

    </div>
  </div>
</body>
</html>








</body>
</html>