@extends('layouts.app')

@section('title', 'Platba kartou')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-purple-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ri-bank-card-line text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Platba kartou</h1>
            <p class="text-lg text-gray-600">Bezpečná platba cez Stripe</p>
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
                    
                    <div class="flex justify-between py-3 text-lg font-bold text-purple-600">
                        <span>Celková suma:</span>
                        <span>{{ $payment->formatted_amount }}</span>
                    </div>
                </div>

                <!-- Bezpečnostné informácie -->
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="ri-shield-check-line text-green-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <h3 class="font-semibold text-green-800 mb-2">Bezpečná platba</h3>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• SSL šifrovanie</li>
                                <li>• PCI DSS certifikácia</li>
                                <li>• 3D Secure overenie</li>
                                <li>• Žiadne uloženie údajov karty</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platobný formulár -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Údaje platobnej karty</h2>
                
                <form id="payment-form" class="space-y-6">
                    <!-- Stripe Elements Container -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Údaje karty</label>
                        <div id="card-element" class="p-4 border border-gray-300 rounded-lg bg-white">
                            <!-- Stripe Elements sa vložia sem -->
                        </div>
                    </div>

                    <!-- Meno držiteľa karty -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meno držiteľa karty</label>
                        <input type="text" 
                               id="card-holder" 
                               placeholder="Ján Novák"
                               value="{{ auth()->user()->name }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" 
                               id="email" 
                               placeholder="jan@example.com"
                               value="{{ auth()->user()->email }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Súhlas -->
                    <div class="flex items-start">
                        <input type="checkbox" 
                               id="terms" 
                               required
                               class="mt-1 h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="terms" class="ml-3 text-sm text-gray-600">
                            Súhlasím s <a href="#" class="text-purple-600 hover:text-purple-700">obchodnými podmienkami</a> 
                            a <a href="#" class="text-purple-600 hover:text-purple-700">zásadami ochrany osobných údajov</a>
                        </label>
                    </div>

                    <!-- Tlačidlo platby -->
                    <button type="submit" 
                            id="submit-button"
                            class="w-full bg-gradient-to-r from-purple-500 to-purple-600 text-white font-semibold py-4 px-6 rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="button-text">
                            <i class="ri-secure-payment-line mr-2"></i>
                            Zaplatiť {{ $payment->formatted_amount }}
                        </span>
                        <span id="loading-text" class="hidden">
                            <i class="ri-loader-4-line animate-spin mr-2"></i>
                            Spracováva sa...
                        </span>
                    </button>
                </form>

                <!-- Chybové hlásenie -->
                <div id="card-errors" class="mt-4 text-red-600 text-sm hidden"></div>
            </div>
        </div>

        <!-- Podporované karty -->
        <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Podporované platobné karty</h2>
            
            <div class="flex justify-center items-center space-x-6 flex-wrap">
                <img src="https://js.stripe.com/v3/fingerprinted/img/visa-729c05c240c4bdb47b03ac81d9945bfe.svg" alt="Visa" class="h-8">
                <img src="https://js.stripe.com/v3/fingerprinted/img/mastercard-4d8844094130711885b5e41b28c9848f.svg" alt="Mastercard" class="h-8">
                <img src="https://js.stripe.com/v3/fingerprinted/img/amex-a49b82f46c5cd6a96a6e418a6ca1717c.svg" alt="American Express" class="h-8">
                <img src="https://js.stripe.com/v3/fingerprinted/img/discover-ac52cd46f89fa40a29a0bfb954e33173.svg" alt="Discover" class="h-8">
            </div>
        </div>

        <!-- Akcie -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('ads.payment.packages', $payment->ad->id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                <i class="ri-arrow-left-line mr-2"></i>
                Späť na výber balíčka
            </a>
            
            <a href="{{ route('ads.payment.stripe.checkout', $payment->payment_id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-colors">
                <i class="ri-external-link-line mr-2"></i>
                Použiť Stripe Checkout
            </a>
            
            @if(app()->environment('local'))
            <a href="{{ route('ads.payment.stripe.simulate-success', $payment->payment_id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors">
                <i class="ri-check-line mr-2"></i>
                Simulovať úspešnú platbu
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Stripe JS -->
<script src="https://js.stripe.com/v3/"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializácia Stripe
            const stripe = Stripe('{{ \App\Models\Setting::get("stripe_publishable_key") ?? config("services.stripe.key") }}');
    const elements = stripe.elements();

    // Vytvorenie Card Element
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
                fontFamily: 'system-ui, -apple-system, sans-serif',
            },
            invalid: {
                color: '#9e2146',
            },
        },
    });

    cardElement.mount('#card-element');

    // Spracovanie chýb v reálnom čase
    cardElement.on('change', function(event) {
        const cardErrors = document.getElementById('card-errors');
        if (event.error) {
            showError(event.error.message);
        } else {
            hideError();
        }
    });

    // Spracovanie formulára
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const loadingText = document.getElementById('loading-text');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validácia
        const cardHolder = document.getElementById('card-holder').value;
        const email = document.getElementById('email').value;
        const terms = document.getElementById('terms').checked;

        if (!cardHolder.trim()) {
            showError('Zadajte meno držiteľa karty');
            return;
        }

        if (!email.trim() || !email.includes('@')) {
            showError('Zadajte platný email');
            return;
        }

        if (!terms) {
            showError('Musíte súhlasiť s obchodnými podmienkami');
            return;
        }

        // Zobrazenie loading stavu
        setLoading(true);
        hideError();

        try {
            // Získanie Payment Intent client secret
            const response = await fetch(`/platba/{{ $payment->payment_id }}/stripe/payment-intent`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Nepodarilo sa vytvoriť platbu');
            }

            // Potvrdenie platby cez Stripe
            const result = await stripe.confirmCardPayment(data.client_secret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: cardHolder,
                        email: email,
                    },
                }
            });

            if (result.error) {
                // Chyba pri platbe
                showError(result.error.message);
                setLoading(false);
            } else {
                // Platba úspešná
                console.log('Payment succeeded:', result.paymentIntent);
                
                // Presmerovanie na úspešnú stránku
                window.location.href = `{{ route('ads.payment.stripe.success', $payment->payment_id) }}?payment_intent=${result.paymentIntent.id}`;
            }

        } catch (error) {
            console.error('Payment error:', error);
            showError(error.message || 'Nastala chyba pri spracovaní platby');
            setLoading(false);
        }
    });

    function setLoading(loading) {
        submitButton.disabled = loading;
        if (loading) {
            buttonText.classList.add('hidden');
            loadingText.classList.remove('hidden');
        } else {
            buttonText.classList.remove('hidden');
            loadingText.classList.add('hidden');
        }
    }

    function showError(message) {
        const cardErrors = document.getElementById('card-errors');
        cardErrors.textContent = message;
        cardErrors.classList.remove('hidden');
    }

    function hideError() {
        const cardErrors = document.getElementById('card-errors');
        cardErrors.classList.add('hidden');
    }
});
</script>
@endsection 