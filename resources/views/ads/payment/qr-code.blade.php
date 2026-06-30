@extends('layouts.app')

@section('title', 'QR platba')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-green-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-qr-code-line text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">QR platba</h1>
            <p class="text-lg text-gray-600">Naskenujte QR kód mobilnou aplikáciou banky</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- QR kód -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 text-center">
                <h2 class="text-xl font-bold text-gray-900 mb-6">QR kód na platbu</h2>
                
                <!-- QR kód placeholder -->
                <div class="bg-gray-100 rounded-2xl p-8 mb-6">
                    <div class="w-64 h-64 mx-auto bg-white rounded-lg shadow-inner flex items-center justify-center">
                        <!-- Simulovaný QR kód -->
                        <div class="grid grid-cols-8 gap-1">
                            @for($i = 0; $i < 64; $i++)
                                <div class="w-2 h-2 {{ rand(0, 1) ? 'bg-black' : 'bg-white' }} rounded-sm"></div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-center">
                        <i class="ri-smartphone-line text-green-600 text-xl mr-3"></i>
                        <div class="text-left">
                            <h3 class="font-semibold text-green-800">Naskenujte QR kód</h3>
                            <p class="text-sm text-green-700">Použite mobilnú aplikáciu vašej banky</p>
                        </div>
                    </div>
                </div>

                <!-- Podporované banky -->
                <div class="text-center">
                    <h3 class="font-semibold text-gray-900 mb-3">Podporované banky</h3>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="bg-gray-50 p-2 rounded">VÚB Banka</div>
                        <div class="bg-gray-50 p-2 rounded">Slovenská sporiteľňa</div>
                        <div class="bg-gray-50 p-2 rounded">Tatra banka</div>
                        <div class="bg-gray-50 p-2 rounded">ČSOB</div>
                        <div class="bg-gray-50 p-2 rounded">UniCredit Bank</div>
                        <div class="bg-gray-50 p-2 rounded">mBank</div>
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
                    
                    <div class="flex justify-between py-3 text-lg font-bold text-green-600">
                        <span>Celková suma:</span>
                        <span>{{ $payment->formatted_amount }}</span>
                    </div>
                </div>

                <!-- Stav platby -->
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="ri-time-line text-yellow-600 text-xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-yellow-800">Čaká na platbu</h3>
                            <p class="text-sm text-yellow-700">QR kód je platný 15 minút</p>
                        </div>
                    </div>
                    
                    <!-- Odpočítavanie -->
                    <div class="mt-3">
                        <div class="flex items-center justify-between text-sm text-yellow-700">
                            <span>Zostávajúci čas:</span>
                            <span id="countdown" class="font-mono font-bold">15:00</span>
                        </div>
                        <div class="mt-2 bg-yellow-200 rounded-full h-2">
                            <div id="progress-bar" class="bg-yellow-500 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inštrukcie -->
        <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Ako zaplatiť cez QR kód</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-green-600 font-bold text-lg">1</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Otvorte aplikáciu</h3>
                    <p class="text-sm text-gray-600">Spustite mobilnú aplikáciu vašej banky</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-green-600 font-bold text-lg">2</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Naskenujte QR kód</h3>
                    <p class="text-sm text-gray-600">Použite funkciu "QR platba" alebo "Skenovanie"</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-green-600 font-bold text-lg">3</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Potvrďte platbu</h3>
                    <p class="text-sm text-gray-600">Skontrolujte údaje a potvrďte platbu</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-green-600 font-bold text-lg">4</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Hotovo</h3>
                    <p class="text-sm text-gray-600">Platba bude spracovaná okamžite</p>
                </div>
            </div>
        </div>

        <!-- Alternatívne možnosti -->
        <div class="mt-8 bg-gray-50 rounded-2xl p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Nemôžete skenovať QR kód?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            </div>
        </div>

        <!-- Akcie -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('ads.payment.status', $payment->payment_id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors">
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
document.addEventListener('DOMContentLoaded', function() {
    // Odpočítavanie času
    let timeLeft = 15 * 60; // 15 minút v sekundách
    const countdownElement = document.getElementById('countdown');
    const progressBar = document.getElementById('progress-bar');
    const totalTime = timeLeft;

    function updateCountdown() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Aktualizácia progress baru
        const progress = (timeLeft / totalTime) * 100;
        progressBar.style.width = progress + '%';
        
        if (timeLeft <= 0) {
            // QR kód vypršal
            countdownElement.textContent = '00:00';
            progressBar.style.width = '0%';
            
            // Zobrazenie upozornenia
            const expiredNotice = document.createElement('div');
            expiredNotice.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
            expiredNotice.innerHTML = `
                <div class="flex items-center">
                    <i class="ri-time-line mr-2"></i>
                    <div>
                        <div class="font-semibold">QR kód vypršal</div>
                        <div class="text-sm">Obnovte stránku pre nový QR kód</div>
                    </div>
                </div>
            `;
            document.body.appendChild(expiredNotice);
            
            return;
        }
        
        timeLeft--;
    }

    // Spustenie odpočítavania
    updateCountdown();
    const interval = setInterval(updateCountdown, 1000);

    // Simulácia úspešnej platby po naskenovaní (pre demo účely)
    let qrScanned = false;
    
    // Simulácia skenovania po kliknutí na QR kód
    document.querySelector('.grid.grid-cols-8').addEventListener('click', function() {
        if (!qrScanned && timeLeft > 0) {
            qrScanned = true;
            clearInterval(interval);
            
            // Zobrazenie loading stavu
            const loadingNotice = document.createElement('div');
            loadingNotice.className = 'fixed top-4 right-4 bg-blue-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
            loadingNotice.innerHTML = `
                <div class="flex items-center">
                    <i class="ri-loader-4-line animate-spin mr-2"></i>
                    <div>
                        <div class="font-semibold">Spracováva sa platba</div>
                        <div class="text-sm">Čakajte prosím...</div>
                    </div>
                </div>
            `;
            document.body.appendChild(loadingNotice);
            
            // Presmerovanie po 2 sekundách
            setTimeout(() => {
                window.location.href = '{{ route("ads.payment.success", $payment->payment_id) }}';
            }, 2000);
        }
    });
});
</script>
@endsection 