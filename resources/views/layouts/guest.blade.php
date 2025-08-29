<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Klinik GKN') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            html, body { height: 100%; }
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #f8f9fa; /* Warna latar abu-abu muda */
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div>
            {{ $slot }}
        </div>
    </body>
</html>
