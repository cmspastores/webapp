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

    <!-- 🎨 Modern Color Scheme -->
    <style>
        body {
            background-color: #1e293b; 
            color: #f1f5f9; 
            font-family: 'Figtree', sans-serif;
        }

        .login-screen {
            background: linear-gradient(to bottom right, #1e3a8a, #0f766e); 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 40px;
        }

        .login-box {
            background-color: #f8fafc; 
            color: #1e293b; 
            border: 3px solid #7dd3fc; 
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 420px;
            width: 100%;
            padding: 28px;
        }

        .logo {
            margin-bottom: 24px;
        }
    </style>
</head>

<body>
    <!--  Debug Block -->
    <div style="position: absolute; top: 0; left: 0; background: #020060ff; color: #ffffffff; padding: 10px; z-index: 9999; font-weight: bold;">
       guest.blade.php is loading
    </div>

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
