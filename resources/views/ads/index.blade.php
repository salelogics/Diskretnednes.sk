@extends('layouts.user-dashboard')

@section('header', 'Moje inzeráty')

@section('content')
<style>
    @media (max-width: 640px) {
        .flex.items-center.justify-between {
            flex-direction: column;
            align-items: flex-start;
        }
        .flex.items-center.space-x-3 {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .flex.items-center.space-x-4 {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .text-lg.font-medium.text-gray-900.truncate {
            white-space: normal;
        }
        
        /* Mobile dropdown positioning fix */
        .dropdown-mobile {
            position: fixed !important;
            top: 50% !important;
            left: 1rem !important;
            right: 1rem !important;
            width: auto !important;
            max-width: calc(100vw - 2rem) !important;
            transform: translateY(-50%) !important;
            z-index: 50 !important;
            margin-top: 0 !important;
        }
        
        /* Adjust card layout on mobile */
        .mobile-card-actions {
            margin-top: 1rem;
            width: 100%;
            display: flex;
            justify-content: center;
        }
        
        /* Improve card layout on mobile */
        .ad-card-mobile {
            flex-direction: column;
            align-items: stretch;
        }
        
        .ad-info-mobile {
            margin-bottom: 1rem;
        }
    }
</style>
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Flash správy -->
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

    <!-- Štatistiky inzerátov -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5 mb-8">
        <!-- Celkový počet inzerátov -->
        <div class="relative bg-gradient-to-br from-pink-500 to-pink-600 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Celkovo inzerátov</dt>
                        <dd class="text-2xl font-bold text-white">{{ $stats['total_ads'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Aktívne inzeráty -->
        <div class="relative bg-gradient-to-br from-pink-400 to-pink-500 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Aktívne</dt>
                        <dd class="text-2xl font-bold text-white">{{ $stats['active_ads'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Celkové zobrazenia -->
        <div class="relative bg-gradient-to-br from-pink-300 to-pink-400 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Zobrazenia</dt>
                        <dd class="text-2xl font-bold text-white">{{ number_format($stats['total_views']) }}</dd>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Celkové kliky -->
        <div class="relative bg-gradient-to-br from-pink-200 to-pink-300 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Kliky na číslo</dt>
                        <dd class="text-2xl font-bold text-white">{{ number_format($stats['total_clicks']) }}</dd>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Priemerný CTR -->
        <div class="relative bg-gradient-to-br from-pink-500 to-pink-600 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Priemerný CTR</dt>
                        <dd class="text-2xl font-bold text-white">{{ $stats['avg_ctr'] }}%</dd>
                    </div>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>
    </div>

    <!-- Hlavný obsah -->
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Vaše inzeráty</h3>
                    <p class="text-sm text-gray-500 mt-1">Spravujte svoje inzeráty a sledujte ich výkonnosť</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('ads.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nový inzerát
                    </a>
                </div>
            </div>
        </div>

        <!-- Zoznam inzerátov -->
        <div class="divide-y divide-gray-200">
            @forelse($ads as $ad)
                <div class="p-6 hover:bg-pink-50 transition-colors">
                    <div class="flex items-center justify-between ad-card-mobile">
                        <div class="flex-1 min-w-0 ad-info-mobile">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('ads.statistics', $ad->id) }}" class="flex-shrink-0" title="Detail inzerátu">
                                    @if($ad->verification_image_url)
                                        <img src="{{ $ad->verification_image_url }}" alt="{{ $ad->nickname ?: 'Profilová fotka' }}" class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <div class="w-16 h-16 bg-gradient-to-br from-pink-400 to-pink-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                                            {{ substr($ad->ad_type_label, 0, 1) }}
                                        </div>
                                    @endif
                                </a>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-lg font-medium text-gray-900 truncate">
                                            <a href="{{ route('ads.statistics', $ad->id) }}" class="hover:text-pink-600" title="Detail inzerátu">
                                                @if($ad->nickname)
                                                    {{ $ad->nickname }} - {{ $ad->city_label }}
                                                @else
                                                    {{ $ad->offer_type_label }} - {{ $ad->city_label }}
                                                @endif
                                            </a>
                                        </h4>
                                        @if($ad->featured)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                Zvýraznený
                                            </span>
                                        @endif
                                        @if($ad->phone_verified)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-pink-100 text-pink-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Overené
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            {{ $ad->ad_type_label }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $ad->city_label }}@if($ad->street), {{ $ad->street }}@endif
                                        </span>
                                        <span class="flex items-center font-medium text-gray-900">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ $ad->age }} rokov
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center space-x-4 text-sm">
                                        <span class="text-gray-500">ID: <span class="font-mono">AD-{{ $ad->id }}</span></span>
                                        <span class="text-gray-500">{{ count($ad->gallery_photos ?? []) + ($ad->verification_image_url ? 1 : 0) }} fotiek</span>
                                        <span class="text-gray-500">{{ number_format($ad->views) }} zobrazení</span>
                                        <span class="text-gray-500">{{ number_format($ad->clicks) }} klikov na číslo</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-4 sm:space-y-0">
                            <!-- Stavy -->
                            <div class="text-right sm:text-left">
                                <div class="flex flex-col space-y-1">
                                                        @if($ad->status === 'active' && $ad->isSubscriptionActive())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            Aktívny
                        </span>
                                    @elseif($ad->status === 'active' && !$ad->isSubscriptionActive())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                                            Neviditeľný (Expirované)
                                        </span>
                                    @elseif($ad->status === 'draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                            Koncept
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                            Neaktívny
                                        </span>
                                    @endif

                                    @if($ad->isSubscriptionActive())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                            Predplatné aktívne
                                        </span>
                                    @elseif($ad->isSubscriptionExpired())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Predplatné vypršalo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Bez predplatného
                                        </span>
                                    @endif
                                </div>
                                @if($ad->subscription_expires_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Vyprší: {{ $ad->subscription_expires_at->format('d.m.Y') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Akcie -->
                            <div class="mobile-card-actions">
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <div>
                                        <button type="button" @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                                            Akcie
                                            <svg class="ml-2 -mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="dropdown-mobile sm:origin-top-right sm:absolute sm:right-0 sm:mt-2 sm:w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                    <div class="py-1">
                                        <!-- Upraviť -->
                                        <a href="{{ route('ads.edit', $ad->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Upraviť inzerát
                                        </a>

                                        <!-- Zobraziť inzerát -->
                                        <a href="{{ route('ad.show', $ad->id) }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Zobraziť inzerát
                                            <svg class="ml-auto h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>

                                        @if(!$ad->isSubscriptionActive() && $classicPackage)
                                            <!-- Aktivovať zdarma (Classic) -->
                                            <button type="button" onclick="activateFreePackage({{ $ad->id }}, {{ $classicPackage->id }})" class="group flex items-center px-4 py-2 text-sm text-pink-700 hover:bg-pink-50 hover:text-pink-900 w-full text-left">
                                                <svg class="mr-3 h-4 w-4 text-pink-400 group-hover:text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                                </svg>
                                                Aktivovať zdarma
                                            </button>
                                        @endif

                                        <!-- Detail a správa (predplatné, pozastavenie, štatistiky) -->
                                        <a href="{{ route('ads.statistics', $ad->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                            <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            Detail inzerátu
                                        </a>



                                        <!-- Aktivovať/Deaktivovať -->
                                        @if(!$ad->isSubscriptionActive())
                                            {{-- Bez predplatného (koncept/expirované) - tlačidlo "Aktivovať zdarma"
                                                 vyššie už rieši aktiváciu, tu netreba duplicitný text --}}
                                        @elseif($ad->status === 'active' && $ad->isSubscriptionActive())
                                            <!-- Deaktivovať (iba s aktívnym predplatným) -->
                                            <button type="button" onclick="toggleAdStatus({{ $ad->id }}, '{{ $ad->status }}')" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left">
                                                <svg class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Deaktivovať
                                            </button>
                                        @elseif($ad->isSubscriptionActive())
                                            <!-- Aktivovať (iba ak má aktívne predplatné) -->
                                            <button type="button" onclick="toggleAdStatus({{ $ad->id }}, '{{ $ad->status }}')" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left">
                                                <svg class="mr-3 h-4 w-4 text-green-400 group-hover:text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Aktivovať
                                            </button>
                                        @endif

                                        <div class="border-t border-gray-100"></div>

                                        <!-- Vymazať -->
                                        <button type="button" onclick="deleteAd({{ $ad->id }})" class="group flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50 hover:text-red-900 w-full text-left">
                                            <svg class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Vymazať inzerát
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Žiadne inzeráty</h3>
                    <p class="mt-1 text-sm text-gray-500">Začnite vytvorením svojho prvého inzerátu.</p>
                    <div class="mt-6">
                        <a href="{{ route('ads.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Vytvoriť inzerát
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modál pre aktiváciu/deaktiváciu -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-pink-100 mb-4">
                <svg id="statusIcon" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="statusTitle">Potvrdiť akciu</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="statusMessage">
                    Naozaj chcete vykonať túto akciu?
                </p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
                <button id="cancelStatusBtn" type="button" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Zrušiť
                </button>
                <button id="confirmStatusBtn" type="button" class="px-4 py-2 bg-pink-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500">
                    Potvrdiť
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modál pre vymazanie -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Vymazať inzerát</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Naozaj chcete vymazať tento inzerát? Táto akcia sa nedá vrátiť späť.
                </p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
                <button id="cancelDeleteBtn" type="button" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Zrušiť
                </button>
                <button id="confirmDeleteBtn" type="button" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Vymazať
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading modál -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-pink-100 mb-4">
                <svg class="animate-spin h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Spracovávam...</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Prosím čakajte, spracovávame vašu požiadavku.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// All subscription modal functionality is now handled by the component

// Toggle ad status
function toggleAdStatus(adId, currentStatus) {
    const modal = document.getElementById('statusModal');
    const title = document.getElementById('statusTitle');
    const message = document.getElementById('statusMessage');
    const icon = document.getElementById('statusIcon');
    const confirmBtn = document.getElementById('confirmStatusBtn');
    const cancelBtn = document.getElementById('cancelStatusBtn');
    
    // Set modal content based on current status
    if (currentStatus === 'active') {
        title.textContent = 'Deaktivovať inzerát';
        message.textContent = 'Naozaj chcete deaktivovať tento inzerát? Nebude viditeľný pre návštevníkov.';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />';
        icon.className = 'h-6 w-6 text-red-600';
        confirmBtn.textContent = 'Deaktivovať';
        confirmBtn.className = 'px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500';
    } else {
        title.textContent = 'Aktivovať inzerát';
        message.textContent = 'Naozaj chcete aktivovať tento inzerát? Bude viditeľný pre návštevníkov.';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
        icon.className = 'h-6 w-6 text-green-600';
        confirmBtn.textContent = 'Aktivovať';
        confirmBtn.className = 'px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500';
    }
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Handle confirm button
    confirmBtn.onclick = function() {
        document.getElementById('loadingModal').classList.remove('hidden');
        modal.classList.add('hidden');
        
        fetch(`/inzeraty/${adId}/prepnut-stav`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingModal').classList.add('hidden');
            if (data.success) {
                // Reload page to update UI
                window.location.reload();
            } else {
                alert('Chyba: ' + (data.message || 'Neočakávaná chyba'));
            }
        })
        .catch(error => {
            document.getElementById('loadingModal').classList.add('hidden');
            console.error('Error:', error);
            alert('Nastala chyba pri spracovaní požiadavky');
        });
    };
    
    // Handle cancel button
    cancelBtn.onclick = function() {
        modal.classList.add('hidden');
    };
    
    // Close modal when clicking outside
    modal.onclick = function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    };
}

// Delete ad
function deleteAd(adId) {
    const modal = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Handle confirm button
    confirmBtn.onclick = function() {
        document.getElementById('loadingModal').classList.remove('hidden');
        modal.classList.add('hidden');
        
        fetch(`/inzeraty/${adId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('loadingModal').classList.add('hidden');
            if (data.success) {
                // Reload page to update UI
                window.location.reload();
            } else {
                alert('Chyba: ' + (data.message || 'Neočakávaná chyba'));
            }
        })
        .catch(error => {
            document.getElementById('loadingModal').classList.add('hidden');
            console.error('Error:', error);
            alert('Nastala chyba pri spracovaní požiadavky: ' + error.message);
        });
    };
    
    // Handle cancel button
    cancelBtn.onclick = function() {
        modal.classList.add('hidden');
    };
    
    // Close modal when clicking outside
    modal.onclick = function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    };
}

function activateFreePackage(adId, packageId) {
    fetch(`/inzeraty/${adId}/platba`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ package_id: packageId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neočakávaná chyba'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Nastala chyba pri aktivácii: ' + error.message);
    });
}
</script>

@endsection 