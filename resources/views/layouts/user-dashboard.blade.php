<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- RemixIcon CSS -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" as="style">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Ensure mobile menu button stays in place */
        @media (max-width: 767px) {
            nav .flex.justify-between {
                flex-wrap: nowrap !important;
            }
            nav .flex.items-center:first-child {
                flex: 0 1 auto;
                min-width: 0;
                overflow: hidden;
            }
            nav .flex.md\\:hidden {
                flex: 0 0 auto;
                margin-left: auto !important;
            }
        }
    </style>

    <!-- Google Tag Manager -->
</head>
<body class="h-full">
    <x-impersonation-banner />
    <div class="min-h-full">
        <!-- Sticky header -->
        <nav class="bg-black bg-opacity-80 backdrop-blur-sm sticky top-0 z-[9999]" x-data="{ mobileMenuOpen: false }">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="border-b border-gray-700">
                        <div class="flex h-16 items-center justify-between px-4 sm:px-0 flex-nowrap">
                            <div class="flex items-center flex-shrink-0" style="min-width: 0; max-width: calc(100% - 80px);">
                                <div class="shrink-0">
                                    <a href="{{ url('/') }}" class="block">
                                        <img class="h-8 w-auto" src="{{ asset('images/uploads/erotikon-logo.webp') }}" alt="Erotikon">
                                    </a>
                                </div>
                                <div class="hidden md:block">
                                    <div class="ml-10 flex items-baseline space-x-4">
                                        <a href="{{ route('dashboard') }}" class="rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium" aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">Nástenka</a>
                                        <a href="{{ route('ads.index') }}" class="rounded-md {{ request()->routeIs('ads.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium">Inzeráty</a>
                                        <a href="{{ route('payments.index') }}" class="rounded-md {{ request()->routeIs('payments.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium">Platby</a>
                                        <a href="{{ route('statistics.index') }}" class="rounded-md {{ request()->routeIs('statistics.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium">Štatistiky</a>
                                        <a href="{{ route('customer-report.index') }}" class="rounded-md {{ request()->routeIs('customer-report.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium">Nahlásenie zákazníka</a>
                                        <a href="{{ route('pricing-dashboard') }}" class="rounded-md {{ request()->routeIs('pricing.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium">Cenník</a>
                                        <a href="{{ route('notifications.index') }}" class="rounded-md {{ request()->routeIs('notifications.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium">Notifikácie</a>
                                        <a href="{{ route('support.index') }}" class="rounded-md {{ request()->routeIs('support.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-sm font-medium">Podpora</a>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden md:block">
                                <div class="ml-4 flex items-center md:ml-6">
                                    <x-notification-bell />

                                    <!-- Profile dropdown -->
                                    <div class="relative ml-3" x-data="{ open: false }">
                                        <div>
                                            <button type="button" @click="open = !open" class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                                <span class="absolute -inset-1.5"></span>
                                                <span class="sr-only">Open user menu</span>
                                                @if(auth()->user()->photo)
                                                    <img class="size-8 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}" alt="Profilová fotka">
                                                @else
                                                    <div class="size-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-medium text-sm">
                                                        {{ substr(auth()->user()->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </button>
                                        </div>
                                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-[99999] mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">Váš profil</a>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">
                                                    Odhlásiť sa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex md:hidden ml-auto flex-shrink-0">
                                <!-- Mobile menu button -->
                                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 flex-shrink-0" aria-controls="mobile-menu" aria-expanded="false">
                                    <span class="absolute -inset-0.5"></span>
                                    <span class="sr-only">Otvoriť hlavné menu</span>
                                    <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" x-show="!mobileMenuOpen">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                    </svg>
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" x-show="mobileMenuOpen">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div x-show="mobileMenuOpen" class="border-b border-gray-700 md:hidden" id="mobile-menu">
                    <div class="space-y-1 px-2 py-3 sm:px-3">
                        <a href="{{ route('dashboard') }}" class="block rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-base font-medium" aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">Nástenka</a>
                        <a href="{{ route('ads.index') }}" class="block rounded-md text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 text-base font-medium">Inzeráty</a>
                        <a href="{{ route('payments.index') }}" class="block rounded-md {{ request()->routeIs('payments.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-base font-medium">Platby</a>
                        <a href="{{ route('statistics.index') }}" class="block rounded-md {{ request()->routeIs('statistics.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-base font-medium">Štatistiky</a>
                        <a href="{{ route('customer-report.index') }}" class="block rounded-md {{ request()->routeIs('customer-report.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-base font-medium">Nahlásenie zákazníka</a>
                                                    <a href="{{ route('pricing-dashboard') }}" class="block rounded-md {{ request()->routeIs('pricing.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-base font-medium">Cenník</a>
                        <a href="{{ route('notifications.index') }}" class="block rounded-md {{ request()->routeIs('notifications.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-base font-medium">Notifikácie</a>
                        <a href="{{ route('support.index') }}" class="block rounded-md {{ request()->routeIs('support.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-3 py-2 text-base font-medium">Podpora</a>
                    </div>
                    <div class="border-t border-gray-700 pb-3 pt-4">
                        <div class="flex items-center px-5">
                            <div class="shrink-0">
                                @if(auth()->user()->photo)
                                    <img class="size-10 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}" alt="Profilová fotka">
                                @else
                                    <div class="size-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-medium">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3">
                                <div class="text-base/5 font-medium text-white">{{ auth()->user()->name }}</div>
                                <div class="text-sm font-medium text-gray-400">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('notifications.index') }}" class="relative ml-auto shrink-0 rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors">
                                <span class="absolute -inset-1.5"></span>
                                <span class="sr-only">Zobraziť notifikácie</span>
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                            </a>
                        </div>
                        <div class="mt-3 space-y-1 px-2">
                            <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Váš profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Odhlásiť sa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

        <div class="pb-32 relative" style="background-image: url('{{ asset('images/uploads/hero-bg.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <!-- Overlay pre lepšiu čitateľnosť -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <header class="py-10 relative z-30">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold tracking-tight text-white">@yield('header', 'Dashboard')</h1>
                </div>
            </header>
        </div>

        <main class="-mt-32 relative z-30">
            <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white px-5 py-6 shadow sm:px-6">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>


</body>
</html> 