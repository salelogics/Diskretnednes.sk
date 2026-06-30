@extends('layouts.admin-dashboard')

@section('header', 'Detail používateľa')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Späť na zoznam používateľov
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Hlavné informácie -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profil používateľa -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Informácie o používateľovi</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center space-x-6">
                        @if($user->photo)
                            <img class="h-20 w-20 rounded-full object-cover" src="{{ $user->profile_photo_url }}" alt="Používateľ">
                        @else
                            <div class="h-20 w-20 rounded-full bg-pink-600 flex items-center justify-center">
                                <span class="text-2xl font-medium text-white">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h4 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h4>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            <div class="flex items-center space-x-2 mt-2">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Overený email
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Neoverený email
                                    </span>
                                @endif
                                @if($user->is_admin)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9.243 3.03a1 1 0 01.727 1.213L9.53 6h2.94l.56-2.243a1 1 0 111.94.486L14.53 6H17a1 1 0 110 2h-2.97l-1 4H15a1 1 0 110 2h-2.47l-.56 2.242a1 1 0 11-1.94-.485L10.47 14H7.53l-.56 2.242a1 1 0 11-1.94-.485L5.47 14H3a1 1 0 110-2h2.97l1-4H5a1 1 0 110-2h2.47l.56-2.243a1 1 0 011.213-.727zM9.03 8l-1 4h2.94l1-4H9.03z" clip-rule="evenodd"></path>
                                        </svg>
                                        Administrátor
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Registrácia</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Posledná aktivita</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at ? $user->updated_at->format('d.m.Y H:i') : 'Neuvedené' }}</dd>
                        </div>
                        @if($user->email_verified_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email overený</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->email_verified_at->format('d.m.Y H:i') }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID používateľa</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $user->id }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inzeráty používateľa -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Inzeráty ({{ $user->ads->count() }})
                    </h3>
                </div>
                <div class="px-6 py-4">
                    @if($user->ads->count() > 0)
                        <div class="space-y-4">
                            @foreach($user->ads->take(5) as $ad)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $ad->title }}</h4>
                                        <p class="text-sm text-gray-500">{{ Str::limit($ad->description, 100) }}</p>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <span class="text-xs text-gray-500">{{ $ad->created_at ? $ad->created_at->format('d.m.Y') : 'Neuvedené' }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $ad->status === 'active' ? 'green' : 'gray' }}-100 text-{{ $ad->status === 'active' ? 'green' : 'gray' }}-800">
                                                {{ $ad->status === 'active' ? 'Aktívny' : 'Neaktívny' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('admin.inzeraty.show', $ad) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            Zobraziť
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            @if($user->ads->count() > 5)
                                <div class="text-center">
                                    <a href="{{ route('admin.inzeraty.index', ['user' => $user->id]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                        Zobraziť všetky inzeráty ({{ $user->ads->count() }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Žiadne inzeráty</h3>
                            <p class="mt-1 text-sm text-gray-500">Tento používateľ zatiaľ nevytvoril žiadne inzeráty.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Support tickety -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Support tickety ({{ $user->supportTickets->count() }})
                    </h3>
                </div>
                <div class="px-6 py-4">
                    @if($user->supportTickets->count() > 0)
                        <div class="space-y-4">
                            @foreach($user->supportTickets->take(3) as $ticket)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $ticket->subject }}</h4>
                                        <p class="text-sm text-gray-500">{{ Str::limit($ticket->message, 100) }}</p>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <span class="text-xs text-gray-500">{{ $ticket->created_at ? $ticket->created_at->format('d.m.Y H:i') : 'Neuvedené' }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $ticket->status === 'open' ? 'yellow' : ($ticket->status === 'closed' ? 'green' : 'blue') }}-100 text-{{ $ticket->status === 'open' ? 'yellow' : ($ticket->status === 'closed' ? 'green' : 'blue') }}-800">
                                                {{ $ticket->status === 'open' ? 'Otvorený' : ($ticket->status === 'closed' ? 'Uzavretý' : 'V riešení') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('admin.support-tickets.show', $ticket) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            Zobraziť
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                            @if($user->supportTickets->count() > 3)
                                <div class="text-center">
                                    <a href="{{ route('admin.support-tickets.index', ['user' => $user->id]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                        Zobraziť všetky tickety ({{ $user->supportTickets->count() }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Žiadne support tickety</h3>
                            <p class="mt-1 text-sm text-gray-500">Tento používateľ zatiaľ nevytvoril žiadne support tickety.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bočný panel -->
        <div class="space-y-6">
            <!-- Štatistiky -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Štatistiky</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Celkom inzerátov</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $user->ads->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Aktívne inzeráty</dt>
                            <dd class="mt-1 text-2xl font-semibold text-green-600">{{ $user->ads->where('status', 'active')->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Support tickety</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $user->supportTickets->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Platby</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $user->adPayments->count() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Akcie -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Akcie</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if(!$user->email_verified_at)
                        <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Overiť email
                        </button>
                    @endif
                    
                    <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Poslať email
                    </button>
                    
                    @if(!$user->is_admin)
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Ste si istí, že chcete vymazať tohto používateľa? Táto akcia je nevratná.')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Vymazať používateľa
                            </button>
                        </form>
                    @endif
                    @if(!$user->is_admin)
                        <form method="POST" action="{{ route('admin.pouzivatelia.impersonate', $user) }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" onclick="return confirm('Chcete sa prepnúť na používateľa {{ $user->name }}? Budete presmerovaní na používateľský dashboard.')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Prepnúť sa na používateľa
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Posledné platby -->
            @if($user->adPayments->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Posledné platby</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            @foreach($user->adPayments->take(3) as $payment)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $payment->amount }} €</p>
                                        <p class="text-xs text-gray-500">{{ $payment->created_at ? $payment->created_at->format('d.m.Y') : 'Neuvedené' }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $payment->status === 'completed' ? 'green' : 'yellow' }}-100 text-{{ $payment->status === 'completed' ? 'green' : 'yellow' }}-800">
                                        {{ $payment->status === 'completed' ? 'Zaplatené' : 'Čaká' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        @if($user->adPayments->count() > 3)
                            <div class="mt-4 text-center">
                                <a href="{{ route('admin.platby.index', ['user' => $user->id]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                    Zobraziť všetky platby
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 