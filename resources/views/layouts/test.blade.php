<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'KENAM SERVICES'))</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome-fixed.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div style="padding: 20px; background: #f0fdf4; min-height: 100vh;">
        <h1 style="color: #16a34a;">Test de la syntaxe</h1>
        <p>Si vous voyez ce message, la syntaxe est correcte.</p>
        @yield('content')
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
