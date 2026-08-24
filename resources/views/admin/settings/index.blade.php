@extends('layouts.admin-dashboard')

@section('header', 'Nastavenia systému')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Flash správy -->
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-gradient-to-r from-emerald-50 to-green-50 p-4 border border-emerald-200/50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 p-4 border border-red-200/50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Hlavný formulár -->
    <div class="relative bg-gradient-to-br from-pink-50 via-white to-rose-50 shadow-xl rounded-3xl border border-pink-200/50 overflow-hidden">
        <!-- Dekoratívne pozadie -->
        <div class="absolute inset-0 bg-gradient-to-br from-pink-500/5 via-transparent to-rose-500/5"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-pink-200/20 to-transparent rounded-full -mr-32 -mt-32"></div>
        
        <div class="relative px-8 py-8 border-b border-pink-100/50 backdrop-blur-sm">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                        Konfigurácia systému
                    </h3>
                    <p class="text-gray-600 mt-2">Upravte kľúčové nastavenia aplikácie a integrácie</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.update') }}" x-data="settingsForm()" class="relative" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-8 space-y-8">
                <!-- Aplikačné nastavenia -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-pink-50 to-rose-50 border-b border-pink-100/50">
                        <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            Aplikačné nastavenia
                        </h4>
                        <p class="text-sm text-gray-600 mt-1">Základné nastavenia aplikácie</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="app_name" class="block text-sm font-medium text-gray-900 mb-2">Názov aplikácie</label>
                                <input type="text" name="app_name" id="app_name" value="{{ old('app_name', $settings['app_name']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" required>
                                @error('app_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="app_url" class="block text-sm font-medium text-gray-900 mb-2">URL aplikácie</label>
                                <input type="url" name="app_url" id="app_url" value="{{ old('app_url', $settings['app_url']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" required>
                                @error('app_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stripe nastavenia -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Stripe platby</h4>
                                    <p class="text-sm text-gray-600">Nastavenia pre Stripe platobný systém</p>
                                </div>
                            </div>
                            <button type="button" @click="toggleSection('stripe')" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                <span x-show="!sections.stripe">Zobraziť</span>
                                <span x-show="sections.stripe">Skryť</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="sections.stripe" x-transition class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="stripe_key" class="block text-sm font-medium text-gray-900 mb-2">Stripe Public Key</label>
                                <input type="text" name="stripe_key" id="stripe_key" value="{{ old('stripe_key', $settings['stripe_key']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="pk_test_...">
                                @error('stripe_key')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="stripe_secret" class="block text-sm font-medium text-gray-900 mb-2">Stripe Secret Key</label>
                                <input type="password" name="stripe_secret" id="stripe_secret" value="{{ old('stripe_secret', $settings['stripe_secret']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="sk_test_...">
                                @error('stripe_secret')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="stripe_webhook_secret" class="block text-sm font-medium text-gray-900 mb-2">Stripe Webhook Secret</label>
                                <input type="password" name="stripe_webhook_secret" id="stripe_webhook_secret" value="{{ old('stripe_webhook_secret', $settings['stripe_webhook_secret']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="whsec_...">
                                @error('stripe_webhook_secret')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Webhook URL info -->
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <h5 class="text-sm font-medium text-blue-900 mb-2">Webhook URL pre Stripe</h5>
                                <div class="space-y-2">
                                    <div>
                                        <label class="text-xs font-medium text-blue-700">Produkčný webhook URL:</label>
                                        <div class="mt-1 flex items-center space-x-2">
                                            <code class="flex-1 px-3 py-2 bg-white border border-blue-200 rounded-lg text-sm text-gray-900 font-mono">{{ config('app.url') }}/stripe/webhook</code>
                                            <button type="button" onclick="copyToClipboard('{{ config('app.url') }}/stripe/webhook')" class="px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                                                Kopírovať
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-blue-700">Testovací webhook URL (localhost):</label>
                                        <div class="mt-1 flex items-center space-x-2">
                                            <code class="flex-1 px-3 py-2 bg-white border border-blue-200 rounded-lg text-sm text-gray-900 font-mono">http://127.0.0.1:8000/stripe/webhook</code>
                                            <button type="button" onclick="copyToClipboard('http://127.0.0.1:8000/stripe/webhook')" class="px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                                                Kopírovať
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-blue-600">
                                    <p><strong>Potrebné eventy:</strong> payment_intent.succeeded, payment_intent.payment_failed, checkout.session.completed</p>
                                    <p class="mt-1"><strong>Pre localhost testovanie:</strong> Použite Stripe CLI: <code class="bg-white px-1 rounded">stripe listen --forward-to localhost:8000/stripe/webhook</code></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google OAuth -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-orange-50 border-b border-red-100/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Google prihlásenie</h4>
                                    <p class="text-sm text-gray-600">Nastavenia pre Google OAuth</p>
                                </div>
                            </div>
                            <button type="button" @click="toggleSection('google')" class="px-4 py-2 text-sm font-medium text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                <span x-show="!sections.google">Zobraziť</span>
                                <span x-show="sections.google">Skryť</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="sections.google" x-transition class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="google_client_id" class="block text-sm font-medium text-gray-900 mb-2">Google Client ID</label>
                                <input type="text" name="google_client_id" id="google_client_id" value="{{ old('google_client_id', $settings['google_client_id']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="123456789-...">
                                @error('google_client_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="google_client_secret" class="block text-sm font-medium text-gray-900 mb-2">Google Client Secret</label>
                                <input type="password" name="google_client_secret" id="google_client_secret" value="{{ old('google_client_secret', $settings['google_client_secret']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="GOCSPX-...">
                                @error('google_client_secret')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facebook OAuth -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-100/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Facebook prihlásenie</h4>
                                    <p class="text-sm text-gray-600">Nastavenia pre Facebook OAuth</p>
                                </div>
                            </div>
                            <button type="button" @click="toggleSection('facebook')" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                <span x-show="!sections.facebook">Zobraziť</span>
                                <span x-show="sections.facebook">Skryť</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="sections.facebook" x-transition class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="facebook_client_id" class="block text-sm font-medium text-gray-900 mb-2">Facebook App ID</label>
                                <input type="text" name="facebook_client_id" id="facebook_client_id" value="{{ old('facebook_client_id', $settings['facebook_client_id']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="1234567890123456">
                                @error('facebook_client_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="facebook_client_secret" class="block text-sm font-medium text-gray-900 mb-2">Facebook App Secret</label>
                                <input type="password" name="facebook_client_secret" id="facebook_client_secret" value="{{ old('facebook_client_secret', $settings['facebook_client_secret']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="abcdef123456...">
                                @error('facebook_client_secret')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Analytics & Tag Manager -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-yellow-50 border-b border-orange-100/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Google Analytics & Tag Manager</h4>
                                    <p class="text-sm text-gray-600">Sledovanie návštevnosti a konverzií</p>
                                </div>
                            </div>
                            <button type="button" @click="toggleSection('analytics')" class="px-4 py-2 text-sm font-medium text-orange-600 bg-orange-100 rounded-lg hover:bg-orange-200 transition-colors duration-200">
                                <span x-show="!sections.analytics">Zobraziť</span>
                                <span x-show="sections.analytics">Skryť</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="sections.analytics" x-transition class="p-6">
                        <div class="space-y-6">
                            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl p-4 border border-orange-200/50">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-orange-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h5 class="text-sm font-semibold text-orange-800">Informácie o nastavení</h5>
                                        <div class="mt-2 text-sm text-orange-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li><strong>Google Analytics ID:</strong> Formát GA4: G-XXXXXXXXXX alebo Universal: UA-XXXXXXXX-X</li>
                                                <li><strong>Tag Manager ID:</strong> Formát: GTM-XXXXXXX</li>
                                                <li>Kódy sa automaticky vložia do všetkých stránok aplikácie</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- UPLOAD JSON KĽÚČA -->
                            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl p-4 border border-orange-200/50">
                                <label class="block text-sm font-medium text-gray-900 mb-2">Google Analytics API kľúč (service account JSON)</label>
                                @if(file_exists(storage_path('app/analytics/service-account-credentials.json')))
                                    <div class="mb-2 text-green-700 flex items-center">
                                        <i class="ri-checkbox-circle-line text-lg mr-1"></i> Kľúč je nahratý
                                    </div>
                                @else
                                    <div class="mb-2 text-red-700 flex items-center">
                                        <i class="ri-error-warning-line text-lg mr-1"></i> Kľúč nie je nahratý
                                    </div>
                                @endif
                                <input type="file" name="ga_service_account" accept="application/json" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                <p class="mt-2 text-xs text-gray-500">Nahrajte JSON kľúč stiahnutý z Google Cloud Console (Service Account). <span class="font-semibold text-orange-700">Po výbere súboru kliknite na „Uložiť nastavenia" nižšie.</span></p>
                            </div>
                            
                            <div>
                                <label for="google_analytics_id" class="block text-sm font-medium text-gray-900 mb-2">
                                    Google Analytics ID
                                    <span class="text-xs text-gray-500 font-normal">(GA4 alebo Universal Analytics)</span>
                                </label>
                                <input type="text" name="google_analytics_id" id="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="G-XXXXXXXXXX alebo UA-XXXXXXXX-X">
                                @error('google_analytics_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">
                                    💡 Nájdete v Google Analytics → Admin → Property Settings → Tracking ID
                                </p>
                            </div>
                            
                            <div>
                                <label for="google_tag_manager_id" class="block text-sm font-medium text-gray-900 mb-2">
                                    Google Tag Manager ID
                                    <span class="text-xs text-gray-500 font-normal">(Container ID)</span>
                                </label>
                                <input type="text" name="google_tag_manager_id" id="google_tag_manager_id" value="{{ old('google_tag_manager_id', $settings['google_tag_manager_id']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="GTM-XXXXXXX">
                                @error('google_tag_manager_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-gray-500">
                                    💡 Nájdete v Google Tag Manager → Workspace → Container ID (GTM-XXXXXXX)
                                </p>
                            </div>
                            
                            <!-- Preview sekcií -->
                            <div class="border-t border-gray-200 pt-6">
                                <h5 class="text-md font-semibold text-gray-900 mb-4">Náhľad implementácie</h5>
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Google Analytics Preview -->
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            Google Analytics
                                        </h6>
                                        <div class="text-xs text-gray-600 font-mono bg-white rounded p-2 border">
                                            <span class="text-gray-400">&lt;script&gt;</span><br>
                                            <span class="text-blue-600">gtag</span>(<span class="text-green-600">'config'</span>, <span class="text-orange-600">'<span x-text="document.getElementById('google_analytics_id').value || 'G-XXXXXXXXXX'"></span>'</span>);<br>
                                            <span class="text-gray-400">&lt;/script&gt;</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Tag Manager Preview -->
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <h6 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            Tag Manager
                                        </h6>
                                        <div class="text-xs text-gray-600 font-mono bg-white rounded p-2 border">
                                            <span class="text-gray-400">&lt;script&gt;</span><br>
                                            <span class="text-purple-600">dataLayer</span> = [<span class="text-orange-600">'<span x-text="document.getElementById('google_tag_manager_id').value || 'GTM-XXXXXXX'"></span>'</span>];<br>
                                            <span class="text-gray-400">&lt;/script&gt;</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Platobné nastavenia -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Platobné nastavenia</h4>
                                    <p class="text-sm text-gray-600">IBAN a ďalšie platobné údaje</p>
                                </div>
                            </div>
                            <button type="button" @click="toggleSection('payment')" class="px-4 py-2 text-sm font-medium text-green-600 bg-green-100 rounded-lg hover:bg-green-200 transition-colors duration-200">
                                <span x-show="!sections.payment">Zobraziť</span>
                                <span x-show="sections.payment">Skryť</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="sections.payment" x-transition class="p-6">
                        <div>
                            <label for="payment_iban" class="block text-sm font-medium text-gray-900 mb-2">IBAN</label>
                            <input type="text" name="payment_iban" id="payment_iban" value="{{ old('payment_iban', $settings['payment_iban']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="SK7111000000002626000007">
                            @error('payment_iban')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SMS platby nastavenia -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-b border-yellow-100/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">SMS platby</h4>
                                    <p class="text-sm text-gray-600">Nastavenia pre PlatbaMobilom.sk</p>
                                </div>
                            </div>
                            <button type="button" @click="toggleSection('sms')" class="px-4 py-2 text-sm font-medium text-yellow-600 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition-colors duration-200">
                                <span x-show="!sections.sms">Zobraziť</span>
                                <span x-show="sections.sms">Skryť</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="sections.sms" x-transition class="p-6">
                        <div class="space-y-6">
                            <!-- Informačný box -->
                            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl p-4 border border-yellow-200/50">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h5 class="text-sm font-semibold text-yellow-800">Informácie o SMS platbách</h5>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <li><strong>PID:</strong> Identifikátor obchodníka pridelený od PlatbaMobilom.sk</li>
                                                <li><strong>Kľúč:</strong> 64-bajtový tajný kľúč pre podpisovanie</li>
                                                <li><strong>Minimálna suma:</strong> 0,15€ (podľa dokumentácie)</li>
                                                <li><strong>Podporované sumy:</strong> 0,15-1,05€ (krok 0,05€), 1,10-20,00€ (krok 0,10€), 20-30€ (krok 0,50€)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulárové polia -->
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="sms_payment_pid" class="block text-sm font-medium text-gray-900 mb-2">
                                        PID (Partner ID)
                                        <span class="text-xs text-gray-500 font-normal">(Identifikátor obchodníka)</span>
                                    </label>
                                    <input type="text" name="sms_payment_pid" id="sms_payment_pid" value="{{ old('sms_payment_pid', $settings['sms_payment_pid']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="22334">
                                    @error('sms_payment_pid')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500">
                                        💡 Číselný identifikátor získaný pri podpise zmluvy s PlatbaMobilom.sk
                                    </p>
                                </div>

                                <div>
                                    <label for="sms_payment_key" class="block text-sm font-medium text-gray-900 mb-2">
                                        Tajný kľúč
                                        <span class="text-xs text-gray-500 font-normal">(64-bajtový kľúč pre podpisovanie)</span>
                                    </label>
                                    <input type="password" name="sms_payment_key" id="sms_payment_key" value="{{ old('sms_payment_key', $settings['sms_payment_key']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••">
                                    @error('sms_payment_key')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500">
                                        💡 Tajný kľúč pre HMAC-SHA-256 podpisovanie parametrov
                                    </p>
                                </div>

                                <div>
                                    <label for="sms_payment_url" class="block text-sm font-medium text-gray-900 mb-2">
                                        URL platobnej brány
                                        <span class="text-xs text-gray-500 font-normal">(Adresa PlatbaMobilom.sk)</span>
                                    </label>
                                    <input type="url" name="sms_payment_url" id="sms_payment_url" value="{{ old('sms_payment_url', $settings['sms_payment_url']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="https://pay.platbamobilom.sk/pay/">
                                    @error('sms_payment_url')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-xs text-gray-500">
                                        💡 Štandardná adresa: https://pay.platbamobilom.sk/pay/
                                    </p>
                                </div>
                            </div>

                            <!-- Preddefinované cenové balíčky -->
                            <div class="border-t border-gray-200 pt-6">
                                <h5 class="text-md font-semibold text-gray-900 mb-4">Preddefinované cenové balíčky</h5>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    <div class="text-center p-3 bg-blue-50 rounded-xl">
                                        <div class="text-2xl font-bold text-blue-600">5€</div>
                                        <div class="text-xs text-blue-800">Classic 1 deň</div>
                                    </div>
                                    <div class="text-center p-3 bg-purple-50 rounded-xl">
                                        <div class="text-2xl font-bold text-purple-600">7€</div>
                                        <div class="text-xs text-purple-800">Premium 1 deň</div>
                                    </div>
                                    <div class="text-center p-3 bg-yellow-50 rounded-xl">
                                        <div class="text-2xl font-bold text-yellow-600">10€</div>
                                        <div class="text-xs text-yellow-800">Gold 1 deň</div>
                                    </div>
                                    <div class="text-center p-3 bg-green-50 rounded-xl">
                                        <div class="text-2xl font-bold text-green-600">20€</div>
                                        <div class="text-xs text-green-800">Gold 5 dní</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Test SMS platby -->
                            <div class="border-t border-gray-200 pt-6">
                                <h5 class="text-md font-semibold text-gray-900 mb-4">Test SMS platby</h5>
                                <div class="bg-gray-50 rounded-xl p-4">
                                        <p class="text-sm text-gray-600 mb-4">
                                            SMS platby pre topovanie inzerátov s cenovými hladinami: 5€ | 7€ | 9€ | 10€ | 13€ | 15€ | 20€
                                        </p>
                                    <div class="flex gap-4">
                                        <select class="rounded-xl border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-pink-600 text-sm">
                                            <option value="5.00">Classic 1 deň - 5€</option>
                                            <option value="7.00">Premium 1 deň - 7€</option>
                                            <option value="10.00">Gold 1 deň - 10€</option>
                                            <option value="13.00">Gold 2 dni - 13€</option>
                                            <option value="15.00">Gold 3 dni - 15€</option>
                                            <option value="20.00">Gold 5 dní - 20€</option>
                                        </select>
                                        <button type="button" class="px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all duration-200 text-sm font-medium">
                                            Testovať SMS platbu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nastavenia faktúr -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-green-50 border-b border-emerald-100/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Nastavenia faktúr</h4>
                                    <p class="text-sm text-gray-600">Údaje firmy a platobné informácie pre faktúry</p>
                                </div>
                            </div>
                            <button type="button" @click="toggleSection('invoices')" class="px-4 py-2 text-sm font-medium text-emerald-600 bg-emerald-100 rounded-lg hover:bg-emerald-200 transition-colors duration-200">
                                <span x-show="!sections.invoices">Zobraziť</span>
                                <span x-show="sections.invoices">Skryť</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="sections.invoices" x-transition class="p-6">
                        <div class="space-y-8">
                            <!-- Údaje firmy -->
                            <div>
                                <h5 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Údaje firmy
                                </h5>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="invoice_company_name" class="block text-sm font-medium text-gray-900 mb-2">Názov firmy</label>
                                        <input type="text" name="invoice_company_name" id="invoice_company_name" value="{{ old('invoice_company_name', $settings['invoice_company_name']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="DiskretneDnes.sk">
                                        @error('invoice_company_name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_company_email" class="block text-sm font-medium text-gray-900 mb-2">Email firmy</label>
                                        <input type="email" name="invoice_company_email" id="invoice_company_email" value="{{ old('invoice_company_email', $settings['invoice_company_email']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="faktury@diskretnednes.sk">
                                        @error('invoice_company_email')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_company_address" class="block text-sm font-medium text-gray-900 mb-2">Adresa</label>
                                        <input type="text" name="invoice_company_address" id="invoice_company_address" value="{{ old('invoice_company_address', $settings['invoice_company_address']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="Hlavná 123">
                                        @error('invoice_company_address')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_company_phone" class="block text-sm font-medium text-gray-900 mb-2">Telefón</label>
                                        <input type="text" name="invoice_company_phone" id="invoice_company_phone" value="{{ old('invoice_company_phone', $settings['invoice_company_phone']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="+421 900 123 456">
                                        @error('invoice_company_phone')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_company_city" class="block text-sm font-medium text-gray-900 mb-2">Mesto</label>
                                        <input type="text" name="invoice_company_city" id="invoice_company_city" value="{{ old('invoice_company_city', $settings['invoice_company_city']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="Bratislava">
                                        @error('invoice_company_city')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_company_postal_code" class="block text-sm font-medium text-gray-900 mb-2">PSČ</label>
                                        <input type="text" name="invoice_company_postal_code" id="invoice_company_postal_code" value="{{ old('invoice_company_postal_code', $settings['invoice_company_postal_code']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="811 01">
                                        @error('invoice_company_postal_code')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_company_country" class="block text-sm font-medium text-gray-900 mb-2">Krajina</label>
                                        <input type="text" name="invoice_company_country" id="invoice_company_country" value="{{ old('invoice_company_country', $settings['invoice_company_country']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="Slovenská republika">
                                        @error('invoice_company_country')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_company_website" class="block text-sm font-medium text-gray-900 mb-2">Webstránka</label>
                                        <input type="text" name="invoice_company_website" id="invoice_company_website" value="{{ old('invoice_company_website', $settings['invoice_company_website']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="www.diskretnednes.sk">
                                        @error('invoice_company_website')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Daňové údaje -->
                            <div class="border-t border-gray-200 pt-6">
                                <h5 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Daňové údaje
                                </h5>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                    <div>
                                        <label for="invoice_company_ico" class="block text-sm font-medium text-gray-900 mb-2">IČO</label>
                                        <input type="text" name="invoice_company_ico" id="invoice_company_ico" value="{{ old('invoice_company_ico', $settings['invoice_company_ico']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="12345678">
                                        @error('invoice_company_ico')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_company_dic" class="block text-sm font-medium text-gray-900 mb-2">DIČ</label>
                                        <input type="text" name="invoice_company_dic" id="invoice_company_dic" value="{{ old('invoice_company_dic', $settings['invoice_company_dic']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="1234567890">
                                        @error('invoice_company_dic')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_company_ic_dph" class="block text-sm font-medium text-gray-900 mb-2">IČ DPH</label>
                                        <input type="text" name="invoice_company_ic_dph" id="invoice_company_ic_dph" value="{{ old('invoice_company_ic_dph', $settings['invoice_company_ic_dph']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="SK1234567890">
                                        @error('invoice_company_ic_dph')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Bankové údaje -->
                            <div class="border-t border-gray-200 pt-6">
                                <h5 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    Bankové údaje
                                </h5>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="invoice_bank_name" class="block text-sm font-medium text-gray-900 mb-2">Názov banky</label>
                                        <input type="text" name="invoice_bank_name" id="invoice_bank_name" value="{{ old('invoice_bank_name', $settings['invoice_bank_name']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="Slovenská sporiteľňa">
                                        @error('invoice_bank_name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_bank_account" class="block text-sm font-medium text-gray-900 mb-2">Číslo účtu</label>
                                        <input type="text" name="invoice_bank_account" id="invoice_bank_account" value="{{ old('invoice_bank_account', $settings['invoice_bank_account']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="1234567890/0900">
                                        @error('invoice_bank_account')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_bank_iban" class="block text-sm font-medium text-gray-900 mb-2">IBAN</label>
                                        <input type="text" name="invoice_bank_iban" id="invoice_bank_iban" value="{{ old('invoice_bank_iban', $settings['invoice_bank_iban']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="SK89 0900 0000 0012 3456 7890">
                                        @error('invoice_bank_iban')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="invoice_bank_swift" class="block text-sm font-medium text-gray-900 mb-2">SWIFT</label>
                                        <input type="text" name="invoice_bank_swift" id="invoice_bank_swift" value="{{ old('invoice_bank_swift', $settings['invoice_bank_swift']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="GIBASKBX">
                                        @error('invoice_bank_swift')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="invoice_constant_symbol" class="block text-sm font-medium text-gray-900 mb-2">Konštantný symbol</label>
                                        <input type="text" name="invoice_constant_symbol" id="invoice_constant_symbol" value="{{ old('invoice_constant_symbol', $settings['invoice_constant_symbol']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm transition-all duration-200" placeholder="0308">
                                        @error('invoice_constant_symbol')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email a SMTP nastavenia -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-purple-100/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Email a SMTP nastavenia</h4>
                                    <p class="text-sm text-gray-600">Konfigurácia pre odosielanie emailov</p>
                                </div>
                            </div>
                            <button type="button" @click="toggleSection('email')" class="px-4 py-2 text-sm font-medium text-purple-600 bg-purple-100 rounded-lg hover:bg-purple-200 transition-colors duration-200">
                                <span x-show="!sections.email">Zobraziť</span>
                                <span x-show="sections.email">Skryť</span>
                            </button>
                        </div>
                    </div>
                    <div x-show="sections.email" x-transition class="p-6">
                        <div class="space-y-6">
                            <!-- Základné email nastavenia -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="mail_from_address" class="block text-sm font-medium text-gray-900 mb-2">Email adresa odosielateľa</label>
                                    <input type="email" name="mail_from_address" id="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="info@diskretnednes.sk">
                                    @error('mail_from_address')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="mail_from_name" class="block text-sm font-medium text-gray-900 mb-2">Meno odosielateľa</label>
                                    <input type="text" name="mail_from_name" id="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="DiskretneDnes.sk">
                                    @error('mail_from_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Admin notifikácie -->
                            <div class="border-t border-gray-200 pt-6">
                                <h5 class="text-md font-semibold text-gray-900 mb-4">Admin notifikácie</h5>
                                <div>
                                    <label for="admin_notification_emails" class="block text-sm font-medium text-gray-900 mb-2">Email adresy pre admin notifikácie</label>
                                    <textarea name="admin_notification_emails" id="admin_notification_emails" rows="3" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="admin@diskretnednes.sk, info@diskretnednes.sk">{{ old('admin_notification_emails', $settings['admin_notification_emails']) }}</textarea>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <i class="ri-information-line text-blue-500 mr-1"></i>
                                        Zadajte email adresy oddelené čiarkami. Na tieto adresy sa budú posielať admin notifikácie (nové inzeráty, hlásenia, platby, atď.).
                                    </p>
                                    @error('admin_notification_emails')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    
                                    <!-- Test admin notifikácií -->
                                    <div class="mt-4">
                                        <button type="button" @click="testAdminNotifications()" :disabled="testingAdminNotifications" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center text-sm">
                                            <svg x-show="testingAdminNotifications" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <i class="ri-notification-3-line mr-2" x-show="!testingAdminNotifications"></i>
                                            <span x-text="testingAdminNotifications ? 'Testujem...' : 'Test admin notifikácií'"></span>
                                        </button>
                                        <div x-show="adminNotificationResult" x-transition class="mt-3 p-3 rounded-lg text-sm" :class="adminNotificationResult?.success ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'">
                                            <p x-text="adminNotificationResult?.message"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SMTP nastavenia -->
                            <div class="border-t border-gray-200 pt-6">
                                <h5 class="text-md font-semibold text-gray-900 mb-4">SMTP Server nastavenia</h5>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <label for="mail_host" class="block text-sm font-medium text-gray-900 mb-2">SMTP Host</label>
                                        <input type="text" name="mail_host" id="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="smtp.gmail.com">
                                        @error('mail_host')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="mail_port" class="block text-sm font-medium text-gray-900 mb-2">SMTP Port</label>
                                        <input type="number" name="mail_port" id="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="587">
                                        @error('mail_port')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="mail_username" class="block text-sm font-medium text-gray-900 mb-2">SMTP Používateľské meno</label>
                                        <input type="text" name="mail_username" id="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="your-email@gmail.com">
                                        @error('mail_username')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="mail_password" class="block text-sm font-medium text-gray-900 mb-2">SMTP Heslo</label>
                                        <input type="password" name="mail_password" id="mail_password" value="{{ old('mail_password', $settings['mail_password']) }}" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200" placeholder="••••••••">
                                        @error('mail_password')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="mail_encryption" class="block text-sm font-medium text-gray-900 mb-2">Šifrovanie</label>
                                        <select name="mail_encryption" id="mail_encryption" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200">
                                            <option value="">Žiadne</option>
                                            <option value="tls" {{ old('mail_encryption', $settings['mail_encryption']) == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        </select>
                                        @error('mail_encryption')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Test email -->
                            <div class="border-t border-gray-200 pt-6">
                                <h5 class="text-md font-semibold text-gray-900 mb-4">Test SMTP spojenia</h5>
                                <div class="space-y-4">
                                    <div class="flex gap-4">
                                        <div class="flex-1">
                                            <input type="email" x-model="testEmail" placeholder="Zadajte email pre test" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-pink-600 sm:text-sm transition-all duration-200">
                                        </div>
                                        <button type="button" @click="sendTestEmail()" :disabled="testingEmail" class="px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center">
                                            <svg x-show="testingEmail" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span x-text="testingEmail ? 'Odosielam...' : 'Odoslať test'"></span>
                                        </button>
                                    </div>
                                    <div class="flex justify-center space-x-3">
                                        <button type="button" @click="diagnoseSMTP()" :disabled="diagnosingSMTP" class="px-6 py-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center">
                                            <svg x-show="diagnosingSMTP" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <i class="ri-stethoscope-line mr-2" x-show="!diagnosingSMTP"></i>
                                            <span x-text="diagnosingSMTP ? 'Diagnostika...' : 'Diagnóza SMTP'"></span>
                                        </button>
                                        <button type="button" @click="applyRecommendedSettings()" class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 flex items-center">
                                            <i class="ri-settings-4-line mr-2"></i>
                                            <span>Použiť odporúčané</span>
                                        </button>
                                    </div>
                                    
                                    <!-- External Email Test -->
                                    <div class="border-t border-gray-200 pt-6 mt-6">
                                        <h6 class="text-sm font-semibold text-gray-900 mb-3">🌐 Test na external email (Gmail, Outlook, atď.)</h6>
                                        <div class="flex gap-4">
                                            <div class="flex-1">
                                                <input type="email" x-model="externalEmail" placeholder="Zadajte Gmail, Outlook alebo iný email" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm transition-all duration-200">
                                            </div>
                                            <button type="button" @click="testExternalEmail()" :disabled="testingExternalEmail || !externalEmail" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center">
                                                <svg x-show="testingExternalEmail" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <i class="ri-send-plane-line mr-2" x-show="!testingExternalEmail"></i>
                                                <span x-text="testingExternalEmail ? 'Odosielam...' : 'Test external'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <!-- Výsledky diagnostiky -->
                                <div x-show="smtpDiagnosisResult" x-transition class="mt-4 p-4 rounded-xl" :class="smtpDiagnosisResult?.success ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-orange-50 text-orange-800 border border-orange-200'">
                                    <p class="font-semibold mb-2" x-text="smtpDiagnosisResult?.message"></p>
                                    <div x-show="smtpDiagnosisResult?.details" class="mt-3 text-sm">
                                        <pre class="whitespace-pre-wrap font-mono text-xs bg-white/50 p-3 rounded border" x-text="smtpDiagnosisResult?.details"></pre>
                                    </div>
                                    <div x-show="smtpDiagnosisResult?.warnings && smtpDiagnosisResult?.warnings.length > 0" class="mt-2 text-sm">
                                        <strong>Upozornenia:</strong>
                                        <template x-for="warning in smtpDiagnosisResult?.warnings">
                                            <div class="ml-2" x-text="warning"></div>
                                        </template>
                                    </div>
                                </div>
                                <!-- Výsledky external email testu -->
                                <div x-show="externalEmailResult" x-transition class="mt-4 p-4 rounded-xl" :class="externalEmailResult?.success ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'">
                                    <p class="font-semibold mb-2" x-text="externalEmailResult?.message"></p>
                                    <div x-show="externalEmailResult?.details" class="mt-3 text-sm">
                                        <pre class="whitespace-pre-wrap font-mono text-xs bg-white/50 p-3 rounded border" x-text="externalEmailResult?.details"></pre>
                                    </div>
                                    <div x-show="externalEmailResult?.suggestions && externalEmailResult?.suggestions.length > 0" class="mt-2 text-sm">
                                        <strong>Odporúčania:</strong>
                                        <template x-for="suggestion in externalEmailResult?.suggestions">
                                            <div class="ml-2" x-text="suggestion"></div>
                                        </template>
                                    </div>
                                </div>
                                <!-- Výsledky testu -->
                                <div x-show="testResult" x-transition class="mt-4 p-4 rounded-xl" :class="testResult.success ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'">
                                    <p class="font-semibold mb-2" x-text="testResult.message"></p>
                                    <div x-show="testResult.details" class="mt-3 text-sm">
                                        <pre class="whitespace-pre-wrap font-mono text-xs bg-white/50 p-3 rounded border" x-text="testResult.details"></pre>
                                    </div>
                                    <div x-show="testResult.errors && testResult.errors.length > 0" class="mt-2 text-sm">
                                        <strong>Chyby:</strong>
                                        <template x-for="error in testResult.errors">
                                            <div class="ml-2" x-text="error"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Tlačidlá -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-pink-100/50">
                    <button type="button" onclick="window.location.reload()" class="px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        Zrušiť
                    </button>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-pink-600 to-rose-600 text-white text-sm font-semibold rounded-xl hover:from-pink-700 hover:to-rose-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Uložiť nastavenia
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function settingsForm() {
    return {
        sections: {
            stripe: false,
            google: false,
            facebook: false,
            analytics: false,
            payment: false,
            sms: false,
            invoices: false,
            email: false
        },
        testEmail: '',
        testingEmail: false,
        testResult: null,
        testingAdminNotifications: false,
        adminNotificationResult: null,
        diagnosingSMTP: false,
        smtpDiagnosisResult: null,
        testingExternalEmail: false,
        externalEmailResult: null,
        externalEmail: '',
        
        toggleSection(section) {
            this.sections[section] = !this.sections[section];
        },
        
        async sendTestEmail() {
            if (!this.testEmail) {
                this.testResult = {
                    success: false,
                    message: 'Prosím zadajte email adresu pre test'
                };
                return;
            }
            
            this.testingEmail = true;
            this.testResult = null;
            
            try {
                const formData = new FormData();
                formData.append('test_email', this.testEmail);
                formData.append('mail_host', document.getElementById('mail_host').value);
                formData.append('mail_port', document.getElementById('mail_port').value);
                formData.append('mail_username', document.getElementById('mail_username').value);
                formData.append('mail_password', document.getElementById('mail_password').value);
                formData.append('mail_encryption', document.getElementById('mail_encryption').value);
                formData.append('mail_from_address', document.getElementById('mail_from_address').value);
                formData.append('mail_from_name', document.getElementById('mail_from_name').value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                const response = await fetch('{{ route("admin.settings.test-email") }}', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                this.testResult = data;
                
            } catch (error) {
                this.testResult = {
                    success: false,
                    message: 'Chyba pri odosielaní testu: ' + error.message
                };
            } finally {
                this.testingEmail = false;
            }
        },
        
        async testAdminNotifications() {
            this.testingAdminNotifications = true;
            this.adminNotificationResult = null;
            
            try {
                const formData = new FormData();
                formData.append('mail_host', document.getElementById('mail_host').value);
                formData.append('mail_port', document.getElementById('mail_port').value);
                formData.append('mail_username', document.getElementById('mail_username').value);
                formData.append('mail_password', document.getElementById('mail_password').value);
                formData.append('mail_encryption', document.getElementById('mail_encryption').value);
                formData.append('mail_from_address', document.getElementById('mail_from_address').value);
                formData.append('mail_from_name', document.getElementById('mail_from_name').value);
                formData.append('admin_notification_emails', document.getElementById('admin_notification_emails').value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                const response = await fetch('{{ route("admin.settings.test-admin-notifications") }}', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                this.adminNotificationResult = data;
                
            } catch (error) {
                this.adminNotificationResult = {
                    success: false,
                    message: 'Chyba pri testovaní admin notifikácií: ' + error.message
                };
            } finally {
                this.testingAdminNotifications = false;
            }
        },
        
        async diagnoseSMTP() {
            this.diagnosingSMTP = true;
            this.smtpDiagnosisResult = null;
            
            try {
                const formData = new FormData();
                formData.append('mail_host', document.getElementById('mail_host').value);
                formData.append('mail_port', document.getElementById('mail_port').value);
                formData.append('mail_username', document.getElementById('mail_username').value);
                formData.append('mail_password', document.getElementById('mail_password').value);
                formData.append('mail_encryption', document.getElementById('mail_encryption').value);
                formData.append('mail_from_address', document.getElementById('mail_from_address').value);
                formData.append('mail_from_name', document.getElementById('mail_from_name').value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                const response = await fetch('{{ route("admin.settings.diagnose-smtp") }}', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                this.smtpDiagnosisResult = data;
                
            } catch (error) {
                this.smtpDiagnosisResult = {
                    success: false,
                    message: 'Chyba pri diagnostike SMTP: ' + error.message
                };
                         } finally {
                 this.diagnosingSMTP = false;
             }
         },
         
         async testExternalEmail() {
             if (!this.externalEmail) {
                 this.externalEmailResult = {
                     success: false,
                     message: 'Prosím zadajte email adresu pre external test'
                 };
                 return;
             }
             
             this.testingExternalEmail = true;
             this.externalEmailResult = null;
             
             try {
                 const formData = new FormData();
                 formData.append('external_email', this.externalEmail);
                 formData.append('mail_host', document.getElementById('mail_host').value);
                 formData.append('mail_port', document.getElementById('mail_port').value);
                 formData.append('mail_username', document.getElementById('mail_username').value);
                 formData.append('mail_password', document.getElementById('mail_password').value);
                 formData.append('mail_encryption', document.getElementById('mail_encryption').value);
                 formData.append('mail_from_address', document.getElementById('mail_from_address').value);
                 formData.append('mail_from_name', document.getElementById('mail_from_name').value);
                 formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                 
                 const response = await fetch('{{ route("admin.settings.test-external-email") }}', {
                     method: 'POST',
                     body: formData
                 });
                 
                 const data = await response.json();
                 this.externalEmailResult = data;
                 
             } catch (error) {
                 this.externalEmailResult = {
                     success: false,
                     message: 'Chyba pri testovaní external emailu: ' + error.message
                 };
             } finally {
                 this.testingExternalEmail = false;
             }
         },
         
         
         
         applyRecommendedSettings() {
             // Aplikovanie odporúčaných nastavení pre HostCreators
             document.getElementById('mail_port').value = '587';
             document.getElementById('mail_encryption').value = 'tls';
             
             // Upozornenie používateľa
             alert('✅ Aplikované odporúčané nastavenia:\n- Port: 587\n- Šifrovanie: TLS\n\nNezabudnite uložiť nastavenia!');
             
             // Skryť diagnostiku aby si mohli znovu spustiť
             this.smtpDiagnosisResult = null;
         }
     }
}

// Funkcia pre kopírovanie do schránky
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Zobrazíme notifikáciu
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.textContent = 'URL skopírované do schránky!';
        document.body.appendChild(notification);
        
        // Odstránime notifikáciu po 3 sekundách
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 3000);
    }).catch(function(err) {
        console.error('Chyba pri kopírovaní: ', err);
        alert('Chyba pri kopírovaní do schránky');
    });
}
</script>
@endpush
@endsection 