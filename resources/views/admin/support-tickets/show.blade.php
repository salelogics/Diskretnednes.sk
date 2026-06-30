@extends('layouts.admin-dashboard')

@section('header', 'Support Ticket #' . $ticket->id)

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Breadcrumbs a header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <!-- Breadcrumbs -->
                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-3">
                    <a href="{{ route('admin.nastenka') }}" class="hover:text-pink-600 transition-colors">Admin</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="{{ route('admin.support-tickets.index') }}" class="hover:text-pink-600 transition-colors">Support Tickety</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">#{{ $ticket->id }}</span>
                </div>
                
                <!-- Nadpis -->
                <h1 class="text-3xl font-bold text-gray-900">
                    Support Ticket #{{ $ticket->id }}
                </h1>
                <p class="mt-2 text-gray-600 font-medium">{{ $ticket->subject }}</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                    @if($ticket->status === 'new') bg-pink-100 text-pink-800 border border-pink-200
                    @elseif($ticket->status === 'in_progress') bg-amber-100 text-amber-800 border border-amber-200
                    @elseif($ticket->status === 'resolved') bg-emerald-100 text-emerald-800 border border-emerald-200
                    @else bg-gray-100 text-gray-800 border border-gray-200
                    @endif">
                    {{ $ticket->status_label }}
                </span>
                <a href="{{ route('admin.support-tickets.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Späť na zoznam
                </a>
            </div>
        </div>
    </div>

    <!-- Flash správy -->
    @if(session('success'))
        <div class="mb-8 rounded-lg bg-emerald-50 p-4 border border-emerald-200">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Hlavný obsah -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Pôvodná správa -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-200">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Pôvodná správa</h3>
                            <p class="text-sm text-gray-600">Od používateľa {{ $ticket->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <div class="prose max-w-none">
                        <p class="text-gray-900 whitespace-pre-wrap leading-relaxed">{{ $ticket->message }}</p>
                    </div>
                </div>
            </div>

            <!-- Admin odpoveď (ak existuje) -->
            @if($ticket->admin_response)
                <div class="bg-blue-50 shadow-lg rounded-2xl border border-blue-200">
                    <div class="px-6 py-5 border-b border-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-blue-900">Admin odpoveď</h3>
                                    <p class="text-sm text-blue-700">Oficiálna odpoveď administrátora</p>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-blue-600 bg-blue-100 px-3 py-1 rounded-full">
                                {{ $ticket->responded_at->format('d.m.Y H:i') }}
                            </span>
                        </div>
                    </div>
                    <div class="px-6 py-5">
                        <div class="prose max-w-none">
                            <p class="text-blue-900 whitespace-pre-wrap leading-relaxed">{{ $ticket->admin_response }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulár pre odpoveď -->
            @if(!$ticket->admin_response || $ticket->status !== 'closed')
                <div class="bg-white shadow-lg rounded-2xl border border-gray-200">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $ticket->admin_response ? 'Aktualizovať odpoveď' : 'Odpovedať zákazníkovi' }}
                                </h3>
                                <p class="text-sm text-gray-600">Napíšte odpoveď pre používateľa</p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.support-tickets.respond', $ticket) }}" method="POST" class="px-6 py-5 space-y-5">
                        @csrf
                        
                        <div>
                            <label for="response" class="block text-sm font-semibold text-gray-700 mb-2">Odpoveď</label>
                            <textarea name="response" id="response" rows="6" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('response') border-red-300 @enderror"
                                      placeholder="Napíšte odpoveď pre zákazníka...">{{ old('response', $ticket->admin_response) }}</textarea>
                            @error('response')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Nový status</label>
                            <select name="status" id="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500 @error('status') border-red-300 @enderror">
                                <option value="in_progress" {{ old('status', $ticket->status) === 'in_progress' ? 'selected' : '' }}>V riešení</option>
                                <option value="resolved" {{ old('status', $ticket->status) === 'resolved' ? 'selected' : '' }}>Vyriešený</option>
                                <option value="closed" {{ old('status', $ticket->status) === 'closed' ? 'selected' : '' }}>Uzavretý</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="send_email" id="send_email" value="1" checked 
                                   class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="send_email" class="ml-2 block text-sm font-medium text-gray-700">
                                Odoslať email používateľovi s odpoveďou
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-pink-600 hover:bg-pink-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                {{ $ticket->admin_response ? 'Aktualizovať odpoveď' : 'Odoslať odpoveď' }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar s informáciami -->
        <div class="space-y-6">
            <!-- Informácie o tickete -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Informácie o tickete</h3>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Typ problému</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->problem_type_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vytvorené</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @if($ticket->responded_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Posledná odpoveď</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->responded_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informácie o používateľovi -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Informácie o používateľovi</h3>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Meno</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm font-medium">
                            <a href="mailto:{{ $ticket->email }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                {{ $ticket->email }}
                            </a>
                        </dd>
                    </div>
                    @if($ticket->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telefón</dt>
                            <dd class="mt-1 text-sm font-medium">
                                <a href="tel:{{ $ticket->phone }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                                    {{ $ticket->phone }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Registrovaný</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->user->created_at->format('d.m.Y') }}</dd>
                    </div>
                </div>
            </div>

            <!-- Rýchle akcie -->
            <div class="bg-white shadow-lg rounded-2xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Rýchle akcie</h3>
                    </div>
                </div>
                <div class="px-5 py-4 space-y-2">
                    <form action="{{ route('admin.support-tickets.update-status', $ticket) }}" method="POST" class="w-full">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="in_progress">
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ $ticket->status === 'in_progress' ? 'bg-amber-100 text-amber-800' : 'text-gray-700 hover:bg-amber-50' }}">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-amber-100 rounded flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                Označiť ako "V riešení"
                            </div>
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.support-tickets.update-status', $ticket) }}" method="POST" class="w-full">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="resolved">
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ $ticket->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'text-gray-700 hover:bg-emerald-50' }}">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-emerald-100 rounded flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                Označiť ako "Vyriešený"
                            </div>
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.support-tickets.update-status', $ticket) }}" method="POST" class="w-full">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="closed">
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : 'text-gray-700 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-gray-100 rounded flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                Označiť ako "Uzavretý"
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 