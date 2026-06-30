@extends('layouts.admin-dashboard')

@section('header', 'Nahlásenia zákazníkov')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    
    <!-- Štatistiky nahlásení zákazníkov -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Celkom nahlásení -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Celkom nahlásení</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_reports'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    všetky reporty
                </div>
            </div>
        </div>

        <!-- Unikátne čísla -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Unikátne čísla</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['unique_numbers'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    rôzne telefóny
                </div>
            </div>
        </div>

        <!-- Veľmi nebezpečné -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Veľmi nebezpečné</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['very_dangerous'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    20+ nahlásení
                </div>
            </div>
        </div>

        <!-- Nebezpečné -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Nebezpečné</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['dangerous'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    10-19 nahlásení
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Filtre</h2>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.nahlasenia-zakaznikov.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="phone_search" class="block text-sm font-medium text-gray-700 mb-2">
                            Vyhľadať telefónne číslo
                        </label>
                        <input type="text" 
                               id="phone_search" 
                               name="phone_search" 
                               value="{{ request('phone_search') }}"
                               placeholder="Zadajte telefónne číslo..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="danger_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Úroveň nebezpečenstva
                        </label>
                        <select id="danger_level" 
                                name="danger_level"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Všetky úrovne</option>
                            <option value="very_dangerous" {{ request('danger_level') === 'very_dangerous' ? 'selected' : '' }}>
                                Veľmi nebezpečné (20+ nahlásení)
                            </option>
                            <option value="dangerous" {{ request('danger_level') === 'dangerous' ? 'selected' : '' }}>
                                Nebezpečné (10-19 nahlásení)
                            </option>
                            <option value="suspicious" {{ request('danger_level') === 'suspicious' ? 'selected' : '' }}>
                                Podozrivé (1-9 nahlásení)
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="ri-search-line mr-2"></i>
                            Filtrovať
                        </button>
                    </div>
                </div>
                
                @if(request()->hasAny(['phone_search', 'danger_level']))
                <div class="flex justify-end">
                    <a href="{{ route('admin.nahlasenia-zakaznikov.index') }}" 
                       class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <i class="ri-close-line mr-2"></i>
                        Vymazať filtre
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Nahlásené telefónne čísla</h2>
        </div>
        
        @if($reports->count() > 0)
            <div class="overflow-hidden">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">
                                Telefónne číslo
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/8">
                                Počet
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">
                                Úroveň
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                Prvé
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                Posledné
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                Akcie
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900 font-mono">
                                        {{ $report->phone_number }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($report->reports_count >= 20) bg-red-100 text-red-800
                                        @elseif($report->reports_count >= 10) bg-orange-100 text-orange-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ $report->reports_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if($report->danger_level === 'very_dangerous')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="ri-error-warning-line mr-1"></i>
                                            Veľmi nebezpečné
                                        </span>
                                    @elseif($report->danger_level === 'dangerous')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <i class="ri-alert-line mr-1"></i>
                                            Nebezpečné
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="ri-question-line mr-1"></i>
                                            Podozrivé
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-center text-sm text-gray-500">
                                    <div class="text-xs">{{ $report->first_report_date->format('d.m.Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $report->first_report_date->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-4 text-center text-sm text-gray-500">
                                    <div class="text-xs">{{ $report->last_report_date->format('d.m.Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $report->last_report_date->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.nahlasenia-zakaznikov.show', $report->phone_number) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors duration-200">
                                            <i class="ri-eye-line mr-1"></i>
                                            Detail
                                        </a>
                                        
                                        <form action="{{ route('admin.nahlasenia-zakaznikov.destroy', $report->phone_number) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Naozaj chcete vymazať všetky nahlásenia pre číslo {{ $report->phone_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors duration-200">
                                                <i class="ri-delete-bin-line mr-1"></i>
                                                Vymazať
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $reports->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="ri-phone-line text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Žiadne nahlásenia</h3>
                <p class="text-gray-500">
                    @if(request()->hasAny(['phone_search', 'danger_level']))
                        Žiadne nahlásenia nevyhovujú zadaným filtrom.
                    @else
                        Zatiaľ neboli nahlásené žiadne telefónne čísla.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection 