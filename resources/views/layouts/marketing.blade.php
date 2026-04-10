<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name', 'Groupage'))</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                .btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border-radius:.75rem;padding:.75rem 1.1rem;font-size:.95rem;font-weight:600;transition:background-color .15s,color .15s,border-color .15s;}
                .btn-primary{background:#2563eb;color:#fff;}
                .btn-primary:hover{background:#1d4ed8;}
                .btn-success{background:#16a34a;color:#fff;}
                .btn-success:hover{background:#15803d;}
                .btn-outline{border:1px solid #e5e7eb;color:#111827;background:#fff;}
                .btn-outline:hover{background:#f9fafb;}
                .card{background:#fff;border:1px solid #e5e7eb;border-radius:1rem;box-shadow:0 1px 2px rgba(0,0,0,.05);}
            </style>
        @endif
    </head>
    <body class="bg-white text-gray-900">
        <div class="min-h-screen bg-white">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-blue-50/60 via-white to-white"></div>
            <div class="relative">
                @yield('content')
            </div>
        </div>
    </body>
</html>
