@extends('layouts.user-dashboard')

@section('header', 'Upraviť inzerát')

@section('content')
<div class="px-4 sm:px-6 lg:px-8" x-data="{ currentStep: {{ $errors->any() ? (old('current_step') ?? 1) : 1 }}, totalSteps: 5 }">
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
                                     step === 2 ? 'Kontakt a čas' :
                                     step === 3 ? 'Zážitky a popis' :
                                     step === 4 ? 'Fotky & Video' :
                                     'Vzhľad'"></span>
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

    <form action="{{ route('ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data" id="multi-step-form" onsubmit="return validateForm()">
        @csrf
        @method('PUT')
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
                        <input type="text" id="nickname" name="nickname" value="{{ old('nickname', $ad->nickname) }}" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Zadajte prezývku pre tento inzerát">
                        <p class="mt-1 text-sm text-gray-500">Prezývka bude zobrazená v inzeráte a musí byť jedinečná</p>
                    </div>

                    <!-- Prvý riadok: Typ inzerátu a Národnosť -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="ad_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Typ profilu <span class="text-red-500">*</span>
                            </label>
                            <select id="ad_type" name="ad_type" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte typ profilu</option>
                                <option value="zena" {{ old('ad_type', $ad->ad_type) === 'zena' ? 'selected' : '' }}>Žena</option>
                                <option value="trans" {{ old('ad_type', $ad->ad_type) === 'trans' ? 'selected' : '' }}>Trans</option>
                                <option value="par" {{ old('ad_type', $ad->ad_type) === 'par' ? 'selected' : '' }}>Pár</option>
                                <option value="klub" {{ old('ad_type', $ad->ad_type) === 'klub' ? 'selected' : '' }}>Masážny salón</option>
                            </select>
                        </div>

                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700 mb-2">
                                Národnosť <span class="text-red-500">*</span>
                            </label>
                            <select id="nationality" name="nationality" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte národnosť</option>
                                <option value="slovenska" {{ old('nationality', $ad->nationality) === 'slovenska' ? 'selected' : '' }}>Slovenská</option>
                                <option value="ceska" {{ old('nationality', $ad->nationality) === 'ceska' ? 'selected' : '' }}>Česká</option>
                                <option value="madarská" {{ old('nationality', $ad->nationality) === 'madarská' ? 'selected' : '' }}>Maďarská</option>
                                <option value="ukrajinska" {{ old('nationality', $ad->nationality) === 'ukrajinska' ? 'selected' : '' }}>Ukrajinská</option>
                                <option value="rumunska" {{ old('nationality', $ad->nationality) === 'rumunska' ? 'selected' : '' }}>Rumunská</option>
                                <option value="ina" {{ old('nationality', $ad->nationality) === 'ina' ? 'selected' : '' }}>Iná</option>
                            </select>
                        </div>
                    </div>

                    <!-- Druhý riadok: Vek a Mesto -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-2">
                                Vek <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="age" name="age" min="18" max="99" value="{{ old('age', $ad->age) }}" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Zadajte vek">
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Mesto <span class="text-red-500">*</span>
                            </label>
                            <select id="city" name="city" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte mesto</option>
                                <option value="bratislava" {{ old('city', $ad->city) === 'bratislava' ? 'selected' : '' }}>Bratislava</option>
                                <option value="kosice" {{ old('city', $ad->city) === 'kosice' ? 'selected' : '' }}>Košice</option>
                                <option value="presov" {{ old('city', $ad->city) === 'presov' ? 'selected' : '' }}>Prešov</option>
                                <option value="zilina" {{ old('city', $ad->city) === 'zilina' ? 'selected' : '' }}>Žilina</option>
                                <option value="banska-bystrica" {{ old('city', $ad->city) === 'banska-bystrica' ? 'selected' : '' }}>Banská Bystrica</option>
                                <option value="nitra" {{ old('city', $ad->city) === 'nitra' ? 'selected' : '' }}>Nitra</option>
                                <option value="trnava" {{ old('city', $ad->city) === 'trnava' ? 'selected' : '' }}>Trnava</option>
                                <option value="martin" {{ old('city', $ad->city) === 'martin' ? 'selected' : '' }}>Martin</option>
                                <option value="trencin" {{ old('city', $ad->city) === 'trencin' ? 'selected' : '' }}>Trenčín</option>
                                <option value="poprad" {{ old('city', $ad->city) === 'poprad' ? 'selected' : '' }}>Poprad</option>
                                <option value="ine" {{ old('city', $ad->city) === 'ine' ? 'selected' : '' }}>Iné</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ulica - celá šírka -->
                    <div>
                        <label for="street" class="block text-sm font-medium text-gray-700 mb-2">Ulica</label>
                        <input type="text" id="street" name="street" value="{{ old('street', $ad->street) }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Uveďte názov ulice, mestskej časti alebo iný orientačný bod">
                    </div>

                    <!-- Tretí riadok: Typ stretnutia -->
                    <div>
                        <label for="offer_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Typ stretnutia <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2 border border-gray-300 rounded-md p-3 max-h-32 overflow-y-auto">
                            @php
                                $offerTypes = [
                                    'stretnutie-u-mna' => 'Stretnutie u mňa',
                                    'stretnutie-u-teba' => 'Stretnutie u teba',
                                    'masaz' => 'Masáž',
                                ];
                                $selectedTypes = old('offer_type', $ad->offer_type ?? []);
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
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $ad->phone) }}" required pattern="[0-9]+" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Iba číslice, napr. 905123456">
                        <p class="mt-1 text-sm text-gray-500">Iba slovenské alebo české telefónne číslo, bez medzinárodnej predvoľby</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Krok 2: Kontakt a kedy mám čas -->
        <div x-show="currentStep === 2">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900">Krok 2: Kontakt a kedy mám čas</h3>
                    <p class="text-sm text-gray-500 mt-1">Nastavte možnosti kontaktovania a pracovné hodiny</p>
                </div>
                
                <div class="px-6 py-6 space-y-8">
                    <!-- Možnosti kontaktovania -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Možnosti kontaktovania</h4>
                        <div class="space-y-3">
                            @php
                                $contactMethods = old('contact_methods', is_array($ad->contact_methods) ? $ad->contact_methods : []);
                            @endphp
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="whatsapp" {{ in_array('whatsapp', $contactMethods) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">WhatsApp</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="viber" {{ in_array('viber', $contactMethods) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Viber</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="sms" {{ in_array('sms', $contactMethods) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">SMS</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="nerozumiem-po-slovensky" {{ in_array('nerozumiem-po-slovensky', $contactMethods) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Nerozumiem po slovensky</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="nereagujem-na-skryte-cisla" {{ in_array('nereagujem-na-skryte-cisla', $contactMethods) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Nereagujem na skryté čísla (CLIR)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_methods[]" value="na-hovory-odpovedá-telefonistka" {{ in_array('na-hovory-odpovedá-telefonistka', $contactMethods) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Na hovory odpovedá telefonistka</span>
                            </label>
                        </div>
                    </div>

                    <!-- Pracovné hodiny -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Kedy mám čas</h4>
                        <div class="space-y-4">
                            @php
                                $hours = old('hours', is_array($ad->hours) ? $ad->hours : []);
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
                                        <input type="time" name="hours[{{ $day }}][from]" value="{{ old('hours.' . $day . '.from', $hours[$day]['from'] ?? '') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                    </div>
                                    <div>
                                        <input type="time" name="hours[{{ $day }}][to]" value="{{ old('hours.' . $day . '.to', $hours[$day]['to'] ?? '') }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="hours[{{ $day }}][status]" value="available" {{ old('hours.' . $day . '.status', $hours[$day]['status'] ?? '') === 'available' ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">Zavolaj (dohoda)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="hours[{{ $day }}][status]" value="not_working" {{ old('hours.' . $day . '.status', $hours[$day]['status'] ?? '') === 'not_working' ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">Nemám v tento deň čas</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="hours[{{ $day }}][status]" value="busy" {{ old('hours.' . $day . '.status', $hours[$day]['status'] ?? '') === 'busy' ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300">
                                            <span class="ml-2 text-sm text-gray-700">Mám čas celý deň</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Krok 3: Zážitky a detail profilu -->
        <div x-show="currentStep === 3">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900">Krok 3: Zážitky a detail profilu</h3>
                    <p class="text-sm text-gray-500 mt-1">Vyberte služby a napíšte popis inzerátu</p>
                </div>
                
                <div class="px-6 py-6 space-y-8">
                    <!-- Zážitky -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-1">Zážitky</h4>
                        <p class="text-sm text-gray-500 mb-4">Vyberte, čo ponúkate. Kategórie sú farebne rozlíšené.</p>
                        @php
                            $practices = old('practices', is_array($ad->practices) ? $ad->practices : []);
                        @endphp
                        <div class="space-y-4">
                            @foreach(config('zazitky') as $category)
                                <div class="border {{ $category['section'] }} rounded-lg p-4">
                                    <h5 class="text-sm font-semibold {{ $category['heading'] }} mb-3">{{ $category['title'] }}</h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($category['items'] as $value => $label)
                                            <label class="flex items-center">
                                                <input type="checkbox" name="practices[]" value="{{ $value }}" {{ in_array($value, $practices) ? 'checked' : '' }} class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Popis profilu -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Popis profilu</h4>
                        <p class="mb-3 text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-md px-3 py-2">Profily s cenníkmi neschválime, detaily stretnutia si dohodnite priamo s klientom. Povolená je jedna suma, napríklad – stretnutie od XX eur.</p>
                        <textarea id="description" name="description" rows="10" required minlength="20" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500" placeholder="Opíšte svoje služby, čo ponúkate, vaše skúsenosti...">{{ old('description', $ad->description) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Minimálne 20 znakov</p>
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
                        
                        <!-- Existujúca verifikačná fotka -->
                        @if($ad->verification_image_url)
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-start space-x-4">
                                    <img src="{{ $ad->verification_image_url }}" alt="Aktuálna verifikačná fotka" class="w-24 h-24 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Aktuálna verifikačná fotka</p>
                                        <p class="text-xs text-gray-500 mt-1">Môžete nahrať novú fotku pre nahradenie existujúcej</p>
                                        <label class="inline-flex items-center mt-2">
                                            <input type="checkbox" name="remove_verification_photo" value="1" class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                            <span class="ml-2 text-sm text-red-600">Vymazať existujúcu fotku</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="verification_photo" name="verification_photo" accept="image/*" class="hidden">
                            <label for="verification_photo" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-pink-600">{{ $ad->verification_image_url ? 'Nahradiť súbor' : 'Vybrať súbor' }}</span>
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
                        
                        <!-- Existujúce fotky v galérii -->
                        @if($ad->gallery_image_urls && count($ad->gallery_image_urls) > 0)
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg" id="gallery-container">
                                <p class="text-sm font-medium text-gray-900 mb-3">Aktuálne fotky v galérii (<span id="gallery-count">{{ count($ad->gallery_image_urls) }}</span>)</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4" id="gallery-grid">
                                    @foreach($ad->gallery_image_urls as $index => $photoUrl)
                                        <div class="relative group" data-photo-index="{{ $index }}">
                                            <img src="{{ $photoUrl }}" alt="Galéria {{ $index + 1 }}" class="w-full h-24 object-cover rounded-lg">
                                            <button type="button" data-photo-index="{{ $index }}" class="delete-photo-btn absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full shadow-lg p-2 transition-colors group/btn">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                <span class="text-white text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 font-medium">Kliknite na košík pre vymazanie</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <p class="text-sm text-gray-700">
                                        <strong>🗑️ Vymazanie fotiek:</strong> Kliknite na červený košík pri fotke a tá sa okamžite vymaže.
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="gallery_photos" name="gallery_photos[]" accept="image/*" multiple class="hidden">
                            <label for="gallery_photos" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-pink-600">{{ ($ad->gallery_image_urls && count($ad->gallery_image_urls) > 0) ? 'Pridať ďalšie fotky' : 'Vybrať súbory' }}</span>
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
                        
                        <!-- Existujúce video -->
                        @if($ad->video)
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-start space-x-4">
                                    <div class="w-32 h-24 bg-gray-200 rounded-lg overflow-hidden">
                                        <video class="w-full h-full object-cover" controls>
                                            <source src="{{ asset('storage/' . $ad->video) }}" type="video/mp4">
                                            Váš prehliadač nepodporuje video tag.
                                        </video>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">Aktuálne video</p>
                                        <p class="text-xs text-gray-500 mt-1">Môžete nahrať nové video pre nahradenie existujúceho</p>
                                        <label class="inline-flex items-center mt-2">
                                            <input type="checkbox" name="remove_video" value="1" class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                                            <span class="ml-2 text-sm text-red-600">Vymazať existujúce video</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" id="video" name="video" accept="video/*" class="hidden">
                            <label for="video" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-pink-600">{{ $ad->video ? 'Nahradiť súbor' : 'Vybrať súbor' }}</span>
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

        <!-- Krok 5: Vzhľad -->
        <div x-show="currentStep === 5">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100">
                <div class="px-6 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900">Krok 5: Vzhľad</h3>
                    <p class="text-sm text-gray-500 mt-1">Doplňte fyzické charakteristiky</p>
                </div>
                
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Výška -->
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Výška (cm)</label>
                            <input type="number" id="height" name="height" value="{{ old('height', $ad->height) }}" min="140" max="220" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                        </div>

                        <!-- Váha -->
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Váha (kg)</label>
                            <input type="number" id="weight" name="weight" value="{{ old('weight', $ad->weight) }}" min="35" max="150" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                        </div>

                        <!-- Veľkosť pŕs -->
                        <div>
                            <label for="breast_size" class="block text-sm font-medium text-gray-700 mb-2">Prsia</label>
                            <select id="breast_size" name="breast_size" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte veľkosť</option>
                                <option value="velkost-1" {{ old('breast_size', $ad->breast_size) === 'velkost-1' ? 'selected' : '' }}>Veľkosť 1</option>
                                <option value="velkost-2" {{ old('breast_size', $ad->breast_size) === 'velkost-2' ? 'selected' : '' }}>Veľkosť 2</option>
                                <option value="velkost-3" {{ old('breast_size', $ad->breast_size) === 'velkost-3' ? 'selected' : '' }}>Veľkosť 3</option>
                                <option value="velkost-4" {{ old('breast_size', $ad->breast_size) === 'velkost-4' ? 'selected' : '' }}>Veľkosť 4</option>
                                <option value="velkost-5" {{ old('breast_size', $ad->breast_size) === 'velkost-5' ? 'selected' : '' }}>Veľkosť 5</option>
                                <option value="velkost-6" {{ old('breast_size', $ad->breast_size) === 'velkost-6' ? 'selected' : '' }}>Veľkosť 6</option>
                                <option value="velkost-6-plus" {{ old('breast_size', $ad->breast_size) === 'velkost-6-plus' ? 'selected' : '' }}>Veľkosť 6+</option>
                            </select>
                        </div>

                        <!-- Orientácia -->
                        <div>
                            <label for="orientation" class="block text-sm font-medium text-gray-700 mb-2">Orientácia</label>
                            <select id="orientation" name="orientation" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte orientáciu</option>
                                @foreach(['hetero' => 'Hetero', 'bi' => 'Bi', 'lesba' => 'Lesba'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('orientation', $ad->orientation) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Farba očí -->
                        <div>
                            <label for="eye_color" class="block text-sm font-medium text-gray-700 mb-2">Farba očí</label>
                            <select id="eye_color" name="eye_color" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte farbu očí</option>
                                @foreach(['modre' => 'Modré', 'hnede' => 'Hnedé', 'zelene' => 'Zelené', 'sede' => 'Šedé', 'cierne' => 'Čierne'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('eye_color', $ad->eye_color) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Farba vlasov -->
                        <div>
                            <label for="hair_color" class="block text-sm font-medium text-gray-700 mb-2">Farba vlasov</label>
                            <select id="hair_color" name="hair_color" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte farbu vlasov</option>
                                @php
                                $hairColors = [
                                    // Blond odtiene
                                    'svetly-blond' => 'Svetlý blond',
                                    'stredny-blond' => 'Stredný blond',
                                    'tmavý-blond' => 'Tmavý blond',
                                    'platinovy-blond' => 'Platinový blond',
                                    'popolavy-blond' => 'Popolavý blond',
                                    // Hnedé odtiene
                                    'svetlo-hnede' => 'Svetlo hnedé',
                                    'stredne-hnede' => 'Stredne hnedé',
                                    'tmavo-hnede' => 'Tmavo hnedé',
                                    'kastanove' => 'Kaštanové',
                                    'cokoladove' => 'Čokoládové',
                                    // Čierne odtiene
                                    'prirodzene-cierne' => 'Prirodzene čierne',
                                    'uhlikovo-cierne' => 'Uhlíkovo čierne',
                                    // Červené odtiene
                                    'prirodzene-cervene' => 'Prirodzene červené',
                                    'medene' => 'Medené',
                                    'ryzie' => 'Ryžie',
                                    'mahagonove' => 'Mahagónové',
                                    'cervenohnede' => 'Červenohnedé',
                                    // Šedé/Sivé
                                    'strieborno-sede' => 'Strieborno šedé',
                                    'prirodzene-sede' => 'Prirodzene šedé',
                                    'popolave' => 'Popolavé',
                                    // Farebné/Neobvyklé
                                    'fialove' => 'Fialové',
                                    'modre' => 'Modré',
                                    'ruzove' => 'Ružové',
                                    'zelene' => 'Zelené',
                                    'duhovka' => 'Dúhovka',
                                    'ombre' => 'Ombré',
                                    'melir' => 'Melír',
                                    'baleyage' => 'Balayage',
                                    // Ostatné
                                    'bile' => 'Biele',
                                    'prirodzena-farba' => 'Prirodzená farba',
                                    // Zachovanie kompatibility so starými hodnotami
                                    'blonde' => 'Blond',
                                    'hnede' => 'Hnedé',
                                    'cierne' => 'Čierne',
                                    'cervene' => 'Červené',
                                    'sede' => 'Šedé',
                                    'farebne' => 'Farebné',
                                    'ine' => 'Iné'
                                ];
                                @endphp
                                @foreach($hairColors as $value => $label)
                                    <option value="{{ $value }}" {{ old('hair_color', $ad->hair_color) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tetovanie -->
                        <div>
                            <label for="tattoos" class="block text-sm font-medium text-gray-700 mb-2">Tetovanie</label>
                            <select id="tattoos" name="tattoos" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte možnosť</option>
                                @foreach(['nie' => 'Nie', 'ano' => 'Áno', 'male' => 'Malé'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('tattoos', $ad->tattoos) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Piercing -->
                        <div>
                            <label for="piercing" class="block text-sm font-medium text-gray-700 mb-2">Piercing</label>
                            <select id="piercing" name="piercing" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500">
                                <option value="">Vyberte možnosť</option>
                                @foreach(['nie' => 'Nie', 'ano' => 'Áno', 'male' => 'Malé'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('piercing', $ad->piercing) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
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
                <button type="button" x-show="currentStep < totalSteps" @click="currentStep++; document.querySelector('input[name=current_step]').value = currentStep" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    Ďalej
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <button type="submit" x-show="currentStep === totalSteps" id="submit-btn" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <span id="submit-text">Uložiť zmeny</span>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
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

function validateForm() {
    // Validácia offer_type
    const offerTypeCheckboxes = document.querySelectorAll('input[name="offer_type[]"]:checked');
    if (offerTypeCheckboxes.length === 0) {
        alert('Musíte vybrať aspoň jeden typ ponuky.');
        return false;
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

// Funkcia na vymazanie fotky z galérie
function deleteGalleryPhoto(index) {
    console.log('deleteGalleryPhoto called with index:', index);
    
    if (!confirm('Naozaj chcete vymazať túto fotku? Táto akcia sa nedá vrátiť späť.')) {
        return;
    }
    
    const adId = {{ $ad->id }};
    const photoContainer = document.querySelector(`[data-photo-index="${index}"]`);
    
    if (!photoContainer) {
        console.error('Photo container not found for index:', index);
        alert('Chyba: Fotka nebola nájdená.');
        return;
    }
    
    const deleteButton = photoContainer.querySelector('button');
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Chyba: CSRF token nenájdený.');
        return;
    }
    
    console.log('Sending DELETE request to:', `/inzeraty/${adId}/fotka/${index}`);
    
    // Deaktivovať tlačidlo a zobraziť loading
    deleteButton.disabled = true;
    deleteButton.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    // AJAX request
    fetch(`/inzeraty/${adId}/fotka/${index}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Animácia vymazania
            photoContainer.style.transition = 'all 0.3s ease';
            photoContainer.style.opacity = '0';
            photoContainer.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                photoContainer.remove();
                
                // Aktualizovať počet fotiek
                document.getElementById('gallery-count').textContent = data.remaining_count;
                
                // Ak nie sú žiadne fotky, skryť celý kontajner
                if (data.remaining_count === 0) {
                    document.getElementById('gallery-container').style.display = 'none';
                }
                
                // Zobraziť úspešnú správu
                showNotification('Fotka bola úspešne vymazaná.', 'success');
            }, 300);
        } else {
            // Obnoviť tlačidlo pri chybe
            deleteButton.disabled = false;
            deleteButton.innerHTML = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
            showNotification(data.message || 'Chyba pri vymazávaní fotky.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        deleteButton.disabled = false;
        deleteButton.innerHTML = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
        showNotification('Chyba pri vymazávaní fotky.', 'error');
    });
}

// Funkcia na zobrazenie notifikácií
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animácia zobrazenia
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    }, 10);
    
    // Automatické odstránenie po 3 sekundách
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Event listener pre delete tlačidlá
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up delete photo event listeners');
    
    // Pridaj event listenery na všetky delete tlačidlá
    document.querySelectorAll('.delete-photo-btn').forEach(button => {
        button.addEventListener('click', function() {
            const index = this.getAttribute('data-photo-index');
            console.log('Delete button clicked for index:', index);
            deleteGalleryPhoto(index);
        });
    });
});
</script>

@endsection 