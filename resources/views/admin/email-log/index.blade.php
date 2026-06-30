@extends('layouts.admin-dashboard')

@section('header', 'Email Log')

@section('content')
<!-- Štatistiky email-ov -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 mb-8">
    <!-- Celkovo -->
    <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.83 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <dt class="text-sm font-medium text-gray-600">Celkovo</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</dd>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
            <div class="text-sm text-gray-600">
                všetky emaily
            </div>
        </div>
    </div>

    <!-- Odoslané -->
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
                    <dt class="text-sm font-medium text-gray-600">Odoslané</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['sent']) }}</dd>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
            <div class="text-sm text-gray-600">
                úspešne odoslané
            </div>
        </div>
    </div>

    <!-- Neúspešné -->
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
                    <dt class="text-sm font-medium text-gray-600">Neúspešné</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['failed']) }}</dd>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
            <div class="text-sm text-gray-600">
                chyby pri odoslaní
            </div>
        </div>
    </div>

    <!-- Vo fronte -->
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
                    <dt class="text-sm font-medium text-gray-600">Vo fronte</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['queued']) }}</dd>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
            <div class="text-sm text-gray-600">
                čakajú na odoslanie
            </div>
        </div>
    </div>
</div>

<!-- Štatistiky podľa času -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 mb-8">
    <!-- Dnes -->
    <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <dt class="text-sm font-medium text-gray-600">Dnes</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['today']) }}</dd>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
            <div class="text-sm text-gray-600">
                dnešné emaily
            </div>
        </div>
    </div>

    <!-- Tento týždeň -->
    <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <dt class="text-sm font-medium text-gray-600">Tento týždeň</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_week']) }}</dd>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
            <div class="text-sm text-gray-600">
                týždenná aktivita
            </div>
        </div>
    </div>

    <!-- Tento mesiac -->
    <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <dt class="text-sm font-medium text-gray-600">Tento mesiac</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_month']) }}</dd>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
            <div class="text-sm text-gray-600">
                mesačná aktivita
            </div>
        </div>
    </div>

    <!-- Tento rok -->
    <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <dt class="text-sm font-medium text-gray-600">Tento rok</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_year']) }}</dd>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
            <div class="text-sm text-gray-600">
                ročná aktivita
            </div>
        </div>
    </div>
</div>

<!-- Filtrovanie -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Filtrovanie a vyhľadávanie</h3>
    </div>
    <div class="px-6 py-4">
        <form method="GET" action="{{ route('admin.email-log.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Typ emailu -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Typ emailu</label>
                    <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Všetky statusy</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Odoslané</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Neúspešné</option>
                        <option value="queued" {{ request('status') === 'queued' ? 'selected' : '' }}>Vo fronte</option>
                    </select>
                </div>

                <!-- Príjemca -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Príjemca</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Hľadať podľa emailu..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Dátum od -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">Dátum od</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="flex space-x-3">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
                        <i class="ri-search-line text-sm mr-2"></i>
                        Filtrovať
                    </button>
                    <a href="{{ route('admin.email-log.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 flex items-center">
                        <i class="ri-refresh-line text-sm mr-2"></i>
                        Resetovať
                    </a>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.email-log.export', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center">
                        <i class="ri-download-line text-sm mr-2"></i>
                        Export CSV
                    </a>
                    <!-- Červené tlačidlo na vymazanie všetkých logov -->
                    <button type="button" onclick="openDeleteAllModal()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                        <i class="ri-delete-bin-line text-sm mr-2"></i>
                        Vymazať všetky logy
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal na potvrdenie vymazania všetkých logov -->
<div id="deleteAllModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full">
        <h2 class="text-lg font-semibold mb-4">Potvrdenie vymazania</h2>
        <p class="mb-6">Naozaj chcete nenávratne vymazať <b>všetky email logy</b>? Táto akcia je nezvratná!</p>
        <form id="deleteAllForm" method="POST" action="{{ route('admin.email-log.delete-all') }}">
            @csrf
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteAllModal()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Zrušiť</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Vymazať všetko</button>
            </div>
        </form>
    </div>
</div>
<script>
    function openDeleteAllModal() {
        document.getElementById('deleteAllModal').classList.remove('hidden');
    }
    function closeDeleteAllModal() {
        document.getElementById('deleteAllModal').classList.add('hidden');
    }
</script>

<!-- Tabuľka emailov -->
<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">História emailov</h3>
    </div>
    <div class="overflow-x-auto">
        @if($emails->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Príjemca</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Predmet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dátum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcie</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($emails as $email)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $email->to_email }}</div>
                                @if($email->from_email)
                                    <div class="text-sm text-gray-500">od: {{ $email->from_email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $email->short_subject }}</div>
                                @if($email->body)
                                    <div class="text-sm text-gray-500">{{ $email->short_body }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $email->type_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($email->status === 'sent')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="ri-check-line text-xs mr-1"></i>
                                        {{ $email->status_name }}
                                    </span>
                                @elseif($email->status === 'failed')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="ri-close-line text-xs mr-1"></i>
                                        {{ $email->status_name }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="ri-time-line text-xs mr-1"></i>
                                        {{ $email->status_name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $email->sent_at ? $email->sent_at->format('d.m.Y H:i') : $email->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.email-log.show', $email->id) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.email-log.destroy', $email->id) }}" class="inline" onsubmit="return confirm('Naozaj chcete vymazať tento email log?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Paginácia -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $emails->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="ri-mail-line text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">Žiadne emaily</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->anyFilled(['type', 'status', 'search', 'date_from', 'date_to']))
                        Neboli nájdené žiadne emaily podľa zadaných kritérií.
                    @else
                        Zatiaľ nie sú zaznamenané žiadne emaily.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection 