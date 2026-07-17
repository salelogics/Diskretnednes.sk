<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('seo.default_title', config('app.name', 'Laravel')))</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- SEO Meta (predvolené alebo per-stránka) -->
        @hasSection('meta')
            @yield('meta')
        @else
            <meta name="description" content="{{ config('seo.default_description', 'Diskrétne Dnes - erotické služby a inzeráty') }}">
            <!-- Open Graph -->
            <meta property="og:title" content="@yield('title', config('seo.default_title', config('app.name')))">
            <meta property="og:description" content="{{ config('seo.default_description', '') }}">
            <meta property="og:type" content="website">
            <meta property="og:url" content="{{ url()->current() }}">
            <meta property="og:image" content="{{ asset(config('seo.default_image', 'images/uploads/diskretne-dnes-logo-black.png')) }}">
            <!-- Twitter Card -->
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="@yield('title', config('seo.default_title', config('app.name')))">
            <meta name="twitter:description" content="{{ config('seo.default_description', '') }}">
            <meta name="twitter:image" content="{{ asset(config('seo.default_image', 'images/uploads/diskretne-dnes-logo-black.png')) }}">
        @endif

        <!-- Stripe JavaScript SDK (dočasne deaktivované) -->
        <!-- <script src="https://js.stripe.com/v3/"></script> -->
        
        <!-- Subscription Modal Functions - MUST BE IN HEAD -->
        <script>
            // Global variables
            var selectedPackageType = null;
            var selectedDuration = null;
            var selectedPaymentMethod = null;
            var currentAdId = null;
            var stripe = null;
            var elements = null;
            var cardElement = null;

            // Main modal function - MUST BE GLOBAL
            function openSubscriptionModal(adId, title, adName) {
                console.log('openSubscriptionModal called with:', adId, title, adName);
                
                currentAdId = adId || Math.floor(Math.random() * 1000);
                const displayTitle = title || 'Inzerát #' + currentAdId;
                const displayName = adName || '';
                
                var modalElement = document.getElementById('subscriptionModal');
                var titleElement = document.getElementById('modalAdTitle');
                var idElement = document.getElementById('modalAdId');
                var nameElement = document.getElementById('modalAdName');
                
                console.log('Modal elements found:', {
                    modal: !!modalElement,
                    title: !!titleElement,
                    id: !!idElement,
                    name: !!nameElement
                });
                
                if (!modalElement) {
                    console.error('Subscription modal not found!');
                    alert('Chyba: Modal sa nenašiel na stránke');
                    return;
                }
                
                if (titleElement) titleElement.textContent = displayTitle;
                if (idElement) idElement.textContent = 'ID: ' + currentAdId;
                if (nameElement) nameElement.textContent = displayName ? '• ' + displayName : '';
                
                // Reset form
                resetSubscriptionForm();
                
                modalElement.classList.remove('hidden');
                setTimeout(function() {
                    var modalContent = document.querySelector('#subscriptionModal .modal-content');
                    if (modalContent) {
                        modalContent.classList.remove('scale-95');
                        modalContent.classList.add('scale-100');
                    }
                }, 10);
            }

            function closeSubscriptionModal() {
                var modalContent = document.querySelector('#subscriptionModal .modal-content');
                if (modalContent) {
                    modalContent.classList.remove('scale-100');
                    modalContent.classList.add('scale-95');
                }
                setTimeout(function() {
                    var modalElement = document.getElementById('subscriptionModal');
                    if (modalElement) {
                        modalElement.classList.add('hidden');
                    }
                    resetSubscriptionForm();
                }, 300);
            }

            function resetSubscriptionForm() {
                console.log('resetSubscriptionForm called');
                
                // Reset selections
                selectedPackageType = null;
                selectedDuration = null;
                selectedPaymentMethod = null;
                
                // Reset dropdowns
                var packageSelect = document.getElementById('packageTypeSelect');
                var durationSelect = document.getElementById('durationSelect');
                var paymentSelect = document.getElementById('paymentMethodSelect');
                
                if (packageSelect) packageSelect.value = '';
                if (durationSelect) durationSelect.value = '';
                if (paymentSelect) paymentSelect.value = '';
                
                // Hide elements
                var priceElement = document.getElementById('selectedPrice');
                var buttonElement = document.getElementById('createPaymentButton');
                
                if (priceElement) priceElement.classList.add('hidden');
                if (buttonElement) buttonElement.classList.add('hidden');
                
                // Show default state
                var defaultElement = document.getElementById('paymentInstructionsDefault');
                var contentElement = document.getElementById('paymentInstructionsContent');
                var loadingElement = document.getElementById('paymentLoading');
                var successElement = document.getElementById('paymentSuccess');
                
                if (defaultElement) defaultElement.classList.remove('hidden');
                if (contentElement) contentElement.classList.add('hidden');
                if (loadingElement) loadingElement.classList.add('hidden');
                if (successElement) successElement.classList.add('hidden');
                
                console.log('resetSubscriptionForm completed');
            }

            function updatePackageType() {
                selectedPackageType = document.getElementById('packageTypeSelect').value;
                updatePriceDisplay();
                updatePaymentInstructions();
            }

            function updateDuration() {
                selectedDuration = document.getElementById('durationSelect').value;
                updatePriceDisplay();
                updatePaymentInstructions();
            }

            function updatePaymentMethod() {
                selectedPaymentMethod = document.getElementById('paymentMethodSelect').value;
                updatePaymentInstructions();
            }

            function updatePriceDisplay() {
                if (!selectedPackageType || !selectedDuration) {
                    document.getElementById('selectedPrice').classList.add('hidden');
                    document.getElementById('createPaymentButton').classList.add('hidden');
                    return;
                }
                
                // Calculate price based on package type and duration
                var prices = {
                    classic: { 5: 5, 7: 7, 30: 25, 90: 65, 365: 200 },
                    premium: { 5: 10, 7: 14, 30: 50, 90: 130, 365: 400 }
                };
                
                var price = prices[selectedPackageType][selectedDuration];
                
                document.getElementById('priceAmount').textContent = '€' + price;
                document.getElementById('selectedPrice').classList.remove('hidden');
                
                if (selectedPaymentMethod) {
                    document.getElementById('createPaymentButton').classList.remove('hidden');
                }
            }

            function updatePaymentInstructions() {
                var defaultDiv = document.getElementById('paymentInstructionsDefault');
                var contentDiv = document.getElementById('paymentInstructionsContent');
                
                if (!selectedPackageType || !selectedDuration || !selectedPaymentMethod) {
                    defaultDiv.classList.remove('hidden');
                    contentDiv.classList.add('hidden');
                    return;
                }
                
                defaultDiv.classList.add('hidden');
                
                // Show instructions based on payment method
                var paymentMethodNames = {
                    bank_transfer: 'Bankový prevod',
                    stripe: 'Platobná karta',
                    qr_code: 'QR platba',
                    sms: 'SMS platba'
                };
                
                var prices = {
                    classic: { 5: 5, 7: 7, 30: 25, 90: 65, 365: 200 },
                    premium: { 5: 10, 7: 14, 30: 50, 90: 130, 365: 400 }
                };
                
                var price = prices[selectedPackageType][selectedDuration];
                
                // Update payment info (will be filled when payment is created)
                document.getElementById('paymentAmount').textContent = '€' + price;
                document.getElementById('paymentMethod').textContent = paymentMethodNames[selectedPaymentMethod];
                
                // Show create payment button
                document.getElementById('createPaymentButton').classList.remove('hidden');
            }

            function createPayment() {
                if (!selectedPackageType || !selectedDuration || !selectedPaymentMethod) {
                    alert('Prosím vyberte všetky požadované možnosti');
                    return;
                }
                
                // Show loading
                document.getElementById('paymentInstructionsDefault').classList.add('hidden');
                document.getElementById('paymentInstructionsContent').classList.add('hidden');
                document.getElementById('paymentLoading').classList.remove('hidden');
                
                // Simple test - just show success after 2 seconds
                setTimeout(function() {
                    document.getElementById('paymentLoading').classList.add('hidden');
                    document.getElementById('paymentSuccess').classList.remove('hidden');
                    
                    setTimeout(function() {
                        closeSubscriptionModal();
                    }, 3000);
                }, 2000);
            }

            function subscribeForFree() {
                if (!selectedPackageType || !selectedDuration) {
                    alert('Prosím vyberte typ balíčka a dĺžku predplatného');
                    return;
                }
                
                // Show success immediately for admin
                document.getElementById('paymentInstructionsDefault').classList.add('hidden');
                document.getElementById('paymentInstructionsContent').classList.add('hidden');
                document.getElementById('paymentSuccess').classList.remove('hidden');
                
                setTimeout(function() {
                    closeSubscriptionModal();
                }, 3000);
            }

            // Make functions immediately available - NO WAITING
            window.openSubscriptionModal = openSubscriptionModal;
            window.closeSubscriptionModal = closeSubscriptionModal;
            window.updatePackageType = updatePackageType;
            window.updateDuration = updateDuration;
            window.updatePaymentMethod = updatePaymentMethod;
            window.createPayment = createPayment;
            window.subscribeForFree = subscribeForFree;
            
            console.log('Functions immediately assigned to window:', {
                openSubscriptionModal: typeof window.openSubscriptionModal,
                closeSubscriptionModal: typeof window.closeSubscriptionModal,
                updatePackageType: typeof window.updatePackageType,
                updateDuration: typeof window.updateDuration,
                updatePaymentMethod: typeof window.updatePaymentMethod,
                createPayment: typeof window.createPayment,
                subscribeForFree: typeof window.subscribeForFree
            });

            // Ensure Stripe is loaded before proceeding
            window.stripeReady = false;
            window.stripeLoadAttempts = 0;
            
            function checkStripeLoaded() {
                if (typeof Stripe !== 'undefined') {
                    window.stripeReady = true;
                    console.log('Stripe SDK loaded successfully');
                    return true;
                }
                return false;
            }
            
            function loadStripeSDK() {
                window.stripeLoadAttempts++;
                console.log('Loading Stripe SDK, attempt:', window.stripeLoadAttempts);
                
                var script = document.createElement('script');
                script.src = 'https://js.stripe.com/v3/';
                script.onload = function() {
                    console.log('Stripe SDK script loaded');
                    setTimeout(checkStripeLoaded, 100);
                };
                script.onerror = function() {
                    console.error('Failed to load Stripe SDK from CDN, attempt:', window.stripeLoadAttempts);
                    if (window.stripeLoadAttempts < 3) {
                        setTimeout(loadStripeSDK, 1000);
                    }
                };
                document.head.appendChild(script);
            }
            
            // Check immediately
            if (!checkStripeLoaded()) {
                console.warn('Stripe SDK not loaded immediately, trying fallback...');
                loadStripeSDK();
            }
            
            // Final check after page load
            window.addEventListener('load', function() {
                setTimeout(function() {
                    if (!checkStripeLoaded()) {
                        console.warn('Stripe SDK still not loaded after page load, final attempt...');
                        loadStripeSDK();
                    }
                }, 1000);
            });
        </script>

        <!-- RemixIcon CSS -->
        <link rel="preload" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" as="style">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Google Tag Manager -->
        <x-google-tag-manager />
        
        <!-- Google Analytics -->
        <x-google-analytics />
    </head>
    <body class="h-full font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <!-- Google Tag Manager (noscript) -->
        <x-google-tag-manager-noscript />
        
        <div class="min-h-screen">
            <x-header />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </body>
</html>
