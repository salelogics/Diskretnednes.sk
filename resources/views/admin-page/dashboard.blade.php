@extends('layouts.admin-dashboard')

@section('header', 'Admin Nástenka')

@section('content')
<div class="space-y-8" x-data="{ showWelcome: true }">
    <!-- Uvítacia sekcia -->
    <div x-show="showWelcome" x-transition class="relative bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl p-6 text-white shadow-xl">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold mb-2">Vitajte v administrácii, {{ auth()->user()->name }}</h1>
                <p class="text-pink-100">Prehľad systému a správa obsahu.</p>
            </div>
            <button @click="showWelcome = false" class="text-pink-200 hover:text-white transition-colors">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Štatistiky prehľad -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="ri-group-line text-xl text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Používatelia</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $usersCount }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    celkovo registrovaných
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="ri-advertisement-line text-xl text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Inzeráty</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $adsCount }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    {{ $activeAdsCount }} aktívnych
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="ri-customer-service-line text-xl text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Support tikety</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $supportTicketsCount }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    {{ $openSupportTicketsCount }} otvorených
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="ri-building-line text-xl text-orange-600"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Kluby</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $clubsCount }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    erotické kluby
                </div>
            </div>
        </div>
    </div>

    <!-- Hlavné sekcie -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Správa obsahu -->
        <div class="bg-white rounded-2xl border border-pink-500/20 shadow-xl">
            <div class="px-6 py-4 border-b border-pink-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="ri-file-text-line text-xl mr-2 text-pink-600"></i>
                    Správa obsahu
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg border border-pink-100">
                    <div class="flex items-center">
                        <i class="ri-building-line text-lg text-pink-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Erotické kluby</p>
                            <p class="text-sm text-gray-500">Spravovať kluby a ich informácie</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.kluby.index') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="ri-arrow-right-line mr-1"></i>
                        Spravovať
                    </a>
                </div>
                <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="flex items-center">
                        <i class="ri-advertisement-line text-lg text-purple-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Inzeráty</p>
                            <p class="text-sm text-gray-500">Správa všetkých inzerátov</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.inzeraty.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="ri-eye-line mr-1"></i>
                        Zobraziť
                    </a>
                </div>
                <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                    <div class="flex items-center">
                        <i class="ri-article-line text-lg text-indigo-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Články</p>
                            <p class="text-sm text-gray-500">Blog články a obsah</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.clanky.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="ri-settings-line mr-1"></i>
                        Spravovať
                    </a>
                </div>
            </div>
        </div>

        <!-- Používatelia & Nahlásenia -->
        <div class="bg-white rounded-2xl border border-pink-500/20 shadow-xl">
            <div class="px-6 py-4 border-b border-pink-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="ri-user-line text-xl mr-2 text-pink-600"></i>
                    Používatelia
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="flex items-center">
                        <i class="ri-group-line text-lg text-blue-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Zoznam používateľov</p>
                            <p class="text-sm text-gray-500">Správa registrovaných používateľov</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.pouzivatelia.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="ri-eye-line mr-1"></i>
                        Zobraziť
                    </a>
                </div>
                <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg border border-orange-100">
                    <div class="flex items-center">
                        <i class="ri-user-unfollow-line text-lg text-orange-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Nahlásenia zákazníkov</p>
                            <p class="text-sm text-gray-500">Riešiť sťažnosti zákazníkov</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.nahlasenia-zakaznikov.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="ri-tools-line mr-1"></i>
                        Riešiť
                    </a>
                </div>
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-100">
                    <div class="flex items-center">
                        <i class="ri-flag-line text-lg text-red-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Nahlásenia inzerátov</p>
                            <p class="text-sm text-gray-500">Moderovať nahlásené inzeráty</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.nahlasenia-inzeratov.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="ri-shield-check-line mr-1"></i>
                        Moderovať
                    </a>
                </div>
            </div>
        </div>

        <!-- Platby & Support -->
        <div class="bg-white rounded-2xl border border-pink-500/20 shadow-xl">
            <div class="px-6 py-4 border-b border-pink-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="ri-bank-card-line text-xl mr-2 text-pink-600"></i>
                    Platby & Support
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-100">
                    <div class="flex items-center">
                        <i class="ri-bank-card-line text-lg text-green-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Platby a faktúry</p>
                            <p class="text-sm text-gray-500">Prehľad všetkých platieb a faktúr</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.platby.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="ri-eye-line mr-1"></i>
                        Zobraziť
                    </a>
                </div>
                <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                    <div class="flex items-center">
                        <i class="ri-customer-service-line text-lg text-yellow-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Support tikety</p>
                            <p class="text-sm text-gray-500">Spracovať požiadavky používateľov</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.support-tickets.index') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="ri-eye-line mr-1"></i>
                        Zobraziť
                    </a>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex items-center">
                        <i class="ri-bar-chart-line text-lg text-gray-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">Štatistiky</p>
                            <p class="text-sm text-gray-500">Analýzy a reporty</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.statistiky.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="ri-eye-line mr-1"></i>
                        Zobraziť
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Posledné aktivity -->
    <div class="bg-white rounded-2xl border border-pink-500/20 shadow-xl">
        <div class="px-6 py-4 border-b border-pink-100">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="ri-time-line text-xl mr-2 text-pink-600"></i>
                Posledné aktivity
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @php
                    $recentUsers = \App\Models\User::latest()->take(3)->get();
                    $recentAds = \App\Models\Ad::latest()->take(3)->get();
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                            <i class="ri-user-add-line text-lg mr-2 text-blue-600"></i>
                            Najnovší používatelia
                        </h4>
                        <div class="space-y-2">
                            @forelse($recentUsers as $user)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="ri-user-line text-gray-400 mr-2"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-400 flex items-center">
                                        <i class="ri-time-line mr-1"></i>
                                        {{ $user->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm flex items-center">
                                    <i class="ri-information-line mr-2"></i>
                                    Žiadni noví používatelia
                                </p>
                            @endforelse
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                            <i class="ri-add-line text-lg mr-2 text-green-600"></i>
                            Najnovšie inzeráty
                        </h4>
                        <div class="space-y-2">
                            @forelse($recentAds as $ad)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="ri-advertisement-line text-gray-400 mr-2"></i>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $ad->nickname }}</p>
                                            <p class="text-sm text-gray-500">{{ ucfirst($ad->city) }} - {{ ucfirst($ad->ad_type) }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-400 flex items-center">
                                        <i class="ri-time-line mr-1"></i>
                                        {{ $ad->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm flex items-center">
                                    <i class="ri-information-line mr-2"></i>
                                    Žiadne nové inzeráty
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
