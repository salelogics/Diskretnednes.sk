@extends('layouts.app')

@section('title', 'SMS Platba')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-center mb-6">SMS Platba</h1>
        
        <div class="mb-4">
            <p class="text-gray-600 mb-2"><strong>Popis:</strong> {{ $params['DESC'] }}</p>
            <p class="text-gray-600 mb-2"><strong>Suma:</strong> {{ $params['PRICE'] }} €</p>
            <p class="text-gray-600 mb-4"><strong>ID platby:</strong> {{ $params['ID'] }}</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">Ako platiť:</h3>
            <ol class="text-sm text-blue-700 space-y-1">
                <li>1. Kliknite na tlačidlo "Platiť SMS"</li>
                <li>2. Budete presmerovaní na PlatbaMobilom.sk</li>
                <li>3. Pošlite SMS s kódom na číslo 8866</li>
                <li>4. Po úspešnej platbe budete presmerovaní späť</li>
            </ol>
        </div>

        <form action="{{ $payment_url }}" method="POST" class="space-y-4">
            @foreach($params as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                Platiť SMS - {{ $params['PRICE'] }} €
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-gray-700 text-sm">
                ← Späť
            </a>
        </div>
    </div>
</div>
@endsection 