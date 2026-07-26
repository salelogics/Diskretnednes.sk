@extends('layouts.user-dashboard')

@section('header', 'Detail a správa inzerátu')

@section('content')
<div class="mx-auto max-w-5xl px-6 py-8 lg:px-8">
    <!-- Hlavička -->
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($ad->verification_image_url)
                <img src="{{ $ad->verification_image_url }}" alt="{{ $ad->nickname }}" class="w-16 h-16 rounded-xl object-cover">
            @else
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center text-white font-bold text-xl">
                    {{ substr($ad->ad_type_label, 0, 1) }}
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $ad->nickname ?: 'Inzerát #' . $ad->id }}</h1>
                <p class="text-sm text-gray-500 mt-1">ID: AD-{{ $ad->id }} &middot; {{ $ad->offer_type_label }} &middot; {{ $ad->city_label }}</p>
            </div>
        </div>
        <a href="{{ route('ads.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Späť na zoznam
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ľavý stĺpec: predplatné a správa -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Predplatné - prehľadné riadky -->
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Predplatné a stav</h3>
                    <div class="flex gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                            {{ $ad->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $ad->status === 'active' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $ad->status_label }}
                        </span>
                        @if($ad->top_ad && $ad->isSubscriptionActive())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                <i class="ri-vip-crown-line mr-1"></i> Premium
                            </span>
                        @endif
                    </div>
                </div>
                <dl class="divide-y divide-gray-100">
                    <div class="px-6 py-4 grid grid-cols-2 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Typ inzerátu</dt>
                        <dd class="text-sm text-gray-900 text-right">{{ $ad->ad_type_label }}</dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-2 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Predplatné</dt>
                        <dd class="text-sm text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($ad->isSubscriptionActive()) bg-green-100 text-green-800
                                @elseif($ad->isSubscriptionExpired()) bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-700
                                @endif">
                                @if($ad->isSubscriptionActive()) Aktívne
                                @elseif($ad->isSubscriptionExpired()) Vypršalo
                                @else Neaktívne
                                @endif
                            </span>
                        </dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-2 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Balíček</dt>
                        <dd class="text-sm text-gray-900 text-right">
                            @if($ad->top_ad && $ad->isSubscriptionActive())
                                Premium (topované)
                            @elseif($ad->isSubscriptionActive())
                                Classic
                            @else
                                &mdash;
                            @endif
                        </dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-2 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Platnosť do</dt>
                        <dd class="text-sm text-gray-900 text-right">
                            @if($ad->subscription_expires_at)
                                {{ $ad->subscription_expires_at->format('d.m.Y') }}
                                @if($ad->isSubscriptionActive())
                                    <span class="text-gray-500">({{ now()->diffInDays($ad->subscription_expires_at) }} {{ now()->diffInDays($ad->subscription_expires_at) == 1 ? 'deň' : 'dní' }})</span>
                                @endif
                            @else
                                Bez časového limitu
                            @endif
                        </dd>
                    </div>
                    <div class="px-6 py-4 grid grid-cols-2 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Vytvorený</dt>
                        <dd class="text-sm text-gray-900 text-right">{{ $ad->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Správa inzerátu -->
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Správa inzerátu</h3>
                </div>
                <div class="p-6 flex flex-wrap gap-3">
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

                    <!-- Predplatiť/Predĺžiť Premium - rovnaká akcia, len iný text podľa toho, či
                         inzerát už má bežiace topovanie (platba sa v tom prípade pripočíta k
                         zvyšnému predplatnému, nie prepíše od dnešného dňa). -->
                    <button type="button" onclick="openSubscriptionModal({{ $ad->id }}, '{{ addslashes($ad->nickname ?: 'Inzerát #' . $ad->id) }}', '{{ addslashes($ad->nickname ?? '') }}')" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-500 hover:bg-amber-600">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        {{ $ad->top_ad && $ad->isSubscriptionActive() ? 'Predĺžiť Premium' : 'Predplatiť Premium' }}
                    </button>

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

        <!-- Pravý stĺpec: štatistiky -->
        <div class="space-y-6">
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Štatistiky</h3>
                </div>
                <dl class="divide-y divide-gray-100">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Zobrazenia dnes</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ number_format($stats['views_today']) }}</dd>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Kliky dnes</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ number_format($stats['clicks_today']) }}</dd>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Zobrazenia tento týždeň</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ number_format($stats['views_week']) }}</dd>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Kliky tento týždeň</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ number_format($stats['clicks_week']) }}</dd>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Zobrazenia celkovo</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ number_format($stats['views_month']) }}</dd>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">Kliky celkovo</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ number_format($stats['clicks_month']) }}</dd>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-between">
                        <dt class="text-sm text-gray-500">CTR</dt>
                        <dd class="text-sm font-semibold text-pink-600">{{ $stats['ctr'] }}%</dd>
                    </div>
                </dl>
            </div>
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
