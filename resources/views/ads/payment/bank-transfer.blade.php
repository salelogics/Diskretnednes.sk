@extends('layouts.app')

@section('title', 'Bankový prevod')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-bank-line text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Bankový prevod</h1>
            <p class="text-lg text-gray-600">Dokončite platbu bankovým prevodom</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informácie o platbe -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Detaily platby</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-600">ID platby:</span>
                        <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $payment->payment_id }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-600">Balíček:</span>
                        <span class="font-semibold">{{ $payment->paymentPackage->name }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-600">Trvanie:</span>
                        <span class="font-semibold">{{ $payment->duration_label }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-600">Inzerát:</span>
                        <span class="font-semibold">ID {{ $payment->ad->id }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 text-lg font-bold text-blue-600">
                        <span>Celková suma:</span>
                        <span>{{ $payment->formatted_amount }}</span>
                    </div>
                </div>
            </div>

            <!-- Údaje na prevod -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Údaje na prevod</h2>
                
                <div class="space-y-4">
                    @if(\App\Models\Setting::get('invoice_company_name'))
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Príjemca</label>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-lg font-bold text-blue-600">{{ \App\Models\Setting::get('invoice_company_name') }}</span>
                            <button onclick="copyToClipboard('{{ \App\Models\Setting::get('invoice_company_name') }}')" class="text-blue-600 hover:text-blue-700">
                                <i class="ri-file-copy-line text-xl"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    @if(\App\Models\Setting::get('invoice_bank_iban'))
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">IBAN</label>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-lg font-bold text-blue-600">{{ \App\Models\Setting::get('invoice_bank_iban') }}</span>
                            <button onclick="copyToClipboard('{{ \App\Models\Setting::get('invoice_bank_iban') }}')" class="text-blue-600 hover:text-blue-700">
                                <i class="ri-file-copy-line text-xl"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    @if(\App\Models\Setting::get('invoice_bank_swift'))
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">SWIFT/BIC</label>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-lg font-bold text-blue-600">{{ \App\Models\Setting::get('invoice_bank_swift') }}</span>
                            <button onclick="copyToClipboard('{{ \App\Models\Setting::get('invoice_bank_swift') }}')" class="text-blue-600 hover:text-blue-700">
                                <i class="ri-file-copy-line text-xl"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Variabilný symbol</label>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-lg font-bold text-blue-600">{{ $payment->id }}</span>
                            <button onclick="copyToClipboard('{{ $payment->id }}')" class="text-blue-600 hover:text-blue-700">
                                <i class="ri-file-copy-line text-xl"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Suma</label>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-lg font-bold text-blue-600">{{ $payment->formatted_amount }}</span>
                            <button onclick="copyToClipboard('{{ number_format($payment->amount, 2) }}')" class="text-blue-600 hover:text-blue-700">
                                <i class="ri-file-copy-line text-xl"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Správa pre príjemcu</label>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-sm text-blue-600">Platba {{ $payment->payment_id }}</span>
                            <button onclick="copyToClipboard('Platba {{ $payment->payment_id }}')" class="text-blue-600 hover:text-blue-700">
                                <i class="ri-file-copy-line text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inštrukcie -->
        <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Inštrukcie na platbu</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-blue-600 font-bold text-lg">1</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Skopírujte údaje</h3>
                    <p class="text-sm text-gray-600">Skopírujte si údaje na prevod kliknutím na ikonu kopírovania</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-blue-600 font-bold text-lg">2</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Vykonajte prevod</h3>
                    <p class="text-sm text-gray-600">Prihláste sa do internetbankingu a vykonajte prevod</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-blue-600 font-bold text-lg">3</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Čakajte na spracovanie</h3>
                    <p class="text-sm text-gray-600">Platba bude spracovaná do 24 hodín</p>
                </div>
            </div>
        </div>

        <!-- Dôležité informácie -->
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
            <div class="flex items-start">
                <i class="ri-information-line text-yellow-600 text-xl mr-3 mt-1"></i>
                <div>
                    <h3 class="font-semibold text-yellow-800 mb-2">Dôležité informácie</h3>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Platba bude spracovaná do 24 hodín od prijatia na účet</li>
                        <li>• Nezabudnite uviesť správny variabilný symbol</li>
                        <li>• Po spracovaní platby vám bude zaslaná faktúra na email</li>
                        <li>• V prípade problémov nás kontaktujte na info@diskretnednes.sk</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Akcie -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('ads.payment.status', $payment->payment_id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                <i class="ri-eye-line mr-2"></i>
                Skontrolovať stav platby
            </a>
            
            <a href="{{ route('ads.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                <i class="ri-arrow-left-line mr-2"></i>
                Späť na inzeráty
            </a>
            
            @if(app()->environment('local'))
            <a href="{{ route('ads.payment.simulate-success', $payment->payment_id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors">
                <i class="ri-check-line mr-2"></i>
                Simulovať úspešnú platbu
            </a>
            @endif
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Zobrazenie notifikácie
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.textContent = 'Skopírované do schránky!';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 2000);
    }).catch(function(err) {
        console.error('Chyba pri kopírovaní: ', err);
    });
}
</script>
@endsection 