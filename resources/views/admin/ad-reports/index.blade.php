@extends('layouts.admin-dashboard')

@section('header', 'Nahlásenia inzerátov')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Štatistiky nahlásení inzerátov -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5 mb-8">
        <!-- Celkom nahlásení -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6H8.5l-1 1H5a2 2 0 01-2-2zm9-13.5V9" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Celkom nahlásení</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_reports']) }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    všetky reporty
                </div>
            </div>
        </div>

        <!-- Čakajúce -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Čakajúce</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_reports']) }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    na spracovanie
                </div>
            </div>
        </div>

        <!-- Preskúmané -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Preskúmané</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['reviewed_reports']) }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    skontrolované
                </div>
            </div>
        </div>

        <!-- Vyriešené -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Vyriešené</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['resolved_reports']) }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    úspešne vybavené
                </div>
            </div>
        </div>

        <!-- Zamietnuté -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Zamietnuté</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['dismissed_reports']) }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    neoprávnené
                </div>
            </div>
        </div>
    </div>

    <!-- Tabuľka nahlásení -->
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <h3 class="text-xl font-semibold text-gray-900">Nahlásenia inzerátov</h3>
            <p class="text-sm text-gray-500 mt-1">Prehľad všetkých nahlásených inzerátov</p>
        </div>
        
        @if($adReports->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Inzerát
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dôvod
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stav
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nahlásené
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            IP adresa
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Akcie
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($adReports as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($report->ad && $report->ad->user)
                                        <div class="h-10 w-10 rounded-full bg-pink-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-pink-600">
                                                {{ substr($report->ad->user->name ?? 'N', 0, 1) }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                            <i class="ri-user-line text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if($report->ad)
                                            <a href="{{ route('admin.inzeraty.show', $report->ad->id) }}" class="text-pink-600 hover:text-pink-900">
                                                Inzerát #{{ $report->ad->id }}
                                            </a>
                                        @else
                                            <span class="text-gray-500">Inzerát bol vymazaný</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($report->ad && $report->ad->user)
                                            {{ $report->ad->user->name ?? 'Neznámy používateľ' }}
                                        @else
                                            Neznámy používateľ
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $report->reason }}</div>
                            @if($report->details)
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ Str::limit($report->details, 50) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $report->created_at->format('d.m.Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $report->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->reporter_ip }}
                            @if($report->reporter_email)
                                <div class="text-xs text-gray-400">{{ $report->reporter_email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <div>
                                    <button type="button" @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                                        Akcie
                                        <i class="ri-arrow-down-s-line ml-2 h-4 w-4"></i>
                                    </button>
                                </div>

                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" style="display: none;">
                                    <div class="py-1">
                                        <!-- Detail -->
                                        <button onclick="showReportDetail({{ $report->id }})" 
                                                class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="ri-eye-line mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500"></i>
                                            Zobraziť detail
                                        </button>

                                        @if($report->ad)
                                        <!-- Prejsť na inzerát -->
                                        <a href="{{ route('ad.show', $report->ad->id) }}" 
                                           target="_blank"
                                           class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="ri-external-link-line mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500"></i>
                                            Prejsť na inzerát
                                        </a>

                                        <!-- Admin inzerát -->
                                        <a href="{{ route('admin.inzeraty.show', $report->ad->id) }}" 
                                           class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="ri-settings-line mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500"></i>
                                            Spravovať inzerát
                                        </a>
                                        @endif

                                        <!-- Statusy -->
                                        @if($report->status === 'pending')
                                        <div class="border-t border-gray-100"></div>
                                        <form action="{{ route('admin.nahlasenia-inzeratov.update', $report->id) }}" 
                                              method="POST" class="block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="reviewed">
                                            <button type="submit" 
                                                    class="group flex items-center w-full px-4 py-2 text-sm text-blue-700 hover:bg-blue-50">
                                                <i class="ri-check-line mr-3 h-4 w-4 text-blue-400 group-hover:text-blue-500"></i>
                                                Označiť ako preskúmané
                                            </button>
                                        </form>
                                        @endif

                                        @if($report->status === 'reviewed')
                                        <div class="border-t border-gray-100"></div>
                                        <form action="{{ route('admin.nahlasenia-inzeratov.update', $report->id) }}" 
                                              method="POST" class="block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="resolved">
                                            <button type="submit" 
                                                    class="group flex items-center w-full px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                                <i class="ri-check-double-line mr-3 h-4 w-4 text-green-400 group-hover:text-green-500"></i>
                                                Označiť ako vyriešené
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.nahlasenia-inzeratov.update', $report->id) }}" 
                                              method="POST" class="block">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="dismissed">
                                            <button type="submit" 
                                                    onclick="return confirm('Naozaj chcete zamietnuť toto nahlásenie?')"
                                                    class="group flex items-center w-full px-4 py-2 text-sm text-orange-700 hover:bg-orange-50">
                                                <i class="ri-close-line mr-3 h-4 w-4 text-orange-400 group-hover:text-orange-500"></i>
                                                Zamietnuť nahlásenie
                                            </button>
                                        </form>
                                        @endif

                                        <!-- Vymazať -->
                                        <div class="border-t border-gray-100"></div>
                                        <form action="{{ route('admin.nahlasenia-inzeratov.destroy', $report->id) }}" 
                                              method="POST" class="block" 
                                              onsubmit="return confirm('Naozaj chcete vymazať toto nahlásenie?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="group flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                <i class="ri-delete-bin-line mr-3 h-4 w-4 text-red-400 group-hover:text-red-500"></i>
                                                Vymazať nahlásenie
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($adReports->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $adReports->links() }}
        </div>
        @endif
        @else
        <div class="p-6">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Žiadne nahlásenia</h3>
                <p class="mt-1 text-sm text-gray-500">Zatiaľ neboli nahlásené žiadne inzeráty.</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Container -->
<div id="modal-container"></div>

<script>
    // 🔍 AJAX DETAIL MODAL PRE NAHLÁSENIA INZERÁTOV
    function showReportDetail(reportId) {
        console.log('🔍 Loading report detail:', reportId);
        
        // Zobrazenie loading state
        const modalContainer = document.getElementById('modal-container');
        modalContainer.innerHTML = `
            <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                <div class="relative bg-white rounded-xl shadow-2xl p-8">
                    <div class="flex items-center justify-center">
                        <i class="ri-loader-4-line animate-spin text-pink-600 text-2xl mr-3"></i>
                        <span class="text-gray-700">Načítavam detail nahlásenia...</span>
                    </div>
                </div>
            </div>
        `;
        
        // AJAX request pre detail
        fetch(`/admin/nahlasenia-inzeratov/${reportId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                modalContainer.innerHTML = data.html;
            } else {
                throw new Error(data.message || 'Nepodarilo sa načítať detail nahlásenia');
            }
        })
        .catch(error => {
            console.error('❌ Error loading report detail:', error);
            modalContainer.innerHTML = `
                <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeReportModal()"></div>
                    <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md">
                        <div class="text-center">
                            <i class="ri-error-warning-line text-red-500 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Chyba pri načítavaní</h3>
                            <p class="text-sm text-gray-600 mb-4">${error.message}</p>
                            <button onclick="closeReportModal()" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Zavrieť
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    // 🔐 CLOSE MODAL
    function closeReportModal() {
        const modalContainer = document.getElementById('modal-container');
        modalContainer.innerHTML = '';
    }
    
    // ⌨️ ESC KEY CLOSE
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeReportModal();
        }
    });
</script>
@endsection 