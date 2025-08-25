<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


<!-- 🎨 Muted Sunset Tropical Dormitel CSS with Enhanced Gradients -->
<style>
    /* Body background: soft off-white with subtle warm radial glow */
    body{ background:radial-gradient(circle at top left,#FFFDF8,#FAF9F7); color:#2C2C2C; font-family:'Figtree',sans-serif; margin:0; }

    /* Login screen gradient: warm tropical sunset with added soft orange overlay */
    .login-screen{ background:linear-gradient(135deg,#F4C38C,#F7E1B5 60%,#FFECD2 90%); min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:0 16px; }

    /* Login box: cream gradient with soft glow, muted orange border, rounded corners */
    .login-box{ background:linear-gradient(180deg,#FFFDFB 0%,#FFF9F5 50%,#FFF5EE 100%); border:2px solid #E6A574; border-radius:14px; box-shadow:0 8px 20px rgba(0,0,0,0.1); max-width:420px; width:100%; padding:28px; text-align:center; box-sizing:border-box; }

    /* Logo container spacing */
    .logo{ margin-bottom:24px; text-align:center; }

    /* Logo SVG color: muted terracotta orange */
    .logo svg{ fill:#D98348; }

    /* Input fields: soft gradient background, muted orange border, dark gray text, rounded corners */
    input[type=text],input[type=password],input[type=email]{ background:linear-gradient(180deg,#FFF9F5,#FFF7F0); border:1.5px solid #E6A574; border-radius:8px; padding:10px 12px; width:100%; margin-bottom:16px; color:#2C2C2C; font-size:1rem; transition:0.2s; box-sizing:border-box; }

    /* Input focus: slightly darker terracotta border, subtle shadow */
    input:focus{ border-color:#D98348; box-shadow:0 0 0 3px rgba(217,131,72,0.2); outline:none; }

    /* Button background: gradient from terracotta to muted orange, text white */
    button{ background:linear-gradient(90deg,#D98348,#E6A574); color:#FFF; font-weight:600; padding:10px 20px; border-radius:10px; border:none; cursor:pointer; display:inline-block; margin:16px auto 0 auto; transition:0.2s; }

    /* Button hover: deeper terracotta to muted orange gradient, subtle lift */
    button:hover{ background:linear-gradient(90deg,#C46A32,#D98348); transform:translateY(-1px); }
</style>






</head>

<body>
    <!--  Login Screen Container -->
    <div class="login-screen">
        <!--  Logo Area -->
        <div class="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-sky-300" />
            </a>
        </div>

        <!-- Login Form Box -->
        <div class="login-box">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
