<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-g">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Klinik GKN') }}</title>

    {{-- Styles and Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-light">
    {{-- Memanggil komponen navigasi atas Anda --}}
    @include('layouts.navigation-top')

    {{-- Container untuk konten utama tanpa struktur sidebar --}}
    <div class="container-fluid" style="margin-top: 70px;">
        <main class="py-4">
            {{-- @yield('content') akan merender isi dari halaman yang menggunakan layout ini --}}
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
