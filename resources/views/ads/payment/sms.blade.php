@extends('layouts.app')

@section('title', 'SMS platba')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-orange-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-message-3-line text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">SMS platba</h1>
            <p class="text-lg text-gray-600">Zaplatte jednoducho cez SMS správu</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- SMS inštrukcie -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Ako zaplatiť cez SMS</h2>
                
                <!-- SMS údaje -->
                <div class="bg-orange-50 border border-orange-200 rounded-2xl p-6 mb-6">
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-orange-800 mb-4">Pošlite SMS správu</h3>
                        
                        <div class="space-y-4">
                            <div class="bg-white rounded-lg p-4 border border-orange-200">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Číslo:</label>
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-orange-600 font-mono">8866</span>
                                    <button onclick="copyToClipboard('8866')" class="text-orange-600 hover:text-orange-700">
                                        <i class="ri-file-copy-line text-xl"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-lg p-4 border border-orange-200">
                                <label class="block text-sm font-medium text-gray-700 mb-2">SMS text:</label>
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-orange-600 font-mono">ERO TEST</span>
                                    <button onclick="copyToClipboard('ERO TEST')" class="text-orange-600 hover:text-orange-700">
                                        <i class="ri-file-copy-line text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-sm text-orange-700">
                            <p><strong>Cena SMS:</strong> {{ $payment->formatted_amount }}</p>
                            <p class="text-xs mt-1">Platba bude pripísaná na váš telefónny účet</p>
                        </div>
                    </div>
                </div>

                <!-- Podporovaní operátori -->
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Podporovaní operátori</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                            <div class="font-semibold text-red-800">Telekom</div>
                            <div class="text-xs text-red-600">Všetky tarify</div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                            <div class="font-semibold text-yellow-800">Orange</div>
                            <div class="text-xs text-yellow-600">Všetky tarify</div>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-center">
                            <div class="font-semibold text-purple-800">O2</div>
                            <div class="text-xs text-purple-600">Všetky tarify</div>
                        </div>
                    </div>
                </div>

                <!-- Stav platby -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="ri-time-line text-yellow-600 text-xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-yellow-800">Čaká na SMS</h3>
                            <p class="text-sm text-yellow-700">Platba bude spracovaná do 5 minút</p>
                        </div>
                    </div>
                </div>
            </div>

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
                    
                    <div class="flex justify-between py-3 text-lg font-bold text-orange-600">
                        <span>Celková suma:</span>
                        <span>{{ $payment->formatted_amount }}</span>
                    </div>
                </div>

                <!-- Výhody SMS platby -->
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-semibold text-green-800 mb-3">Výhody SMS platby</h3>
                    <ul class="text-sm text-green-700 space-y-2">
                        <li class="flex items-center">
                            <i class="ri-check-line text-green-600 mr-2"></i>
                            Rýchle spracovanie (do 5 minút)
                        </li>
                        <li class="flex items-center">
                            <i class="ri-check-line text-green-600 mr-2"></i>
                            Žiadna registrácia potrebná
                        </li>
                        <li class="flex items-center">
                            <i class="ri-check-line text-green-600 mr-2"></i>
                            Platba cez telefónny účet
                        </li>
                        <li class="flex items-center">
                            <i class="ri-check-line text-green-600 mr-2"></i>
                            Bezpečné a overené
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Krok za krokom -->
        <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Postup platby</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-orange-600 font-bold text-lg">1</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Otvorte SMS</h3>
                    <p class="text-sm text-gray-600">Spustite aplikáciu na posielanie SMS správ</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-orange-600 font-bold text-lg">2</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Zadajte údaje</h3>
                    <p class="text-sm text-gray-600">Číslo: 8866<br>Text: {{ $smsCode }}</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-orange-600 font-bold text-lg">3</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Pošlite SMS</h3>
                    <p class="text-sm text-gray-600">Potvrďte odoslanie SMS správy</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-orange-600 font-bold text-lg">4</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Čakajte na potvrdenie</h3>
                    <p class="text-sm text-gray-600">Dostanete potvrdzujúcu SMS</p>
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
                        <li>• SMS správa musí obsahovať presne text "{{ $smsCode }}"</li>
                        <li>• Platba bude pripísaná na váš mesačný telefónny účet</li>
                        <li>• Po úspešnej platbe dostanete potvrdzujúcu SMS</li>
                        <li>• V prípade problémov kontaktujte zákaznícku podporu</li>
                        <li>• Služba je dostupná 24/7</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Alternatívne možnosti -->
        <div class="mt-8 bg-gray-50 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Iné možnosti platby</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('ads.payment.packages', $payment->ad->id) }}" 
                   class="flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                    <i class="ri-bank-line mr-2"></i>
                    Bankový prevod
                </a>
                
                <a href="{{ route('ads.payment.packages', $payment->ad->id) }}" 
                   class="flex items-center justify-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-colors">
                    <i class="ri-bank-card-line mr-2"></i>
                    Platobná karta
                </a>
                
                <a href="{{ route('ads.payment.packages', $payment->ad->id) }}" 
                   class="flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors">
                    <i class="ri-qr-code-line mr-2"></i>
                    QR platba
                </a>
            </div>
        </div>

        <!-- Akcie -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('ads.payment.status', $payment->payment_id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
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

// Simulácia SMS platby (pre demo účely)
document.addEventListener('DOMContentLoaded', function() {
    // Simulácia prijatia SMS po 10 sekundách (len pre demo)
    setTimeout(() => {
        if (Math.random() > 0.7) { // 30% šanca na simuláciu
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-blue-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="ri-message-3-line mr-2"></i>
                    <div>
                        <div class="font-semibold">SMS prijatá</div>
                        <div class="text-sm">Spracováva sa platba...</div>
                    </div>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Presmerovanie po ďalších 3 sekundách
            setTimeout(() => {
                window.location.href = '{{ route("ads.payment.success", $payment->payment_id) }}';
            }, 3000);
        }
    }, 10000);
});
</script>
@endsection 