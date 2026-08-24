<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Administrácia</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
    <x-google-tag-manager />
    
    <!-- Google Analytics -->
    <x-google-analytics />
</head>
<body class="h-full">
    <!-- Google Tag Manager (noscript) -->
    <x-google-tag-manager-noscript />
    
    <div class="min-h-full">
        <!-- Sticky header -->
        <nav class="bg-black bg-opacity-80 backdrop-blur-sm sticky top-0 z-[9999]" x-data="{ mobileMenuOpen: false }">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="border-b border-gray-700">
                        <div class="flex h-16 items-center justify-between px-4 sm:px-0 flex-nowrap">
                            <div class="flex items-center flex-shrink-0" style="min-width: 0; max-width: calc(100% - 80px);">
                                <div class="shrink-0">
                                    <a href="{{ url('/') }}" class="block">
                                        <img class="h-8 w-auto" src="{{ asset('images/uploads/diskretne-dnes-logo-white.png') }}" alt="Diskrétne Dnes">
                                    </a>
                                </div>
                                <div class="hidden md:block">
                                    <div class="ml-10 flex items-baseline space-x-2">
                                        <a href="{{ route('admin.nastenka') }}" class="rounded-md {{ request()->routeIs('admin.nastenka') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-2 py-1.5 text-xs font-medium flex items-center" aria-current="{{ request()->routeIs('admin.nastenka') ? 'page' : 'false' }}">
                                            <i class="ri-dashboard-line text-sm mr-1"></i>
                                            Nástenka
                                        </a>
                                        
                                        <!-- Dropdown menu pre Obsah -->
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="rounded-md {{ request()->routeIs('admin.kluby.*', 'admin.inzeraty.*', 'admin.clanky.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-2 py-1.5 text-xs font-medium flex items-center">
                                                <i class="ri-file-text-line text-sm mr-1"></i>
                                                Obsah
                                                <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-44 bg-white rounded-md shadow-lg py-1 z-50">
                                                <a href="{{ route('admin.kluby.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-building-line text-sm mr-1.5"></i>
                                                    Kluby
                                                </a>
                                                <a href="{{ route('admin.inzeraty.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-advertisement-line text-sm mr-1.5"></i>
                                                    Inzeráty
                                                </a>
                                                <a href="{{ route('admin.clanky.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-article-line text-sm mr-1.5"></i>
                                                    Články
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <!-- Dropdown menu pre Používatelia -->
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="rounded-md {{ request()->routeIs('admin.pouzivatelia.*', 'admin.nahlasenia-*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-2 py-1.5 text-xs font-medium flex items-center">
                                                <i class="ri-user-line text-sm mr-1"></i>
                                                Používatelia
                                                <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                                <a href="{{ route('admin.pouzivatelia.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-group-line text-sm mr-1.5"></i>
                                                    Zoznam používateľov
                                                </a>
                                                <a href="{{ route('admin.nahlasenia-zakaznikov.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-user-unfollow-line text-sm mr-1.5"></i>
                                                    Nahlásenia zákazníkov
                                                </a>
                                                <a href="{{ route('admin.nahlasenia-inzeratov.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-flag-line text-sm mr-1.5"></i>
                                                    Nahlásenia inzerátov
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <!-- Platby -->
                                        <a href="{{ route('admin.platby.index') }}" class="rounded-md {{ request()->routeIs('admin.platby.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-2 py-1.5 text-xs font-medium flex items-center">
                                            <i class="ri-bank-card-line text-sm mr-1"></i>
                                            Platby
                                        </a>
                                        
                                        <a href="{{ route('admin.support-tickets.index') }}" class="rounded-md {{ request()->routeIs('admin.support-tickets.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-2 py-1.5 text-xs font-medium flex items-center">
                                            <i class="ri-customer-service-line text-sm mr-1"></i>
                                            Support
                                        </a>
                                        
                                        <a href="{{ route('admin.notifications.index') }}" class="rounded-md {{ request()->routeIs('admin.notifications.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-2 py-1.5 text-xs font-medium flex items-center">
                                            <i class="ri-notification-line text-sm mr-1"></i>
                                            Notifikácie
                                        </a>
                                        
                                        <!-- Dropdown menu pre Štatistiky -->
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="rounded-md {{ request()->routeIs('admin.statistiky.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-2 py-1.5 text-xs font-medium flex items-center">
                                                <i class="ri-bar-chart-line text-sm mr-1"></i>
                                                Štatistiky
                                                <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-44 bg-white rounded-md shadow-lg py-1 z-50">
                                                <a href="{{ route('admin.statistiky.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-line-chart-line text-sm mr-1.5"></i>
                                                    Systémové štatistiky
                                                </a>
                                                <a href="{{ route('admin.statistiky.analytics') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-google-line text-sm mr-1.5"></i>
                                                    Google Analytics
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <!-- Dropdown menu pre Nastavenia -->
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="rounded-md {{ request()->routeIs('admin.settings.*', 'admin.email-templates.*', 'admin.email-log.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} px-2 py-1.5 text-xs font-medium flex items-center">
                                                <i class="ri-settings-line text-sm mr-1"></i>
                                                Nastavenia
                                                <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                                <a href="{{ route('admin.settings.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-settings-3-line text-sm mr-1.5"></i>
                                                    Systémové nastavenia
                                                </a>
                                                <a href="{{ route('admin.email-templates.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-mail-line text-sm mr-1.5"></i>
                                                    Email Templates
                                                </a>
                                                <a href="{{ route('admin.email-log.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-mail-send-line text-sm mr-1.5"></i>
                                                    Email Log
                                                </a>
                                                <a href="{{ route('admin.seo.index') }}" class="block px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="ri-seo-line text-sm mr-1.5"></i>
                                                    SEO
                                                </a>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden md:block">
                                <div class="ml-4 flex items-center md:ml-6">
                                    <x-notification-bell :isAdmin="true" />

                                    <div class="relative ml-3" x-data="{ open: false }">
                                        <div>
                                            <button type="button" @click="open = !open" class="relative flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                                <span class="absolute -inset-1.5"></span>
                                                <span class="sr-only">Otvoriť používateľské menu</span>
                                                @if(auth()->user()->photo)
                                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}" alt="Profilová fotka">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-pink-600 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </button>
                                        </div>
                                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-[9999] mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                            <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1">Váš profil</a>
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
                                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 flex-shrink-0" aria-controls="mobile-menu" aria-expanded="false">
                                    <span class="absolute -inset-0.5"></span>
                                    <span class="sr-only">Otvoriť hlavné menu</span>
                                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" x-show="!mobileMenuOpen">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                    </svg>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" x-show="mobileMenuOpen">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="mobileMenuOpen" class="border-b border-gray-700 md:hidden" id="mobile-menu">
                        <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
                            <a href="{{ route('admin.nastenka') }}" class="rounded-md {{ request()->routeIs('admin.nastenka') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center" aria-current="{{ request()->routeIs('admin.nastenka') ? 'page' : 'false' }}">
                                <i class="ri-dashboard-line text-sm mr-2"></i>
                                Nástenka
                            </a>
                            
                            <!-- Obsah sekcia -->
                            <div class="border-t border-gray-600 pt-2 mt-2">
                                <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider px-2 mb-1">Obsah</div>
                                <a href="{{ route('admin.kluby.index') }}" class="rounded-md {{ request()->routeIs('admin.kluby.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-building-line text-sm mr-2"></i>
                                    Kluby
                                </a>
                                <a href="{{ route('admin.inzeraty.index') }}" class="rounded-md {{ request()->routeIs('admin.inzeraty.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-advertisement-line text-sm mr-2"></i>
                                    Inzeráty
                                </a>
                                <a href="{{ route('admin.clanky.index') }}" class="rounded-md {{ request()->routeIs('admin.clanky.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-article-line text-sm mr-2"></i>
                                    Články
                                </a>
                            </div>
                            
                            <!-- Používatelia sekcia -->
                            <div class="border-t border-gray-600 pt-2 mt-2">
                                <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider px-2 mb-1">Používatelia</div>
                                <a href="{{ route('admin.pouzivatelia.index') }}" class="rounded-md {{ request()->routeIs('admin.pouzivatelia.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-group-line text-sm mr-2"></i>
                                    Zoznam používateľov
                                </a>
                                <a href="{{ route('admin.nahlasenia-zakaznikov.index') }}" class="rounded-md {{ request()->routeIs('admin.nahlasenia-zakaznikov.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-user-unfollow-line text-sm mr-2"></i>
                                    Nahlásenia zákazníkov
                                </a>
                                <a href="{{ route('admin.nahlasenia-inzeratov.index') }}" class="rounded-md {{ request()->routeIs('admin.nahlasenia-inzeratov.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-flag-line text-sm mr-2"></i>
                                    Nahlásenia inzerátov
                                </a>
                            </div>
                            
                            <!-- Ostatné -->
                            <div class="border-t border-gray-600 pt-2 mt-2">
                                <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider px-2 mb-1">Systém</div>
                                <a href="{{ route('admin.platby.index') }}" class="rounded-md {{ request()->routeIs('admin.platby.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-bank-card-line text-sm mr-2"></i>
                                    Platby
                                </a>
                                <a href="{{ route('admin.support-tickets.index') }}" class="rounded-md {{ request()->routeIs('admin.support-tickets.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-customer-service-line text-sm mr-2"></i>
                                    Support
                                </a>
                                <a href="{{ route('admin.notifications.index') }}" class="rounded-md {{ request()->routeIs('admin.notifications.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-notification-line text-sm mr-2"></i>
                                    Notifikácie
                                </a>
                                <a href="{{ route('admin.statistiky.index') }}" class="rounded-md {{ request()->routeIs('admin.statistiky.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                    <i class="ri-bar-chart-line text-sm mr-2"></i>
                                    Štatistiky
                                </a>
                                
                                <!-- Nastavenia sekcia -->
                                <div class="border-t border-gray-600 pt-2 mt-2">
                                    <div class="text-gray-400 text-xs font-semibold uppercase tracking-wider px-2 mb-1">Nastavenia</div>
                                    <a href="{{ route('admin.settings.index') }}" class="rounded-md {{ request()->routeIs('admin.settings.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                        <i class="ri-settings-3-line text-sm mr-2"></i>
                                        Systémové nastavenia
                                    </a>
                                    <a href="{{ route('admin.email-templates.index') }}" class="rounded-md {{ request()->routeIs('admin.email-templates.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                        <i class="ri-mail-line text-sm mr-2"></i>
                                        Email Templates
                                    </a>
                                    <a href="{{ route('admin.email-log.index') }}" class="rounded-md {{ request()->routeIs('admin.email-log.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                        <i class="ri-mail-send-line text-sm mr-2"></i>
                                        Email Log
                                    </a>
                                    <a href="{{ route('admin.seo.index') }}" class="rounded-md {{ request()->routeIs('admin.seo.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} block px-2 py-1.5 text-xs font-medium flex items-center">
                                        <i class="ri-seo-line text-sm mr-2"></i>
                                        SEO
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div class="border-t border-gray-700 pb-3 pt-4">
                            <div class="flex items-center px-5">
                                <div class="flex-shrink-0">
                                    @if(auth()->user()->photo)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}" alt="Profilová fotka">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-pink-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <div class="text-base font-medium leading-none text-white">{{ auth()->user()->name }}</div>
                                    <div class="text-sm font-medium leading-none text-gray-400">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                            <div class="mt-3 space-y-1 px-2">
                                <a href="{{ route('admin.profile') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">Váš profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full rounded-md px-3 py-2 text-left text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">
                                        Odhlásiť sa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

        <div class="pb-32 relative" style="background-image: url('{{ asset('images/uploads/hero-bg.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <!-- Overlay pre lepšiu čitateľnosť -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <header class="py-10 relative z-10">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold tracking-tight text-white">@yield('header', 'Administrácia')</h1>
                </div>
            </header>
        </div>

        <main class="-mt-32 relative z-10">
            <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white px-5 py-6 shadow sm:px-6">
                    @if(session('success'))
                        <div class="mb-6 rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html> 