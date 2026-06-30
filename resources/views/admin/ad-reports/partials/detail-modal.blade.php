<!-- Modal Wrapper -->
<div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" id="ad-report-modal">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeReportModal()"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Nahlásenie #{{ $report->id }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $report->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div class="flex items-center space-x-3">
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
                <button onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Content -->
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
                            <dd class="text-sm text-gray-900">{{ $report->created_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Informácie o inzeráte -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Nahlásený inzerát</h4>
                    @if($report->ad)
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-xs font-medium text-gray-500">ID inzerátu</dt>
                                <dd class="text-sm text-gray-900">
                                    <a href="{{ route('admin.inzeraty.show', $report->ad->id) }}" 
                                       class="text-pink-600 hover:text-pink-800"
                                       target="_blank">
                                        #{{ $report->ad->id }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Prezývka</dt>
                                <dd class="text-sm text-gray-900">{{ $report->ad->nickname }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Používateľ</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($report->ad->user)
                                        {{ $report->ad->user->name }} ({{ $report->ad->user->email }})
                                    @else
                                        Neznámy používateľ
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Stav inzerátu</dt>
                                <dd class="text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($report->ad->status === 'active') bg-green-100 text-green-800
                                        @elseif($report->ad->status === 'inactive') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ $report->ad->status_label }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500">Inzerát bol vymazaný</p>
                    @endif
                </div>
            </div>

            <!-- Podrobnosti nahlásenia -->
            @if($report->details)
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Podrobnosti nahlásenia</h4>
                <p class="text-sm text-gray-700">{{ $report->details }}</p>
            </div>
            @endif

            <!-- Admin poznámky -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Admin poznámky</h4>
                <form action="{{ route('admin.nahlasenia-inzeratov.update', $report->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Stav</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                            <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Čakajúce</option>
                            <option value="reviewed" {{ $report->status === 'reviewed' ? 'selected' : '' }}>Preskúmané</option>
                            <option value="resolved" {{ $report->status === 'resolved' ? 'selected' : '' }}>Vyriešené</option>
                            <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>Zamietnuté</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="admin_notes" class="block text-sm font-medium text-gray-700">Poznámky</label>
                        <textarea name="admin_notes" id="admin_notes" rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                  placeholder="Pridajte poznámky k nahláseniu...">{{ $report->admin_notes }}</textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeReportModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Zrušiť
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-pink-600 text-white rounded-md text-sm font-medium hover:bg-pink-700">
                            Uložiť zmeny
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 