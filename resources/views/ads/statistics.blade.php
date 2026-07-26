@extends('layouts.user-dashboard')

@section('header', 'Detail a správa inzerátu')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Hlavička -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $ad->nickname ?: 'Inzerát #' . $ad->id }}</h1>
                <p class="text-sm text-gray-500 mt-1">ID: AD-{{ $ad->id }} | {{ $ad->offer_type_label }} - {{ $ad->city }}</p>
            </div>
            <a href="{{ route('ads.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Späť na zoznam
            </a>
        </div>
    </div>

    <!-- Štatistiky karty -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Zobrazenia dnes -->
        <div class="relative bg-gradient-to-br from-pink-500 to-pink-600 overflow-hidden shadow-lg rounded-xl">
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
                        <dt class="text-sm font-medium text-pink-100">Zobrazenia dnes</dt>
                        <dd class="text-2xl font-bold text-white">{{ $stats['views_today'] }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kliky dnes -->
        <div class="relative bg-gradient-to-br from-pink-400 to-pink-500 overflow-hidden shadow-lg rounded-xl">
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
                        <dt class="text-sm font-medium text-pink-100">Kliky dnes</dt>
                        <dd class="text-2xl font-bold text-white">{{ $stats['clicks_today'] }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Zobrazenia celkovo -->
        <div class="relative bg-gradient-to-br from-pink-300 to-pink-400 overflow-hidden shadow-lg rounded-xl">
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
                        <dt class="text-sm font-medium text-pink-100">Zobrazenia celkovo</dt>
                        <dd class="text-2xl font-bold text-white">{{ number_format($stats['views_month']) }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTR -->
        <div class="relative bg-gradient-to-br from-pink-200 to-pink-300 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">CTR</dt>
                        <dd class="text-2xl font-bold text-white">{{ $stats['ctr'] }}%</dd>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailné štatistiky -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Týždenné štatistiky -->
        <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
            <div class="px-6 py-6 border-b border-pink-100">
                <h3 class="text-lg font-semibold text-gray-900">Týždenné štatistiky</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Zobrazenia tento týždeň</span>
                        <span class="text-lg font-bold text-pink-600">{{ number_format($stats['views_week']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Kliky tento týždeň</span>
                        <span class="text-lg font-bold text-pink-600">{{ number_format($stats['clicks_week']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">CTR tento týždeň</span>
                        <span class="text-lg font-bold text-pink-600">
                            {{ $stats['views_week'] > 0 ? round(($stats['clicks_week'] / $stats['views_week']) * 100, 2) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informácie o inzeráte -->
        <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
            <div class="px-6 py-6 border-b border-pink-100">
                <h3 class="text-lg font-semibold text-gray-900">Informácie o inzeráte</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Typ</span>
                        <span class="text-sm text-gray-900">{{ $ad->ad_type_label }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $ad->status === 'active' ? 'bg-pink-100 text-pink-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $ad->status_label }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Predplatné</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($ad->isSubscriptionActive()) bg-pink-100 text-pink-800
                            @elseif($ad->isSubscriptionExpired()) bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($ad->isSubscriptionActive()) Aktívne
                            @elseif($ad->isSubscriptionExpired()) Vypršalo
                            @else Neaktívne
                            @endif
                        </span>
                    </div>
                    @if($ad->top_ad)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Balíček</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                <i class="ri-vip-crown-line mr-1"></i> Premium (topované)
                            </span>
                        </div>
                    @endif
                    @if($ad->subscription_expires_at)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Vyprší</span>
                            <span class="text-sm text-gray-900">{{ $ad->subscription_expires_at->format('d.m.Y') }}</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Vyprší</span>
                            <span class="text-sm text-gray-900">Bez časového limitu</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Vytvorený</span>
                        <span class="text-sm text-gray-900">{{ $ad->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Akcie -->
    <div class="mt-8 bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <h3 class="text-lg font-semibold text-gray-900">Správa inzerátu</h3>
        </div>
        <div class="p-6 flex flex-wrap justify-center gap-4">
            <a href="{{ route('ads.edit', $ad->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Upraviť inzerát
            </a>

            @if(!$ad->isSubscriptionActive() && $classicPackage)
                <button type="button" onclick="activateFreePackage({{ $ad->id }}, {{ $classicPackage->id }})" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Aktivovať zdarma
                </button>
            @endif

            @unless($ad->top_ad)
                <button type="button" onclick="openSubscriptionModal({{ $ad->id }}, '{{ addslashes($ad->nickname ?: 'Inzerát #' . $ad->id) }}', '{{ addslashes($ad->nickname ?? '') }}')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-500 hover:bg-amber-600">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Predplatiť Premium
                </button>
            @endunless

            @if($ad->status === 'active' && $ad->isSubscriptionActive())
                <button type="button" onclick="toggleThisAdStatus()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pozastaviť
                </button>
            @elseif($ad->status === 'inactive' && $ad->isSubscriptionActive())
                <button type="button" onclick="toggleThisAdStatus()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Znova aktivovať
                </button>
            @endif
        </div>
    </div>
</div>

<x-subscription-modal :isAdmin="false" :user="auth()->user()" />

<script>
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
    .then(response => response.json())
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

function toggleThisAdStatus() {
    const currentStatus = '{{ $ad->status }}';
    const confirmText = currentStatus === 'active'
        ? 'Naozaj chcete pozastaviť tento inzerát? Nebude viditeľný pre návštevníkov, zvyšné predplatné sa zachová.'
        : 'Naozaj chcete znova aktivovať tento inzerát?';

    if (!confirm(confirmText)) {
        return;
    }

    fetch(`/inzeraty/{{ $ad->id }}/prepnut-stav`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Chyba: ' + (data.message || 'Neočakávaná chyba'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Nastala chyba pri spracovaní požiadavky: ' + error.message);
    });
}
</script>
@endsection 