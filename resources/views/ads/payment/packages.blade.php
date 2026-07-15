@extends('layouts.app')

@section('title', 'Predplatiť inzerát - ' . $ad->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-rose-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Predplatiť inzerát
            </h1>
            <p class="text-xl text-gray-600 mb-6">
                Vyberte si balíček pre váš inzerát: <strong>{{ $ad->title }}</strong>
            </p>
            <div class="inline-flex items-center px-4 py-2 bg-pink-100 text-pink-800 rounded-full text-sm font-medium">
                <i class="ri-information-line mr-2"></i>
                Inzerát ID: {{ $ad->id }}
            </div>
        </div>

        <!-- Classic balíčky -->
        <div class="mb-16">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Classic balíčky</h2>
                <p class="text-lg text-gray-600">Dostupný cenový balíček so štandardným zobrazením</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach($classicPackages as $package)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ri-star-line text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $package->duration_label }}</h3>
                            <div class="text-3xl font-bold text-blue-600 mb-1">{{ $package->formatted_price }}</div>
                            <p class="text-sm text-gray-500">{{ $package->description }}</p>
                        </div>

                        <!-- Features -->
                        <div class="space-y-3 mb-6">
                            @foreach($package->features as $feature)
                            <div class="flex items-start">
                                <i class="ri-check-line text-green-500 text-lg mr-3 mt-0.5 flex-shrink-0"></i>
                                <span class="text-sm text-gray-600">{{ $feature }}</span>
                            </div>
                            @endforeach
                        </div>

                        <!-- Button -->
                        <button onclick="selectPackage({{ $package->id }}, '{{ $package->name }}', '{{ $package->formatted_price }}', {{ $package->is_free ? 'true' : 'false' }})"
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105">
                            {{ $package->is_free ? 'Aktivovať zadarmo' : 'Vybrať balíček' }}
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Premium balíčky -->
        <div class="mb-16">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Premium balíčky</h2>
                <p class="text-lg text-gray-600">Neustále na vrchu zoznamu s TOPovaním</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach($premiumPackages as $package)
                <div class="bg-white rounded-2xl shadow-lg border-2 border-gradient-to-r from-pink-500 to-rose-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden">
                    <!-- Premium badge -->
                    <div class="absolute top-0 right-0 bg-gradient-to-r from-pink-500 to-rose-500 text-white px-3 py-1 text-xs font-bold rounded-bl-lg">
                        PREMIUM
                    </div>
                    
                    <div class="p-6">
                        <!-- Header -->
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ri-vip-crown-line text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $package->duration_label }}</h3>
                            <div class="text-3xl font-bold text-pink-600 mb-1">{{ $package->formatted_price }}</div>
                            <p class="text-sm text-gray-500">{{ $package->description }}</p>
                        </div>

                        <!-- Features -->
                        <div class="space-y-3 mb-6">
                            @foreach($package->features as $feature)
                            <div class="flex items-start">
                                <i class="ri-check-line text-green-500 text-lg mr-3 mt-0.5 flex-shrink-0"></i>
                                <span class="text-sm text-gray-600">{{ $feature }}</span>
                            </div>
                            @endforeach
                        </div>

                        <!-- Button -->
                        <button onclick="selectPackage({{ $package->id }}, '{{ $package->name }}', '{{ $package->formatted_price }}')" 
                                class="w-full bg-gradient-to-r from-pink-500 to-rose-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-pink-600 hover:to-rose-700 transition-all duration-300 transform hover:scale-105">
                            Vybrať balíček
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Späť na inzeráty -->
        <div class="text-center">
            <a href="{{ route('ads.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-300">
                <i class="ri-arrow-left-line mr-2"></i>
                Späť na moje inzeráty
            </a>
        </div>
    </div>
</div>

<!-- Payment Method Modal -->
<div id="paymentModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity duration-300" onclick="closePaymentModal()"></div>
    
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" id="modalContent">
            <div class="p-6">
                <!-- Header -->
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Spôsob platby</h3>
                    <p class="text-gray-600">Vyberte si spôsob platby pre balíček</p>
                    <div class="mt-3 p-3 bg-pink-50 rounded-lg">
                        <div class="text-sm text-gray-600">Balíček: <span id="selectedPackageName" class="font-semibold"></span></div>
                        <div class="text-lg font-bold text-pink-600" id="selectedPackagePrice"></div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <form id="paymentForm" method="POST" action="{{ route('ads.payment.create', $ad->id) }}">
                    @csrf
                    <input type="hidden" name="package_id" id="selectedPackageId">
                    
                    <div class="space-y-4 mb-6">
                        <!-- Bankový prevod -->
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-pink-300 transition-colors duration-300">
                            <input type="radio" name="payment_method" value="bank_transfer" class="sr-only">
                            <div class="flex items-center w-full">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                    <i class="ri-bank-line text-blue-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Bankový prevod</div>
                                    <div class="text-sm text-gray-500">Platba prevodom na účet</div>
                                </div>
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center">
                                    <div class="w-3 h-3 bg-pink-500 rounded-full hidden"></div>
                                </div>
                            </div>
                        </label>

                        <!-- QR platba -->
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-pink-300 transition-colors duration-300">
                            <input type="radio" name="payment_method" value="qr_code" class="sr-only">
                            <div class="flex items-center w-full">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                    <i class="ri-qr-code-line text-green-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">QR platba</div>
                                    <div class="text-sm text-gray-500">Naskenujte QR kód</div>
                                </div>
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center">
                                    <div class="w-3 h-3 bg-pink-500 rounded-full hidden"></div>
                                </div>
                            </div>
                        </label>

                        <!-- SMS platba -->
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-pink-300 transition-colors duration-300">
                            <input type="radio" name="payment_method" value="sms" class="sr-only">
                            <div class="flex items-center w-full">
                                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                    <i class="ri-message-line text-orange-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">SMS platba</div>
                                    <div class="text-sm text-gray-500">Platba cez SMS</div>
                                </div>
                                <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center">
                                    <div class="w-3 h-3 bg-pink-500 rounded-full hidden"></div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-3">
                        <button type="button" onclick="closePaymentModal()" 
                                class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-300">
                            Zrušiť
                        </button>
                        <button type="submit" 
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-pink-500 to-rose-600 text-white rounded-xl hover:from-pink-600 hover:to-rose-700 transition-all duration-300">
                            Pokračovať
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPackageId = null;

function selectPackage(packageId, packageName, packagePrice, isFree = false) {
    if (isFree) {
        activateFreePackage(packageId, packageName);
        return;
    }

    selectedPackageId = packageId;
    document.getElementById('selectedPackageId').value = packageId;
    document.getElementById('selectedPackageName').textContent = packageName;
    document.getElementById('selectedPackagePrice').textContent = packagePrice;

    // Reset radio buttons
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.checked = false;
        radio.closest('label').classList.remove('border-pink-500', 'bg-pink-50');
        radio.closest('label').querySelector('.w-3').classList.add('hidden');
    });

    openPaymentModal();
}

function activateFreePackage(packageId, packageName) {
    if (!confirm(`Aktivovať balíček „${packageName}" zadarmo?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('package_id', packageId);

    fetch(document.getElementById('paymentForm').action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = `/platba/${data.payment_id}/stav`;
        } else {
            alert(data.message || data.error || 'Nastala chyba pri aktivácii balíčka');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Nastala chyba pri spracovaní požiadavky');
    });
}

function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('modalContent');
    
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Handle radio button selection
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Reset all
        document.querySelectorAll('input[name="payment_method"]').forEach(r => {
            r.closest('label').classList.remove('border-pink-500', 'bg-pink-50');
            r.closest('label').querySelector('.w-3').classList.add('hidden');
        });
        
        // Highlight selected
        if (this.checked) {
            this.closest('label').classList.add('border-pink-500', 'bg-pink-50');
            this.closest('label').querySelector('.w-3').classList.remove('hidden');
        }
    });
});

// Handle form submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const paymentMethod = formData.get('payment_method');
    
    if (!paymentMethod) {
        alert('Vyberte spôsob platby');
        return;
    }
    
    // Show loading state
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Spracováva sa...';
    
    // Submit form
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                window.location.href = `/platba/${data.payment_id}/stav`;
            }
        } else {
            alert(data.error || 'Nastala chyba pri vytváraní platby');
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Nastala chyba pri spracovaní požiadavky');
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentModal();
    }
});
</script>
@endsection 