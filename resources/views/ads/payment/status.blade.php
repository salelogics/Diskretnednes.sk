@extends('layouts.user-dashboard')

@section('header', 'Stav platby')

@section('content')
<div class="mx-auto max-w-4xl px-6 py-8 lg:px-8">
    <!-- Flash správy -->
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Hlavný obsah -->
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <!-- Header -->
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center
                    @if($payment->status === 'completed') bg-green-100
                    @elseif($payment->status === 'pending' || $payment->status === 'processing') bg-yellow-100
                    @elseif($payment->status === 'failed') bg-red-100
                    @else bg-gray-100
                    @endif">
                    @if($payment->status === 'completed')
                        <i class="ri-check-line text-2xl text-green-600"></i>
                    @elseif($payment->status === 'pending' || $payment->status === 'processing')
                        <i class="ri-time-line text-2xl text-yellow-600"></i>
                    @elseif($payment->status === 'failed')
                        <i class="ri-close-line text-2xl text-red-600"></i>
                    @else
                        <i class="ri-question-line text-2xl text-gray-600"></i>
                    @endif
                </div>
                <h1 class="text-2xl font-bold text-gray-900">
                    @if($payment->status === 'completed')
                        Platba úspešná!
                    @elseif($payment->status === 'pending')
                        Platba čaká na spracovanie
                    @elseif($payment->status === 'processing')
                        Platba sa spracováva
                    @elseif($payment->status === 'failed')
                        Platba neúspešná
                    @else
                        Stav platby
                    @endif
                </h1>
                <p class="text-gray-600 mt-2">ID platby: {{ $payment->payment_id }}</p>
            </div>
        </div>

        <!-- Detaily platby -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informácie o platbe -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Detaily platby</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Suma:</span>
                            <span class="font-semibold">{{ $payment->formatted_amount }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Spôsob platby:</span>
                            <span class="font-semibold">{{ $payment->payment_method_label }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Balíček:</span>
                            <span class="font-semibold">{{ $payment->paymentPackage->name }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Trvanie:</span>
                            <span class="font-semibold">{{ $payment->duration_label }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Stav:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($payment->status === 'completed') bg-green-100 text-green-800
                                @elseif($payment->status === 'pending' || $payment->status === 'processing') bg-yellow-100 text-yellow-800
                                @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $payment->status_label }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Vytvorené:</span>
                            <span class="font-semibold">{{ $payment->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        
                        @if($payment->subscription_starts_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Predplatné od:</span>
                            <span class="font-semibold">{{ $payment->subscription_starts_at->format('d.m.Y') }}</span>
                        </div>
                        @endif
                        
                        @if($payment->subscription_ends_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Predplatné do:</span>
                            <span class="font-semibold">{{ $payment->subscription_ends_at->format('d.m.Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Informácie o inzeráte -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Inzerát</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            @if($payment->ad->verification_image_url)
                                <img src="{{ $payment->ad->verification_image_url }}"
                                     alt="Inzerát"
                                     class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                                    <i class="ri-image-line text-pink-600"></i>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">
                                    @if($payment->ad->nickname)
                                        {{ $payment->ad->nickname }}
                                    @else
                                        Inzerát #{{ $payment->ad->id }}
                                    @endif
                                </h4>
                                <p class="text-sm text-gray-600">{{ $payment->ad->city }} • {{ $payment->ad->age }} rokov</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Akcie -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                @if($payment->status === 'completed' && $payment->invoice_number)
                    <a href="{{ route('ads.payment.invoice', $payment->payment_id) }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                        <i class="ri-download-line mr-2"></i>
                        Stiahnuť faktúru
                    </a>
                @endif
                
                <a href="{{ route('ads.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-pink-600 text-white font-semibold rounded-xl hover:bg-pink-700 transition-colors">
                    <i class="ri-arrow-left-line mr-2"></i>
                    Späť na inzeráty
                </a>
                
                @if($payment->status === 'failed' || $payment->status === 'cancelled')
                    <a href="{{ route('ads.payment.packages', $payment->ad->id) }}" 
                       class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors">
                        <i class="ri-refresh-line mr-2"></i>
                        Skúsiť znovu
                    </a>
                @endif
            </div>

            <!-- Dodatočné informácie pre rôzne stavy -->
            @if($payment->status === 'pending')
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="ri-information-line text-yellow-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-yellow-800 mb-2">Čaká sa na platbu</h4>
                            @if($payment->payment_method === 'bank_transfer')
                                <p class="text-sm text-yellow-700 mb-4">
                                    Uhraďte prosím sumu na nižšie uvedený účet. Platbu spracujeme ručne po
                                    pripísaní na účet (zvyčajne do 24 hodín) - inzerát sa aktivuje, hneď
                                    ako platbu priradíme podľa variabilného symbolu.
                                </p>
                                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    @if(\App\Models\Setting::get('invoice_company_name'))
                                        <div>
                                            <dt class="text-yellow-800 font-medium">Príjemca</dt>
                                            <dd class="font-mono text-gray-900">{{ \App\Models\Setting::get('invoice_company_name') }}</dd>
                                        </div>
                                    @endif
                                    @if(\App\Models\Setting::get('invoice_bank_iban'))
                                        <div>
                                            <dt class="text-yellow-800 font-medium">IBAN</dt>
                                            <dd class="font-mono text-gray-900">{{ \App\Models\Setting::get('invoice_bank_iban') }}</dd>
                                        </div>
                                    @endif
                                    @if(\App\Models\Setting::get('invoice_bank_swift'))
                                        <div>
                                            <dt class="text-yellow-800 font-medium">SWIFT/BIC</dt>
                                            <dd class="font-mono text-gray-900">{{ \App\Models\Setting::get('invoice_bank_swift') }}</dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt class="text-yellow-800 font-medium">Suma</dt>
                                        <dd class="font-mono text-gray-900">{{ $payment->formatted_amount }}</dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="text-yellow-800 font-medium">Variabilný symbol</dt>
                                        <dd class="font-mono text-gray-900">{{ $payment->payment_id }}</dd>
                                    </div>
                                </dl>
                            @elseif($payment->payment_method === 'stripe')
                                <p class="text-sm text-yellow-700">
                                    Dokončite platbu na stránke Stripe.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif($payment->status === 'completed')
                <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="ri-check-line text-green-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-green-800 mb-2">Platba úspešná</h4>
                            <p class="text-sm text-green-700">
                                Váš inzerát bol úspešne predplatený a je aktívny.
                                @if($payment->is_featured)
                                    Inzerát je zvýraznený.
                                @endif
                                @if($payment->is_top_ad)
                                    Inzerát je umiestnený na vrchu zoznamu.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($payment->status === 'failed')
                <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="ri-error-warning-line text-red-600 text-xl mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-red-800 mb-2">Platba neúspešná</h4>
                            <p class="text-sm text-red-700">
                                Platba sa nepodarila spracovať. Skúste to znovu alebo zvoľte iný spôsob platby.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 