<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <title>{{ config('app.name') }}</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @routes

    <script>
        document.querySelector('[data-theme]').dataset.theme = localStorage.getItem('theme') ?? 'dark';
    </script>
</head>
<body>
@inertia
</body>
</html>
