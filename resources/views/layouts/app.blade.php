<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Inventaris') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        <script>
            (function () {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (!stored && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-background text-foreground">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            @include('layouts.sidebar')

            <div class="lg:pl-64">
                {{-- Topbar --}}
                <header class="sticky top-0 z-30 flex h-14 items-center gap-4 border-b bg-background/80 backdrop-blur px-4 sm:px-6 lg:px-8">
                    <button @click="sidebarOpen = true" type="button" class="lg:hidden inline-flex items-center justify-center h-9 w-9 rounded-md hover:bg-accent">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>

                    <div class="flex-1">
                        @isset($header)
                            <div class="text-sm text-muted-foreground">{{ $header }}</div>
                        @endisset
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            x-data="{
                                dark: document.documentElement.classList.contains('dark'),
                                toggle() {
                                    this.dark = !this.dark;
                                    document.documentElement.classList.toggle('dark', this.dark);
                                    localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                                }
                            }"
                            @click="toggle()"
                            :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
                            class="inline-flex items-center justify-center h-9 w-9 rounded-md border border-input hover:bg-accent transition-colors"
                        >
                            <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m8.485-8.485H21M3 12h1.515m14.02-6.02l-1.06 1.06M6.525 17.475l-1.06 1.06m0-13.06l1.06 1.06m10.95 10.95l1.06 1.06M12 7.5A4.5 4.5 0 1 1 12 16.5a4.5 4.5 0 0 1 0-9z" /></svg>
                            <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" /></svg>
                        </button>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 rounded-md px-2 h-9 hover:bg-accent transition-colors">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-semibold">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <span class="hidden sm:inline text-sm font-medium">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2 border-b border-border">
                                    <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-muted-foreground truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
