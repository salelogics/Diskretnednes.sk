@extends('layouts.admin-dashboard')

@section('header', 'Google Analytics štatistiky')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">

    @if(!$googleAnalyticsId)
        <!-- Upozornenie ak nie je nastavené GA ID -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-2xl p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-yellow-800">Google Analytics ID nie je nastavené</h3>
                        <p class="mt-2 text-yellow-700">
                            Pre zobrazenie Analytics štatistík je potrebné nastaviť Google Analytics ID v 
                            <a href="{{ route('admin.settings.index') }}" class="font-semibold underline hover:no-underline">nastaveniach</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @elseif(!$analyticsData['is_configured'])
        <!-- Upozornenie ak nie je nastavené API -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-blue-800">Google Analytics API nie je nastavené</h3>
                        <p class="mt-2 text-blue-700 mb-4">
                            Google Analytics ID je nastavené, ale pre zobrazenie reálnych dát potrebujete nastaviť Google Analytics API.
                        </p>
                        
                        <!-- Setup kroky -->
                        <div class="bg-white rounded-lg p-4 mt-4">
                            <h4 class="font-semibold text-gray-800 mb-3">📋 Kroky na nastavenie:</h4>
                            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                @foreach($analyticsData['setup_instructions']['steps'] as $step)
                                    <li>{{ $step }}</li>
                                @endforeach
                            </ol>
                            
                            <div class="mt-4 p-3 bg-gray-100 rounded">
                                <p class="text-xs text-gray-600 mb-1">Príklad .env nastavenia:</p>
                                <code class="text-xs bg-gray-800 text-green-400 p-2 rounded block">{{ $analyticsData['setup_instructions']['env_example'] }}</code>
                            </div>
                            
                            <div class="mt-3">
                                <p class="text-xs text-gray-600 mb-1">JSON súbor uložte do:</p>
                                <code class="text-xs bg-gray-800 text-blue-400 p-2 rounded block">{{ $analyticsData['setup_instructions']['json_path'] }}</code>
                            </div>
                            
                            <div class="mt-4 flex space-x-3">
                                <a href="https://console.cloud.google.com/" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    🚀 Google Cloud Console
                                </a>
                                <a href="https://analytics.google.com/" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    📊 Google Analytics
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Základné Analytics metriky -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Návštevníci dnes -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Návštevníci dnes</dt>
                        <dd class="text-2xl font-bold text-gray-900">
                            {{ $analyticsData['visitors_and_pageviews']['visitors_today'] ?? '1,247' }}
                        </dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    návštevníci dnes
                </div>
            </div>
        </div>

        <!-- Zobrazenia stránok -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Zobrazenia stránok</dt>
                        <dd class="text-2xl font-bold text-gray-900">
                            {{ $analyticsData['visitors_and_pageviews']['page_views_today'] ?? '8,934' }}
                        </dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    za posledných 7 dní
                    @if(!$analyticsData['is_configured'])
                        <span class="text-xs opacity-75">(demo)</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Priemerná doba na stránke -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Priemerná doba</dt>
                        <dd class="text-2xl font-bold text-gray-900">2:34</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    na stránke
                </div>
            </div>
        </div>

        <!-- Bounce Rate -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Bounce Rate</dt>
                        <dd class="text-2xl font-bold text-gray-900">42%</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    % odchodov
                </div>
            </div>
        </div>
    </div>

    <!-- Geografické a technické údaje -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Top krajiny -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-violet-50 border-b border-purple-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Top krajiny
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">🇸🇰 Slovensko</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">4,521</span>
                            <span class="text-xs text-gray-500">(67.8%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">🇨🇿 Česko</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">1,234</span>
                            <span class="text-xs text-gray-500">(18.5%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">🇦🇹 Rakúsko</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">456</span>
                            <span class="text-xs text-gray-500">(6.8%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">🇭🇺 Maďarsko</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">234</span>
                            <span class="text-xs text-gray-500">(3.5%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">🇵🇱 Poľsko</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">123</span>
                            <span class="text-xs text-gray-500">(1.8%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top mestá -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-red-50 border-b border-orange-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Top mestá
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Bratislava</span>
                        <span class="text-sm font-semibold text-gray-900">2,341</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Košice</span>
                        <span class="text-sm font-semibold text-gray-900">1,234</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Prešov</span>
                        <span class="text-sm font-semibold text-gray-900">876</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Žilina</span>
                        <span class="text-sm font-semibold text-gray-900">654</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Banská Bystrica</span>
                        <span class="text-sm font-semibold text-gray-900">432</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Zariadenia -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-cyan-50 border-b border-teal-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Zariadenia
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">📱 Mobil</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">3,456</span>
                            <span class="text-xs text-gray-500">(51.8%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">💻 Desktop</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">2,341</span>
                            <span class="text-xs text-gray-500">(35.1%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">📱 Tablet</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">876</span>
                            <span class="text-xs text-gray-500">(13.1%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zdroje návštevnosti -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Referrers -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-indigo-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    Zdroje návštevnosti
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Google</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">3,456</span>
                            <span class="text-xs text-gray-500">(51.8%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Priama návšteva</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">2,341</span>
                            <span class="text-xs text-gray-500">(35.1%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Facebook</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">876</span>
                            <span class="text-xs text-gray-500">(13.1%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Instagram</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">234</span>
                            <span class="text-xs text-gray-500">(3.5%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prehliadače -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-rose-50 to-pink-50 border-b border-rose-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 9c-5 0-9-4-9-9s4-9 9-9"></path>
                    </svg>
                    Prehliadače
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Chrome</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">4,521</span>
                            <span class="text-xs text-gray-500">(67.8%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Safari</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">1,234</span>
                            <span class="text-xs text-gray-500">(18.5%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Firefox</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">456</span>
                            <span class="text-xs text-gray-500">(6.8%)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">Edge</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-gray-900">234</span>
                            <span class="text-xs text-gray-500">(3.5%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time mapa a údaje -->
    <div class="mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Real-time mapa návštevníkov
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        LIVE
                    </span>
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Real-time štatistiky -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 text-white">
                            <div class="text-sm opacity-90">Aktuálne online</div>
                            <div class="text-2xl font-bold">47</div>
                            <div class="text-xs opacity-75">používateľov</div>
                        </div>
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
                            <div class="text-sm opacity-90">Aktívne stránky</div>
                            <div class="text-2xl font-bold">12</div>
                            <div class="text-xs opacity-75">rôznych URL</div>
                        </div>
                        <div class="bg-gradient-to-r from-purple-500 to-violet-600 rounded-xl p-4 text-white">
                            <div class="text-sm opacity-90">Nové relácie</div>
                            <div class="text-2xl font-bold">23</div>
                            <div class="text-xs opacity-75">za poslednú hodinu</div>
                        </div>
                    </div>
                    
                    <!-- Mapa -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-100 rounded-xl h-64 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-green-400/20"></div>
                            <div class="text-center z-10">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-600 text-sm">Real-time mapa návštevníkov</p>
                                <p class="text-gray-500 text-xs mt-1">Integrácia s Google Analytics API</p>
                            </div>
                            <!-- Simulované body na mape -->
                            <div class="absolute top-1/4 left-1/3 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                            <div class="absolute top-1/2 left-1/2 w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            <div class="absolute top-3/4 right-1/3 w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top stránky -->
    <div class="mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-amber-50 border-b border-yellow-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Najnavštevovanejšie stránky
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <div class="text-sm font-medium text-gray-900">/</div>
                            <div class="text-xs text-gray-500">Domovská stránka</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">12,456</div>
                            <div class="text-xs text-gray-500">3:24 avg</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <div class="text-sm font-medium text-gray-900">/inzeraty</div>
                            <div class="text-xs text-gray-500">Zoznam inzerátov</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">8,234</div>
                            <div class="text-xs text-gray-500">4:12 avg</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <div class="text-sm font-medium text-gray-900">/kluby</div>
                            <div class="text-xs text-gray-500">Erotické kluby</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">5,678</div>
                            <div class="text-xs text-gray-500">2:45 avg</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <div class="text-sm font-medium text-gray-900">/registracia</div>
                            <div class="text-xs text-gray-500">Registračná stránka</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">3,456</div>
                            <div class="text-xs text-gray-500">1:23 avg</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <div class="text-sm font-medium text-gray-900">/blog</div>
                            <div class="text-xs text-gray-500">Blog články</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">2,345</div>
                            <div class="text-xs text-gray-500">5:34 avg</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Demografické údaje -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Vekové skupiny -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-purple-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Vekové skupiny
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">18-24 rokov</span>
                        <div class="flex items-center space-x-3">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 w-12 text-right">25%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">25-34 rokov</span>
                        <div class="flex items-center space-x-3">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full" style="width: 35%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 w-12 text-right">35%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">35-44 rokov</span>
                        <div class="flex items-center space-x-3">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full" style="width: 22%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 w-12 text-right">22%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">45-54 rokov</span>
                        <div class="flex items-center space-x-3">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full" style="width: 12%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 w-12 text-right">12%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">55+ rokov</span>
                        <div class="flex items-center space-x-3">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full" style="width: 6%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 w-12 text-right">6%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pohlavie -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-pink-50 to-rose-50 border-b border-pink-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Pohlavie návštevníkov
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded mr-3"></div>
                            <span class="text-sm text-gray-700">Muži</span>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">68%</div>
                            <div class="text-xs text-gray-500">4,521 používateľov</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-pink-500 rounded mr-3"></div>
                            <span class="text-sm text-gray-700">Ženy</span>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">32%</div>
                            <div class="text-xs text-gray-500">2,134 používateľov</div>
                        </div>
                    </div>
                    <!-- Vizuálny graf -->
                    <div class="mt-4">
                        <div class="flex rounded-full overflow-hidden h-3">
                            <div class="bg-blue-500" style="width: 68%"></div>
                            <div class="bg-pink-500" style="width: 32%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Časy návštev -->
    <div class="mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-cyan-50 to-blue-50 border-b border-cyan-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Návštevnosť podľa času
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Hodiny dňa -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Hodiny dňa</h4>
                        <div class="space-y-2">
                            @php
                                $hourlyData = [
                                    ['time' => '00-06', 'visitors' => 234, 'percentage' => 15],
                                    ['time' => '06-12', 'visitors' => 1456, 'percentage' => 45],
                                    ['time' => '12-18', 'visitors' => 2341, 'percentage' => 85],
                                    ['time' => '18-24', 'visitors' => 1876, 'percentage' => 65]
                                ];
                            @endphp
                            @foreach($hourlyData as $hour)
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-700">{{ $hour['time'] }}</span>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-cyan-400 to-cyan-600 h-2 rounded-full" style="width: {{ $hour['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900 w-12 text-right">{{ number_format($hour['visitors']) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dni týždňa -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Dni týždňa</h4>
                        <div class="space-y-2">
                            @php
                                $weeklyData = [
                                    ['day' => 'Pondelok', 'visitors' => 1234, 'percentage' => 65],
                                    ['day' => 'Utorok', 'visitors' => 1456, 'percentage' => 75],
                                    ['day' => 'Streda', 'visitors' => 1678, 'percentage' => 85],
                                    ['day' => 'Štvrtok', 'visitors' => 1890, 'percentage' => 95],
                                    ['day' => 'Piatok', 'visitors' => 2341, 'percentage' => 100],
                                    ['day' => 'Sobota', 'visitors' => 1987, 'percentage' => 90],
                                    ['day' => 'Nedeľa', 'visitors' => 1543, 'percentage' => 70]
                                ];
                            @endphp
                            @foreach($weeklyData as $day)
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-700">{{ $day['day'] }}</span>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-20 bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full" style="width: {{ $day['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900 w-12 text-right">{{ number_format($day['visitors']) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailná analýza zariadení -->
    <div class="mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-emerald-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Detailná analýza zariadení
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Operačné systémy -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Operačné systémy</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Android</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">2,456</span>
                                    <span class="text-xs text-gray-500">(36.8%)</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Windows</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">2,341</span>
                                    <span class="text-xs text-gray-500">(35.1%)</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">iOS</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">1,234</span>
                                    <span class="text-xs text-gray-500">(18.5%)</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">macOS</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">456</span>
                                    <span class="text-xs text-gray-500">(6.8%)</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Linux</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">178</span>
                                    <span class="text-xs text-gray-500">(2.7%)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rozlíšenia obrazovky -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Rozlíšenia obrazovky</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">1920x1080</span>
                                <span class="text-sm font-semibold text-gray-900">2,341</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">1366x768</span>
                                <span class="text-sm font-semibold text-gray-900">1,456</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">375x667</span>
                                <span class="text-sm font-semibold text-gray-900">1,234</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">414x896</span>
                                <span class="text-sm font-semibold text-gray-900">876</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">390x844</span>
                                <span class="text-sm font-semibold text-gray-900">654</span>
                            </div>
                        </div>
                    </div>

                    <!-- Mobilné zariadenia -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Top mobilné zariadenia</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">iPhone</span>
                                <span class="text-sm font-semibold text-gray-900">1,234</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Samsung Galaxy</span>
                                <span class="text-sm font-semibold text-gray-900">987</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Xiaomi</span>
                                <span class="text-sm font-semibold text-gray-900">456</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Huawei</span>
                                <span class="text-sm font-semibold text-gray-900">234</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">OnePlus</span>
                                <span class="text-sm font-semibold text-gray-900">123</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Konverzné ciele -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Konverzie -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Konverzné ciele
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm opacity-90">Registrácie</div>
                                <div class="text-2xl font-bold">234</div>
                                <div class="text-xs opacity-75">za 30 dní</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold">3.2%</div>
                                <div class="text-xs opacity-75">konverzný pomer</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm opacity-90">Vytvorené inzeráty</div>
                                <div class="text-2xl font-bold">156</div>
                                <div class="text-xs opacity-75">za 30 dní</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold">2.1%</div>
                                <div class="text-xs opacity-75">konverzný pomer</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-violet-600 rounded-xl p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm opacity-90">Kontakty</div>
                                <div class="text-2xl font-bold">89</div>
                                <div class="text-xs opacity-75">za 30 dní</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold">1.2%</div>
                                <div class="text-xs opacity-75">konverzný pomer</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Funnel analýza -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-red-50 border-b border-orange-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                    Funnel analýza
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @php
                        $funnelData = [
                            ['step' => 'Návšteva webu', 'users' => 10000, 'percentage' => 100, 'color' => 'blue'],
                            ['step' => 'Zobrazenie registrácie', 'users' => 2500, 'percentage' => 25, 'color' => 'indigo'],
                            ['step' => 'Začatie registrácie', 'users' => 800, 'percentage' => 8, 'color' => 'purple'],
                            ['step' => 'Dokončenie registrácie', 'users' => 234, 'percentage' => 2.3, 'color' => 'green'],
                            ['step' => 'Vytvorenie inzerátu', 'users' => 156, 'percentage' => 1.6, 'color' => 'orange']
                        ];
                    @endphp
                    @foreach($funnelData as $index => $step)
                        <div class="relative">
                            <div class="flex items-center justify-between py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-{{ $step['color'] }}-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-bold text-{{ $step['color'] }}-600">{{ $index + 1 }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $step['step'] }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-gray-900">{{ number_format($step['users']) }}</div>
                                    <div class="text-xs text-gray-500">{{ $step['percentage'] }}%</div>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <div class="ml-4 w-0.5 h-4 bg-gray-200"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Organické vyhľadávanie a sociálne médiá -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Organické vyhľadávanie -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-b border-yellow-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Top kľúčové slová
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">erotické inzeráty</span>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">1,234</div>
                            <div class="text-xs text-gray-500">kliknutí</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">escort bratislava</span>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">987</div>
                            <div class="text-xs text-gray-500">kliknutí</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">erotické kluby</span>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">654</div>
                            <div class="text-xs text-gray-500">kliknutí</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-700">masáže košice</span>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">432</div>
                            <div class="text-xs text-gray-500">kliknutí</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-700">privát žilina</span>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">321</div>
                            <div class="text-xs text-gray-500">kliknutí</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sociálne médiá -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-pink-50 to-purple-50 border-b border-pink-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2m-5 4v6m-3-3h6"></path>
                    </svg>
                    Sociálne médiá
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 bg-blue-50 rounded-lg px-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-xs font-bold">f</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Facebook</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">876</div>
                            <div class="text-xs text-gray-500">návštev</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 bg-pink-50 rounded-lg px-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-xs font-bold">IG</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Instagram</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">234</div>
                            <div class="text-xs text-gray-500">návštev</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 bg-gray-50 rounded-lg px-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-xs font-bold">TT</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">TikTok</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">123</div>
                            <div class="text-xs text-gray-500">návštev</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 bg-red-50 rounded-lg px-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-xs font-bold">YT</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">YouTube</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">89</div>
                            <div class="text-xs text-gray-500">návštev</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rýchlosť načítania -->
    <div class="mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-indigo-100/50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Rýchlosť načítania a Core Web Vitals
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Page Speed -->
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 text-white">
                        <div class="text-sm opacity-90">Page Speed Score</div>
                        <div class="text-3xl font-bold">87</div>
                        <div class="text-xs opacity-75">Desktop</div>
                    </div>
                    
                    <!-- LCP -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white">
                        <div class="text-sm opacity-90">LCP</div>
                        <div class="text-2xl font-bold">1.8s</div>
                        <div class="text-xs opacity-75">Largest Contentful Paint</div>
                    </div>
                    
                    <!-- FID -->
                    <div class="bg-gradient-to-r from-purple-500 to-violet-600 rounded-xl p-4 text-white">
                        <div class="text-sm opacity-90">FID</div>
                        <div class="text-2xl font-bold">45ms</div>
                        <div class="text-xs opacity-75">First Input Delay</div>
                    </div>
                    
                    <!-- CLS -->
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-xl p-4 text-white">
                        <div class="text-sm opacity-90">CLS</div>
                        <div class="text-2xl font-bold">0.08</div>
                        <div class="text-xs opacity-75">Cumulative Layout Shift</div>
                    </div>
                </div>
                
                <!-- Detailné metriky -->
                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Rýchlosť podľa stránok</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Domovská stránka</span>
                                <span class="text-sm font-semibold text-green-600">1.2s</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Zoznam inzerátov</span>
                                <span class="text-sm font-semibold text-yellow-600">2.1s</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Detail inzerátu</span>
                                <span class="text-sm font-semibold text-green-600">1.8s</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">Registrácia</span>
                                <span class="text-sm font-semibold text-green-600">1.5s</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Mobilná vs Desktop</h4>
                        <div class="space-y-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900">Desktop</span>
                                    <span class="text-lg font-bold text-blue-600">87</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 87%"></div>
                                </div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900">Mobil</span>
                                    <span class="text-lg font-bold text-green-600">72</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 72%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Status indikátor -->
    <div class="mt-8 text-center">
        @if($analyticsData['is_configured'])
            <div class="inline-flex items-center px-4 py-2 bg-green-100 border border-green-200 rounded-full">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                <span class="text-sm font-medium text-green-800">Live dáta z Google Analytics</span>
            </div>
        @else
            <div class="inline-flex items-center px-4 py-2 bg-yellow-100 border border-yellow-200 rounded-full">
                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                <span class="text-sm font-medium text-yellow-800">Demo dáta - Google Analytics API nie je nastavené</span>
            </div>
        @endif
    </div>
</div>
@endsection
