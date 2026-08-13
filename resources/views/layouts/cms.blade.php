@php
    $siteName = \App\Models\Setting::read('site_name', config('app.name', 'Laravel'));
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ filled($title ?? null) ? $title.' - ' : '' }}{{ $siteName }} CMS</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-screen overflow-hidden bg-cms-bg font-cms text-cms-text antialiased">
        <div class="flex h-full">
            @include('partials.cms.sidebar', ['siteName' => $siteName])

            <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
                {{ $slot }}
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @stack('scripts')

        @fluxScripts
    </body>
</html>
