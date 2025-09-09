<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Klinik GKN'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { padding-top: 56px; }
        .sidebar { position: fixed; top: 56px; bottom: 0; left: 0; z-index: 1000; width: 240px; box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1); }
        .main-content { margin-left: 240px; }
    </style>

    {{-- ================== PERBAIKAN DI SINI ================== --}}
    {{-- Menambahkan @stack('styles') untuk memuat CSS dari child view --}}
    @stack('styles')
    {{-- ======================================================= --}}

</head>
<body class="bg-light">
    @include('layouts.navigation-top')
    <div class="container-fluid">
        <div class="row">
            @include('layouts.navigation-sidebar')
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Ini sudah ada dan benar, tidak perlu diubah --}}
    @stack('scripts')

</body>
</html>