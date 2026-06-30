@extends('layouts.admin-dashboard')

@section('header', 'Detail platby #' . $payment->id)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li>
                <div>
                    <a href="{{ route('admin.platby.index') }}" class="text-gray-400 hover:text-gray-500">
                        <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Späť</span>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <a href="{{ route('admin.platby.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Platby</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-500">#{{ $payment->id }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header s akciami -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Platba #{{ $payment->id }}</h1>
            <p class="text-gray-600">Detail platby a súvisiace informácie</p>
        </div>
        <div class="flex space-x-3">
            @if($payment->invoice_number)
                <a href="{{ route('admin.platby.invoice.show', $payment) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Zobraziť faktúru
                </a>
            @else
                <button onclick="generateInvoice({{ $payment->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    Vytvoriť faktúru
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Hlavné informácie -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Základné údaje -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Základné informácie</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID platby</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $payment->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Suma</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $payment->formatted_amount }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Metóda platby</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->payment_method_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($payment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $payment->status_label }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dátum vytvorenia</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $payment->created_at->format('d.m.Y H:i:s') }}</dd>
                        </div>
                        @if($payment->subscription_ends_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Platnosť do</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $payment->subscription_ends_at->format('d.m.Y H:i:s') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Informácie o inzeráte -->
            @if($payment->ad)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Súvisiaci inzerát</h3>
                        <div class="flex items-center space-x-4">
                            @if($payment->ad->photos->first())
                                <img class="h-16 w-16 rounded-lg object-cover" src="{{ asset('storage/' . $payment->ad->photos->first()->path) }}" alt="Inzerát">
                            @else
                                <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $payment->ad->nickname }}</h4>
                                <p class="text-sm text-gray-500">{{ ucfirst($payment->ad->city) }} - {{ ucfirst($payment->ad->ad_type) }}</p>
                                <p class="text-sm text-gray-500">{{ $payment->ad->age }} rokov</p>
                            </div>
                            <div>
                                <a href="{{ route('ad.show', $payment->ad->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Zobraziť inzerát
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Technické detaily -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Technické detaily</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        @if($payment->gateway_payment_id)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gateway Payment ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $payment->gateway_payment_id }}</dd>
                            </div>
                        @endif
                        @if($payment->stripe_payment_intent_id)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Stripe Payment Intent</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $payment->stripe_payment_intent_id }}</dd>
                            </div>
                        @endif
                        @if($payment->ip_address)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">IP adresa</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $payment->ip_address }}</dd>
                            </div>
                        @endif
                        @if($payment->invoice_number)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Číslo faktúry</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $payment->invoice_number }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Bočný panel -->
        <div class="space-y-6">
            <!-- Informácie o používateľovi -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Používateľ</h3>
                    <div class="flex items-center space-x-3">
                        @if($payment->user->photo)
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $payment->user->profile_photo_url }}" alt="Používateľ">
                        @else
                            <div class="h-10 w-10 rounded-full bg-pink-600 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">{{ substr($payment->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $payment->user->email }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.pouzivatelia.show', $payment->user) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            Zobraziť profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Balík -->
            @if($payment->paymentPackage)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Balík</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $payment->paymentPackage->name }}</p>
                                <p class="text-sm text-gray-500">{{ $payment->duration_label }}</p>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Cena:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $payment->formatted_amount }}</span>
                            </div>
                            @if($payment->is_featured)
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-500">Zvýraznený</span>
                                </div>
                            @endif
                            @if($payment->is_top_ad)
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-red-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm text-gray-500">Top inzerát</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Akcie -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Akcie</h3>
                    <div class="space-y-3">
                        @if($payment->status === 'pending')
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Označiť ako zaplatené
                            </button>
                            <button class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Zrušiť platbu
                            </button>
                        @endif
                        @if($payment->status === 'completed' && !$payment->invoice_number)
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Vytvoriť faktúru
                            </button>
                        @endif
                        @if($payment->invoice_number)
                            <a href="{{ route('admin.platby.invoice.download', $payment) }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors text-center block">
                                Stiahnuť faktúru
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 