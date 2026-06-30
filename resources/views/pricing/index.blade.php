@extends('layouts.user-dashboard')

@section('header', 'Cenník')

@section('content')
<div class="mx-auto max-w-6xl px-6 py-8 lg:px-8">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Cenník služieb</h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Jednoduché a transparentné ceny pre vaše inzeráty. Vyberte si balíček, ktorý najlepšie vyhovuje vašim potrebám.
        </p>
    </div>

    <!-- Porovnanie balíčkov -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Classic balík -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h3 class="text-xl font-bold text-white">Classic</h3>
                <p class="text-blue-100 text-sm">Štandardné zobrazenie inzerátu</p>
            </div>
            <div class="p-6">
                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-sm">
                        <i class="ri-check-line text-green-500 mr-3"></i>
                        <span>Zobrazenie v zozname inzerátov</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="ri-check-line text-green-500 mr-3"></i>
                        <span>Štandardné umiestnenie podľa času</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="ri-check-line text-green-500 mr-3"></i>
                        <span>Plná funkcionalita inzerátu</span>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">5 dní</span>
                        <span class="font-semibold text-blue-600">10€</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">7 dní</span>
                        <span class="font-semibold text-blue-600">13€</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">30 dní</span>
                        <span class="font-semibold text-blue-600">25€</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">90 dní</span>
                        <span class="font-semibold text-blue-600">70€</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">365 dní</span>
                        <span class="font-semibold text-blue-600">200€</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium balík -->
        <div class="bg-white rounded-2xl shadow-lg border-2 border-pink-500 overflow-hidden relative">
            <div class="absolute top-4 right-4 bg-pink-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                ODPORÚČANÉ
            </div>
            <div class="bg-gradient-to-r from-pink-500 to-rose-600 px-6 py-4">
                <h3 class="text-xl font-bold text-white">Premium Topované</h3>
                <p class="text-pink-100 text-sm">Prioritné umiestnenie na vrchu</p>
            </div>
            <div class="p-6">
                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-sm">
                        <i class="ri-check-line text-green-500 mr-3"></i>
                        <span>Všetko z Classic balíčka</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="ri-vip-crown-line text-pink-500 mr-3"></i>
                        <span>Neustále na vrchu zoznamu</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="ri-star-line text-pink-500 mr-3"></i>
                        <span>Označenie ako TOPovaný</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="ri-eye-line text-pink-500 mr-3"></i>
                        <span>Výrazne vyššia viditeľnosť</span>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">5 dní</span>
                        <span class="font-semibold text-pink-600">20€</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">7 dní</span>
                        <span class="font-semibold text-pink-600">25€</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">30 dní</span>
                        <span class="font-semibold text-pink-600">40€</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">90 dní</span>
                        <span class="font-semibold text-pink-600">90€</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">365 dní</span>
                        <span class="font-semibold text-pink-600">240€</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platobné metódy -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4 text-center">Dostupné platobné metódy</h3>
        <div class="flex justify-center">
            <img src="{{ asset('images/uploads/moznosti-platby.webp') }}" alt="Možnosti platby" class="max-w-full h-auto">
        </div>
    </div>

    <!-- Informácie -->
    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
            Všetky ceny sú uvedené s DPH. Pre predplatenie inzerátu prejdite do sekcie 
            <a href="{{ route('ads.index') }}" class="text-pink-600 hover:text-pink-700 font-medium">Moje inzeráty</a>.
        </p>
    </div>
</div>
@endsection 