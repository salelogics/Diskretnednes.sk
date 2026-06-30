@extends('layouts.user-dashboard')

@section('header', 'Platby')

@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }
    
    .dropdown-menu {
        backdrop-filter: blur(8px);
    }
    
    .table-container {
        position: relative;
        overflow-x: auto;
        overflow-y: visible;
    }
    
    .table-container::-webkit-scrollbar {
        height: 8px;
    }
    
    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .table-container::-webkit-scrollbar-thumb {
        background: #ec4899;
        border-radius: 4px;
    }
    
    .table-container::-webkit-scrollbar-thumb:hover {
        background: #db2777;
    }
</style>
@endpush

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

    <!-- Štatistiky platieb -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Celkovo zaplatené -->
        <div class="relative bg-gradient-to-br from-pink-500 to-pink-600 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Celkovo zaplatené</dt>
                        <dd class="text-2xl font-bold text-white">{{ number_format($stats['total_spent'], 2) }} €</dd>
                    </div>
                </div>
            </div>
            <div class="bg-black bg-opacity-10 px-6 py-3">
                <div class="text-sm text-pink-100">
                    za všetky platby
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Počet platieb -->
        <div class="relative bg-gradient-to-br from-pink-400 to-pink-500 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Počet platieb</dt>
                        <dd class="text-2xl font-bold text-white">{{ $stats['total_payments'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-black bg-opacity-10 px-6 py-3">
                <div class="text-sm text-pink-100">
                    celkovo
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Posledná platba -->
        <div class="relative bg-gradient-to-br from-pink-300 to-pink-400 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Posledná platba</dt>
                        <dd class="text-2xl font-bold text-white">{{ number_format($stats['last_payment']['amount'], 2) }} €</dd>
                    </div>
                </div>
            </div>
            <div class="bg-black bg-opacity-10 px-6 py-3">
                <div class="text-sm text-pink-100">
                    {{ \Carbon\Carbon::parse($stats['last_payment']['date'])->format('d.m.Y') }}
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Najčastejší spôsob -->
        <div class="relative bg-gradient-to-br from-pink-200 to-pink-300 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Najčastejší spôsob</dt>
                        <dd class="text-2xl font-bold text-white">{{ $stats['most_used_method'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-black bg-opacity-10 px-6 py-3">
                <div class="text-sm text-pink-100">
                    platby
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>
    </div>

    <!-- História platieb -->
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">História platieb</h3>
                    <p class="text-sm text-gray-500 mt-1">Prehľad všetkých vašich platieb a faktúr</p>
                </div>
                <div class="flex space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                        <div class="w-2 h-2 bg-pink-500 rounded-full mr-2"></div>
                        Zaplatené
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                        Čaká na platbu
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Desktop Table (hidden on mobile) -->
        <div class="hidden md:block overflow-visible">
            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-pink-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Inzerátu</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dátum</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spôsob platby</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cena</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcie</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-pink-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 font-mono">{{ $payment['ad_id'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment['id'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($payment['date'])->format('d.m.Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment['date'])->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($payment['method'] === 'Karta')
                                            <div class="w-6 h-6 bg-pink-100 rounded flex items-center justify-center mr-2">
                                                <svg class="w-3 h-3 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" />
                                                </svg>
                                            </div>
                                        @elseif($payment['method'] === 'Prevodom')
                                            <div class="w-6 h-6 bg-pink-100 rounded flex items-center justify-center mr-2">
                                                <svg class="w-3 h-3 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 114 0 2 2 0 01-4 0zm8-2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 bg-pink-100 rounded flex items-center justify-center mr-2">
                                                <svg class="w-3 h-3 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $payment['method'] }}</span>
                                            <div class="text-xs text-gray-500">{{ $payment['duration'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($payment['amount'], 2) }} €</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment['status'] === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Zaplatené
                                        </span>
                                        @if($payment['subscription_ends_at'])
                                            <div class="text-xs text-gray-500 mt-1">
                                                @if($payment['subscription_expired'])
                                                    <span class="text-red-600">Predplatné skončilo</span>
                                                @else
                                                    <span class="text-green-600">Aktívne do {{ $payment['subscription_ends_at'] }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @elseif($payment['status'] === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            Čaká na platbu
                                        </span>
                                    @elseif($payment['status'] === 'failed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-8a1 1 0 112 0v4a1 1 0 11-2 0v-4zm1-3a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                            </svg>
                                            Neúspešná
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            {{ ucfirst($payment['status']) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="relative inline-block text-left" x-data="{ open: false, dropUp: false }" 
                                         x-init="$watch('open', value => {
                                             if (value) {
                                                 $nextTick(() => {
                                                     const dropdown = $el.querySelector('.dropdown-menu');
                                                     const rect = dropdown.getBoundingClientRect();
                                                     const tableContainer = $el.closest('.overflow-x-auto');
                                                     
                                                     // Kontrola či je dropdown pod spodným okrajom viewportu
                                                     if (rect.bottom > window.innerHeight - 20) {
                                                         dropUp = true;
                                                     } else {
                                                         dropUp = false;
                                                     }
                                                     
                                                     // Ujisti sa, že dropdown nepresiahne okraje tabuľky
                                                     if (tableContainer) {
                                                         const containerRect = tableContainer.getBoundingClientRect();
                                                         if (rect.right > containerRect.right) {
                                                             dropdown.style.right = '0';
                                                             dropdown.style.left = 'auto';
                                                         }
                                                     }
                                                 });
                                             }
                                         })">
                                        <div>
                                            <button type="button" @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                                                Akcie
                                                <svg class="ml-2 -mr-1 h-4 w-4 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div x-show="open" @click.away="open = false" 
                                             x-transition:enter="transition ease-out duration-200" 
                                             x-transition:enter-start="transform opacity-0 scale-95 translate-y-1" 
                                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0" 
                                             x-transition:leave="transition ease-in duration-150" 
                                             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0" 
                                             x-transition:leave-end="transform opacity-0 scale-95 translate-y-1"
                                             class="dropdown-menu absolute right-0 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none border border-gray-200 z-50"
                                             :class="dropUp ? 'bottom-full mb-2 mt-0' : 'top-full'"
                                             style="transform-origin: top right"
                                             x-cloak>
                                            <div class="py-1">
                                                @if($payment['can_renew'])
                                                    <!-- Predplatiť znovu s výberom platby -->
                                                    <button onclick="openSubscriptionModal({{ str_replace('AD-', '', $payment['ad_id']) }}, '{{ addslashes($payment['ad_title']) }}', '{{ addslashes($payment['ad_title']) }}')" class="group flex items-center px-4 py-2 text-sm text-pink-700 hover:bg-pink-50 hover:text-pink-900 transition-colors w-full text-left">
                                                        <i class="ri-vip-crown-line mr-3 text-pink-500 group-hover:text-pink-700"></i>
                                                        Predplatiť znovu
                                                    </button>
                                                @else
                                                    <!-- Informácia o aktívnom predplatnom -->
                                                    <div class="flex items-center px-4 py-2 text-sm text-gray-500">
                                                        <svg class="mr-3 h-4 w-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        @if($payment['subscription_ends_at'])
                                                            Aktívne do {{ $payment['subscription_ends_at'] }}
                                                        @else
                                                            Predplatné je aktívne
                                                        @endif
                                                    </div>
                                                @endif

                                                @if($payment['has_invoice'])
                                                    <!-- Stiahnuť faktúru -->
                                                    <a href="{{ route('payments.download-invoice', $payment['payment_id']) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                                        <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Stiahnuť faktúru
                                                    </a>
                                                @endif

                                                @if($payment['is_sms'])
                                                    <!-- Informácia o SMS platbe -->
                                                    <div class="flex items-center px-4 py-2 text-sm text-gray-500">
                                                        <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        SMS platba - faktúra sa nevystavuje
                                                    </div>
                                                @endif

                                                <!-- Zobraziť detaily -->
                                                <button type="button" onclick="showPaymentDetails('{{ $payment['id'] }}', '{{ $payment['ad_id'] }}', '{{ $payment['date'] }}', '{{ $payment['method'] }}', '{{ $payment['duration'] }}', '{{ $payment['amount'] }}', '{{ $payment['status'] }}')" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 w-full text-left">
                                                    <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Zobraziť detaily
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Mobile Card Layout (visible on mobile only) -->
        <div class="md:hidden space-y-4">
            @foreach($payments as $payment)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="text-sm font-medium text-gray-900 font-mono">{{ $payment['ad_id'] }}</div>
                            <div class="text-xs text-gray-500">{{ $payment['id'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-gray-900">{{ number_format($payment['amount'], 2) }} €</div>
                            <div class="text-xs text-gray-500">{{ $payment['duration'] }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            @if($payment['method'] === 'Karta')
                                <div class="w-6 h-6 bg-pink-100 rounded flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" />
                                    </svg>
                                </div>
                            @elseif($payment['method'] === 'Prevodom')
                                <div class="w-6 h-6 bg-pink-100 rounded flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 114 0 2 2 0 01-4 0zm8-2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            @else
                                <div class="w-6 h-6 bg-pink-100 rounded flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $payment['method'] }}</span>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($payment['date'])->format('d.m.Y H:i') }}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            @if($payment['status'] === 'completed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Zaplatené
                                </span>
                                @if($payment['subscription_ends_at'])
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if($payment['subscription_expired'])
                                            <span class="text-red-600">Predplatné skončilo</span>
                                        @else
                                            <span class="text-green-600">Aktívne do {{ $payment['subscription_ends_at'] }}</span>
                                        @endif
                                    </div>
                                @endif
                            @elseif($payment['status'] === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    Čaká na platbu
                                </span>
                            @elseif($payment['status'] === 'failed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-8a1 1 0 112 0v4a1 1 0 11-2 0v-4zm1-3a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                    Neúspešná
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    {{ ucfirst($payment['status']) }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Mobile Actions -->
                        <div class="flex space-x-2">
                            @if($payment['can_renew'])
                                <button onclick="openSubscriptionModal({{ str_replace('AD-', '', $payment['ad_id']) }}, '{{ addslashes($payment['ad_title']) }}', '{{ addslashes($payment['ad_title']) }}')" class="p-2 bg-pink-100 text-pink-600 rounded-lg hover:bg-pink-200 transition-colors">
                                    <i class="ri-vip-crown-line text-sm"></i>
                                </button>
                            @endif
                            
                            @if($payment['has_invoice'])
                                <a href="{{ route('payments.download-invoice', $payment['payment_id']) }}" class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="ri-download-line text-sm"></i>
                                </a>
                            @endif
                            
                            <button type="button" onclick="showPaymentDetails('{{ $payment['id'] }}', '{{ $payment['ad_id'] }}', '{{ $payment['date'] }}', '{{ $payment['method'] }}', '{{ $payment['duration'] }}', '{{ $payment['amount'] }}', '{{ $payment['status'] }}')" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                                <i class="ri-information-line text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Paginácia -->
        @if(isset($pagination) && $pagination->hasPages())
            <div class="px-6 py-4 border-t border-pink-100">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Zobrazuje sa {{ $pagination->firstItem() }} - {{ $pagination->lastItem() }} z {{ $pagination->total() }} platieb
                    </div>
                    <div class="flex space-x-1">
                        {{-- Predchádzajúca stránka --}}
                        @if ($pagination->onFirstPage())
                            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                                Predchádzajúca
                            </span>
                        @else
                            <a href="{{ $pagination->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                Predchádzajúca
                            </a>
                        @endif

                        {{-- Čísla stránok --}}
                        @foreach ($pagination->getUrlRange(1, $pagination->lastPage()) as $page => $url)
                            @if ($page == $pagination->currentPage())
                                <span class="px-3 py-2 text-sm text-white bg-pink-600 rounded-md">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Nasledujúca stránka --}}
                        @if ($pagination->hasMorePages())
                            <a href="{{ $pagination->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                Nasledujúca
                            </a>
                        @else
                            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                                Nasledujúca
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal pre detaily platby -->
<div id="paymentDetailsModal" class="relative z-10 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div>
                    <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-pink-100">
                        <svg class="size-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-base font-semibold text-gray-900" id="modal-title">Detaily platby</h3>
                        <div class="mt-4 text-left">
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ID Platby</dt>
                                    <dd class="text-sm text-gray-900 font-mono" id="detail-payment-id"></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ID Inzerátu</dt>
                                    <dd class="text-sm text-gray-900 font-mono" id="detail-ad-id"></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Dátum platby</dt>
                                    <dd class="text-sm text-gray-900" id="detail-date"></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Spôsob platby</dt>
                                    <dd class="text-sm text-gray-900" id="detail-method"></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Trvanie predplatného</dt>
                                    <dd class="text-sm text-gray-900" id="detail-duration"></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Suma</dt>
                                    <dd class="text-sm text-gray-900 font-semibold" id="detail-amount"></dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Stav</dt>
                                    <dd class="text-sm" id="detail-status"></dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6">
                    <button type="button" onclick="closePaymentDetails()" class="inline-flex w-full justify-center rounded-md bg-pink-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-pink-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600">
                        Zavrieť
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showPaymentDetails(paymentId, adId, date, method, duration, amount, status) {
    document.getElementById('detail-payment-id').textContent = paymentId;
    document.getElementById('detail-ad-id').textContent = adId;
    document.getElementById('detail-date').textContent = new Date(date).toLocaleDateString('sk-SK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    document.getElementById('detail-method').textContent = method;
    document.getElementById('detail-duration').textContent = duration;
    document.getElementById('detail-amount').textContent = parseFloat(amount).toFixed(2) + ' €';
    
    const statusElement = document.getElementById('detail-status');
    if (status === 'completed') {
        statusElement.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">Zaplatené</span>';
    } else if (status === 'pending') {
        statusElement.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Čaká na platbu</span>';
    } else if (status === 'failed') {
        statusElement.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Neúspešná</span>';
    } else {
        statusElement.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    }
    
    document.getElementById('paymentDetailsModal').classList.remove('hidden');
}

function closePaymentDetails() {
    document.getElementById('paymentDetailsModal').classList.add('hidden');
}

// Zatvorenie modalu pri stlačení Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentDetails();
    }
});
</script>

<!-- Include Subscription Modal Component -->
<x-subscription-modal :isAdmin="false" :user="auth()->user()" />

@endsection 