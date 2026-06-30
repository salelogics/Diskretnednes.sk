@extends('layouts.user-dashboard')

@section('header', 'Nástenka')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8 space-y-8" x-data="{ showWelcome: true }">
    <!-- Uvítacia sekcia -->
    <div x-show="showWelcome" x-transition class="relative bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl p-6 text-white shadow-xl">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold mb-2">Vitajte späť, {{ auth()->user()->name }}</h1>
                <p class="text-pink-100">Prehľad vašich aktivít a najdôležitejších informácií.</p>
            </div>
            <button @click="showWelcome = false" class="text-pink-200 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Štatistiky prehľad -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl border border-pink-500/20 p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Moje inzeráty</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_ads'] }}</p>
                    <p class="text-gray-500 text-xs">{{ $stats['active_ads'] }} aktívnych</p>
                </div>
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-pink-500/20 p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Zobrazenia</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['views']) }}</p>
                    <p class="text-gray-500 text-xs">celkovo</p>
                </div>
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-pink-500/20 p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Kliky</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['clicks']) }}</p>
                    <p class="text-gray-500 text-xs">celkovo</p>
                </div>
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-pink-500/20 p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Výdavky</p>
                    <p class="text-2xl font-semibold text-gray-900">€{{ number_format($stats['total_spent'], 2) }}</p>
                    <p class="text-gray-500 text-xs">€{{ number_format($stats['monthly_spent'], 2) }} tento mesiac</p>
                </div>
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Hlavné funkcie -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Správa inzerátov -->
        <div class="bg-white rounded-2xl border border-pink-500/20 shadow-xl">
            <div class="px-6 py-4 border-b border-pink-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    Správa inzerátov
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg border border-pink-100">
                    <div>
                        <p class="font-medium text-gray-900">Vytvoriť nový inzerát</p>
                        <p class="text-sm text-gray-500">Pridajte nový inzerát s fotkami a popisom</p>
                    </div>
                    <a href="{{ route('ads.create') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                        Vytvoriť
                    </a>
                </div>
                <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg border border-pink-100">
                    <div>
                        <p class="font-medium text-gray-900">Spravovať inzeráty</p>
                        <p class="text-sm text-gray-500">Upravte, aktivujte alebo deaktivujte inzeráty</p>
                    </div>
                    <a href="{{ route('ads.index') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                        Spravovať
                    </a>
                </div>
                <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg border border-pink-100">
                    <div>
                        <p class="font-medium text-gray-900">Štatistiky inzerátov</p>
                        <p class="text-sm text-gray-500">Sledujte výkonnosť vašich inzerátov</p>
                    </div>
                    <a href="{{ route('statistics.index') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                        Zobraziť
                    </a>
                </div>
            </div>
        </div>

        <!-- Finančné služby -->
        <div class="bg-white rounded-2xl border border-pink-500/20 shadow-xl">
            <div class="px-6 py-4 border-b border-pink-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Platby & Predplatné
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg border border-pink-100">
                    <div>
                        <p class="font-medium text-gray-900">História platieb</p>
                        <p class="text-sm text-gray-500">Prehľad všetkých platieb a faktúr</p>
                    </div>
                    <a href="{{ route('payments.index') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                        Zobraziť
                    </a>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Cenník služieb</p>
                        <p class="text-sm text-gray-500">Pozrite si naše cenové balíky</p>
                    </div>
                    <a href="{{ route('pricing-dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                        Cenník
                    </a>
                </div>
                @if($subscriptionAlert)
                    @php
                        $daysLeft = $subscriptionAlert['days_left'];
                        $isExpired = $subscriptionAlert['is_expired'];
                        $bgColor = $isExpired ? 'bg-red-50 border-red-200' : ($daysLeft <= 1 ? 'bg-red-50 border-red-200' : 'bg-amber-50 border-amber-200');
                        $textColor = $isExpired ? 'text-red-800' : ($daysLeft <= 1 ? 'text-red-800' : 'text-amber-800');
                        $subtextColor = $isExpired ? 'text-red-600' : ($daysLeft <= 1 ? 'text-red-600' : 'text-amber-600');
                        $buttonColor = $isExpired ? 'bg-red-600 hover:bg-red-700' : ($daysLeft <= 1 ? 'bg-red-600 hover:bg-red-700' : 'bg-amber-600 hover:bg-amber-700');
                    @endphp
                    
                    <div class="flex items-center justify-between p-4 {{ $bgColor }} rounded-lg">
                        <div>
                            @if($isExpired)
                                @if($daysLeft == -1)
                                    <p class="font-medium {{ $textColor }}">⚠️ Predplatné expiroval včera!</p>
                                @else
                                    <p class="font-medium {{ $textColor }}">⚠️ Predplatné expiroval pred {{ abs($daysLeft) }} dňami!</p>
                                @endif
                                <p class="text-sm {{ $subtextColor }}">Inzerát "{{ $subscriptionAlert['expiring_ad']->nickname }}" - obnovte okamžite</p>
                            @elseif($daysLeft == 0)
                                <p class="font-medium {{ $textColor }}">🔥 Predplatné končí dnes!</p>
                                <p class="text-sm {{ $subtextColor }}">Inzerát "{{ $subscriptionAlert['expiring_ad']->nickname }}" - obnovte okamžite</p>
                            @elseif($daysLeft == 1)
                                <p class="font-medium {{ $textColor }}">⏰ Predplatné končí zajtra</p>
                                <p class="text-sm {{ $subtextColor }}">Inzerát "{{ $subscriptionAlert['expiring_ad']->nickname }}" - obnovte čím skôr</p>
                            @else
                                <p class="font-medium {{ $textColor }}">📅 Predplatné končí za {{ $daysLeft }} {{ $daysLeft == 2 ? 'dni' : ($daysLeft <= 4 ? 'dni' : 'dní') }}</p>
                                <p class="text-sm {{ $subtextColor }}">Inzerát "{{ $subscriptionAlert['expiring_ad']->nickname }}" - obnovte si predplatné</p>
                            @endif
                            
                            @if($subscriptionAlert['total_expiring'] > 1)
                                <p class="text-xs {{ str_replace('text-', 'text-', $subtextColor) }} opacity-75 mt-1">
                                    + {{ $subscriptionAlert['total_expiring'] - 1 }} ďalších inzerátov potrebuje pozornosť
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('ads.index') }}" class="{{ $buttonColor }} text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                            {{ $isExpired ? 'Aktivovať' : 'Obnoviť' }}
                        </a>
                    </div>
                @else
                    <!-- Ak nie je žiadny alert, zobrazíme štandardný box -->
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div>
                            <p class="font-medium text-green-800">Všetky predplatné sú aktívne</p>
                            <p class="text-sm text-green-600">Vaše inzeráty majú aktívne predplatné</p>
                        </div>
                        <a href="{{ route('ads.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                            Spravovať
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Podpora a nástroje -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Nahlásenie zákazníka</h3>
                    <p class="text-sm text-gray-500">Nahláste problematických zákazníkov</p>
                </div>
            </div>
            <a href="{{ route('customer-report.index') }}" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-md text-sm font-medium transition-colors text-center block">
                Nahlásiť zákazníka
            </a>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Podpora</h3>
                    <p class="text-sm text-gray-500">Potrebujete pomoc? Kontaktujte nás</p>
                </div>
            </div>
            <a href="{{ route('support.index') }}" class="w-full bg-pink-600 hover:bg-pink-700 text-white py-2 px-3 rounded-md text-sm font-medium transition-colors text-center block">
                Kontaktovať podporu
            </a>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Môj profil</h3>
                    <p class="text-sm text-gray-500">Upravte svoje osobné údaje</p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-md text-sm font-medium transition-colors text-center block">
                Upraviť profil
            </a>
        </div>
    </div>

    <!-- Posledná aktivita -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Posledná aktivita
            </h3>
        </div>
        <div class="p-6">
            @if($recentActivities && $recentActivities->count() > 0)
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                        <div class="flex items-center p-3 bg-pink-50 rounded-lg border border-pink-100">
                            <div class="w-2 h-2 bg-pink-500 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['time']->diffForHumans() }}</p>
                                @if(isset($activity['data']['amount']) && $activity['type'] === 'payment')
                                    <p class="text-xs text-pink-600 mt-1">{{ number_format($activity['data']['amount'], 2) }}€</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm">Zatiaľ nemáte žiadne aktivity</p>
                    <p class="text-gray-400 text-xs mt-1">Aktivity sa zobrazia po vytvorení inzerátov alebo platbách</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 