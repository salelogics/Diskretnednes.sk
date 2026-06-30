@extends('layouts.admin-dashboard')

@section('header', 'Detail nahlásenia #' . $report->id)

@section('content')
<div class="mx-auto max-w-4xl px-6 py-8 lg:px-8">
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Nahlásenie #{{ $report->id }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $report->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <div class="flex space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        @if($report->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($report->status === 'reviewed') bg-blue-100 text-blue-800
                        @elseif($report->status === 'resolved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        @if($report->status === 'pending') Čakajúce
                        @elseif($report->status === 'reviewed') Preskúmané
                        @elseif($report->status === 'resolved') Vyriešené
                        @else Zamietnuté
                        @endif
                    </span>
                    <a href="{{ route('admin.nahlasenia-inzeratov.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Späť na zoznam
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Informácie o nahlásení -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Informácie o nahlásení</h4>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Dôvod nahlásenia</dt>
                            <dd class="text-sm text-gray-900">{{ $report->reason }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">IP adresa</dt>
                            <dd class="text-sm text-gray-900">{{ $report->reporter_ip }}</dd>
                        </div>
                        @if($report->reporter_email)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-900">{{ $report->reporter_email }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Dátum nahlásenia</dt>
                            <dd class="text-sm text-gray-900">{{ $report->created_at->format('d.m.Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Informácie o inzeráte</h4>
                    @if($report->ad)
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">ID inzerátu</dt>
                            <dd class="text-sm text-gray-900">
                                <a href="{{ route('admin.inzeraty.show', $report->ad->id) }}" class="text-pink-600 hover:text-pink-900">
                                    #{{ $report->ad->id }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Prezývka</dt>
                            <dd class="text-sm text-gray-900">{{ $report->ad->nickname ?: 'Nezadané' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Typ inzerátu</dt>
                            <dd class="text-sm text-gray-900">{{ $report->ad->ad_type_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Mesto</dt>
                            <dd class="text-sm text-gray-900">{{ $report->ad->city_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Stav inzerátu</dt>
                            <dd class="text-sm">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($report->ad->status === 'active') bg-green-100 text-green-800
                                    @elseif($report->ad->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($report->ad->status === 'inactive') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($report->ad->status) }}
                                </span>
                            </dd>
                        </div>
                        @if($report->ad->user)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Používateľ</dt>
                            <dd class="text-sm text-gray-900">
                                <a href="{{ route('admin.pouzivatelia.show', $report->ad->user->id) }}" class="text-pink-600 hover:text-pink-900">
                                    {{ $report->ad->user->name }}
                                </a>
                            </dd>
                        </div>
                        @endif
                    </dl>
                    @else
                    <p class="text-sm text-gray-500">Inzerát bol vymazaný</p>
                    @endif
                </div>
            </div>

            <!-- Detaily nahlásenia -->
            @if($report->details)
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Dodatočné informácie</h4>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $report->details }}</p>
            </div>
            @endif

            <!-- Admin poznámky -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Admin poznámky</h4>
                <form action="{{ route('admin.nahlasenia-inzeratov.update', $report->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <textarea name="admin_notes" rows="4" 
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500"
                              placeholder="Pridajte poznámky k tomuto nahláseniu...">{{ $report->admin_notes }}</textarea>
                    
                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex space-x-3">
                            <select name="status" class="border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Čakajúce</option>
                                <option value="reviewed" {{ $report->status === 'reviewed' ? 'selected' : '' }}>Preskúmané</option>
                                <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Vyriešené</option>
                                <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>Zamietnuté</option>
                            </select>
                        </div>
                        
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-pink-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-pink-700 active:bg-pink-900 focus:outline-none focus:border-pink-900 focus:ring ring-pink-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="ri-save-line mr-2"></i>
                            Uložiť zmeny
                        </button>
                    </div>
                </form>
            </div>

            <!-- Akcie -->
            @if($report->ad)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-red-900 mb-3">Akcie s inzerátom</h4>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.inzeraty.show', $report->ad->id) }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                        <i class="ri-eye-line mr-2"></i>
                        Zobraziť inzerát
                    </a>
                    
                    @if($report->ad->status === 'active')
                    <form action="{{ route('admin.inzeraty.update-status', $report->ad->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="inactive">
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                onclick="return confirm('Naozaj chcete deaktivovať tento inzerát?')">
                            <i class="ri-pause-circle-line mr-2"></i>
                            Deaktivovať inzerát
                        </button>
                    </form>
                    @endif
                    
                    <form action="{{ route('admin.inzeraty.destroy', $report->ad->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                onclick="return confirm('Naozaj chcete vymazať tento inzerát? Táto akcia sa nedá vrátiť späť.')">
                            <i class="ri-delete-bin-line mr-2"></i>
                            Vymazať inzerát
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 