@extends('layouts.admin-dashboard')

@section('header', 'Vytvoriť nový inzerát (Admin)')

@section('content')
<!-- Breadcrumb -->
<div class="px-4 sm:px-6 lg:px-8 mb-8">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.inzeraty.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-pink-600">
                    <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Inzeráty
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Vytvoriť nový</span>
                </div>
            </li>
        </ol>
    </nav>
</div>

<div class="px-4 sm:px-6 lg:px-8" x-data="{ 
    currentStep: {{ $errors->any() ? (old('current_step') ?? 1) : 1 }}, 
    totalSteps: 5,
    init() {
        // Poslúchaj na zmeny krokov
        window.addEventListener('step-change', (event) => {
            this.currentStep = event.detail.step;
        });
    }
}">
    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2 md:space-x-4 overflow-x-auto">
                <template x-for="step in totalSteps" :key="step">
                    <div class="flex items-center flex-shrink-0">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium"
                             :class="step <= currentStep ? 'bg-pink-600 text-white' : 'bg-gray-200 text-gray-500'">
                            <span class="leading-none" x-text="step"></span>
                        </div>
                        <span class="ml-2 text-sm font-medium hidden md:inline"
                              :class="step <= currentStep ? 'text-gray-900' : 'text-gray-500'"
                              x-text="step === 1 ? 'Základné údaje' : 
                                     step === 2 ? 'Kontakt & Hodiny' : 
                                     step === 3 ? 'Služby & Popis' : 
                                     step === 4 ? 'Fotky & Video' : 
                                     'Fyzické údaje'"></span>
                        <div x-show="step < totalSteps" class="w-8 md:w-16 h-1 flex-shrink-0 ml-2"
                             :class="step < currentStep ? 'bg-pink-600' : 'bg-gray-200'"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Flash správy -->
    @if($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Opravte nasledujúce chyby:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Chyba:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.inzeraty.store') }}" method="POST" enctype="multipart/form-data" id="multi-step-form" onsubmit="return validateForm()">
        @csrf
        <input type="hidden" name="current_step" x-model="currentStep">
        
        <!-- Krok 1: Základné informácie -->
        <div x-show="currentStep === 1">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900">Krok 1: Základné informácie</h3>
                    <p class="text-sm text-gray-500 mt-1">Vyplňte základné údaje o vašom inzeráte</p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <!-- Prezývka - celá šírka -->
                    <div>
                        <label for="nickname" class="block text-sm font-medium text-gray-700 mb-2">
                            Prezývka <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nickname" name="nickname" value="{{ old('nickname') }}" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Zadajte prezývku pre tento inzerát">
                        <p class="mt-1 text-sm text-gray-500">Prezývka bude zobrazená v inzeráte a musí byť jedinečná</p>
                    </div>

                    <!-- Prvý riadok: Typ inzerátu a Národnosť -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="ad_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Typ inzerátu <span class="text-red-500">*</span>
                            </label>
                            <select id="ad_type" name="ad_type" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte typ inzerátu</option>
                                <option value="zena" {{ old('ad_type') === 'zena' ? 'selected' : '' }}>Žena</option>
                                <option value="par" {{ old('ad_type') === 'par' ? 'selected' : '' }}>Pár</option>
                                <option value="trans" {{ old('ad_type') === 'trans' ? 'selected' : '' }}>Trans</option>
                                <option value="klub" {{ old('ad_type') === 'klub' ? 'selected' : '' }}>Klub</option>
                            </select>
                        </div>

                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">
                                Národnosť <span class="text-red-500">*</span>
                            </label>
                            <select id="nationality" name="nationality" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte národnosť</option>
                                <option value="slovenska" {{ old('nationality') === 'slovenska' ? 'selected' : '' }}>Slovenská</option>
                                <option value="ceska" {{ old('nationality') === 'ceska' ? 'selected' : '' }}>Česká</option>
                                <option value="madarská" {{ old('nationality') === 'madarská' ? 'selected' : '' }}>Maďarská</option>
                                <option value="ukrajinska" {{ old('nationality') === 'ukrajinska' ? 'selected' : '' }}>Ukrajinská</option>
                                <option value="rumunska" {{ old('nationality') === 'rumunska' ? 'selected' : '' }}>Rumunská</option>
                                <option value="ina" {{ old('nationality') === 'ina' ? 'selected' : '' }}>Iná</option>
                            </select>
                        </div>
                    </div>

                    <!-- Druhý riadok: Vek a Mesto -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-2">
                                Vek <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="age" name="age" min="18" max="99" value="{{ old('age') }}" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Zadajte vek">
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Mesto <span class="text-red-500">*</span>
                            </label>
                            <select id="city" name="city" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte mesto</option>
                                <option value="bratislava" {{ old('city') === 'bratislava' ? 'selected' : '' }}>Bratislava</option>
                                <option value="kosice" {{ old('city') === 'kosice' ? 'selected' : '' }}>Košice</option>
                                <option value="presov" {{ old('city') === 'presov' ? 'selected' : '' }}>Prešov</option>
                                <option value="zilina" {{ old('city') === 'zilina' ? 'selected' : '' }}>Žilina</option>
                                <option value="banska-bystrica" {{ old('city') === 'banska-bystrica' ? 'selected' : '' }}>Banská Bystrica</option>
                                <option value="nitra" {{ old('city') === 'nitra' ? 'selected' : '' }}>Nitra</option>
                                <option value="trnava" {{ old('city') === 'trnava' ? 'selected' : '' }}>Trnava</option>
                                <option value="martin" {{ old('city') === 'martin' ? 'selected' : '' }}>Martin</option>
                                <option value="trencin" {{ old('city') === 'trencin' ? 'selected' : '' }}>Trenčín</option>
                                <option value="poprad" {{ old('city') === 'poprad' ? 'selected' : '' }}>Poprad</option>
                                <option value="ine" {{ old('city') === 'ine' ? 'selected' : '' }}>Iné</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ulica - celá šírka -->
                    <div>
                        <label for="street" class="block text-sm font-medium text-gray-700 mb-2">Ulica</label>
                        <input type="text" id="street" name="street" value="{{ old('street') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Uveďte názov ulice, mestskej časti alebo iný orientačný bod">
                    </div>

                    <!-- Tretí riadok: Ponúkam -->
                    <div>
                        <label for="offer_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Ponúkam <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2 border border-gray-300 rounded-md p-3 max-h-32 overflow-y-auto">
                            @php
                                $offerTypes = [
                                    'ponukam-privat' => 'Ponúkam privát',
                                    'ponukam-escort' => 'Ponúkam escort',
                                    'ponukam-masaz' => 'Ponúkam masáž',
                                    'hladam-privat' => 'Hľadám privát',
                                    'hladam-escort' => 'Hľadám escort',
                                    'hladam-masaz' => 'Hľadám masáž'
                                ];
                                $selectedTypes = old('offer_type', []);
                            @endphp

                            @foreach($offerTypes as $value => $label)
                                <label class="flex items-center">
                                    <input type="checkbox" name="offer_type[]" value="{{ $value }}" {{ in_array($value, $selectedTypes) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Môžete vybrať viac možností</p>
                    </div>

                    <!-- Telefónne číslo - celá šírka -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefónne číslo <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required pattern="[0-9]+" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Iba číslice, napr. 905123456">
                        <p class="mt-1 text-sm text-gray-500">Iba slovenské alebo české telefónne číslo, bez medzinárodnej predvoľby</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Krok 2: Kontakt & Pracovné hodiny -->
        <div x-show="currentStep === 2">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900">Krok 2: Kontakt & Pracovné hodiny</h3>
                    <p class="text-sm text-gray-500 mt-1">Nastavte možnosti kontaktovania a pracovné hodiny</p>
                </div>
                
                <div class="px-6 py-6 space-y-8">
                    <!-- Možnosti kontaktovania -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Možnosti kontaktovania</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="whatsapp" {{ in_array('whatsapp', old('contact_methods', [])) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">WhatsApp</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="viber" {{ in_array('viber', old('contact_methods', [])) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Viber</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="sms" {{ in_array('sms', old('contact_methods', [])) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">SMS</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="nerozumiem-po-slovensky" {{ in_array('nerozumiem-po-slovensky', old('contact_methods', [])) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Nerozumiem po slovensky</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="nereagujem-na-skryte-cisla" {{ in_array('nereagujem-na-skryte-cisla', old('contact_methods', [])) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Nereagujem na skryté čísla (CLIR)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="na-hovory-odpovedá-telefonistka" {{ in_array('na-hovory-odpovedá-telefonistka', old('contact_methods', [])) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Na hovory odpovedá telefonistka</span>
                            </label>
                        </div>
                    </div>

                    <!-- Pracovné hodiny -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Pracovné hodiny</h4>
                        <div class="space-y-4">
                            @php
                                $days = [
                                    'monday' => 'Pondelok',
                                    'tuesday' => 'Utorok', 
                                    'wednesday' => 'Streda',
                                    'thursday' => 'Štvrtok',
                                    'friday' => 'Piatok',
                                    'saturday' => 'Sobota',
                                    'sunday' => 'Nedeľa'
                                ];
                            @endphp

                            @foreach($days as $day => $dayName)
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center p-4 border border-gray-200 rounded-lg">
                                    <div class="font-medium text-gray-700">{{ $dayName }}</div>
                                    <div>
                                        <input type="time" name="hours[{{ $day }}][from]" value="{{ old('hours.' . $day . '.from') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                    </div>
                                    <div>
                                        <input type="time" name="hours[{{ $day }}][to]" value="{{ old('hours.' . $day . '.to') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="hours[{{ $day }}][status]" value="available" {{ old('hours.' . $day . '.status') === 'available' ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">Zavolaj (dohoda)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="hours[{{ $day }}][status]" value="busy" {{ old('hours.' . $day . '.status') === 'busy' ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">Obsadená</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="hours[{{ $day }}][status]" value="not_working" {{ old('hours.' . $day . '.status') === 'not_working' ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">Nepracujem</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Krok 3: Praktiky & Text inzerátu -->
        <div x-show="currentStep === 3">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900">Krok 3: Služby & Popis</h3>
                    <p class="text-sm text-gray-500 mt-1">Vyberte služby a napíšte popis inzerátu</p>
                </div>
                
                <div class="px-6 py-6 space-y-8">
                    <!-- Zážitky -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-1">Zážitky</h4>
                        <p class="text-sm text-gray-500 mb-4">Kategórie sú farebne rozlíšené.</p>
                        <div class="space-y-4">
                            @foreach(config('zazitky') as $category)
                                <div class="border {{ $category['section'] }} rounded-lg p-4">
                                    <h5 class="text-sm font-semibold {{ $category['heading'] }} mb-3">{{ $category['title'] }}</h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($category['items'] as $value => $label)
                                            <label class="flex items-center">
                                                <input type="checkbox" name="practices[]" value="{{ $value }}" {{ in_array($value, old('practices', [])) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Text inzerátu -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Text inzerátu</h4>
                        <textarea id="description" name="description" rows="10" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Opíšte svoje služby, čo ponúkate, vaše skúsenosti...">{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Minimálne 50 znakov</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Krok 4: Fotky & Video -->
        <div x-show="currentStep === 4">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900">Krok 4: Fotky & Video</h3>
                    <p class="text-sm text-gray-500 mt-1">Nahrajte verifikačnú fotku, galériu a video</p>
                </div>
                
                <div class="px-6 py-6 space-y-8">
                    <!-- Verifikačná fotka -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Verifikačná fotka <span class="text-red-500">*</span></h4>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="verification_photo" name="verification_photo" accept="image/*" required class="hidden">
                            <label for="verification_photo" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-pink-600">Vybrať súbor</span>
                                    <span class="text-sm text-gray-500">alebo pretiahnuť sem</span>
                                </div>
                            </label>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            Nezabudnite nahrať povinné a aktuálne <strong>verifikačné fotografie</strong> pre rok 2025!<br>
                            Podporované formáty: png, jpg, jpeg, bmp, gif, tiff | Max 24 MB | Min rozlíšenie: 480 x 640px
                        </p>
                    </div>

                    <!-- Fotogaléria -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Fotogaléria</h4>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="gallery_photos" name="gallery_photos[]" accept="image/*" multiple class="hidden">
                            <label for="gallery_photos" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-pink-600">Vybrať súbory</span>
                                    <span class="text-sm text-gray-500">alebo pretiahnuť sem</span>
                                </div>
                            </label>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            Podporované formáty: png, jpg, jpeg | Max 24 MB na fotku | Min rozlíšenie: 480 x 640px
                        </p>
                    </div>

                    <!-- Video -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Video</h4>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="video" name="video" accept="video/*" class="hidden">
                            <label for="video" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-pink-600">Vybrať súbor</span>
                                    <span class="text-sm text-gray-500">alebo pretiahnuť sem</span>
                                </div>
                            </label>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            Video musí mať minimálne 10 sekúnd | Podporované formáty: mp4, avi, mov, wmv | Max 100 MB
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Krok 5: Fyzické vlastnosti -->
        <div x-show="currentStep === 5">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900">Krok 5: Fyzické vlastnosti</h3>
                    <p class="text-sm text-gray-500 mt-1">Uveďte svoje fyzické parametre</p>
                </div>
                
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Výška -->
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Výška (cm)</label>
                            <input type="number" id="height" name="height" min="140" max="220" value="{{ old('height') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Výška v cm">
                        </div>

                        <!-- Váha -->
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Váha (kg)</label>
                            <input type="number" id="weight" name="weight" min="40" max="150" value="{{ old('weight') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Váha v kg">
                        </div>

                        <!-- Prsia -->
                        <div>
                            <label for="breast_size" class="block text-sm font-medium text-gray-700 mb-2">Prsia</label>
                            <select id="breast_size" name="breast_size" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte veľkosť</option>
                                <option value="velkost-1" {{ old('breast_size') === 'velkost-1' ? 'selected' : '' }}>Veľkosť 1</option>
                                <option value="velkost-2" {{ old('breast_size') === 'velkost-2' ? 'selected' : '' }}>Veľkosť 2</option>
                                <option value="velkost-3" {{ old('breast_size') === 'velkost-3' ? 'selected' : '' }}>Veľkosť 3</option>
                                <option value="velkost-4" {{ old('breast_size') === 'velkost-4' ? 'selected' : '' }}>Veľkosť 4</option>
                                <option value="velkost-5" {{ old('breast_size') === 'velkost-5' ? 'selected' : '' }}>Veľkosť 5</option>
                                <option value="velkost-6" {{ old('breast_size') === 'velkost-6' ? 'selected' : '' }}>Veľkosť 6</option>
                                <option value="velkost-6-plus" {{ old('breast_size') === 'velkost-6-plus' ? 'selected' : '' }}>Veľkosť 6+</option>
                            </select>
                        </div>

                        <!-- Farba očí -->
                        <div>
                            <label for="eye_color" class="block text-sm font-medium text-gray-700 mb-2">Farba očí</label>
                            <select id="eye_color" name="eye_color" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte farbu očí</option>
                                <option value="hnede" {{ old('eye_color') === 'hnede' ? 'selected' : '' }}>Hnedé</option>
                                <option value="modre" {{ old('eye_color') === 'modre' ? 'selected' : '' }}>Modré</option>
                                <option value="zelene" {{ old('eye_color') === 'zelene' ? 'selected' : '' }}>Zelené</option>
                                <option value="sive" {{ old('eye_color') === 'sive' ? 'selected' : '' }}>Sivé</option>
                                <option value="cierne" {{ old('eye_color') === 'cierne' ? 'selected' : '' }}>Čierne</option>
                            </select>
                        </div>

                        <!-- Farba vlasov -->
                        <div>
                            <label for="hair_color" class="block text-sm font-medium text-gray-700 mb-2">Farba vlasov</label>
                            <select id="hair_color" name="hair_color" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte farbu vlasov</option>
                                <!-- Blond odtiene -->
                                <option value="svetly-blond" {{ old('hair_color') === 'svetly-blond' ? 'selected' : '' }}>Svetlý blond</option>
                                <option value="stredny-blond" {{ old('hair_color') === 'stredny-blond' ? 'selected' : '' }}>Stredný blond</option>
                                <option value="tmavý-blond" {{ old('hair_color') === 'tmavý-blond' ? 'selected' : '' }}>Tmavý blond</option>
                                <option value="platinovy-blond" {{ old('hair_color') === 'platinovy-blond' ? 'selected' : '' }}>Platinový blond</option>
                                <option value="popolavy-blond" {{ old('hair_color') === 'popolavy-blond' ? 'selected' : '' }}>Popolavý blond</option>
                                <!-- Hnedé odtiene -->
                                <option value="svetlo-hnede" {{ old('hair_color') === 'svetlo-hnede' ? 'selected' : '' }}>Svetlo hnedé</option>
                                <option value="stredne-hnede" {{ old('hair_color') === 'stredne-hnede' ? 'selected' : '' }}>Stredne hnedé</option>
                                <option value="tmavo-hnede" {{ old('hair_color') === 'tmavo-hnede' ? 'selected' : '' }}>Tmavo hnedé</option>
                                <option value="kastanove" {{ old('hair_color') === 'kastanove' ? 'selected' : '' }}>Kaštanové</option>
                                <option value="cokoladove" {{ old('hair_color') === 'cokoladove' ? 'selected' : '' }}>Čokoládové</option>
                                <!-- Čierne odtiene -->
                                <option value="prirodzene-cierne" {{ old('hair_color') === 'prirodzene-cierne' ? 'selected' : '' }}>Prirodzene čierne</option>
                                <option value="uhlikovo-cierne" {{ old('hair_color') === 'uhlikovo-cierne' ? 'selected' : '' }}>Uhlíkovo čierne</option>
                                <!-- Červené odtiene -->
                                <option value="prirodzene-cervene" {{ old('hair_color') === 'prirodzene-cervene' ? 'selected' : '' }}>Prirodzene červené</option>
                                <option value="medene" {{ old('hair_color') === 'medene' ? 'selected' : '' }}>Medené</option>
                                <option value="ryzie" {{ old('hair_color') === 'ryzie' ? 'selected' : '' }}>Ryžie</option>
                                <option value="mahagonove" {{ old('hair_color') === 'mahagonove' ? 'selected' : '' }}>Mahagónové</option>
                                <option value="cervenohnede" {{ old('hair_color') === 'cervenohnede' ? 'selected' : '' }}>Červenohnedé</option>
                                <!-- Šedé/Sivé -->
                                <option value="strieborno-sede" {{ old('hair_color') === 'strieborno-sede' ? 'selected' : '' }}>Strieborno šedé</option>
                                <option value="prirodzene-sede" {{ old('hair_color') === 'prirodzene-sede' ? 'selected' : '' }}>Prirodzene šedé</option>
                                <option value="popolave" {{ old('hair_color') === 'popolave' ? 'selected' : '' }}>Popolavé</option>
                                <!-- Farebné/Neobvyklé -->
                                <option value="fialove" {{ old('hair_color') === 'fialove' ? 'selected' : '' }}>Fialové</option>
                                <option value="modre" {{ old('hair_color') === 'modre' ? 'selected' : '' }}>Modré</option>
                                <option value="ruzove" {{ old('hair_color') === 'ruzove' ? 'selected' : '' }}>Ružové</option>
                                <option value="zelene" {{ old('hair_color') === 'zelene' ? 'selected' : '' }}>Zelené</option>
                                <option value="duhovka" {{ old('hair_color') === 'duhovka' ? 'selected' : '' }}>Dúhovka</option>
                                <option value="ombre" {{ old('hair_color') === 'ombre' ? 'selected' : '' }}>Ombré</option>
                                <option value="melir" {{ old('hair_color') === 'melir' ? 'selected' : '' }}>Melír</option>
                                <option value="baleyage" {{ old('hair_color') === 'baleyage' ? 'selected' : '' }}>Balayage</option>
                                <!-- Ostatné -->
                                <option value="bile" {{ old('hair_color') === 'bile' ? 'selected' : '' }}>Biele</option>
                                <option value="prirodzena-farba" {{ old('hair_color') === 'prirodzena-farba' ? 'selected' : '' }}>Prirodzená farba</option>
                                <option value="ine" {{ old('hair_color') === 'ine' ? 'selected' : '' }}>Iné</option>
                            </select>
                        </div>

                        <!-- Tetovanie -->
                        <div>
                            <label for="tattoos" class="block text-sm font-medium text-gray-700 mb-2">Tetovanie</label>
                            <select id="tattoos" name="tattoos" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte možnosť</option>
                                <option value="ano" {{ old('tattoos') === 'ano' ? 'selected' : '' }}>Áno</option>
                                <option value="nie" {{ old('tattoos') === 'nie' ? 'selected' : '' }}>Nie</option>
                            </select>
                        </div>

                        <!-- Piercing -->
                        <div>
                            <label for="piercing" class="block text-sm font-medium text-gray-700 mb-2">Piercing</label>
                            <select id="piercing" name="piercing" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte možnosť</option>
                                <option value="ano" {{ old('piercing') === 'ano' ? 'selected' : '' }}>Áno</option>
                                <option value="nie" {{ old('piercing') === 'nie' ? 'selected' : '' }}>Nie</option>
                            </select>
                        </div>

                        <!-- Orientácia -->
                        <div>
                            <label for="orientation" class="block text-sm font-medium text-gray-700 mb-2">Orientácia</label>
                            <select id="orientation" name="orientation" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte orientáciu</option>
                                <option value="heterosexualna" {{ old('orientation') === 'heterosexualna' ? 'selected' : '' }}>Heterosexuálna</option>
                                <option value="homosexualna" {{ old('orientation') === 'homosexualna' ? 'selected' : '' }}>Homosexuálna</option>
                                <option value="bisexualna" {{ old('orientation') === 'bisexualna' ? 'selected' : '' }}>Bisexuálna</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigačné tlačidlá -->
        <div class="flex justify-between mt-8">
            <button type="button" x-show="currentStep > 1" @click="currentStep--; document.querySelector('input[name=current_step]').value = currentStep" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Späť
            </button>
            
            <div class="flex space-x-4">
                <a href="{{ route('ads.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    Zrušiť
                </a>
                <button type="button" x-show="currentStep < totalSteps" @click="validateCurrentStep()" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    Ďalej
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <button type="submit" x-show="currentStep === totalSteps" id="submit-btn" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <span id="submit-text">Vytvoriť inzerát</span>
                    <span id="submit-loading" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Ukladám...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Validation Error Modal -->
<div id="validationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-5">Chyba validácie</h3>
            <div class="mt-2 px-7 py-3">
                <p id="validationMessage" class="text-sm text-gray-500"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="validationOkBtn" class="px-4 py-2 bg-pink-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-5">Úspech!</h3>
            <div class="mt-2 px-7 py-3">
                <p id="successMessage" class="text-sm text-gray-500">Inzerát bol úspešne vytvorený!</p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="successOkBtn" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                    Prejsť na moje inzeráty
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Funkcie pre popup modály
function showValidationError(message) {
    document.getElementById('validationMessage').textContent = message;
    document.getElementById('validationModal').classList.remove('hidden');
}

function hideValidationError() {
    document.getElementById('validationModal').classList.add('hidden');
}

function showSuccessModal(message) {
    document.getElementById('successMessage').textContent = message;
    document.getElementById('successModal').classList.remove('hidden');
}

function hideSuccessModal() {
    document.getElementById('successModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // Event listenery pre tlačidlá modálov
    document.getElementById('validationOkBtn').addEventListener('click', hideValidationError);
    document.getElementById('successOkBtn').addEventListener('click', function() {
        window.location.href = '{{ route("ads.index") }}';
    });
    
    // Zatvorenie modálov klikom mimo obsah
    document.getElementById('validationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideValidationError();
        }
    });
    
    document.getElementById('successModal').addEventListener('click', function(e) {
        if (e.target === this) {
            window.location.href = '{{ route("ads.index") }}';
        }
    });
    
    // Kontrola či je session success message
    @if(session('success'))
        showSuccessModal('{{ session("success") }}');
    @endif
    // File upload handlers
    setupFileUpload('verification_photo', false);
    setupFileUpload('gallery_photos', true);
    setupFileUpload('video', false);
    
    function setupFileUpload(inputId, multiple = false) {
        const input = document.getElementById(inputId);
        const container = input.closest('.border-dashed');
        const label = container.querySelector('label');
        
        // Click handler
        label.addEventListener('click', function(e) {
            e.preventDefault();
            input.click();
        });
        
        // Drag and drop handlers
        container.addEventListener('dragover', function(e) {
            e.preventDefault();
            container.classList.add('border-pink-500', 'bg-pink-50');
        });
        
        container.addEventListener('dragleave', function(e) {
            e.preventDefault();
            container.classList.remove('border-pink-500', 'bg-pink-50');
        });
        
        container.addEventListener('drop', function(e) {
            e.preventDefault();
            container.classList.remove('border-pink-500', 'bg-pink-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                if (multiple) {
                    input.files = files;
                } else {
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    input.files = dt.files;
                }
                handleFileSelect(input, container);
            }
        });
        
        // File change handler
        input.addEventListener('change', function() {
            handleFileSelect(input, container);
        });
    }
    
    function handleFileSelect(input, container) {
        const files = input.files;
        if (files.length === 0) return;
        
        // Create preview container if it doesn't exist
        let previewContainer = container.querySelector('.file-preview');
        if (!previewContainer) {
            previewContainer = document.createElement('div');
            previewContainer.className = 'file-preview mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4';
            container.appendChild(previewContainer);
        } else {
            previewContainer.innerHTML = '';
        }
        
        // Show file previews
        Array.from(files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'relative bg-gray-100 rounded-lg p-2';
            
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'w-full h-20 object-cover rounded';
                img.src = URL.createObjectURL(file);
                fileItem.appendChild(img);
            } else if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.className = 'w-full h-20 object-cover rounded';
                video.src = URL.createObjectURL(file);
                video.controls = false;
                fileItem.appendChild(video);
            }
            
            const fileName = document.createElement('div');
            fileName.className = 'text-xs text-gray-600 mt-1 truncate';
            fileName.textContent = file.name;
            fileItem.appendChild(fileName);
            
            const fileSize = document.createElement('div');
            fileSize.className = 'text-xs text-gray-500';
            fileSize.textContent = formatFileSize(file.size);
            fileItem.appendChild(fileSize);
            
            // Remove button for gallery photos
            if (input.id === 'gallery_photos') {
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600';
                removeBtn.innerHTML = '×';
                removeBtn.addEventListener('click', function() {
                    removeFileFromInput(input, index);
                    fileItem.remove();
                });
                fileItem.appendChild(removeBtn);
            }
            
            previewContainer.appendChild(fileItem);
        });
        
        // Update label text
        const labelText = container.querySelector('label .mt-2');
        if (labelText) {
            if (files.length === 1) {
                labelText.innerHTML = `<span class="text-sm font-medium text-green-600">1 súbor vybraný</span>`;
            } else if (files.length > 1) {
                labelText.innerHTML = `<span class="text-sm font-medium text-green-600">${files.length} súborov vybraných</span>`;
            }
        }
    }
    
    function removeFileFromInput(input, indexToRemove) {
        const dt = new DataTransfer();
        const files = input.files;
        
        for (let i = 0; i < files.length; i++) {
            if (i !== indexToRemove) {
                dt.items.add(files[i]);
            }
        }
        
        input.files = dt.files;
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});

// Validácia pre jednotlivé kroky
function validateCurrentStep() {
    const currentStep = parseInt(document.querySelector('input[name=current_step]').value);
    let isValid = false;
    
    switch(currentStep) {
        case 1:
            isValid = validateStep1();
            break;
        case 2:
            isValid = validateStep2();
            break;
        case 3:
            isValid = validateStep3();
            break;
        case 4:
            isValid = validateStep4();
            break;
        case 5:
            isValid = validateStep5();
            break;
        default:
            isValid = true;
    }
    
    if (isValid) {
        // Posuň na ďalší krok
        const newStep = currentStep + 1;
        document.querySelector('input[name=current_step]').value = newStep;
        // Aktualizuj Alpine.js premennu
        window.dispatchEvent(new CustomEvent('step-change', { detail: { step: newStep } }));
    }
}

// Validácia kroku 1: Základné informácie
function validateStep1() {
    const requiredFields = ['nickname', 'ad_type', 'nationality', 'age', 'city'];
    const missingFields = [];
    
    // Kontrola textových a select polí
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field || !field.value.trim()) {
            missingFields.push(getFieldLabel(fieldName));
        }
    });
    
    // Kontrola offer_type checkboxov
    const offerTypeCheckboxes = document.querySelectorAll('input[name="offer_type[]"]:checked');
    if (offerTypeCheckboxes.length === 0) {
        missingFields.push('Ponúkam');
    }
    
    if (missingFields.length > 0) {
        showValidationError('Vyplňte povinné polia: ' + missingFields.join(', '));
        return false;
    }
    
    return true;
}

// Validácia kroku 2: Kontakt & Hodiny
function validateStep2() {
    const requiredFields = ['phone'];
    const missingFields = [];
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field || !field.value.trim()) {
            missingFields.push(getFieldLabel(fieldName));
        }
    });
    
    // Kontrola contact_methods checkboxov
    const contactMethodCheckboxes = document.querySelectorAll('input[name="contact_methods[]"]:checked');
    if (contactMethodCheckboxes.length === 0) {
        missingFields.push('Spôsoby kontaktu');
    }
    
    if (missingFields.length > 0) {
        showValidationError('Vyplňte povinné polia: ' + missingFields.join(', '));
        return false;
    }
    
    return true;
}

// Validácia kroku 3: Služby & Popis
function validateStep3() {
    const requiredFields = ['description'];
    const missingFields = [];
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field || !field.value.trim()) {
            missingFields.push(getFieldLabel(fieldName));
        }
    });
    
    if (missingFields.length > 0) {
        showValidationError('Vyplňte povinné polia: ' + missingFields.join(', '));
        return false;
    }
    
    return true;
}

// Validácia kroku 4: Fotky & Video
function validateStep4() {
    const verificationPhoto = document.getElementById('verification_photo');
    
    if (!verificationPhoto.files || verificationPhoto.files.length === 0) {
        showValidationError('Verifikačná fotka je povinná!');
        return false;
    }
    
    const file = verificationPhoto.files[0];
    if (file.size > 25 * 1024 * 1024) { // 25MB
        showValidationError('Verifikačná fotka je príliš veľká! Maximálna veľkosť je 24MB.');
        return false;
    }
    
    // Kontrola galérie
    const galleryPhotos = document.getElementById('gallery_photos');
    if (galleryPhotos.files) {
        for (let i = 0; i < galleryPhotos.files.length; i++) {
            if (galleryPhotos.files[i].size > 25 * 1024 * 1024) {
                showValidationError(`Fotka č. ${i + 1} v galérii je príliš veľká! Maximálna veľkosť je 24MB.`);
                return false;
            }
        }
    }
    
    // Kontrola videa
    const video = document.getElementById('video');
    if (video.files && video.files.length > 0) {
        if (video.files[0].size > 100 * 1024 * 1024) { // 100MB
            showValidationError('Video je príliš veľké! Maximálna veľkosť je 100MB.');
            return false;
        }
    }
    
    return true;
}

// Validácia kroku 5: Fyzické údaje (žiadne povinné polia)
function validateStep5() {
    return true;
}

// Pomocná funkcia pre získanie názvu poľa
function getFieldLabel(fieldName) {
    const labels = {
        'nickname': 'Prezývka',
        'ad_type': 'Typ inzerátu',
        'nationality': 'Národnosť',
        'age': 'Vek',
        'city': 'Mesto',
        'phone': 'Telefón',
        'description': 'Popis'
    };
    
    return labels[fieldName] || fieldName;
}

// Finálna validácia pri odoslaní formulára
function validateForm() {
    // Validácia offer_type
    const offerTypeCheckboxes = document.querySelectorAll('input[name="offer_type[]"]:checked');
    if (offerTypeCheckboxes.length === 0) {
        showValidationError('Musíte vybrať aspoň jeden typ ponuky.');
        return false;
    }
    
    const verificationPhoto = document.getElementById('verification_photo');
    
    if (!verificationPhoto.files || verificationPhoto.files.length === 0) {
        showValidationError('Verifikačná fotka je povinná!');
        return false;
    }
    
    const file = verificationPhoto.files[0];
    if (file.size > 25 * 1024 * 1024) { // 25MB
        showValidationError('Verifikačná fotka je príliš veľká! Maximálna veľkosť je 24MB.');
        return false;
    }
    
    // Kontrola galérie
    const galleryPhotos = document.getElementById('gallery_photos');
    if (galleryPhotos.files) {
        for (let i = 0; i < galleryPhotos.files.length; i++) {
            if (galleryPhotos.files[i].size > 25 * 1024 * 1024) {
                showValidationError(`Fotka č. ${i + 1} v galérii je príliš veľká! Maximálna veľkosť je 24MB.`);
                return false;
            }
        }
    }
    
    // Kontrola videa
    const video = document.getElementById('video');
    if (video.files && video.files.length > 0) {
        if (video.files[0].size > 100 * 1024 * 1024) { // 100MB
            showValidationError('Video je príliš veľké! Maximálna veľkosť je 100MB.');
            return false;
        }
    }
    
    // Zobrazenie loading stavu
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');
    
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');
    
    return true;
}
</script>

@endsection 