@extends('layouts.admin-dashboard')

@section('header', 'Detail inzerátu #' . $ad->id)

@section('content')
<div class="mx-auto max-w-6xl px-6 py-8 lg:px-8">
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Inzerát #{{ $ad->id }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $ad->nickname ?: $ad->offer_type_label }}</p>
                </div>
                <div class="flex space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        @if($ad->status === 'active') bg-green-100 text-green-800
                        @elseif($ad->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($ad->status === 'inactive') bg-gray-100 text-gray-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ ucfirst($ad->status) }}
                    </span>
                    
                    <!-- Admin tlačidlo na predĺženie inzerátu -->
                    <button onclick="openAdminExtendModal({{ $ad->id }}, '{{ addslashes($ad->nickname ?: $ad->offer_type_label) }}', '{{ addslashes($ad->ad_type_label) }}')" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Predĺžiť inzerát
                    </button>
                    
                    <a href="{{ route('admin.inzeraty.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Späť na zoznam
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Základné informácie -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Základné informácie</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Prezývka</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->nickname ?: 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Typ inzerátu</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->ad_type_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Typ ponuky</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->offer_type_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Národnosť</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->nationality ?: 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Vek</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->age ? $ad->age . ' rokov' : 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Mesto</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->city_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ulica</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->street ?: 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telefón</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->phone ?: 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Overený telefón</dt>
                            <dd class="text-sm text-gray-900">
                                @if($ad->phone_verified)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Overený
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Neoverený
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Vytvorené</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->created_at ? $ad->created_at->format('d.m.Y H:i') : 'Neuvedené' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Fyzické vlastnosti -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Fyzické vlastnosti</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Výška</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->height ? $ad->height . ' cm' : 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Váha</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->weight ? $ad->weight . ' kg' : 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Veľkosť pŕs</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->breast_size ?: 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Farba očí</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->eye_color ?: 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Farba vlasov</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->hair_color ?: 'Neuvedené' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tetovanie</dt>
                            <dd class="text-sm text-gray-900">
                                @if($ad->tattoos)
                                    @php
                                        $tattoosMap = [
                                            '1' => 'Áno',
                                            'ano' => 'Áno',
                                            '0' => 'Nie',
                                            'nie' => 'Nie',
                                            'male' => 'Malé',
                                            'velke' => 'Veľké'
                                        ];
                                        $tattoos = $tattoosMap[strtolower($ad->tattoos)] ?? ucfirst(str_replace('-', ' ', $ad->tattoos));
                                    @endphp
                                    {{ $tattoos }}
                                @else
                                    Neuvedené
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Piercing</dt>
                            <dd class="text-sm text-gray-900">
                                @if($ad->piercing)
                                    @php
                                        $piercingMap = [
                                            '1' => 'Áno',
                                            'ano' => 'Áno',
                                            '0' => 'Nie',
                                            'nie' => 'Nie',
                                            'male' => 'Malé'
                                        ];
                                        $piercing = $piercingMap[strtolower($ad->piercing)] ?? ucfirst(str_replace('-', ' ', $ad->piercing));
                                    @endphp
                                    {{ $piercing }}
                                @else
                                    Neuvedené
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Orientácia</dt>
                            <dd class="text-sm text-gray-900">{{ $ad->orientation ?: 'Neuvedené' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Kontaktné metódy -->
            @if($ad->contact_methods && count($ad->contact_methods) > 0)
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Kontaktné metódy</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ad->contact_methods as $method)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($method) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Pracovné hodiny -->
            @if($ad->hours && count($ad->hours) > 0)
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Pracovné hodiny</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($ad->hours as $day => $dayHours)
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="text-sm font-medium text-gray-900">{{ ucfirst($day) }}</div>
                                @if(is_array($dayHours) && isset($dayHours['status']))
                                    @if($dayHours['status'] === 'open')
                                        <div class="text-xs text-green-600">
                                            {{ $dayHours['from'] ?? '00:00' }} - {{ $dayHours['to'] ?? '23:59' }}
                                        </div>
                                    @else
                                        <div class="text-xs text-red-600">Zatvorené</div>
                                    @endif
                                @else
                                    <div class="text-xs text-gray-500">Neuvedené</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Praktiky -->
            @if($ad->practices && count($ad->practices) > 0)
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Praktiky</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ad->practices as $practice)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $practice }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Popis -->
            @if($ad->description)
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Popis</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $ad->description }}</p>
                    </div>
                </div>
            @endif

            <!-- Galéria -->
            @if($ad->gallery_image_urls && count($ad->gallery_image_urls) > 0)
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Galéria ({{ count($ad->gallery_image_urls) }} fotiek)</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($ad->gallery_image_urls as $photoUrl)
                            <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                                <img src="{{ $photoUrl }}" alt="Galéria" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Video -->
            @if($ad->video)
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Video</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <video controls class="w-full max-w-md">
                            <source src="{{ asset('images/uploads/' . $ad->video) }}" type="video/mp4">
                            Váš prehliadač nepodporuje video element.
                        </video>
                    </div>
                </div>
            @endif

            <!-- Overovacia fotka -->
            @if($ad->verification_image_url)
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Overovacia fotka</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <img src="{{ $ad->verification_image_url }}" alt="Overovacia fotka" class="w-32 h-32 object-cover rounded-lg">
                    </div>
                </div>
            @endif

            <!-- Štatistiky -->
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Štatistiky</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $ad->views ?? 0 }}</div>
                        <div class="text-sm text-blue-600">Zobrazení</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $ad->clicks ?? 0 }}</div>
                        <div class="text-sm text-green-600">Kliknutí</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ $ad->ctr ?? 0 }}%</div>
                        <div class="text-sm text-purple-600">CTR</div>
                    </div>
                </div>
            </div>

            <!-- Predplatné -->
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Predplatné</h4>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stav predplatného</dt>
                        <dd class="text-sm text-gray-900">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                @if($ad->isSubscriptionActive()) bg-green-100 text-green-800
                                @elseif($ad->isSubscriptionExpired()) bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($ad->isSubscriptionActive()) Aktívne
                                @elseif($ad->isSubscriptionExpired()) Vypršalo
                                @else Neaktívne
                                @endif
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Predplatné vyprší</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $ad->subscription_expires_at ? $ad->subscription_expires_at->format('d.m.Y H:i') : 'Neuvedené' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Zvýraznený</dt>
                        <dd class="text-sm text-gray-900">
                            @if($ad->featured)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Áno
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Nie
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Top inzerát</dt>
                        <dd class="text-sm text-gray-900">
                            @if($ad->top_ad)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Áno
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Nie
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Používateľ -->
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Používateľ</h4>
                @if($ad->user)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">{{ substr($ad->user->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $ad->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $ad->user->email }}</div>
                                <div class="text-xs text-gray-400">ID: {{ $ad->user->id }}</div>
                            </div>
                            <div>
                                <a href="{{ route('admin.pouzivatelia.show', $ad->user) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Zobraziť profil
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-sm text-gray-500">Používateľ neexistuje</div>
                @endif
            </div>

            <!-- Platby -->
            @if($ad->payments && $ad->payments->count() > 0)
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Platby ({{ $ad->payments->count() }})</h4>
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dátum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suma</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($ad->payments->take(5) as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payment->created_at ? $payment->created_at->format('d.m.Y H:i') : 'Neuvedené' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $payment->amount }} €
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payment->package_type ?? 'Neuvedené' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                @if($payment->status === 'completed') bg-green-100 text-green-800
                                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($ad->payments->count() > 5)
                            <div class="bg-gray-50 px-6 py-3">
                                <div class="text-sm text-gray-500">
                                    Zobrazených {{ min(5, $ad->payments->count()) }} z {{ $ad->payments->count() }} platieb
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Akcie -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex space-x-3">
                    <form method="POST" action="{{ route('admin.inzeraty.update-status', $ad) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Aktivovať
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.inzeraty.update-status', $ad) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="inactive">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                            Deaktivovať
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.inzeraty.destroy', $ad) }}" class="inline" onsubmit="return confirm('Naozaj chcete vymazať tento inzerát?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Vymazať
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admin Extension Modal -->
<div id="adminExtendModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"></div>
    
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-3xl shadow-2xl max-w-lg w-full max-h-[90vh] transform transition-all duration-300 scale-95 relative z-[10000] overflow-hidden flex flex-col">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4 text-white relative">
                <button type="button" id="closeAdminExtendModalBtn" class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors">
                    <i class="ri-close-line text-2xl"></i>
                </button>
                <div class="pr-8">
                    <h3 class="text-xl font-bold mb-1">Predĺžiť inzerát (Admin)</h3>
                    <p class="text-pink-100 text-sm" id="adminModalAdTitle">Načítava sa...</p>
                    <div class="text-xs text-pink-200">
                        <span id="adminModalAdId">ID: -</span>
                        <span id="adminModalAdName" class="ml-2"></span>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6 overflow-y-auto flex-1">
                
                <!-- Balíčky -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Vyberte balíček</h4>
                    
                    <!-- Classic balíčky -->
                    <div class="space-y-3 mb-6">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Classic</h5>
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="selectAdminPackage('classic', 7)" class="admin-package-btn p-3 border-2 border-gray-200 rounded-lg hover:border-pink-300 hover:bg-pink-50 transition-all">
                                <div class="text-center">
                                    <div class="font-medium text-gray-900">7 dní</div>
                                    <div class="text-xs text-gray-500">ZADARMO</div>
                                </div>
                            </button>
                            <button onclick="selectAdminPackage('classic', 30)" class="admin-package-btn p-3 border-2 border-gray-200 rounded-lg hover:border-pink-300 hover:bg-pink-50 transition-all">
                                <div class="text-center">
                                    <div class="font-medium text-gray-900">30 dní</div>
                                    <div class="text-xs text-gray-500">ZADARMO</div>
                                </div>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Premium balíčky -->
                    <div class="space-y-3">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Premium</h5>
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="selectAdminPackage('premium', 7)" class="admin-package-btn p-3 border-2 border-gray-200 rounded-lg hover:border-pink-300 hover:bg-pink-50 transition-all">
                                <div class="text-center">
                                    <div class="font-medium text-gray-900">7 dní</div>
                                    <div class="text-xs text-gray-500">ZADARMO</div>
                                </div>
                            </button>
                            <button onclick="selectAdminPackage('premium', 30)" class="admin-package-btn p-3 border-2 border-gray-200 rounded-lg hover:border-pink-300 hover:bg-pink-50 transition-all">
                                <div class="text-center">
                                    <div class="font-medium text-gray-900">30 dní</div>
                                    <div class="text-xs text-gray-500">ZADARMO</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Informácia -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 mb-1">Admin predĺženie</h3>
                            <div class="text-sm text-blue-700">
                                Ako admin môžete predĺžiť inzerát zadarmo. Inzerát bude okamžite aktívny s novým dátumom expirácie.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zhrnutie -->
                <div id="adminExtendSummary" class="hidden bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Zhrnutie</h4>
                    <div class="text-sm text-gray-600">
                        <div>Balíček: <span id="adminSelectedPackage" class="font-medium"></span></div>
                        <div>Trvanie: <span id="adminSelectedDays" class="font-medium"></span></div>
                        <div class="text-green-600 font-medium">Cena: ZADARMO (Admin)</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelAdminExtendBtn" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Zrušiť
                    </button>
                    <button type="button" id="confirmAdminExtendBtn" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        <span id="adminExtendBtnText">Predĺžiť inzerát</span>
                        <div id="adminExtendBtnLoading" class="hidden inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Predlžuje sa...
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const adminModal = document.getElementById('adminExtendModal');
    let currentAdminAdId = null;
    let selectedAdminPackage = null;
    let selectedAdminDays = null;
    
    // Global function to open admin extend modal
    window.openAdminExtendModal = function(adId, title, adType) {
        currentAdminAdId = adId;
        const displayTitle = title || 'Inzerát #' + adId;
        const displayType = adType || '';
        
        document.getElementById('adminModalAdTitle').textContent = displayTitle;
        document.getElementById('adminModalAdId').textContent = 'ID: ' + adId;
        document.getElementById('adminModalAdName').textContent = displayType ? '• ' + displayType : '';
        
        resetAdminModal();
        adminModal.classList.remove('hidden');
        setTimeout(() => {
            adminModal.querySelector('.modal-content').classList.remove('scale-95');
            adminModal.querySelector('.modal-content').classList.add('scale-100');
        }, 10);
    };

    // Global function to select admin package
    window.selectAdminPackage = function(packageType, days) {
        selectedAdminPackage = packageType;
        selectedAdminDays = days;
        
        // Update button states
        document.querySelectorAll('.admin-package-btn').forEach(btn => {
            btn.classList.remove('border-pink-500', 'bg-pink-50');
            btn.classList.add('border-gray-200');
        });
        
        event.target.closest('.admin-package-btn').classList.remove('border-gray-200');
        event.target.closest('.admin-package-btn').classList.add('border-pink-500', 'bg-pink-50');
        
        // Update summary
        document.getElementById('adminSelectedPackage').textContent = packageType.toUpperCase();
        document.getElementById('adminSelectedDays').textContent = days + (days === 1 ? ' deň' : ' dní');
        document.getElementById('adminExtendSummary').classList.remove('hidden');
        document.getElementById('confirmAdminExtendBtn').disabled = false;
    };

    function resetAdminModal() {
        selectedAdminPackage = null;
        selectedAdminDays = null;
        
        document.querySelectorAll('.admin-package-btn').forEach(btn => {
            btn.classList.remove('border-pink-500', 'bg-pink-50');
            btn.classList.add('border-gray-200');
        });
        
        document.getElementById('adminExtendSummary').classList.add('hidden');
        document.getElementById('confirmAdminExtendBtn').disabled = true;
    }

    function closeAdminModal() {
        adminModal.querySelector('.modal-content').classList.remove('scale-100');
        adminModal.querySelector('.modal-content').classList.add('scale-95');
        setTimeout(() => {
            adminModal.classList.add('hidden');
        }, 300);
    }

    // Close modal events
    document.getElementById('closeAdminExtendModalBtn').addEventListener('click', closeAdminModal);
    document.getElementById('cancelAdminExtendBtn').addEventListener('click', closeAdminModal);
    
    // Click outside to close
    adminModal.addEventListener('click', function(e) {
        if (e.target === adminModal) {
            closeAdminModal();
        }
    });

    // Confirm extend
    document.getElementById('confirmAdminExtendBtn').addEventListener('click', function() {
        if (!selectedAdminPackage || !selectedAdminDays || !currentAdminAdId) {
            alert('Chyba: Chýbajú potrebné údaje');
            return;
        }

        // Show loading state
        document.getElementById('adminExtendBtnText').classList.add('hidden');
        document.getElementById('adminExtendBtnLoading').classList.remove('hidden');
        this.disabled = true;

        // Send request to extend ad
        fetch(`/admin/inzeraty/${currentAdminAdId}/predlzit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                package_type: selectedAdminPackage,
                days: selectedAdminDays
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Inzerát bol úspešne predĺžený!');
                closeAdminModal();
                // Refresh page to show updated data
                window.location.reload();
            } else {
                alert('Chyba: ' + (data.message || 'Nepodarilo sa predĺžiť inzerát'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Chyba: Nepodarilo sa predĺžiť inzerát');
        })
        .finally(() => {
            // Hide loading state
            document.getElementById('adminExtendBtnText').classList.remove('hidden');
            document.getElementById('adminExtendBtnLoading').classList.add('hidden');
            this.disabled = false;
        });
    });
});
</script>

@endsection 