@extends('layouts.app')

@section('title', 'Platba úspešná')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-green-800 mb-2">Platba úspešná!</h1>
            <p class="text-gray-600 mb-6">Vaša SMS platba bola úspešne spracovaná.</p>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-green-800 mb-2">Detaily platby:</h3>
            <div class="text-sm text-green-700 space-y-1">
                <p><strong>ID platby:</strong> {{ $payment_id }}</p>
                @if($phone)
                    <p><strong>Telefónne číslo:</strong> {{ $phone }}</p>
                @endif
                <p><strong>Stav:</strong> Úspešne zaplatené</p>
                <p><strong>Dátum:</strong> {{ now()->format('d.m.Y H:i') }}</p>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">Ďalšie kroky:</h3>
            <p class="text-sm text-blue-700">
                Vaša služba bola aktivovaná. Môžete sa vrátiť na hlavnú stránku a pokračovať v používaní našich služieb.
            </p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg text-center transition duration-200">
                Späť na hlavnú stránku
            </a>
            
            @auth
                <a href="{{ route('dashboard') }}" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg text-center transition duration-200">
                    Môj účet
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection 