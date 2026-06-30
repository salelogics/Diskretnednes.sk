@extends('layouts.app')

@section('title', 'Chyba platby')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-red-800 mb-2">Chyba pri spracovaní platby</h1>
            <p class="text-gray-600 mb-6">Nastala technická chyba pri spracovaní vašej SMS platby.</p>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-red-800 mb-2">Chybová správa:</h3>
            <p class="text-sm text-red-700">{{ $message ?? 'Neznáma chyba pri spracovaní platby' }}</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">Čo robiť:</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Skúste platbu zopakovať za chvíľu</li>
                <li>• Skontrolujte internetové pripojenie</li>
                <li>• Ak problém pretrváva, kontaktujte podporu</li>
                <li>• Uveďte čas a dátum chyby pri kontaktovaní podpory</li>
            </ul>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-800 mb-2">Technické informácie:</h3>
            <div class="text-sm text-gray-600 space-y-1">
                <p><strong>Čas chyby:</strong> {{ now()->format('d.m.Y H:i:s') }}</p>
                <p><strong>Kód chyby:</strong> SMS_PROCESSING_ERROR</p>
            </div>
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