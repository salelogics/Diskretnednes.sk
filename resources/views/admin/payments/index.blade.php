@extends('layouts.admin-dashboard')

@section('header', 'Správa platieb')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Header s tlačidlami -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">Platby a faktúry</h2>
            <p class="text-gray-600 mt-2">Prehľad všetkých platieb a ich faktúr v systéme</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="exportInvoices()" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl text-sm font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export faktúr
            </button>
        </div>
    </div>

    <!-- Štatistiky -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-4">
        <!-- Celkové faktúry -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Celkové faktúry</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $totalInvoices }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    všetky faktúry
                </div>
            </div>
        </div>

        <!-- Zaplatené faktúry -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Zaplatené</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $paidInvoices }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    uhradené faktúry
                </div>
            </div>
        </div>

        <!-- Čakajúce faktúry -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Čakajúce</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $pendingInvoices }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    nevybavené faktúry
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <!-- Po splatnosti -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Po splatnosti</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $overdueInvoices }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    po termíne splatnosti
                </div>
            </div>
        </div>

        <!-- Celková suma zaplatených -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Celková suma</dt>
                        <dd class="text-2xl font-bold text-gray-900">€{{ number_format($totalRevenue, 2, ',', ' ') }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    celkový príjem
                </div>
            </div>
        </div>

        <!-- Čakajúca suma -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Čakajúca suma</dt>
                        <dd class="text-2xl font-bold text-gray-900">€{{ number_format($pendingRevenue, 2, ',', ' ') }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    nevybavené platby
                </div>
            </div>
        </div>
    </div>

    <!-- Tabuľka faktúr -->
    <div class="relative bg-gradient-to-br from-pink-50 via-white to-rose-50 shadow-xl rounded-3xl border border-pink-200/50 overflow-hidden">
        <!-- Dekoratívne pozadie -->
        <div class="absolute inset-0 bg-gradient-to-br from-pink-500/5 via-transparent to-rose-500/5"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-pink-200/20 to-transparent rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-rose-200/20 to-transparent rounded-full -ml-24 -mb-24"></div>
        
        <div class="relative px-8 py-8 border-b border-pink-100/50 backdrop-blur-sm">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                        Zoznam faktúr
                    </h3>
                    <p class="mt-2 text-gray-600">Prehľad všetkých faktúr v systéme s možnosťou správy</p>
                </div>
            </div>
        </div>
        
        <div class="relative p-8">
            <!-- Responzívne karty pre mobil -->
            <div class="block lg:hidden space-y-4">
                @forelse($invoices as $invoice)
                    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-lg font-bold text-gray-900">{{ $invoice->invoice_number ?: 'PL-' . $invoice->id }}</div>
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($invoice->status === 'completed') bg-green-100 text-green-800
                                    @elseif($invoice->status === 'pending') bg-blue-100 text-blue-800
                                    @elseif($invoice->status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $invoice->status_label }}
                                </span>
                            </div>
                            
                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Používateľ:</span>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $invoice->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $invoice->user->email }}</div>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Suma:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $invoice->formatted_amount }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Vystavenie:</span>
                                    <span class="text-sm text-gray-900">{{ $invoice->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Splatnosť:</span>
                                    <span class="text-sm text-gray-900">{{ $invoice->created_at->addDays(30)->format('d.m.Y') }}</span>
                                </div>
                            </div>
                            
                            <!-- Rýchle akcie pre mobil -->
                            <div class="flex flex-wrap gap-2">
                                @if($invoice->payment_method !== 'sms')
                                    <a href="{{ route('admin.platby.invoice.download', $invoice) }}" 
                                       class="flex-1 min-w-0 inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-white bg-pink-600 hover:bg-pink-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Stiahnuť
                                    </a>
                                    <button onclick="regenerateInvoice({{ $invoice->id }})" 
                                            class="flex-1 min-w-0 inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Pregenerovať
                                    </button>
                                    <button onclick="sendInvoice({{ $invoice->id }})" 
                                            class="flex-1 min-w-0 inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Odoslať
                                    </button>
                                @else
                                    <div class="flex-1 min-w-0 inline-flex items-center justify-center px-3 py-2 text-xs font-medium rounded-lg text-gray-600 bg-gray-100">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        SMS platba - faktúra sa nevystavuje
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg border border-pink-100/50 p-8 text-center">
                        <svg class="w-12 h-12 text-gray-300 mb-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">Žiadne faktúry neboli nájdené</p>
                        <p class="text-gray-400 text-sm mt-1">Faktúry sa zobrazia po ich vytvorení</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop tabuľka bez scrollu -->
            <div class="hidden lg:block bg-white/80 backdrop-blur-sm rounded-2xl shadow-inner overflow-hidden">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-pink-50 to-rose-50">
                        <tr>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Číslo faktúry</th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Používateľ</th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Suma</th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Status</th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Vystavenie</th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">Čas</th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Splatnosť</th>
                            <th scope="col" class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-64">Rýchle akcie</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-pink-50/50 transition-colors duration-200">
                                <td class="px-4 py-4 text-sm font-medium text-gray-900">
                                    {{ $invoice->invoice_number ?: 'PL-' . $invoice->id }}
                                </td>
                                <td class="px-4 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-48">{{ $invoice->user->name }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-48">{{ $invoice->user->email }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900">
                                    {{ $invoice->formatted_amount }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($invoice->status === 'completed') bg-green-100 text-green-800
                                        @elseif($invoice->status === 'pending') bg-blue-100 text-blue-800
                                        @elseif($invoice->status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $invoice->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500">
                                    {{ $invoice->created_at->format('d.m.Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500">
                                    {{ $invoice->created_at->format('H:i') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500">
                                    {{ $invoice->created_at->addDays(30)->format('d.m.Y') }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center space-x-1">
                                        <!-- Schvaľovanie/Zamietnutie pre čakajúce platby -->
                                        @if($invoice->status === 'pending')
                                            <button onclick="approvePayment({{ $invoice->id }})" 
                                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 group"
                                                    title="Schváliť platbu">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                            <button onclick="rejectPayment({{ $invoice->id }})" 
                                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 group"
                                                    title="Zamietnuť platbu">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif

                                        <!-- Stiahnuť -->
                                        @if($invoice->payment_method !== 'sms')
                                            <a href="{{ route('admin.platby.invoice.download', $invoice) }}" 
                                               class="inline-flex items-center justify-center w-8 h-8 text-white bg-pink-600 hover:bg-pink-700 rounded-lg transition-colors duration-200 group"
                                               title="Stiahnuť faktúru">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-400 bg-gray-100 rounded-lg" title="SMS platba - faktúra sa nevystavuje">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                            </span>
                                        @endif

                                        <!-- Pregenerovať -->
                                        @if($invoice->payment_method !== 'sms')
                                            <button onclick="regenerateInvoice({{ $invoice->id }})" 
                                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 group"
                                                    title="Pregenerovať faktúru">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-400 bg-gray-100 rounded-lg" title="SMS platba - faktúra sa nevystavuje">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                </svg>
                                            </span>
                                        @endif

                                        <!-- Odoslať -->
                                        @if($invoice->payment_method !== 'sms')
                                            <button onclick="sendInvoice({{ $invoice->id }})" 
                                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 group"
                                                    title="Odoslať faktúru">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-400 bg-gray-100 rounded-lg" title="SMS platba - faktúra sa nevystavuje">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                         @empty
                             <tr>
                                 <td colspan="7" class="px-4 py-8 text-center">
                                     <div class="flex flex-col items-center">
                                         <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                         </svg>
                                         <p class="text-gray-500 text-lg font-medium">Žiadne faktúry neboli nájdené</p>
                                         <p class="text-gray-400 text-sm mt-1">Faktúry sa zobrazia po ich vytvorení</p>
                                     </div>
                                 </td>
                             </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
            
            <!-- Paginácia -->
            @if($invoices->hasPages())
                <div class="mt-6 px-6 pb-6">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript pre faktúry -->
<script>
function regenerateInvoice(invoiceId) {
    showConfirmModal(
        'Pregenerovať faktúru',
        'Naozaj chcete pregenerovať túto faktúru? Pôvodný PDF súbor bude prepísaný.',
        function() {
            fetch(`/admin/platby/faktury/${invoiceId}/pregenerovat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Faktúra bola úspešne pregenerovaná!', 'success');
                } else {
                    showToast(data.message || 'Chyba pri pregenerovaní faktúry.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Nastala chyba pri pregenerovaní faktúry.', 'error');
            });
        }
    );
}

function sendInvoice(invoiceId) {
    showConfirmModal(
        'Poslať faktúru emailom',
        'Naozaj chcete poslať túto faktúru na email zákazníka?',
        function() {
            fetch(`/admin/platby/faktury/${invoiceId}/poslat-email`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Faktúra bola úspešne odoslaná!', 'success');
                    // Refresh page to update status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Chyba pri odosielaní faktúry.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Nastala chyba pri odosielaní faktúry.', 'error');
            });
        }
    );
}

function exportInvoices() {
    showToast('Export faktúr sa pripravuje...', 'info');
    // Tu môžete pridať logiku pre export
}

function approvePayment(invoiceId) {
    showConfirmModal(
        'Schvaľovať platbu',
        'Naozaj chcete schváliť túto platbu?',
        function() {
            fetch(`/admin/platby/faktury/${invoiceId}/schvalit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Platba bola úspešne schválená!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Chyba pri schvaľovaní platby.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Nastala chyba pri schvaľovaní platby.', 'error');
            });
        }
    );
}

function rejectPayment(invoiceId) {
    showConfirmModal(
        'Zamietnuť platbu',
        'Naozaj chcete zamietnuť túto platbu?',
        function() {
            fetch(`/admin/platby/faktury/${invoiceId}/zamietnut`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Platba bola úspešne zamietnutá!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Chyba pri zamietaní platby.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Nastala chyba pri zamietaní platby.', 'error');
            });
        }
    );
}

// Moderný confirm modal
function showConfirmModal(title, message, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 shadow-2xl transform transition-all">
            <div class="text-center">
                <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">${title}</h3>
                <p class="text-gray-600 mb-6">${message}</p>
                <div class="flex space-x-3">
                    <button onclick="closeModal(this)" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors">
                        Zrušiť
                    </button>
                    <button onclick="confirmAction(this, arguments[0])" class="flex-1 bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        Potvrdiť
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Uložíme callback funkciu do modal elementu
    modal.confirmCallback = onConfirm;
}

function closeModal(button) {
    button.closest('.fixed').remove();
}

function confirmAction(button) {
    const modal = button.closest('.fixed');
    const callback = modal.confirmCallback;
    modal.remove();
    if (callback) callback();
}

// Toast notifikácie
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-pink-500'
    };
    
    toast.className = `fixed top-4 right-4 px-6 py-4 rounded-xl text-white z-50 shadow-lg transform transition-all duration-300 ${colors[type]}`;
    toast.innerHTML = `
        <div class="flex items-center">
            <span class="mr-2">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Automatické odstránenie po 5 sekundách
    setTimeout(() => {
        if (toast.parentElement) {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
}
</script>
@endsection 