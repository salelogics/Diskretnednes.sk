@extends('layouts.app')

@section('title', 'Platba neúspešná')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-red-800 mb-2">Platba neúspešná</h1>
            <p class="text-gray-600 mb-6">Vaša SMS platba sa nepodarila spracovať.</p>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-red-800 mb-2">Detaily platby:</h3>
            <div class="text-sm text-red-700 space-y-1">
                <p><strong>ID platby:</strong> {{ $payment_id }}</p>
                <p><strong>Stav:</strong> 
                    @if($result === 'FAIL')
                        Platba zlyhala
                    @elseif($result === 'TIMEOUT')
                        Časový limit vypršal
                    @else
                        {{ $result }}
                    @endif
                </p>
                <p><strong>Dátum:</strong> {{ now()->format('d.m.Y H:i') }}</p>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-yellow-800 mb-2">Možné príčiny:</h3>
            <ul class="text-sm text-yellow-700 space-y-1">
                @if($result === 'TIMEOUT')
                    <li>• Neodoslali ste SMS s kódom do 3 minút</li>
                    <li>• SMS s kódom nebola doručená</li>
                @elseif($result === 'FAIL')
                    <li>• Nedostatok kreditu na telefóne</li>
                    <li>• Nesprávny overovací kód</li>
                    <li>• Technický problém u operátora</li>
                @endif
                <li>• Dočasný výpadok služby</li>
            </ul>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">Čo robiť ďalej:</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Skúste platbu zopakovať</li>
                <li>• Skontrolujte kredit na telefóne</li>
                <li>• Kontaktujte našu podporu ak problém pretrváva</li>
            </ul>
        </div>

        <div class="space-y-3">
            <button onclick="history.back()" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg text-center transition duration-200">
                Skúsiť znovu
            </button>
            
            <a href="{{ route('contact') }}" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg text-center transition duration-200">
                Kontaktovať podporu
            </a>
            
            <a href="{{ route('home') }}" class="block w-full text-gray-500 hover:text-gray-700 text-center py-2">
                Späť na hlavnú stránku
            </a>
        </div>
    </div>
</div>
@endsection 