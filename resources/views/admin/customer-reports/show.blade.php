@extends('layouts.admin-dashboard')

@section('header', 'Detail nahlásení - ' . $phoneNumber)

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Nahlásenia pre číslo {{ $phoneNumber }}
                </h1>
                <p class="mt-2 text-gray-600">
                    Detail všetkých nahlásení pre toto telefónne číslo
                </p>
            </div>
            
            <a href="{{ route('admin.nahlasenia-zakaznikov.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                <i class="ri-arrow-left-line mr-2"></i>
                Späť na zoznam
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="ri-phone-line text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Celkom nahlásení</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_reports'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="ri-calendar-line text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Prvé nahlásenie</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['first_report']->format('d.m.Y') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="ri-time-line text-orange-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Posledné nahlásenie</p>
                    <p class="text-lg font-bold text-gray-900">{{ $stats['last_report']->format('d.m.Y') }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 
                        @if($stats['danger_level'] === 'very_dangerous') bg-red-100 
                        @elseif($stats['danger_level'] === 'dangerous') bg-orange-100 
                        @else bg-yellow-100 
                        @endif rounded-lg flex items-center justify-center">
                        <i class="
                            @if($stats['danger_level'] === 'very_dangerous') ri-error-warning-line text-red-600
                            @elseif($stats['danger_level'] === 'dangerous') ri-alert-line text-orange-600
                            @else ri-question-line text-yellow-600
                            @endif"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Úroveň nebezpečenstva</p>
                    <p class="text-sm font-bold 
                        @if($stats['danger_level'] === 'very_dangerous') text-red-600
                        @elseif($stats['danger_level'] === 'dangerous') text-orange-600
                        @else text-yellow-600
                        @endif">
                        @if($stats['danger_level'] === 'very_dangerous') Veľmi nebezpečné
                        @elseif($stats['danger_level'] === 'dangerous') Nebezpečné
                        @else Podozrivé
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Unique Reasons -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Dôvody nahlásení</h2>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-2">
                @foreach($stats['unique_reasons'] as $reason)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        {{ $reason }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Reports List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Všetky nahlásenia</h2>
        </div>
        
        <div class="divide-y divide-gray-200">
            @foreach($reports as $report)
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4 mb-3">
                                <div class="flex items-center space-x-2">
                                    <i class="ri-calendar-line text-gray-400"></i>
                                    <span class="text-sm text-gray-600">{{ $report->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <i class="ri-flag-line text-gray-400"></i>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $report->reason }}
                                    </span>
                                </div>
                                
                                @if($report->user)
                                    <div class="flex items-center space-x-2">
                                        <i class="ri-user-line text-gray-400"></i>
                                        <span class="text-sm text-gray-600">{{ $report->user->name }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center space-x-2">
                                        <i class="ri-user-unfollow-line text-gray-400"></i>
                                        <span class="text-sm text-gray-600">Anonymné nahlásenie</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                <div class="flex items-center space-x-1">
                                    <i class="ri-global-line"></i>
                                    <span>IP: {{ $report->ip_address }}</span>
                                </div>
                                
                                <div class="flex items-center space-x-1">
                                    <i class="ri-time-line"></i>
                                    <span>{{ $report->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            @if($report->anonymous)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="ri-eye-off-line mr-1"></i>
                                    Anonymné
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-8 flex justify-end space-x-4">
        <form action="{{ route('admin.nahlasenia-zakaznikov.destroy', $phoneNumber) }}" 
              method="POST" 
              class="inline"
              onsubmit="return confirm('Naozaj chcete vymazať všetky nahlásenia pre číslo {{ $phoneNumber }}? Táto akcia sa nedá vrátiť späť.')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-6 py-3 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                <i class="ri-delete-bin-line mr-2"></i>
                Vymazať všetky nahlásenia
            </button>
        </form>
    </div>
</div>
@endsection 