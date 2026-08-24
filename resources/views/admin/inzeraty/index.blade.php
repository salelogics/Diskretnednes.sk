@extends('layouts.admin-dashboard')

@section('header', 'Správa inzerátov')

@section('content')
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
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Celkovo inzerátov -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Celkovo inzerátov</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_ads'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    všetky inzeráty
                </div>
            </div>
        </div>

        <!-- Aktívne -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Aktívne</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['active_ads'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    publikované inzeráty
                </div>
            </div>
        </div>

        <!-- Čakajúce na schválenie -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Čakajúce</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['pending_ads'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    na schválenie
                </div>
            </div>
        </div>

        <!-- Neaktívne -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364L18.364 5.636" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Neaktívne</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['inactive_ads'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    pozastavené inzeráty
                </div>
            </div>
        </div>
    </div>

    <!-- Zoznam inzerátov -->
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Všetky inzeráty</h3>
                    <p class="text-sm text-gray-500 mt-1">Prehľad a správa všetkých inzerátov v systéme</p>
                </div>
                <div class="flex space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        Aktívne
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                        Čakajúce
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                        Neaktívne
                    </span>
                </div>
            </div>
            
            <!-- Vyhľadávacie pole a tlačidlo pridať -->
            <div class="mt-6 flex items-center justify-between">
                <div class="relative max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        id="search-input" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-pink-500 focus:border-pink-500 sm:text-sm" 
                        placeholder="Vyhľadať inzeráty (meno, email, typ ponuky, ID...)"
                        value="{{ request('search') }}"
                    >
                    <div id="search-loading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                        <svg class="animate-spin h-4 w-4 text-pink-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Tlačidlo pridať inzerát -->
                <a href="{{ route('admin.inzeraty.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Pridať inzerát
                </a>
            </div>
            <div id="search-results-count" class="mt-2 text-sm text-gray-600 hidden"></div>

            <!-- Panel hromadných akcií -->
            <div id="bulk-actions-bar" class="mt-4 hidden items-center justify-between rounded-md bg-pink-50 border border-pink-200 px-4 py-3">
                <div class="text-sm font-medium text-gray-700">
                    <span id="bulk-selected-count">0</span> vybraných inzerátov
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" id="bulk-activate-btn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        Schváliť / Aktivovať
                    </button>
                    <button type="button" id="bulk-deactivate-btn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                        Deaktivovať
                    </button>
                    <button type="button" id="bulk-delete-btn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        Vymazať
                    </button>
                    <button type="button" id="bulk-clear-btn" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Zrušiť výber
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                <div id="ads-table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-pink-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <input type="checkbox" id="select-all-checkbox" class="h-4 w-4 rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inzerát</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Používateľ</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ponuka</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vytvorené</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcie</th>
                            </tr>
                        </thead>
                        <tbody id="ads-table-body" class="bg-white divide-y divide-gray-200">
                            @if($ads->count() > 0)
                                @foreach($ads as $ad)
                                    <tr class="hover:bg-pink-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" class="ad-row-checkbox h-4 w-4 rounded border-gray-300 text-pink-600 focus:ring-pink-500" value="{{ $ad->id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <!-- Fotka inzerátu -->
                                                <div class="w-12 h-12 rounded-lg overflow-hidden mr-3 bg-gray-100 flex-shrink-0">
                                                    @if($ad->verification_image_url)
                                                        <img src="{{ $ad->verification_image_url }}" alt="Overovacia fotka" class="w-16 h-16 rounded-lg object-cover">
                                                    @elseif($ad->gallery_image_urls && count($ad->gallery_image_urls) > 0)
                                                        <img src="{{ $ad->gallery_image_urls[0] }}" alt="Galéria" class="w-16 h-16 rounded-lg object-cover">
                                                    @else
                                                        <div class="w-full h-full bg-purple-100 rounded-lg flex items-center justify-center">
                                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">#{{ $ad->id }}</div>
                                                    <div class="text-sm text-gray-500 max-w-xs truncate">{{ $ad->nickname ?: $ad->offer_type_label }}</div>
                                                    @if($ad->gallery_image_urls && count($ad->gallery_image_urls) > 0 || $ad->verification_image_url)
                                                        <div class="text-xs text-gray-400 mt-1">
                                                            {{ count($ad->gallery_image_urls ?? []) + ($ad->verification_image_url ? 1 : 0) }} fotiek
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($ad->user)
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                        <span class="text-xs font-medium text-gray-600">{{ substr($ad->user->name, 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $ad->user->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $ad->user->email }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-500">Používateľ neexistuje</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $ad->offer_type_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $ad->created_at ? $ad->created_at->format('d.m.Y') : 'Neuvedené' }}</div>
                                            <div class="text-xs text-gray-500">{{ $ad->created_at ? $ad->created_at->format('H:i') : '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <!-- Stav inzerátu -->
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($ad->status === 'active') bg-green-100 text-green-800
                                                    @elseif($ad->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($ad->status === 'inactive') bg-gray-100 text-gray-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    @if($ad->status === 'active') Aktívny
                                                    @elseif($ad->status === 'pending') Čakajúci
                                                    @elseif($ad->status === 'inactive') Neaktívny
                                                    @else Zamietnutý
                                                    @endif
                                                </span>
                                                
                                                <!-- Ikonka predplatného -->
                                                @if($ad->isSubscriptionActive())
                                                    <div class="flex items-center" title="Aktívne predplatné do {{ $ad->subscription_expires_at ? $ad->subscription_expires_at->format('d.m.Y') : '' }}">
                                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    </div>
                                                @elseif($ad->isSubscriptionExpired())
                                                    <div class="flex items-center" title="Predplatné vypršalo">
                                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="flex items-center" title="Bez predplatného">
                                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                                <div>
                                                    <button type="button" @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                                                        Akcie
                                                        <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" style="display: none;">
                                                    <div class="py-1">
                                                        <!-- Zobraziť -->
                                                        <a href="{{ route('admin.inzeraty.show', $ad) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Zobraziť detail
                                                        </a>

                                                        <!-- Upraviť inzerát -->
                                                        <a href="{{ route('ads.edit', $ad) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" target="_blank">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            🚀 Upraviť inzerát
                                                        </a>

                                                        <!-- Aktivovať/Deaktivovať -->
                                                        @if($ad->status === 'active')
                                                            <form method="POST" action="{{ route('admin.inzeraty.update-status', $ad) }}" class="block">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="inactive">
                                                                <button type="submit" class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364L18.364 5.636" />
                                                                    </svg>
                                                                    Deaktivovať
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form method="POST" action="{{ route('admin.inzeraty.update-status', $ad) }}" class="block">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="active">
                                                                <button type="submit" class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                                    <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    Aktivovať
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <!-- Divider -->
                                                        <div class="border-t border-gray-100"></div>

                                                        <!-- Vymazať -->
                                                        <form method="POST" action="{{ route('admin.inzeraty.destroy', $ad) }}" class="block" onsubmit="return confirm('Naozaj chcete vymazať tento inzerát?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="group flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                                <svg class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                Vymazať
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Žiadne inzeráty</h3>
                                        <p class="mt-1 text-sm text-gray-500">Zatiaľ neboli vytvorené žiadne inzeráty.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginácia -->
    <div id="pagination-container">
        @if($ads->hasPages())
            <div class="mt-6">
                {{ $ads->links() }}
            </div>
        @endif
    </div>
</div>


<script>
let searchTimeout;
let currentSearchQuery = '';

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchLoading = document.getElementById('search-loading');
    const searchResultsCount = document.getElementById('search-results-count');
    const adsTableContainer = document.getElementById('ads-table-container');
    const paginationContainer = document.getElementById('pagination-container');

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        currentSearchQuery = query;
        
        // Vymazanie predchádzajúceho timeout-u
        clearTimeout(searchTimeout);
        
        // Nastavenie nového timeout-u pre vyhľadávanie (500ms)
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 500);
    });

    function performSearch(query) {
        // Zobrazenie loading indikátora
        searchLoading.classList.remove('hidden');
        
        // AJAX požiadavka
        fetch('{{ route("admin.inzeraty.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                search: query
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Aktualizácia tabuľky
                document.getElementById('ads-table-body').innerHTML = data.html;
                
                // Aktualizácia paginációe
                paginationContainer.innerHTML = data.pagination || '';
                
                // Zobrazenie počtu výsledkov
                if (query.length > 0) {
                    searchResultsCount.textContent = `Nájdených ${data.count} výsledkov pre "${query}"`;
                    searchResultsCount.classList.remove('hidden');
                } else {
                    searchResultsCount.classList.add('hidden');
                }
                
                // Aktualizácia URL bez reload-u stránky
                const url = new URL(window.location);
                if (query.length > 0) {
                    url.searchParams.set('search', query);
                } else {
                    url.searchParams.delete('search');
                }
                window.history.pushState({}, '', url);
            } else {
                console.error('Chyba pri vyhľadávaní:', data.message);
            }
        })
        .catch(error => {
            console.error('AJAX chyba:', error);
        })
        .finally(() => {
            // Skrytie loading indikátora
            searchLoading.classList.add('hidden');
        });
    }

    // Podpora pre spätnú navigáciu v prehliadači
    window.addEventListener('popstate', function(event) {
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search') || '';
        searchInput.value = searchQuery;
        performSearch(searchQuery);
    });
});

// Hromadné akcie (schváliť/aktivovať, deaktivovať, vymazať)
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const adsTableBody = document.getElementById('ads-table-body');
    const bulkBar = document.getElementById('bulk-actions-bar');
    const bulkCount = document.getElementById('bulk-selected-count');

    function getRowCheckboxes() {
        return Array.from(adsTableBody.querySelectorAll('.ad-row-checkbox'));
    }

    function updateBulkBar() {
        const checked = getRowCheckboxes().filter(cb => cb.checked);
        bulkCount.textContent = checked.length;
        bulkBar.classList.toggle('hidden', checked.length === 0);
        bulkBar.classList.toggle('flex', checked.length > 0);

        const rowCheckboxes = getRowCheckboxes();
        selectAllCheckbox.checked = rowCheckboxes.length > 0 && checked.length === rowCheckboxes.length;
        selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < rowCheckboxes.length;
    }

    function resetSelection() {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        updateBulkBar();
    }

    selectAllCheckbox.addEventListener('change', function() {
        getRowCheckboxes().forEach(cb => { cb.checked = selectAllCheckbox.checked; });
        updateBulkBar();
    });

    // Event delegácia - tbody sa pri vyhľadávaní nahrádza cez innerHTML,
    // takže listener na jednotlivých checkboxoch by po refreshi zmizol.
    adsTableBody.addEventListener('change', function(event) {
        if (event.target.classList.contains('ad-row-checkbox')) {
            updateBulkBar();
        }
    });

    // Po každom AJAX vyhľadaní (performSearch nahradí obsah tbody) sa výber vynuluje.
    const tableObserver = new MutationObserver(resetSelection);
    tableObserver.observe(adsTableBody, { childList: true });

    function selectedIds() {
        return getRowCheckboxes().filter(cb => cb.checked).map(cb => cb.value);
    }

    function runBulkAction(action, confirmMessage) {
        const ids = selectedIds();
        if (ids.length === 0) return;
        if (confirmMessage && !confirm(confirmMessage)) return;

        fetch('{{ route("admin.inzeraty.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ ids: ids, action: action })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Chyba pri vykonávaní hromadnej akcie.');
            }
        })
        .catch(() => {
            alert('Chyba pri vykonávaní hromadnej akcie.');
        });
    }

    document.getElementById('bulk-activate-btn').addEventListener('click', function() {
        runBulkAction('activate', `Naozaj chcete schváliť/aktivovať ${selectedIds().length} inzerátov?`);
    });
    document.getElementById('bulk-deactivate-btn').addEventListener('click', function() {
        runBulkAction('deactivate', `Naozaj chcete deaktivovať ${selectedIds().length} inzerátov?`);
    });
    document.getElementById('bulk-delete-btn').addEventListener('click', function() {
        runBulkAction('delete', `Naozaj chcete natrvalo vymazať ${selectedIds().length} inzerátov? Túto akciu nie je možné vrátiť späť.`);
    });
    document.getElementById('bulk-clear-btn').addEventListener('click', function() {
        getRowCheckboxes().forEach(cb => { cb.checked = false; });
        resetSelection();
    });
});

// Admin specific functions - the subscription modal component handles the rest
</script>

@endsection 