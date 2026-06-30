@props(['isAdmin' => false, 'user' => null])

<!-- Subscription Modal -->
<div id="subscriptionModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"></div>
    
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-3xl shadow-2xl max-w-lg w-full max-h-[90vh] transform transition-all duration-300 scale-95 relative z-[10000] overflow-hidden flex flex-col">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4 text-white relative">
                <button type="button" id="closeModalBtn" class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors">
                    <i class="ri-close-line text-2xl"></i>
                </button>
                <div class="pr-8">
                    <h3 class="text-xl font-bold mb-1">Predplatiť inzerát</h3>
                    <p class="text-pink-100 text-sm" id="modalAdTitle">Načítava sa...</p>
                    <div class="text-xs text-pink-200">
                        <span id="modalAdId">ID: -</span>
                        <span id="modalAdName" class="ml-2"></span>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6 overflow-y-auto flex-1">
                
                <!-- Step 1: Package Selection -->
                <div id="step1">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="bg-pink-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm mr-3">1</span>
                        Vyberte balíček
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" class="package-btn border-2 border-gray-200 rounded-2xl p-4 text-center hover:border-pink-300 hover:bg-pink-50 transition-all duration-200" data-package="classic">
                            <div class="text-lg font-bold text-gray-900">Classic</div>
                            <div class="text-sm text-gray-600 mb-2">Štandardné</div>
                            <div class="text-pink-600 font-bold">€10 - €200</div>
                        </button>
                        <button type="button" class="package-btn border-2 border-gray-200 rounded-2xl p-4 text-center hover:border-pink-300 hover:bg-pink-50 transition-all duration-200" data-package="premium">
                            <div class="text-lg font-bold text-gray-900">Premium</div>
                            <div class="text-sm text-gray-600 mb-2">Topované</div>
                            <div class="text-pink-600 font-bold">€20 - €240</div>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Duration Selection -->
                <div id="step2" class="hidden">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="bg-pink-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm mr-3">2</span>
                        Doba trvania
                    </h4>
                    <div class="grid grid-cols-3 gap-2 max-h-96 overflow-y-auto">
                        <button type="button" class="duration-btn border-2 border-gray-200 rounded-xl p-3 text-center hover:border-pink-300 hover:bg-pink-50 transition-all duration-200" data-days="1">
                            <div class="font-semibold text-sm">1 deň</div>
                            <div class="text-pink-600 font-bold text-lg" data-price-classic="5" data-price-premium="5">€5</div>
                        </button>
                        <button type="button" class="duration-btn border-2 border-gray-200 rounded-xl p-3 text-center hover:border-pink-300 hover:bg-pink-50 transition-all duration-200" data-days="5">
                            <div class="font-semibold text-sm">5 dní</div>
                            <div class="text-pink-600 font-bold text-lg" data-price-classic="10" data-price-premium="20">€10</div>
                        </button>
                        <button type="button" class="duration-btn border-2 border-gray-200 rounded-xl p-3 text-center hover:border-pink-300 hover:bg-pink-50 transition-all duration-200" data-days="7">
                            <div class="font-semibold text-sm">7 dní</div>
                            <div class="text-pink-600 font-bold text-lg" data-price-classic="13" data-price-premium="25">€13</div>
                        </button>
                        <button type="button" class="duration-btn border-2 border-gray-200 rounded-xl p-3 text-center hover:border-pink-300 hover:bg-pink-50 transition-all duration-200" data-days="30">
                            <div class="font-semibold text-sm">30 dní</div>
                            <div class="text-pink-600 font-bold text-lg" data-price-classic="25" data-price-premium="40">€25</div>
                        </button>
                        <button type="button" class="duration-btn border-2 border-gray-200 rounded-xl p-3 text-center hover:border-pink-300 hover:bg-pink-50 transition-all duration-200" data-days="90">
                            <div class="font-semibold text-sm">90 dní</div>
                            <div class="text-pink-600 font-bold text-lg" data-price-classic="70" data-price-premium="90">€70</div>
                        </button>
                        <button type="button" class="duration-btn border-2 border-gray-200 rounded-xl p-3 text-center hover:border-pink-300 hover:bg-pink-50 transition-all duration-200" data-days="365">
                            <div class="font-semibold text-sm">365 dní</div>
                            <div class="text-pink-600 font-bold text-lg" data-price-classic="200" data-price-premium="240">€200</div>
                        </button>
                        <button type="button" id="backToStep1" class="border-2 border-gray-300 rounded-xl p-3 text-center hover:bg-gray-50 transition-all duration-200 text-gray-600">
                            <i class="ri-arrow-left-line text-xl"></i>
                            <div class="text-xs">Späť</div>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Summary & Payment -->
                <div id="step3" class="hidden">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="bg-pink-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm mr-3">3</span>
                        Súhrn a platba
                    </h4>
                    
                    <!-- Summary Card -->
                    <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-2xl p-4 mb-4 border border-pink-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold text-gray-900" id="summaryText">Classic - 5 dní</div>
                                <div class="text-sm text-gray-600">Predplatné inzerát</div>
                            </div>
                            <div class="text-2xl font-bold text-pink-600" id="summaryPrice">€5</div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="space-y-3">
                        <div class="payment-method bg-white border-2 border-gray-200 rounded-2xl p-4 cursor-pointer hover:border-green-400 hover:bg-green-50 transition-all duration-200" data-method="bank">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-green-100 p-2 rounded-xl">
                                        <i class="ri-bank-line text-xl text-green-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold">Bankový prevod</div>
                                        <div class="text-sm text-gray-600">Do 24 hodín</div>
                                    </div>
                                </div>
                                <i class="ri-arrow-right-line text-xl text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div class="payment-method bg-white border-2 border-gray-200 rounded-2xl p-4 cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition-all duration-200" data-method="sms">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-orange-100 p-2 rounded-xl">
                                        <i class="ri-message-line text-xl text-orange-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold">SMS platba</div>
                                        <div class="text-sm text-gray-600">Jednoducho</div>
                                    </div>
                                </div>
                                <i class="ri-arrow-right-line text-xl text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Free Option -->
                    @if($isAdmin)
                    <div class="mt-4">
                        <button id="adminFreeBtn" class="w-full bg-green-600 text-white rounded-2xl py-3 px-4 font-semibold hover:bg-green-700 transition-colors duration-200">
                            <i class="ri-vip-crown-line mr-2"></i>
                            Predplatiť zadarmo (Admin)
                        </button>
                    </div>
                    @endif

                    <!-- Back Button -->
                    <div class="text-center mt-4">
                        <button id="backToStep2" class="text-gray-600 hover:text-gray-800 transition-colors text-sm">
                            <i class="ri-arrow-left-line mr-1"></i>
                            Zmeniť výber
                        </button>
                    </div>
                </div>

                <!-- Step 4: Payment Details -->
                <div id="step4" class="hidden">
                    <div class="text-center mb-4">
                        <div class="bg-pink-100 p-3 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                            <i class="ri-secure-payment-line text-2xl text-pink-600"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900">Platobné údaje</h4>
                    </div>

                    <!-- Payment Details Content -->
                    <div id="paymentDetailsContent">
                        <!-- Bank Transfer -->
                        <div id="bankDetails" class="hidden space-y-3">
                            <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
                                <div class="text-center mb-4">
                                    <div class="text-2xl font-bold text-green-600" id="bankAmount">€5</div>
                                    <div class="text-sm text-gray-600">Suma na prevod</div>
                                </div>
                                <div class="space-y-3">
                                    <div class="bg-white p-3 rounded-xl">
                                        <div class="text-xs text-gray-600 mb-1">IBAN</div>
                                        <div class="font-mono font-bold">SK89 1100 0000 0026 2957 7541</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-xl">
                                        <div class="text-xs text-gray-600 mb-1">Variabilný symbol</div>
                                        <div class="font-mono font-bold" id="variableSymbol">123456</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-xl">
                                        <div class="text-xs text-gray-600 mb-1">Správa</div>
                                        <div class="font-semibold" id="bankMessage">Predplatné inzerát 123</div>
                                    </div>
                                </div>
                            </div>
                            <button id="confirmBankBtn" class="w-full bg-green-600 text-white rounded-2xl py-3 font-semibold hover:bg-green-700 transition-colors">
                                Potvrdím platbu
                            </button>
                        </div>

                        <!-- Card Payment -->
                        <div id="cardDetails" class="hidden space-y-4">
                            <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4">
                                <div class="text-center mb-4">
                                    <div class="text-2xl font-bold text-purple-600" id="cardAmount">€5</div>
                                    <div class="text-sm text-gray-600">Suma na zaplatenie</div>
                                </div>
                                
                                <!-- Údaje zákazníka -->
                                <div class="space-y-3 mb-4">
                                    <h5 class="text-sm font-semibold text-gray-700 border-b border-purple-200 pb-2">Údaje zákazníka</h5>
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Meno *</label>
                                            <input type="text" 
                                                   id="customer-first-name" 
                                                   value="{{ $user->first_name ?? '' }}"
                                                   placeholder="Ján"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-purple-500 focus:border-purple-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Priezvisko *</label>
                                            <input type="text" 
                                                   id="customer-last-name" 
                                                   value="{{ $user->last_name ?? '' }}"
                                                   placeholder="Novák"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-purple-500 focus:border-purple-500">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Email *</label>
                                        <input type="email" 
                                               id="customer-email" 
                                               value="{{ $user->email ?? '' }}"
                                               placeholder="jan@example.com"
                                               class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-purple-500 focus:border-purple-500">
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Mesto</label>
                                            <input type="text" 
                                                   id="customer-city" 
                                                   value="{{ $user->city ?? '' }}"
                                                   placeholder="Bratislava"
                                                   class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-purple-500 focus:border-purple-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Krajina</label>
                                            <select id="customer-country" 
                                                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-purple-500 focus:border-purple-500">
                                                <option value="SK" {{ ($user->country ?? 'SK') === 'SK' ? 'selected' : '' }}>SK</option>
                                                <option value="CZ" {{ ($user->country ?? '') === 'CZ' ? 'selected' : '' }}>CZ</option>
                                                <option value="AT" {{ ($user->country ?? '') === 'AT' ? 'selected' : '' }}>AT</option>
                                                <option value="HU" {{ ($user->country ?? '') === 'HU' ? 'selected' : '' }}>HU</option>
                                                <option value="PL" {{ ($user->country ?? '') === 'PL' ? 'selected' : '' }}>PL</option>
                                                <option value="DE" {{ ($user->country ?? '') === 'DE' ? 'selected' : '' }}>DE</option>
                                                <option value="US" {{ ($user->country ?? '') === 'US' ? 'selected' : '' }}>US</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Stripe Elements Inline Form -->
                                <div class="space-y-4">
                                    <h5 class="text-sm font-semibold text-gray-700 border-b border-purple-200 pb-2">Údaje karty</h5>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-2">Číslo karty, dátum expirácie a CVC</label>
                                        <div id="stripe-card-element" class="p-3 border border-gray-300 rounded-xl bg-white min-h-[50px]">
                                            <!-- Stripe Elements sa vložia sem -->
                                        </div>
                                        <div id="stripe-card-errors" class="mt-2 text-red-600 text-sm hidden"></div>
                                    </div>
                                </div>
                            </div>
                            <button id="confirmCardBtn" class="w-full bg-purple-600 text-white rounded-2xl py-3 font-semibold hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="card-button-text">Zaplatiť kartou</span>
                                <span id="card-loading-text" class="hidden">
                                    <i class="ri-loader-4-line animate-spin mr-2"></i>
                                    Spracováva sa...
                                </span>
                            </button>
                        </div>

                        <!-- SMS Payment -->
                        <div id="smsDetails" class="hidden space-y-4">
                            <div class="bg-orange-50 border border-orange-200 rounded-2xl p-4">
                                <div class="text-center space-y-4">
                                    <div class="bg-white p-4 rounded-xl">
                                        <div class="text-3xl font-bold text-orange-600 mb-1">8866</div>
                                        <div class="text-sm text-gray-600">Pošlite SMS na číslo</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-xl">
                                        <div class="text-xl font-mono font-bold text-gray-900" id="smsText">ERO FXO</div>
                                        <div class="text-sm text-gray-600">Text SMS správy</div>
                                    </div>
                                    <div class="bg-white p-4 rounded-xl">
                                        <div class="text-2xl font-bold text-orange-600" id="smsAmount">€10</div>
                                        <div class="text-sm text-gray-600">Cena SMS</div>
                                    </div>
                                </div>
                                

                                
                                <!-- Verifikačný kód input -->
                                <div class="bg-white border border-gray-200 rounded-xl p-4 mt-4">
                                    <label for="verificationCode" class="block text-sm font-medium text-gray-700 mb-2">
                                        Zadajte 6-miestny verifikačný kód:
                                    </label>
                                    <div class="flex gap-2">
                                        <input 
                                            type="text" 
                                            id="verificationCode" 
                                            name="verificationCode"
                                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-center text-lg font-mono tracking-widest focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                            placeholder="123456"
                                            maxlength="6"
                                            pattern="[0-9]{6}"
                                            autocomplete="off"
                                        >
                                        <button 
                                            type="button" 
                                            id="verifyCodeBtn" 
                                            class="px-6 py-3 bg-orange-600 text-white rounded-lg font-semibold hover:bg-orange-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            disabled
                                        >
                                            Overiť
                                        </button>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-2">
                                        Verifikačný kód je platný 25 minút
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="text-center mt-4">
                        <button id="backToStep3" class="text-gray-600 hover:text-gray-800 transition-colors text-sm">
                            <i class="ri-arrow-left-line mr-1"></i>
                            Zmeniť platbu
                        </button>
                    </div>
                </div>

                <!-- Success -->
                <div id="successStep" class="hidden text-center py-6">
                    <div class="bg-green-100 p-4 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <i class="ri-check-line text-3xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Úspešne dokončené!</h3>
                    <p class="text-gray-600 mb-4">Váš inzerát bol predplatený.</p>
                    <button id="successCloseBtn" class="bg-green-600 text-white rounded-2xl py-2 px-6 font-semibold hover:bg-green-700 transition-colors">
                        Zavrieť
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('subscriptionModal');
    let currentAdId = null;
    let selectedPackage = null;
    let selectedDays = null;
    let selectedPrice = 0;
    
    // Stripe Elements initialization - presunuté na začiatok
    let stripe = null;
    let elements = null;
    let cardElement = null;
    let stripeInitialized = false;

    const prices = {
        classic: { 1: 5, 5: 10, 7: 13, 30: 25, 90: 70, 365: 200 },
        premium: { 1: 5, 5: 20, 7: 25, 30: 40, 90: 90, 365: 240 }
    };



    // Global function to open modal
    window.openSubscriptionModal = function(adId, title, adName) {
        currentAdId = adId || Math.floor(Math.random() * 1000);
        const displayTitle = title || 'Inzerát #' + currentAdId;
        const displayName = adName || '';
        
        document.getElementById('modalAdTitle').textContent = displayTitle;
        document.getElementById('modalAdId').textContent = 'ID: ' + currentAdId;
        document.getElementById('modalAdName').textContent = displayName ? '• ' + displayName : '';
        
        resetModal();
        showStep(1);
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('.modal-content').classList.remove('scale-95');
            modal.querySelector('.modal-content').classList.add('scale-100');
        }, 10);
        

    };

    function resetModal() {
        selectedPackage = null;
        selectedDays = null;
        selectedPrice = 0;
        
        // Reset selections
        document.querySelectorAll('.package-btn').forEach(el => el.classList.remove('border-pink-500', 'bg-pink-100'));
        document.querySelectorAll('.duration-btn').forEach(el => el.classList.remove('border-pink-500', 'bg-pink-100'));
        document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('border-green-500', 'bg-green-100', 'border-purple-500', 'bg-purple-100', 'border-orange-500', 'bg-orange-100'));
    }

    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('[id^="step"]').forEach(el => el.classList.add('hidden'));
        document.getElementById('successStep').classList.add('hidden');
        
        // Show current step
        if (step <= 4) {
            document.getElementById('step' + step).classList.remove('hidden');
        } else {
            document.getElementById('successStep').classList.remove('hidden');
        }
    }

    function closeModal() {
        modal.querySelector('.modal-content').classList.remove('scale-100');
        modal.querySelector('.modal-content').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Global close modal function
    window.closeSubscriptionModal = closeModal;

    function updatePrices() {
        if (selectedPackage) {
            document.querySelectorAll('.duration-btn').forEach(btn => {
                const days = btn.dataset.days;
                if (days && prices[selectedPackage] && prices[selectedPackage][days]) {
                    const price = prices[selectedPackage][days];
                    const priceEl = btn.querySelector('[data-price-classic]');
                    if (priceEl) {
                        priceEl.textContent = '€' + price;
                    }
                }
            });
        }
    }

    // Package selection
    document.querySelectorAll('.package-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.package-btn').forEach(b => b.classList.remove('border-pink-500', 'bg-pink-100'));
            this.classList.add('border-pink-500', 'bg-pink-100');
            selectedPackage = this.dataset.package;
            updatePrices();
            setTimeout(() => showStep(2), 200);
        });
    });

    // Duration selection
    document.querySelectorAll('.duration-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.id === 'backToStep1') return;
            
            document.querySelectorAll('.duration-btn').forEach(b => b.classList.remove('border-pink-500', 'bg-pink-100'));
            this.classList.add('border-pink-500', 'bg-pink-100');
            selectedDays = parseInt(this.dataset.days);
            selectedPrice = prices[selectedPackage][selectedDays];
            
            // Update summary
            const packageName = selectedPackage === 'classic' ? 'Classic' : 'Premium';
            const daysText = selectedDays === 1 ? '1 deň' : selectedDays + ' dní';
            document.getElementById('summaryText').textContent = packageName + ' - ' + daysText;
            document.getElementById('summaryPrice').textContent = '€' + selectedPrice;
            
            // Skryť SMS platbu, ak je cena vyššia ako 20 eur
            const smsMethod = document.querySelector('.payment-method[data-method="sms"]');
            if (selectedPrice > 20 && smsMethod) {
                smsMethod.style.display = 'none';
            } else if (smsMethod) {
                smsMethod.style.display = 'block';
            }
            
            setTimeout(() => showStep(3), 200);
        });
    });

    // Payment method selection
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            const paymentType = this.dataset.method;
            
            // Reset payment methods
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('border-green-500', 'bg-green-100', 'border-purple-500', 'bg-purple-100', 'border-orange-500', 'bg-orange-100');
            });
            
            // Highlight selected
            if (paymentType === 'bank') {
                this.classList.add('border-green-500', 'bg-green-100');
                showBankDetails();
            } else if (paymentType === 'card') {
                // Stripe dočasne deaktivovaný
                alert('Platba kartou je dočasne nedostupná. Prosíme použite bankový prevod alebo SMS.');
                return;
            } else if (paymentType === 'sms') {
                this.classList.add('border-orange-500', 'bg-orange-100');
                showSmsDetails();
            }
            
            setTimeout(() => showStep(4), 200);
        });
    });

    function showBankDetails() {
        // Hide all payment details
        document.getElementById('bankDetails').classList.add('hidden');
        document.getElementById('cardDetails').classList.add('hidden');
        document.getElementById('smsDetails').classList.add('hidden');
        
        // Show bank details with proper values
        const safeAdId = currentAdId || 1000;
        const variableSymbol = safeAdId.toString() + Date.now().toString().slice(-6);
        document.getElementById('variableSymbol').textContent = variableSymbol;
        document.getElementById('bankAmount').textContent = '€' + (selectedPrice || 0);
        document.getElementById('bankMessage').textContent = 'Predplatné inzerát ' + safeAdId;
        document.getElementById('bankDetails').classList.remove('hidden');
    }

    function showCardDetails() {
        // Hide all payment details
        document.getElementById('bankDetails').classList.add('hidden');
        document.getElementById('cardDetails').classList.add('hidden');
        document.getElementById('smsDetails').classList.add('hidden');
        
        // Show card details
        document.getElementById('cardAmount').textContent = '€' + (selectedPrice || 0);
        document.getElementById('cardDetails').classList.remove('hidden');
        
        // Initialize Stripe Elements when card section becomes visible
        setTimeout(() => {
                // Debug logs removed for production security
            
            if (typeof Stripe !== 'undefined' && !stripeInitialized) {
                // Initializing Stripe Elements for card payment...
                initializeStripe();
            } else if (typeof Stripe === 'undefined') {
                console.error('Stripe SDK not loaded, attempting to load...');
                loadStripeSDK();
            } else if (stripeInitialized) {
                // Stripe already initialized
            }
        }, 500);
    }
    
    function loadStripeSDK() {
                    // Loading Stripe SDK...
        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        script.onload = () => {
            // Stripe SDK loaded successfully
            setTimeout(() => {
                if (typeof Stripe !== 'undefined') {
                    initializeStripe();
                } else {
                    console.error('Stripe still not available after loading');
                }
            }, 100);
        };
        script.onerror = () => {
            console.error('Failed to load Stripe SDK');
            showStripeError('Nepodarilo sa načítať Stripe SDK. Skúste obnoviť stránku.');
        };
        document.head.appendChild(script);
    }

    async function showSmsDetails() {
        // Hide all payment details first
        document.getElementById('bankDetails').classList.add('hidden');
        document.getElementById('cardDetails').classList.add('hidden');
        document.getElementById('smsDetails').classList.add('hidden');
        
        console.log('📱 Inicializujem SMS platbu pre balíček:', selectedPackage, selectedDays, 'dní');
        
        // Show SMS section
        document.getElementById('smsDetails').classList.remove('hidden');
        document.getElementById('smsAmount').textContent = '€' + selectedPrice;
        
        // Mapovanie na package kódy - iba pre dostupné SMS balíčky
        const packageCodeMap = {
            'classic_1': 'FXO',
            'classic_7': 'FXW', 
            'classic_30': 'FXM'
        };
        
        const packageKey = selectedPackage + '_' + selectedDays;
        const packageCode = packageCodeMap[packageKey];
        
            if (!packageCode) {
            console.error('❌ SMS platba nie je dostupná pre balíček:', packageKey);
            alert(`SMS platba nie je dostupná pre ${selectedPackage.toUpperCase()} ${selectedDays} ${selectedDays === 1 ? 'deň' : 'dní'}.\n\nSMS platba je dostupná iba pre:\n• Classic 1 deň (€5.00)\n• Classic 7 dní (€13.00)\n• Classic 30 dní (€25.00)\n\nPre ostatné balíčky použite bankový prevod.`);
            return;
        }

        // Zobrazíme SMS inštrukcie
        const smsInstruction = `ERO ${packageCode}`;
        
        // Bezpečné nastavenie textContent - kontrolujeme existenciu elementov
        const smsTextElement = document.getElementById('smsText');
        if (smsTextElement) {
            smsTextElement.textContent = smsInstruction;
        }
        
        const smsCodeDisplayElement = document.getElementById('smsCodeDisplay');
        if (smsCodeDisplayElement) {
            smsCodeDisplayElement.textContent = smsInstruction;
        }
        
        // Aktualizujeme verifikačné informácie
        const verificationInfo = document.getElementById('smsVerificationInfo');
        if (verificationInfo) {
            verificationInfo.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mt-3">
                    <div class="text-sm font-medium text-green-800 mb-2">
                        📱 SMS platba - ${selectedPackage.toUpperCase()} ${selectedDays} ${selectedDays === 1 ? 'deň' : 'dni'} za €${selectedPrice}
                    </div>
                    <div class="text-xs text-green-700">
                        <div class="mb-1">1. Pošlite SMS s textom: <strong>${smsInstruction}</strong></div>
                        <div class="mb-1">2. Na číslo: <strong>8866</strong></div>
                        <div class="mb-1">3. Dostanete verifikačný kód</div>
                        <div>4. Zadajte kód do formulára nižšie</div>
                    </div>
                </div>
            `;
        }
        
        console.log('✅ SMS inštrukcie zobrazené pre package:', packageCode);
    }

    async function createRealPayment(paymentMethod) {
        // createRealPayment called with method: [paymentMethod]
        
        if (!selectedPackage || !selectedDays || !currentAdId) {
            alert('Chyba: Chýbajú potrebné údaje pre platbu');
            return;
        }

        // Pre Stripe platby použijeme špeciálny workflow
        if (paymentMethod === 'stripe') {
            // Redirecting to handleStripePayment...
            handleStripePayment();
            return;
        }

        // Nájdeme package_id na základe typu a dní
        const packageId = await getPackageId(selectedPackage, selectedDays);
        if (!packageId) {
            alert('Chyba: Nepodarilo sa nájsť balíček');
            return;
        }

                    // Creating non-Stripe payment...

        // Vytvoríme platbu cez API pre ostatné metódy
        fetch(`/inzeraty/${currentAdId}/platba`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                package_id: packageId,
                payment_method: paymentMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            // Non-Stripe payment response processed
            if (data.success) {
                if (paymentMethod === 'bank_transfer') {
                    // Pre bankový prevod zobrazíme správu o čakaní na potvrdenie
                    showBankTransferPending();
                } else {
                    // Pre ostatné platby zobrazíme úspech
                    showSuccess();
                }
            } else {
                alert('Chyba pri vytváraní platby: ' + (data.message || data.error || 'Neznáma chyba'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Chyba pri komunikácii so serverom');
        });
    }

    async function getPackageId(packageType, days) {
        try {
            // Fetching package ID...
            
            const response = await fetch(`/api/package-id/${packageType}/${days}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            // API response received
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('API error response:', errorText);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            // Package ID response processed
            
            if (data.package_id) {
                return data.package_id;
            }
            
            console.error('Package not found:', data);
            return null;
        } catch (error) {
            console.error('Error getting package ID:', error);
            return null;
        }
    }

    function showBankTransferPending() {
        showStep(5);
        
        // Upravíme text pre bankový prevod
        const successStep = document.getElementById('successStep');
        successStep.innerHTML = `
            <div class="text-center py-6">
                <div class="bg-yellow-100 p-4 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                    <i class="ri-time-line text-3xl text-yellow-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Platba vytvorená!</h3>
                <p class="text-gray-600 mb-4">Váš inzerát bude aktivovaný po potvrdení platby administrátorom (do 24 hodín).</p>
                <button id="successCloseBtn" class="bg-yellow-600 text-white rounded-2xl py-2 px-6 font-semibold hover:bg-yellow-700 transition-colors">
                    Zavrieť
                </button>
            </div>
        `;
        
        // Znovu pridáme event listener pre nové tlačidlo
        document.getElementById('successCloseBtn').addEventListener('click', closeModal);
        
        setTimeout(() => {
            closeModal();
        }, 5000); // Dlhší čas pre prečítanie
    }

    function showSuccess() {
        showStep(5);
        
        // Aktualizuj stav inzerátu po úspešnej platbe
        refreshAdStatus();
        
        setTimeout(() => {
            closeModal();
        }, 3000);
    }
    
    // Funkcia na aktualizáciu stavu inzerátu po platbe
    async function refreshAdStatus() {
        if (!currentAdId) return;
        
        try {
            console.log('🔄 Refreshing ad status for ID:', currentAdId);
            
            const response = await fetch(`/api/ads/${currentAdId}/status`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                console.log('✅ Ad status data:', data);
                
                // Aktualizuj predplatné badgy na stránke
                updateSubscriptionBadges(data);
                
                // Ak sme na detaile inzerátu, aktualizuj jeho stav
                updateAdDetailStatus(data);
                
                // Aktualizuj subscription tlačidlá ak existujú
                updateSubscriptionButtons(data);
                
                console.log('Ad status refreshed successfully');
            } else {
                console.warn('Failed to fetch ad status:', response.status);
            }
        } catch (error) {
            console.error('Failed to refresh ad status:', error);
        }
    }
    
    // Aktualizácia predplatných badgov
    function updateSubscriptionBadges(adData) {
        // Aktualizuj všetky subscription badgy pre tento inzerát
        const adCards = document.querySelectorAll(`[data-ad-id="${adData.id}"]`);
        
        adCards.forEach(card => {
            const subscriptionBadge = card.querySelector('.subscription-badge');
            const premiumIcon = card.querySelector('.premium-icon');
            
            if (adData.subscription_status === 'active') {
                // Pridaj/aktualizuj premium badge
                if (!subscriptionBadge) {
                    const badge = document.createElement('div');
                    badge.className = 'subscription-badge absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full font-bold z-10';
                    badge.innerHTML = '⭐ PREMIUM';
                    card.style.position = 'relative';
                    card.appendChild(badge);
                }
                
                // Pridaj premium efekty
                card.classList.add('premium-ad');
                card.style.boxShadow = '0 0 20px rgba(255, 215, 0, 0.5)';
                card.style.border = '2px solid #ffd700';
                
            } else if (subscriptionBadge) {
                // Odstráň premium badge ak predplatné vypršalo
                subscriptionBadge.remove();
                card.classList.remove('premium-ad');
                card.style.boxShadow = '';
                card.style.border = '';
            }
        });
    }
    
    // Aktualizácia stavu na detaile inzerátu
    function updateAdDetailStatus(adData) {
        // Ak sme na detaile inzerátu, aktualizuj UI prvky
        const adDetailContainer = document.querySelector('.ad-detail-container');
        if (adDetailContainer) {
            // Aktualizuj predplatné informácie
            const subscriptionInfo = document.querySelector('.subscription-info');
            if (subscriptionInfo && adData.subscription_status === 'active') {
                subscriptionInfo.innerHTML = `
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="text-yellow-600 mr-3">⭐</div>
                            <div>
                                <div class="font-semibold text-yellow-800">Premium inzerát</div>
                                <div class="text-sm text-yellow-700">Aktívne do ${adData.subscription_expires_at || 'neuvedené'}</div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
    }

    async function createAdminFreePayment() {
        if (!selectedPackage || !selectedDays || !currentAdId) {
            alert('Chyba: Chýbajú potrebné údaje pre platbu');
            return;
        }

        const packageId = await getPackageId(selectedPackage, selectedDays);
        if (!packageId) {
            alert('Chyba: Nepodarilo sa nájsť balíček');
            return;
        }

        // Admin free platba cez špecifický endpoint
        fetch(`/admin/ads/${currentAdId}/subscribe-free`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                package_id: packageId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess();
            } else {
                alert('Chyba pri vytváraní admin platby: ' + (data.message || data.error || 'Neznáma chyba'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Chyba pri komunikácii so serverom');
        });
    }

    // Event listeners for buttons - s null checks
    const closeModalBtn = document.getElementById('closeModalBtn');
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    
    const backToStep1 = document.getElementById('backToStep1');
    if (backToStep1) backToStep1.addEventListener('click', () => showStep(1));
    
    const backToStep2 = document.getElementById('backToStep2');
    if (backToStep2) backToStep2.addEventListener('click', () => showStep(2));
    
    const backToStep3 = document.getElementById('backToStep3');
    if (backToStep3) backToStep3.addEventListener('click', () => showStep(3));
    
    const successCloseBtn = document.getElementById('successCloseBtn');
    if (successCloseBtn) successCloseBtn.addEventListener('click', closeModal);
    
    const confirmBankBtn = document.getElementById('confirmBankBtn');
    if (confirmBankBtn) confirmBankBtn.addEventListener('click', () => createRealPayment('bank_transfer'));
    
    const confirmCardBtn = document.getElementById('confirmCardBtn');
    if (confirmCardBtn) confirmCardBtn.addEventListener('click', () => createRealPayment('stripe'));
    
    const confirmSmsBtn = document.getElementById('confirmSmsBtn');
    if (confirmSmsBtn) confirmSmsBtn.addEventListener('click', () => createRealPayment('sms'));
    
    @if($isAdmin)
    const adminFreeBtn = document.getElementById('adminFreeBtn');
    if (adminFreeBtn) adminFreeBtn.addEventListener('click', () => createAdminFreePayment());
    @endif

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Close modal on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('backdrop-blur-sm')) {
            closeModal();
        }
    });

    // Prevent modal content clicks from closing modal
    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Test Stripe availability on page load - Debug logs removed for production security
    
    if (typeof Stripe !== 'undefined') {
        // Stripe SDK loaded successfully
    } else {
        console.error('Stripe SDK not available on page load');
    }



    function initializeStripe() {
        if (stripeInitialized) {
            // Stripe already initialized
            return;
        }

        if (typeof Stripe === 'undefined') {
            console.error('Stripe SDK not loaded, attempting to load...');
            // Pokúsime sa načítať Stripe SDK dynamicky
            const script = document.createElement('script');
            script.src = 'https://js.stripe.com/v3/';
            script.onload = () => {
                // Stripe SDK loaded dynamically
                setTimeout(() => initializeStripe(), 500);
            };
            script.onerror = () => {
                console.error('Failed to load Stripe SDK dynamically');
                showStripeError('Nepodarilo sa načítať Stripe SDK. Skúste obnoviť stránku.');
            };
            document.head.appendChild(script);
            return;
        }

        try {
            // Creating Stripe instance
            stripe = Stripe('{{ config("services.stripe.key") ?? '' }}');
            elements = stripe.elements();

            // Skontrolujeme či element existuje
            const cardElementContainer = document.getElementById('stripe-card-element');
            if (!cardElementContainer) {
                console.error('Card element container not found');
                return;
            }

            // Skontrolujeme či je element viditeľný
            const isVisible = cardElementContainer.offsetParent !== null;
            // Card element container visibility checked

            // Create card element with styling
            cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#424770',
                        '::placeholder': {
                            color: '#aab7c4',
                        },
                        fontFamily: 'system-ui, -apple-system, sans-serif',
                        padding: '12px',
                    },
                    invalid: {
                        color: '#9e2146',
                    },
                },
                hidePostalCode: true
            });

            // Mounting card element
            // Mount card element
            cardElement.mount('#stripe-card-element');

            // Handle real-time validation errors
            cardElement.on('change', function(event) {
                // Card element change event
                if (event.error) {
                    showStripeError(event.error.message);
                } else {
                    hideStripeError();
                }
            });

            cardElement.on('ready', function() {
                // Card element is ready
            });

            stripeInitialized = true;
            // Stripe Elements initialized successfully

        } catch (error) {
            console.error('Error initializing Stripe:', error);
        }
    }

    function showStripeError(message) {
        const cardErrors = document.getElementById('stripe-card-errors');
        cardErrors.textContent = message;
        cardErrors.classList.remove('hidden');
    }

    function hideStripeError() {
        const cardErrors = document.getElementById('stripe-card-errors');
        cardErrors.classList.add('hidden');
    }

    function setCardButtonLoading(loading) {
        const button = document.getElementById('confirmCardBtn');
        const buttonText = document.getElementById('card-button-text');
        const loadingText = document.getElementById('card-loading-text');
        
        button.disabled = loading;
        if (loading) {
            buttonText.classList.add('hidden');
            loadingText.classList.remove('hidden');
        } else {
            buttonText.classList.remove('hidden');
            loadingText.classList.add('hidden');
        }
    }

    async function handleStripePayment() {
        // handleStripePayment called - Debug logs removed for production security

        if (!stripe || !cardElement || !stripeInitialized) {
            console.error('Stripe not properly initialized:', {
                stripe: !!stripe,
                cardElement: !!cardElement,
                stripeInitialized: stripeInitialized
            });
            alert('Stripe nie je správne inicializovaný. Skúste obnoviť stránku.');
            return;
        }

        if (!selectedPackage || !selectedDays || !currentAdId) {
            alert('Chyba: Chýbajú potrebné údaje pre platbu');
            return;
        }

        // Získaj údaje zákazníka z formulára
        const customerData = {
            firstName: document.getElementById('customer-first-name').value.trim(),
            lastName: document.getElementById('customer-last-name').value.trim(),
            email: document.getElementById('customer-email').value.trim(),
            city: document.getElementById('customer-city').value.trim(),
            country: document.getElementById('customer-country').value
        };

        // Validácia povinných polí
        if (!customerData.firstName || !customerData.lastName || !customerData.email) {
            showStripeError('Vyplňte prosím meno, priezvisko a email.');
            return;
        }

        if (!customerData.email.includes('@')) {
            showStripeError('Zadajte platný email.');
            return;
        }

        // Starting Stripe payment process...
        setCardButtonLoading(true);
        hideStripeError();

        try {
            // 1. Create payment in backend
            const packageId = await getPackageId(selectedPackage, selectedDays);
            if (!packageId) {
                throw new Error('Nepodarilo sa nájsť balíček');
            }

            // Creating Stripe payment...

            const createResponse = await fetch(`/inzeraty/${currentAdId}/platba`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    package_id: packageId,
                    payment_method: 'stripe',
                    customer_data: customerData
                })
            });

            let createData;
            try {
                createData = await createResponse.json();
            } catch (e) {
                console.error('Failed to parse JSON response:', e);
                throw new Error('Server vrátil neplatnú odpoveď. Skúste to znova.');
            }
            
            // Payment creation response processed

            if (!createData.success) {
                throw new Error(createData.message || createData.error || 'Nepodarilo sa vytvoriť platbu');
            }

            // 2. Confirm payment with Stripe
            
            // Pripravíme billing details - len neprázdne polia
            const billingDetails = {
                name: `${customerData.firstName} ${customerData.lastName}`,
                email: customerData.email
            };
            
            // Pridáme adresu len ak máme nejaké údaje
            if (customerData.city || customerData.country) {
                billingDetails.address = {};
                if (customerData.city) billingDetails.address.city = customerData.city;
                if (customerData.country) billingDetails.address.country = customerData.country;
            }
            
            // Billing details prepared
            
            const result = await stripe.confirmCardPayment(createData.client_secret, {
                payment_method: {
                    card: cardElement,
                    billing_details: billingDetails
                }
            });

            // Stripe confirmation result processed

            if (result.error) {
                // Payment failed
                console.error('Stripe payment failed:', result.error);
                showStripeError(result.error.message);
                setCardButtonLoading(false);
            } else {
                // Payment succeeded
                
                // 3. Verify payment on backend
                const verifyResponse = await fetch(`/platba/${createData.payment_id}/stripe/confirm`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        payment_intent_id: result.paymentIntent.id
                    })
                });

                let verifyData;
                try {
                    verifyData = await verifyResponse.json();
                } catch (e) {
                    console.error('Failed to parse verification JSON response:', e);
                    throw new Error('Platba bola úspešná, ale nepodarilo sa potvrdiť. Skontrolujte svoj účet.');
                }
                
                // Payment verification response processed

                if (verifyData.success) {
                    showSuccess();
                } else {
                    throw new Error('Platba bola spracovaná, ale nepodarilo sa ju potvrdiť. Kontaktujte podporu.');
                }
            }

        } catch (error) {
            console.error('Stripe payment error:', error);
            showStripeError(error.message || 'Nastala chyba pri spracovaní platby');
            setCardButtonLoading(false);
        }
    }

    // SMS Verification Code functionality
    const verificationCodeInput = document.getElementById('verificationCode');
    const verifyCodeBtn = document.getElementById('verifyCodeBtn');

    if (verificationCodeInput && verifyCodeBtn) {
        // Enable/disable verify button based on input
        verificationCodeInput.addEventListener('input', function() {
            const code = this.value.trim();
            const isValid = /^\d{6}$/.test(code);
            verifyCodeBtn.disabled = !isValid;
            
            // Auto-submit when 6 digits are entered
            if (isValid) {
                setTimeout(() => {
                    verifyCodeBtn.click();
                }, 500);
            }
        });

        // Handle verification
        verifyCodeBtn.addEventListener('click', async function() {
            const code = verificationCodeInput.value.trim();
            
            if (!/^\d{6}$/.test(code)) {
                alert('Zadajte platný 6-miestny kód');
                return;
            }

            this.disabled = true;
            this.textContent = 'Overujem...';

            try {
                const response = await fetch('/sms/verify-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        code: code
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Show success message
                    const verificationDiv = verificationCodeInput.closest('.bg-white');
                    verificationDiv.innerHTML = `
                        <div class="text-center py-4">
                            <div class="bg-green-100 p-3 rounded-full w-12 h-12 mx-auto mb-2 flex items-center justify-center">
                                <i class="ri-check-line text-xl text-green-600"></i>
                            </div>
                            <div class="text-green-800 font-semibold">Kód overený!</div>
                            <div class="text-sm text-green-600">Platba bola úspešne aktivovaná</div>
                        </div>
                    `;
                    
                    // Hide SMS button and show success
                    const confirmSmsBtn = document.getElementById('confirmSmsBtn');
                    if (confirmSmsBtn) {
                        confirmSmsBtn.style.display = 'none';
                    }
                    
                    setTimeout(() => {
                        showSuccess();
                        
                        // AJAX refresh stavu inzerátu
                        refreshAdStatus();
                        
                        // Backup: reload stránky po 4 sekundách ak AJAX nefunguje
                        setTimeout(() => {
                            window.location.reload();
                        }, 4000);
                    }, 2000);
                } else {
                    // Show error message in UI instead of ugly alert
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mt-3';
                    errorDiv.innerHTML = `
                        <div class="flex items-center">
                            <i class="ri-error-warning-line mr-2"></i>
                            <span>${data.message || 'Neplatný alebo expirovaný verifikačný kód'}</span>
                        </div>
                    `;
                    
                    // Remove any existing error messages
                    const parentContainer = verificationCodeInput.closest('.bg-white');
                    if (parentContainer) {
                        const existingError = parentContainer.querySelector('.bg-red-100');
                        if (existingError) {
                            existingError.remove();
                        }
                        
                        // Add error message after input
                        parentContainer.appendChild(errorDiv);
                    }
                    
                    // Remove error after 5 seconds
                    setTimeout(() => {
                        if (errorDiv && errorDiv.parentNode) {
                            errorDiv.remove();
                        }
                    }, 5000);
                    
                    this.disabled = false;
                    this.textContent = 'Overiť';
                    verificationCodeInput.focus();
                }
            } catch (error) {
                console.error('Verification error:', error);
                
                // Show error in UI instead of alert
                const errorDiv = document.createElement('div');
                errorDiv.className = 'bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mt-3';
                errorDiv.innerHTML = `
                    <div class="flex items-center">
                        <i class="ri-error-warning-line mr-2"></i>
                        <span>Chyba pri overovaní kódu. Skúste to znova.</span>
                    </div>
                `;
                
                const parentContainer = verificationCodeInput.closest('.bg-white');
                if (parentContainer) {
                    const existingError = parentContainer.querySelector('.bg-red-100');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    parentContainer.appendChild(errorDiv);
                }
                
                setTimeout(() => {
                    if (errorDiv && errorDiv.parentNode) {
                        errorDiv.remove();
                    }
                }, 5000);
                
                this.disabled = false;
                this.textContent = 'Overiť';
            }
        });

        // Allow only numbers
        verificationCodeInput.addEventListener('keypress', function(e) {
            if (!/\d/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
        });
    }
    
    // Aktualizuj subscription tlačidlá a indikátory
    function updateSubscriptionButtons(adData) {
        // Aktualizuj "Predplatiť" tlačidlá na aktivovaných inzerátoch
        const subscribeButtons = document.querySelectorAll(`[onclick*="openSubscriptionModal(${adData.id}"]`);
        
        subscribeButtons.forEach(button => {
            if (adData.subscription_status === 'active') {
                // Zmeň na "Aktívne predplatné"
                button.innerHTML = '<i class="ri-check-line mr-1"></i> Aktívne predplatné';
                button.className = button.className.replace('bg-pink-600', 'bg-green-600');
                button.className = button.className.replace('hover:bg-pink-700', 'hover:bg-green-700');
                button.disabled = true;
            }
        });
        
        // Aktualizuj status indikátory
        const statusIndicators = document.querySelectorAll(`[data-status-for="${adData.id}"]`);
        statusIndicators.forEach(indicator => {
            if (adData.subscription_status === 'active') {
                indicator.innerHTML = '<span class="text-green-600 font-semibold">✓ Aktívne</span>';
            }
        });
        
        console.log('Subscription buttons updated for ad:', adData.id);
    }
});
</script> 