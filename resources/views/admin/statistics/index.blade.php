@extends('layouts.admin-dashboard')

@section('header', 'Systémové štatistiky')

@section('content')
<div class="space-y-6">
    <!-- Štatistiky systému -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <x-stat-box color="blue" icon="ri-user-3-line" title="Celkom používateľov" :value="number_format($totalUsers)" subtitle="registrovaní" />
        <x-stat-box color="green" icon="ri-user-star-line" title="Aktívni" :value="number_format($activeUsers)" subtitle="overení používatelia" :extra="'+' . $newUsersLast30Days . ' za 30 dní'" />
        <x-stat-box color="yellow" icon="ri-file-list-3-line" title="Celkom inzerátov" :value="number_format($totalAds)" subtitle="všetky inzeráty" :extra="$activeAds . ' aktívnych'" />
        <x-stat-box color="purple" icon="ri-money-euro-circle-line" title="Celkové príjmy" :value="number_format($totalRevenue, 2) . '€'" subtitle="celkový zisk" :extra="number_format($revenueThisMonth, 2) . '€ tento mesiac'" />
        <x-stat-box color="indigo" icon="ri-bank-card-line" title="Platby" :value="number_format($totalPayments)" subtitle="platby" :extra="$completedPayments . ' úspešných'" />
        <x-stat-box color="pink" icon="ri-customer-service-line" title="Support tickety" :value="number_format($totalTickets)" subtitle="tickety" :extra="$ticketsByStatus['open'] . ' otvorených'" />
        <x-stat-box color="rose" icon="ri-building-line" title="Kluby" :value="number_format($totalClubs)" subtitle="kluby" />
        <x-stat-box color="cyan" icon="ri-article-line" title="Články" :value="number_format($totalArticles)" subtitle="články" />
    </div>

    <!-- Trendy a detailné štatistiky -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Registrácie za posledných 7 dní -->
        <div class="bg-white overflow-hidden shadow rounded-2xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Registrácie za posledných 7 dní</h3>
                    <span class="text-sm text-gray-500">Celkom: {{ $registrationsLast7Days->sum() }}</span>
                </div>
                <div class="space-y-3">
                    @foreach($registrationsLast7Days as $day => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $day }}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-gradient-to-r from-purple-500 to-violet-600 h-2 rounded-full" style="width: {{ $count > 0 ? ($count / max($registrationsLast7Days->values()->toArray() + [1])) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 w-6 text-right">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Inzeráty podľa kategórií -->
        <div class="bg-white overflow-hidden shadow rounded-2xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Inzeráty podľa kategórií</h3>
                    <span class="text-sm text-gray-500">Celkom: {{ $adsByCategory->sum() }}</span>
                </div>
                <div class="space-y-3">
                    @foreach($adsByCategory as $category => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $category }}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-gradient-to-r from-teal-500 to-cyan-600 h-2 rounded-full" style="width: {{ $count > 0 ? ($count / max($adsByCategory->values()->toArray() + [1])) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 w-6 text-right">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Inzeráty podľa statusu -->
        <div class="bg-white overflow-hidden shadow rounded-2xl">
            <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Inzeráty podľa statusu</h3>
                <div class="space-y-3">
                    @foreach($adsByStatus as $status => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if($status === 'active')
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Aktívne</span>
                                @elseif($status === 'pending')
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Čakajúce</span>
                                @elseif($status === 'inactive')
                                    <div class="w-3 h-3 bg-gray-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Neaktívne</span>
                                @else
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Zamietnuté</span>
                                @endif
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Support tickety podľa statusu -->
        <div class="bg-white overflow-hidden shadow rounded-2xl">
            <div class="p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Support tickety podľa statusu</h3>
                <div class="space-y-3">
                    @foreach($ticketsByStatus as $status => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if($status === 'open')
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Otvorené</span>
                                @elseif($status === 'in_progress')
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Vo vybavení</span>
                                @elseif($status === 'resolved')
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Vyriešené</span>
                                @else
                                    <div class="w-3 h-3 bg-gray-500 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Zatvorené</span>
                                @endif
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Top mestá -->
    <h2 class="text-xl font-semibold text-gray-800 mt-10 mb-2 flex items-center"><i class="ri-map-pin-line text-pink-500 text-2xl mr-2"></i>Top mestá podľa počtu inzerátov</h2>
    <div class="bg-white overflow-hidden shadow rounded-2xl mb-8">
        <div class="p-6">
            <div class="flex flex-wrap gap-4 justify-center">
                @foreach($topCities as $index => $city)
                    <div class="flex flex-col items-center px-4 py-2 rounded-xl {{ $index === 0 ? 'bg-pink-100 shadow-lg scale-110 border-2 border-pink-400' : 'bg-gray-50' }} transition-all">
                        <div class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="ri-building-2-line mr-1 {{ $index === 0 ? 'text-pink-500' : 'text-gray-400' }}"></i>
                            {{ $city->count }}
                        </div>
                        <div class="text-sm text-gray-600">{{ $city->city }}</div>
                        <div class="text-xs text-gray-400">#{{ $index + 1 }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Najnovšie registrácie -->
    <h2 class="text-xl font-semibold text-gray-800 mt-10 mb-2 flex items-center"><i class="ri-user-add-line text-blue-500 text-2xl mr-2"></i>Najnovšie registrácie</h2>
    <div class="bg-white overflow-hidden shadow rounded-2xl mb-8">
        <div class="p-6">
            <ul class="divide-y divide-gray-100">
                @foreach($recentUsers as $user)
                    <li class="flex items-center py-3">
                        <span class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center mr-3">
                            <i class="ri-user-line text-white"></i>
                        </span>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </div>
                        <div class="text-xs text-gray-400 ml-2">{{ $user->created_at->format('d.m.Y H:i') }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection 