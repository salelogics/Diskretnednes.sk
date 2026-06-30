@extends('layouts.user-dashboard')

@section('header', 'Štatistiky')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Hlavné štatistiky -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Celkové zobrazenia -->
        <div class="relative bg-gradient-to-br from-pink-500 to-pink-600 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Zobrazenia</dt>
                        <dd class="text-2xl font-bold text-white">{{ number_format($statistics['total_views']) }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-black bg-opacity-10 px-6 py-3">
                <div class="text-sm text-pink-100">
                    <span class="font-semibold text-white">+{{ $statistics['monthly_growth'] }}%</span>
                    tento mesiac
                </div>
            </div>
            <!-- Dekoratívny element -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Celkové kliky -->
        <div class="relative bg-gradient-to-br from-pink-400 to-pink-500 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Kliky</dt>
                        <dd class="text-2xl font-bold text-white">{{ number_format($statistics['total_clicks']) }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-black bg-opacity-10 px-6 py-3">
                <div class="text-sm text-pink-100">
                    <span class="font-semibold text-white">CTR {{ $statistics['click_rate'] }}%</span>
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Priemerný čas -->
        <div class="relative bg-gradient-to-br from-pink-300 to-pink-400 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Priemerný čas</dt>
                        <dd class="text-2xl font-bold text-white">{{ $statistics['avg_time_on_page'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-black bg-opacity-10 px-6 py-3">
                <div class="text-sm text-pink-100">
                    na stránke
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>

        <!-- Aktívne inzeráty -->
        <div class="relative bg-gradient-to-br from-pink-200 to-pink-300 overflow-hidden shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-pink-100">Aktívne inzeráty</dt>
                        <dd class="text-2xl font-bold text-white">{{ $statistics['active_ads'] }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-black bg-opacity-10 px-6 py-3">
                <div class="text-sm text-pink-100">
                    z {{ $statistics['ads_count'] }} celkom
                </div>
            </div>
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
        </div>
    </div>

    <!-- Hlavný graf -->
    <div class="bg-white shadow-xl rounded-2xl mb-8 border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Aktivita za posledných 7 dní</h3>
                    <p class="text-sm text-gray-500 mt-1">Sledovanie zobrazení a klikov v čase</p>
                </div>
                <div class="flex space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                        <div class="w-2 h-2 bg-pink-500 rounded-full mr-2"></div>
                        Zobrazenia
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <div class="w-2 h-2 bg-purple-600 rounded-full mr-2"></div>
                        Kliky
                    </span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="h-80">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Prehľad inzerátov -->
    @if(isset($userAds) && $userAds->count() > 0)
        <div class="bg-white shadow-xl rounded-2xl mb-8 border border-pink-500/20">
            <div class="px-6 py-6 border-b border-pink-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Vaše inzeráty</h3>
                        <p class="text-sm text-gray-500 mt-1">Prehľad výkonnosti jednotlivých inzerátov</p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $statistics['active_ads'] }} aktívnych
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $statistics['ads_count'] }} celkom
                        </span>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-pink-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inzerát</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zobrazenia</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kliky</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CTR</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($userAds->sortByDesc('views') as $ad)
                            <tr class="hover:bg-pink-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">{{ substr($ad->nickname, 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $ad->nickname }}</div>
                                            <div class="text-sm text-gray-500">{{ $ad->city_label }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        @if($ad->status === 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></div>
                                                Aktívny
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1"></div>
                                                {{ $ad->status_label }}
                                            </span>
                                        @endif
                                        
                                        @if($ad->top_ad)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="ri-star-line mr-1"></i>
                                                TOP
                                            </span>
                                        @endif
                                        
                                        @if($ad->featured)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <i class="ri-flashlight-line mr-1"></i>
                                                Zvýraznený
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="ri-eye-line text-gray-400 mr-2"></i>
                                        {{ number_format($ad->views) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="ri-cursor-line text-gray-400 mr-2"></i>
                                        {{ number_format($ad->clicks) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $ctr = $ad->views > 0 ? round(($ad->clicks / $ad->views) * 100, 1) : 0;
                                        $ctrColor = match(true) {
                                            $ctr >= 5 => 'text-green-600',
                                            $ctr >= 2 => 'text-yellow-600',
                                            default => 'text-red-600'
                                        };
                                    @endphp
                                    <span class="font-medium {{ $ctrColor }}">{{ $ctr }}%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ad->ad_type_label }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Prázdny stav -->
        <div class="bg-white shadow-xl rounded-2xl mb-8 border border-pink-500/20">
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Žiadne inzeráty</h3>
                <p class="mt-1 text-sm text-gray-500">Začnite vytvorením svojho prvého inzerátu.</p>
                <div class="mt-6">
                    <a href="{{ route('ads.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                        <i class="ri-add-line mr-2"></i>
                        Vytvoriť inzerát
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Krajiny a zariadenia -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top krajiny -->
        <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
            <div class="px-6 py-6 border-b border-pink-100">
                <h3 class="text-xl font-semibold text-gray-900">Top krajiny</h3>
                <p class="text-sm text-gray-500 mt-1">Zobrazenia podľa geografického umiestnenia</p>
            </div>
            <div class="p-6">
                <div class="space-y-5">
                    @foreach($statistics['country_stats'] as $index => $country)
                        <div class="flex items-center justify-between group hover:bg-pink-50 -mx-2 px-2 py-2 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="w-8 h-8 mr-4 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-xs font-bold text-gray-700 shadow-sm">
                                    {{ $country['code'] }}
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $country['country'] }}</span>
                                    <div class="text-xs text-gray-500">{{ number_format($country['views']) }} zobrazení</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="w-32 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-pink-500 to-pink-600 h-2.5 rounded-full transition-all duration-1000 ease-out" 
                                         style="width: {{ $country['percentage'] }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 w-12 text-right">{{ $country['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Zariadenia -->
        <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
            <div class="px-6 py-6 border-b border-pink-100">
                <h3 class="text-xl font-semibold text-gray-900">Zariadenia</h3>
                <p class="text-sm text-gray-500 mt-1">Rozdelenie návštevníkov podľa typu zariadenia</p>
            </div>
            <div class="p-6">
                <div class="space-y-5">
                    @foreach($statistics['device_stats'] as $index => $device)
                        @php
                            $colors = [
                                'Mobil' => 'from-pink-500 to-pink-600',
                                'Desktop' => 'from-pink-400 to-pink-500',
                                'Tablet' => 'from-pink-300 to-pink-400'
                            ];
                            $iconColors = [
                                'Mobil' => 'text-pink-600',
                                'Desktop' => 'text-pink-500',
                                'Tablet' => 'text-pink-400'
                            ];
                        @endphp
                        <div class="flex items-center justify-between group hover:bg-pink-50 -mx-2 px-2 py-2 rounded-lg transition-colors">
                            <div class="flex items-center">
                                <div class="w-8 h-8 mr-4 rounded-lg bg-pink-100 flex items-center justify-center {{ $iconColors[$device['device']] ?? 'text-gray-600' }}">
                                    @if($device['device'] === 'Mobil')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V4a2 2 0 00-2-2H7zM8 5a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm1 9a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($device['device'] === 'Desktop')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V4a2 2 0 00-2-2H5zm0 2h10v12H5V4z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $device['device'] }}</span>
                                    <div class="text-xs text-gray-500">{{ number_format($device['views']) }} zobrazení</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="w-32 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r {{ $colors[$device['device']] ?? 'from-gray-500 to-gray-600' }} h-2.5 rounded-full transition-all duration-1000 ease-out" 
                                         style="width: {{ $device['percentage'] }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 w-12 text-right">{{ $device['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hlavný graf - kombinovaný zobrazenia a kliky
    const mainCtx = document.getElementById('mainChart').getContext('2d');
    new Chart(mainCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($statistics['daily_views'], 'date')) !!}.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('sk-SK', {weekday: 'short', month: 'short', day: 'numeric'});
            }),
            datasets: [{
                label: 'Zobrazenia',
                data: {!! json_encode(array_column($statistics['daily_views'], 'views')) !!},
                borderColor: 'rgb(219, 39, 119)',
                backgroundColor: 'rgba(219, 39, 119, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointBackgroundColor: 'rgb(219, 39, 119)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }, {
                label: 'Kliky',
                data: {!! json_encode(array_column($statistics['daily_views'], 'clicks')) !!},
                borderColor: 'rgb(147, 51, 234)',
                backgroundColor: 'rgba(147, 51, 234, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointBackgroundColor: 'rgb(147, 51, 234)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Skryjeme legendu lebo máme vlastnú
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>
@endsection 