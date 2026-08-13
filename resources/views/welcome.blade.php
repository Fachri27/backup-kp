@php
    $siteName = data_get($settings, 'site_name', 'Pertanian Lainnya');
    $tagline = data_get($settings, 'tagline', 'Platform Data Pertanian Indonesia');
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $siteName }} - {{ $tagline }}</title>

        <meta name="description" content="{{ data_get($settings, 'meta_description', '') }}">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen overflow-x-hidden bg-tani-cream font-display text-tani-ink antialiased">
        @include('partials.landing.navbar')

        <main>
            @include('partials.landing.hero')
            @include('partials.landing.about')
            @include('partials.landing.stats')
            @include('partials.landing.map')
            @include('partials.landing.dashboard')
        </main>

        @include('partials.landing.footer')

        {{-- Livewire ships Alpine, so the landing page gets it without adding a separate dependency. --}}
        @livewireScripts
    </body>
</html>
