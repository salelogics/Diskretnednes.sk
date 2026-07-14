@extends('layouts.main')

@section('title', ($ad->nickname ?: 'Inzerát') . ' - ' . $ad->city_label . ' | Erotikon')

@section('meta')
    <meta name="description" content="{{ Str::limit(strip_tags($ad->description ?: config('seo.default_description')), 160) }}">
    <meta name="keywords" content="erotika, {{ $ad->city_label }}, {{ $ad->ad_type_label }}, {{ $ad->offer_type_label }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ ($ad->nickname ?: 'Inzerát') . ' - ' . $ad->city_label }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($ad->description ?: config('seo.default_description')), 160) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($ad->verification_image_url)
        <meta property="og:image" content="{{ $ad->verification_image_url }}">
    @endif
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ ($ad->nickname ?: 'Inzerát') . ' - ' . $ad->city_label }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($ad->description ?: config('seo.default_description')), 160) }}">
@endsection

@section('content')
    <!-- Age verification modal -->
    <div id="age-verification-modal" class="fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <div class="relative flex flex-col bg-white dark:bg-slate-900 shadow-lg rounded-xl w-full max-w-md m-3 opacity-0 transform -translate-y-4 transition-all duration-300" style="min-height: 340px;">
            <div class="absolute top-4 end-4">
                <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 disabled:opacity-50 disabled:pointer-events-none" onclick="closeModal()">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 p-4 sm:p-8 text-center flex flex-col items-center">
                <img src="{{ asset('images/uploads/erotikon-logo.webp') }}" alt="Erotikon" class="w-36 mb-6">
                
                <div class="space-y-4 text-left max-w-sm mx-auto">
                    <p class="text-gray-800 dark:text-gray-200 font-medium">
                        Stránky sú určené výhradne pre osoby staršie ako 18 rokov.
                    </p>
                    
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Vstupom na webstránku a kliknutím na tlačidlo Súhlasím potvrdzujem, že mám viac ako 18 rokov a že tieto webové stránky nikdy nebudem ukazovať deťom a maloletým.
                    </p>

                    <div class="pt-2">
                        <h4 class="text-gray-800 dark:text-gray-200 font-medium mb-1">Cookies</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            Kliknutím na tlačidlo Súhlasím, povoliť cookies sa uložia technické a analytické súbory cookie, súbory cookie používame na analýzu údajov o našich návštevníkoch, meranie funkčnosti a na zlepšenie našich webových stránok, aby sme vám poskytli skvelý zážitok z webu.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <button 
                    type="button" 
                    class="w-full py-5 px-4 inline-flex justify-center items-center text-base font-normal rounded-bl-xl bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none border-t border-r border-gray-200 dark:border-gray-700" 
                    onclick="window.location.href='https://www.google.com'"
                >
                    Nesúhlasím
                </button>
                <button 
                    type="button" 
                    class="w-full py-5 px-4 inline-flex justify-center items-center text-base font-normal rounded-br-xl bg-pink-500 text-white hover:bg-pink-600 disabled:opacity-50 disabled:pointer-events-none border-t border-gray-200 dark:border-gray-700" 
                    onclick="verifyAge()"
                >
                    Súhlasím
                </button>
            </div>
        </div>
    </div>

    <script>
        function showModal() {
            const modal = document.getElementById('age-verification-modal');
            const modalContent = modal.querySelector('.relative');
            modal.style.display = 'flex';
            setTimeout(() => {
                modalContent.classList.remove('opacity-0', '-translate-y-4');
                modalContent.classList.add('opacity-100', 'translate-y-0');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('age-verification-modal');
            const modalContent = modal.querySelector('.relative');
            modalContent.classList.remove('opacity-100', 'translate-y-0');
            modalContent.classList.add('opacity-0', '-translate-y-4');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        function verifyAge() {
            let date = new Date();
            date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
            document.cookie = 'ageVerified=true; expires=' + date.toUTCString() + '; path=/';
            localStorage.setItem('ageVerified', 'true');
            closeModal();
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('ageVerified')) {
                showModal();
            }
        });
    </script>

    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden pt-14">
        @if($ad->verification_image_url)
            <img src="{{ $ad->verification_image_url }}" alt="{{ $ad->nickname }}" class="absolute inset-0 -z-10 size-full object-cover">
        @else
            <img src="{{ asset('images/uploads/hero-bg.jpg') }}" alt="" class="absolute inset-0 -z-10 size-full object-cover">
        @endif
        <div class="absolute inset-0 -z-10" style="background-color: #000000cc;"></div>
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-4xl py-12 sm:py-14 lg:py-16">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-4 mb-6">
                        <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                            {{ $ad->nickname ?: 'Anonymný' }}
                        </h1>
                        <!-- Favorite button -->
                        <button onclick="toggleFavorite({{ $ad->id }}, this)" class="favorite-btn p-3 rounded-full bg-white/20 hover:bg-white/30 transition-all backdrop-blur-sm" data-ad-id="{{ $ad->id }}">
                            <svg class="w-8 h-8" viewBox="0 0 20 20">
                                <path class="heart-path text-white" fill="currentColor" fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-6 text-lg text-gray-300 mb-4">
                        <span class="flex items-center gap-2">
                            <i class="ri-map-pin-line text-pink-400"></i>
                            {{ $ad->city_label }}
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="ri-user-line text-pink-400"></i>
                            {{ $ad->ad_type_label }}
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="ri-heart-line text-pink-400"></i>
                            {{ $ad->offer_type_label }}
                        </span>
                    </div>

                    <!-- Dostupnosť -->
                    @if($ad->current_availability)
                        <div class="flex items-center justify-center gap-3 mb-6">
                            <div class="flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                <div class="w-3 h-3 rounded-full {{ $ad->availability_color }} animate-pulse"></div>
                                <span class="text-sm font-medium text-white">{{ ucfirst($ad->current_availability) }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Badges -->
                    <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
                        @if($ad->top_ad)
                            <span class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold rounded-full bg-orange-500/90 text-white backdrop-blur-sm">
                                <i class="ri-trophy-fill mr-1"></i>
                                Topované
                            </span>
                        @endif
                        @if($ad->featured)
                            <span class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold rounded-full bg-yellow-500/90 text-white backdrop-blur-sm">
                                <i class="ri-star-fill mr-1"></i>
                                Zvýraznený
                            </span>
                        @endif
                        @if($ad->phone_verified)
                            <span class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold rounded-full bg-green-500/90 text-white backdrop-blur-sm">
                                <i class="ri-shield-check-fill mr-1"></i>
                                Overené
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </div>

    <!-- Main content -->
    <div class="bg-white dark:bg-slate-950 py-16 transition-colors duration-300">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left column - Images and Description -->
                <div class="lg:col-span-2">

                    <!-- Gallery -->
                    @if(count($ad->gallery_image_urls) > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="ri-image-line text-pink-600 mr-2"></i>
                                Galéria ({{ count($ad->gallery_image_urls) }})
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach($ad->gallery_image_urls as $index => $photoUrl)
                                    <div class="relative group aspect-square overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800 cursor-pointer" onclick="openLightbox({{ $index }})">
                                        <img src="{{ $photoUrl }}" alt="Galéria {{ $index + 1 }}" class="w-full h-full object-cover transition-all duration-300 group-hover:scale-110">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                                            <i class="ri-zoom-in-line text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                                        </div>
                                        @if($index === 0)
                                            <div class="absolute top-2 left-2 bg-pink-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                                Hlavná
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Zážitky -->
                    @if($ad->practices && is_array($ad->practices) && count($ad->practices) > 0)
                        @php
                            $zazitkyLookup = [];
                            foreach (config('zazitky') as $cat) {
                                foreach ($cat['items'] as $val => $lbl) {
                                    $zazitkyLookup[$val] = ['label' => $lbl, 'chip' => $cat['chip']];
                                }
                            }
                        @endphp
                        <div class="bg-gray-50 dark:bg-slate-900 rounded-2xl p-6 mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                <i class="ri-heart-pulse-line text-pink-600 mr-2"></i>
                                Zážitky
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($ad->practices as $practice)
                                    @php $z = $zazitkyLookup[$practice] ?? null; @endphp
                                    @if($z)
                                        <span class="px-3 py-1 {{ $z['chip'] }} rounded-full text-sm font-medium">{{ $z['label'] }}</span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">{{ $practice }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Contact methods -->
                    @if($ad->contact_methods && is_array($ad->contact_methods) && count($ad->contact_methods) > 0)
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-6 mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                <i class="ri-message-3-line text-blue-600 mr-2"></i>
                                Spôsoby kontaktu
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($ad->contact_methods as $method)
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full text-sm font-medium">{{ $method }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    @if($ad->description && trim($ad->description) !== '')
                        <div class="bg-gray-50 dark:bg-slate-900 rounded-2xl p-6 mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Popis profilu</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{!! nl2br(e($ad->description)) !!}</p>
                        </div>
                    @endif

                    <!-- Video -->
                    @if($ad->video)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Video</h3>
                            <div class="rounded-2xl overflow-hidden">
                                <video controls class="w-full h-64 object-cover">
                                    <source src="{{ asset('storage/' . $ad->video) }}" type="video/mp4">
                                    Váš prehliadač nepodporuje video element.
                                </video>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right column - Details -->
                <div class="space-y-6">
                    <!-- Basic info -->
                    <div class="bg-white dark:bg-slate-900 border border-pink-200 dark:border-pink-700 rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="ri-information-line text-pink-600 mr-2"></i>
                            Základné údaje
                        </h3>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Vek:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ad->age }} rokov</span>
                            </div>
                            @if($ad->street && trim($ad->street) !== '')
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Ulica:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ad->street }}</span>
                                </div>
                            @endif
                            @if($ad->nationality && trim($ad->nationality) !== '' && $ad->nationality !== '-')
                                @php
                                    $nationalityMap = [
                                        'ceska' => 'Česká',
                                        'slovenska' => 'Slovenská',
                                        'madarská' => 'Maďarská',
                                        'rumunska' => 'Rumunská',
                                        'ukrajinska' => 'Ukrajinská',
                                        'ruska' => 'Ruská',
                                        'polska' => 'Poľská',
                                        'nemecka' => 'Nemecká',
                                        'francuzska' => 'Francúzska',
                                        'spanielska' => 'Španielska',
                                        'talianská' => 'Talianska',
                                        'ina' => 'Iná'
                                    ];
                                    $nationality = $nationalityMap[strtolower($ad->nationality)] ?? ucfirst(str_replace('-', ' ', $ad->nationality));
                                @endphp
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Národnosť:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $nationality }}</span>
                                </div>
                            @endif
                            @if($ad->orientation && trim($ad->orientation) !== '' && $ad->orientation !== '-')
                                @php
                                    $orientationMap = [
                                        'heterosexualna' => 'Heterosexuálna',
                                        'homosexualna' => 'Homosexuálna',
                                        'bisexualna' => 'Bisexuálna',
                                        'pansexualna' => 'Pansexuálna'
                                    ];
                                    $orientation = $orientationMap[strtolower($ad->orientation)] ?? ucfirst(str_replace('-', ' ', $ad->orientation));
                                @endphp
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Orientácia:</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $orientation }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Physical details -->
                    @if($ad->height || $ad->weight || $ad->breast_size || $ad->eye_color || $ad->hair_color || $ad->tattoos || $ad->piercing)
                        <div class="bg-white dark:bg-slate-900 border border-pink-200 dark:border-pink-700 rounded-2xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="ri-body-scan-line text-pink-600 mr-2"></i>
                                Vzhľad a parametre
                            </h3>
                            <div class="space-y-3">
                                @if($ad->height && $ad->height > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Výška:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ad->height }} cm</span>
                                    </div>
                                @endif
                                @if($ad->weight && $ad->weight > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Váha:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ad->weight }} kg</span>
                                    </div>
                                @endif
                                @if($ad->breast_size && trim($ad->breast_size) !== '')
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Veľkosť pŕs:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ad->breast_size }}</span>
                                    </div>
                                @endif
                                @if($ad->eye_color && trim($ad->eye_color) !== '' && $ad->eye_color !== '-')
                                    @php
                                        $eyeColorMap = [
                                            'modre' => 'Modré',
                                            'cierne' => 'Čierne', 
                                            'hnede' => 'Hnedé',
                                            'zelene' => 'Zelené',
                                            'sede' => 'Šedé',
                                            // Anglické hodnoty z WordPressu
                                            'blue' => 'Modré',
                                            'black' => 'Čierne',
                                            'brown' => 'Hnedé',
                                            'green' => 'Zelené',
                                            'gray' => 'Šedé',
                                            'grey' => 'Šedé'
                                        ];
                                        $eyeColor = $eyeColorMap[strtolower($ad->eye_color)] ?? ucfirst(str_replace('-', ' ', $ad->eye_color));
                                    @endphp
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Farba očí:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $eyeColor }}</span>
                                    </div>
                                @endif
                                @if($ad->hair_color && trim($ad->hair_color) !== '' && $ad->hair_color !== '-')
                                    @php
                                        $hairColorMap = [
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
                                            'cierne' => 'Čierne',
                                            'hnede' => 'Hnedé', 
                                            'blonde' => 'Blond',
                                            'cervene' => 'Červené',
                                            'sede' => 'Šedé',
                                            'farebne' => 'Farebné',
                                            'ine' => 'Iné',
                                            // Anglické hodnoty z WordPressu
                                            'black' => 'Čierne',
                                            'brown' => 'Hnedé',
                                            'blond' => 'Blond',
                                            'red' => 'Červené',
                                            'gray' => 'Šedé',
                                            'grey' => 'Šedé',
                                            'other' => 'Iné'
                                        ];
                                        $hairColor = $hairColorMap[strtolower($ad->hair_color)] ?? ucfirst(str_replace('-', ' ', $ad->hair_color));
                                    @endphp
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Farba vlasov:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $hairColor }}</span>
                                    </div>
                                @endif
                                @if($ad->tattoos && trim($ad->tattoos) !== '' && $ad->tattoos !== '-')
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
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Tetovanie:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $tattoos }}</span>
                                    </div>
                                @endif
                                @if($ad->piercing && trim($ad->piercing) !== '' && $ad->piercing !== '-')
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
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Piercing:</span>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $piercing }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Contact -->
                    <div class="bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-700 rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            <i class="ri-phone-line text-pink-600 mr-2"></i>
                            Kontakt
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="ri-phone-fill text-gray-400 dark:text-gray-500 mr-2"></i>
                                    Telefón:
                                </span>
                                <a href="tel:{{ $ad->phone }}" onclick="trackPhoneClick()" class="font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2 hover:text-pink-600 transition-colors">
                                    <i class="ri-phone-fill text-green-600"></i>
                                    {{ $ad->phone }}
                                </a>
                            </div>

                            @if(($ad->hours && is_array($ad->hours) && count($ad->hours) > 0) || $ad->availability)
                                <div class="border-t border-pink-200 dark:border-pink-700 pt-4">
                                    <span class="text-gray-600 dark:text-gray-400 block mb-3 flex items-center">
                                        <i class="ri-time-line text-gray-400 dark:text-gray-500 mr-2"></i>
                                        @if($ad->hours && is_array($ad->hours) && count($ad->hours) > 0)
                                            Kedy mám čas:
                                        @else
                                            Dostupnosť:
                                        @endif
                                    </span>
                                    <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2 bg-white dark:bg-slate-800 rounded-lg p-3">
                                                                @if($ad->hours && is_array($ad->hours) && count($ad->hours) > 0)
                            @php
                                $currentDayEn = strtolower(now()->format('l')); // monday, tuesday, etc.
                                $dayTranslations = [
                                    'monday' => 'Pondelok',
                                    'tuesday' => 'Utorok', 
                                    'wednesday' => 'Streda',
                                    'thursday' => 'Štvrtok',
                                    'friday' => 'Piatok',
                                    'saturday' => 'Sobota',
                                    'sunday' => 'Nedeľa'
                                ];
                                // Správne poradie dní od pondelka
                                $dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                            @endphp
                            @foreach($dayOrder as $day)
                                @if(isset($ad->hours[$day]))
                                    @php
                                        $hours = $ad->hours[$day];
                                        $isToday = strtolower($day) === $currentDayEn;
                                        $dayInSlovak = $dayTranslations[strtolower($day)] ?? ucfirst($day);
                                    @endphp
                                                <div class="flex justify-between items-center {{ $isToday ? 'bg-pink-50 dark:bg-pink-900/30 border border-pink-200 dark:border-pink-700 rounded-lg px-3 py-2 font-semibold text-pink-800 dark:text-pink-200' : '' }}">
                                                    <span class="{{ $isToday ? 'font-bold' : 'font-medium' }}">
                                                        {{ $dayInSlovak }}:
                                                        @if($isToday)
                                                            <span class="text-xs bg-pink-500 text-white px-2 py-1 rounded-full ml-2">DNES</span>
                                                        @endif
                                                    </span>
                                                    <span class="{{ $isToday ? 'font-bold' : 'text-gray-600 dark:text-gray-400' }}">
                                                        @if(is_array($hours))
                                                            {{ $hours['from'] ?? '' }} - {{ $hours['to'] ?? '' }}
                                                        @else
                                                            {{ $hours }}
                                                        @endif
                                                    </span>
                                                </div>
                                                @endif
                                            @endforeach
                                        @elseif($ad->availability)
                                            <div class="text-center py-2">
                                                <span class="px-4 py-2 bg-pink-100 dark:bg-pink-800 text-pink-800 dark:text-pink-200 rounded-full font-medium">
                                                    {{ ucfirst($ad->availability) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Reminder text -->
                            <div class="border-t border-pink-200 dark:border-pink-700 pt-4">
                                <div class="flex items-center gap-3 p-3 bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-700 rounded-lg">
                                    <i class="ri-information-line text-pink-600 flex-shrink-0"></i>
                                    <p class="text-sm text-pink-800 dark:text-pink-200">
                                        Spomeňte, že voláte z <strong>erotikon.sk</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Share -->
                    <div class="bg-gradient-to-r from-pink-50 to-purple-50 dark:from-pink-900/20 dark:to-purple-900/20 rounded-2xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="ri-share-line text-pink-600 mr-2"></i>
                            Zdieľať
                        </h3>
                        <div class="flex gap-3">
                            <button onclick="shareOnFacebook()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="ri-facebook-fill"></i>
                                <span class="hidden sm:inline">Facebook</span>
                            </button>
                            <button onclick="shareOnTwitter()" class="flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors">
                                <i class="ri-twitter-fill"></i>
                                <span class="hidden sm:inline">Twitter</span>
                            </button>
                        </div>
                    </div>

                    <!-- Report -->
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-2xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                            <i class="ri-flag-line text-red-600 mr-2"></i>
                            Nahlásiť problém
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Ak tento inzerát porušuje pravidlá alebo obsahuje nevhodný obsah, môžete ho nahlásiť.
                        </p>
                        <button onclick="reportAd()" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                            <i class="ri-flag-fill"></i>
                            Nahlásiť inzerát
                        </button>
                    </div>

                    <!-- Info -->
                    <div class="bg-gray-50 dark:bg-slate-900 rounded-2xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="ri-information-line text-gray-600 dark:text-gray-400 mr-2"></i>
                            Informácie
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="ri-calendar-line text-gray-400 dark:text-gray-500 mr-2"></i>
                                    Pridané:
                                </span>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ad->created_at->format('d.m.Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="ri-eye-line text-gray-400 dark:text-gray-500 mr-2"></i>
                                    Zobrazenia:
                                </span>
                                <span class="font-medium text-blue-600">{{ number_format($ad->views ?? 0) }}×</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                    <i class="ri-hashtag text-gray-400 dark:text-gray-500 mr-2"></i>
                                    ID inzerátu:
                                </span>
                                <span class="font-medium font-mono text-pink-600">AD-{{ $ad->id }}</span>
                            </div>
                            @if($ad->phone_verified)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                        <i class="ri-shield-check-line text-gray-400 dark:text-gray-500 mr-2"></i>
                                        Overenie:
                                    </span>
                                    <span class="font-medium text-green-600 flex items-center">
                                        <i class="ri-check-line mr-1"></i>
                                        Overený telefón
                                    </span>
                                </div>
                            @endif
                            @if($ad->featured)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                        <i class="ri-star-line text-gray-400 dark:text-gray-500 mr-2"></i>
                                        Status:
                                    </span>
                                    <span class="font-medium text-yellow-600 flex items-center">
                                        <i class="ri-star-fill mr-1"></i>
                                        Zvýraznený inzerát
                                    </span>
                                </div>
                            @endif
                            @if($ad->top_ad)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400 flex items-center">
                                        <i class="ri-trophy-line text-gray-400 dark:text-gray-500 mr-2"></i>
                                        Pozícia:
                                    </span>
                                    <span class="font-medium text-orange-600 flex items-center">
                                        <i class="ri-trophy-fill mr-1"></i>
                                        Topovaný inzerát
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Lightbox -->
    <div id="lightbox" class="fixed inset-0 z-[100] hidden bg-black bg-opacity-95">
        <!-- Background overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-95 transition-opacity duration-300" onclick="closeLightbox()"></div>
        
        <!-- Main lightbox container -->
        <div class="relative w-full h-full flex flex-col">
            <!-- Header -->
            <div class="absolute top-0 left-0 right-0 z-10 bg-gradient-to-b from-black/50 to-transparent p-4">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center gap-4">
                        <h3 class="text-lg font-semibold">{{ $ad->nickname ?: 'Galéria' }}</h3>
                        <span id="lightbox-counter" class="text-sm text-gray-300 bg-black/30 px-3 py-1 rounded-full"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="downloadImage()" class="p-2 hover:bg-white/20 rounded-full transition-colors" title="Stiahnuť">
                            <i class="ri-download-line text-xl"></i>
                        </button>
                        <button onclick="shareImage()" class="p-2 hover:bg-white/20 rounded-full transition-colors" title="Zdieľať">
                            <i class="ri-share-line text-xl"></i>
                        </button>
                        <button onclick="closeLightbox()" class="p-2 hover:bg-white/20 rounded-full transition-colors" title="Zavrieť">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main image area -->
            <div class="absolute inset-0 flex items-center justify-center p-4" style="top: 80px; bottom: 120px;">
                <!-- Previous button -->
                <button id="prev-btn" onclick="previousImage()" class="absolute left-4 top-1/2 transform -translate-y-1/2 p-3 bg-black/50 hover:bg-black/70 text-white rounded-full transition-all duration-200 hover:scale-110 z-10">
                    <i class="ri-arrow-left-line text-2xl"></i>
                </button>

                <!-- Image container -->
                <div class="relative w-full h-full flex items-center justify-center overflow-hidden">
                    <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full w-auto h-auto object-contain rounded-lg shadow-2xl transition-all duration-300">
                    
                    <!-- Loading spinner -->
                    <div id="lightbox-loading" class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-lg hidden">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white"></div>
                    </div>
                </div>

                <!-- Next button -->
                <button id="next-btn" onclick="nextImage()" class="absolute right-4 top-1/2 transform -translate-y-1/2 p-3 bg-black/50 hover:bg-black/70 text-white rounded-full transition-all duration-200 hover:scale-110 z-10">
                    <i class="ri-arrow-right-line text-2xl"></i>
                </button>
            </div>

            <!-- Thumbnails -->
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                <div class="flex justify-center">
                    <div id="lightbox-thumbnails" class="flex gap-2 overflow-x-auto max-w-full pb-2 scrollbar-hide">
                        @if(count($ad->gallery_image_urls) > 0)
                            @foreach($ad->gallery_image_urls as $index => $photoUrl)
                                <div class="thumbnail-item flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-pink-400 transition-all duration-200" onclick="goToImage({{ $index }})" data-index="{{ $index }}">
                                    <img src="{{ $photoUrl }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        .thumbnail-item.active {
            border-color: #ec4899 !important;
            transform: scale(1.1);
        }
        
        #lightbox {
            backdrop-filter: blur(10px);
        }
    </style>

    <script>
        // Načítanie stavu obľúbených pri načítaní stránky
        document.addEventListener('DOMContentLoaded', function() {
            const favoriteButton = document.querySelector('.favorite-btn');
            if (favoriteButton) {
                const adId = favoriteButton.dataset.adId;
                    fetch(`/oblubene/${adId}/check`)
                    .then(response => response.json())
                        .then(data => {
                            const heartPath = favoriteButton.querySelector('.heart-path');
                        if (data.is_favorite) {
                                heartPath.classList.add('text-red-500');
                                heartPath.classList.remove('text-gray-400');
                            }
                        })
                    .catch(error => console.error('Error:', error));
            }
        });

        function toggleFavorite(adId, button) {
            fetch(`/oblubene/${adId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                return response.json();
            })
            .then(data => {
                const heartPath = button.querySelector('.heart-path');
                    if (data.is_favorite) {
                        heartPath.classList.add('text-red-500');
                        heartPath.classList.remove('text-gray-400');
                    } else {
                        heartPath.classList.add('text-gray-400');
                        heartPath.classList.remove('text-red-500');
                }
                // Aktualizácia počtu obľúbených v headeri
                if (window.updateFavoritesCount) {
                    window.updateFavoritesCount();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Chyba pri pridávaní do obľúbených. Skúste to znovu.');
            });
        }

        function trackPhoneClick() {
            // Increment clicks pre telefónne číslo
            fetch(`/inzerat/{{ $ad->id }}/klik`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(error => {
                console.log('Click tracking error:', error);
            });
        }

        // Lightbox functionality
        let currentImageIndex = 0;
        const galleryImages = @json($ad->gallery_image_urls);
        
        function openLightbox(index = 0) {
            // Track gallery image click
            fetch(`/inzerat/{{ $ad->id }}/klik`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(error => {
                console.log('Gallery click tracking error:', error);
            });

            currentImageIndex = index;
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            updateLightboxImage();
            updateThumbnails();
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function updateLightboxImage() {
            if (galleryImages.length === 0) return;
            
            const image = document.getElementById('lightbox-image');
            const loading = document.getElementById('lightbox-loading');
            const counter = document.getElementById('lightbox-counter');
            
            // Show loading
            loading.classList.remove('hidden');
            
            // Update counter
            counter.textContent = `${currentImageIndex + 1} / ${galleryImages.length}`;
            
            // Load new image
            const newSrc = galleryImages[currentImageIndex];
            
            const img = new Image();
            img.onload = function() {
                image.src = newSrc;
                loading.classList.add('hidden');
            };
            img.onerror = function() {
                loading.classList.add('hidden');
            };
            img.src = newSrc;
            
            // Update navigation buttons
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            
            if (galleryImages.length <= 1) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'block';
                nextBtn.style.display = 'block';
                prevBtn.style.opacity = currentImageIndex === 0 ? '0.5' : '1';
                nextBtn.style.opacity = currentImageIndex === galleryImages.length - 1 ? '0.5' : '1';
            }
        }

        function updateThumbnails() {
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            thumbnails.forEach((thumb, index) => {
                if (index === currentImageIndex) {
                    thumb.classList.add('active');
                } else {
                    thumb.classList.remove('active');
                }
            });
            
            // Scroll active thumbnail into view
            const activeThumbnail = document.querySelector('.thumbnail-item.active');
            if (activeThumbnail) {
                activeThumbnail.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        }

        function previousImage() {
            if (currentImageIndex > 0) {
                currentImageIndex--;
                updateLightboxImage();
                updateThumbnails();
            }
        }

        function nextImage() {
            if (currentImageIndex < galleryImages.length - 1) {
                currentImageIndex++;
                updateLightboxImage();
                updateThumbnails();
            }
        }

        function goToImage(index) {
            currentImageIndex = index;
            updateLightboxImage();
            updateThumbnails();
        }

        function downloadImage() {
            if (galleryImages.length === 0) return;
            
            const currentImage = galleryImages[currentImageIndex];
            const link = document.createElement('a');
            link.href = currentImage;
            link.download = `galeria-${currentImageIndex + 1}.jpg`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function shareImage() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $ad->nickname ?: "Galéria" }}',
                    text: 'Pozri si túto galériu',
                    url: window.location.href
                });
            } else {
                copyLink();
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(event) {
            const lightbox = document.getElementById('lightbox');
            if (!lightbox.classList.contains('hidden')) {
                switch(event.key) {
                    case 'Escape':
                        closeLightbox();
                        break;
                    case 'ArrowLeft':
                        previousImage();
                        break;
                    case 'ArrowRight':
                        nextImage();
                        break;
                }
            }
        });

        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const lightbox = document.getElementById('lightbox');
            if (lightbox) {
                lightbox.addEventListener('touchstart', function(event) {
                    touchStartX = event.changedTouches[0].screenX;
                });

                lightbox.addEventListener('touchend', function(event) {
                    touchEndX = event.changedTouches[0].screenX;
                    handleSwipe();
                });
            }
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    // Swipe left - next image
                    nextImage();
                } else {
                    // Swipe right - previous image
                    previousImage();
                }
            }
        }

        // Share functions
        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent('{{ $ad->nickname ?: "Inzerát" }} - {{ $ad->city_label }}');
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="ri-check-line"></i><span class="hidden sm:inline">Skopírované!</span>';
                button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                button.classList.add('bg-green-600', 'hover:bg-green-700');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-gray-600', 'hover:bg-gray-700');
                }, 2000);
            }).catch(function() {
                alert('Nepodarilo sa skopírovať odkaz');
            });
        }

        function reportAd() {
            const reasons = [
                'Nevhodný obsah',
                'Falošný inzerát',
                'Spam',
                'Porušenie pravidiel',
                'Iný dôvod'
            ];
            
            let reasonsHtml = reasons.map((reason, index) => 
                `<label class="flex items-center gap-2 p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded cursor-pointer">
                    <input type="radio" name="report_reason" value="${reason}" id="reason_${index}" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <span class="text-gray-900 dark:text-gray-100 cursor-pointer flex-1">${reason}</span>
                </label>`
            ).join('');

            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 overflow-y-auto flex items-center justify-center';
            modal.id = 'report-modal'; // 🔥 PRIDANÉ ID PRE JEDNOZNAČNÚ IDENTIFIKÁCIU
            modal.innerHTML = `
                <div class="fixed inset-0 bg-black bg-opacity-50" onclick="this.parentElement.remove()"></div>
                <div class="relative bg-white dark:bg-slate-900 rounded-xl p-6 m-4 max-w-md w-full">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Nahlásiť inzerát</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>
                    <div class="space-y-2 mb-4">
                        ${reasonsHtml}
                    </div>
                    <input type="email" name="email" placeholder="Váš email (voliteľné)" class="w-full p-3 border border-gray-300 dark:border-gray-600 dark:bg-slate-800 dark:text-gray-100 rounded-lg mb-3">
                    <textarea placeholder="Dodatočné informácie (voliteľné)" class="w-full p-3 border border-gray-300 dark:border-gray-600 dark:bg-slate-800 dark:text-gray-100 rounded-lg resize-none" rows="3"></textarea>
                    <div class="flex gap-3 mt-4">
                        <button onclick="this.closest('.fixed').remove()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                            Zrušiť
                        </button>
                        <button onclick="submitReport()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Nahlásiť
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        function submitReport() {
            console.log('=== SUBMIT REPORT STARTED ===');
            console.log('Current URL:', window.location.href);
            console.log('Target URL:', `/inzerat/{{ $ad->id }}/nahlas`);
            
            const modal = document.getElementById('report-modal');
            if (!modal) {
                console.error('❌ Modal not found!');
                alert('Chyba: Modal sa nenašiel');
                return;
            }
            console.log('✅ Modal found:', modal);

            // Debug: Check all form elements
            const allInputs = modal.querySelectorAll('input, textarea');
            console.log('📝 All form inputs:', allInputs.length);
            allInputs.forEach((input, i) => {
                console.log(`  Input ${i}:`, {
                    type: input.type,
                    name: input.name || 'no-name',
                    value: input.value || 'empty',
                    placeholder: input.placeholder || 'no-placeholder'
                });
            });

            console.log('🔍 Looking for reason radio buttons...');
            
            // 🎯 HĽADÁME CHECKED RADIO - VIACERO SPÔSOBOV
            const allRadios = modal.querySelectorAll('input[name="report_reason"]');
            console.log('📻 All radio buttons found:', allRadios.length);
            allRadios.forEach((radio, i) => {
                console.log(`📻 Radio ${i}: value="${radio.value}" checked=${radio.checked}`);
            });
            
            // 🔄 FALLBACK: Ak querySelector nefunguje, skúsime manuálne
            let reasonElement = modal.querySelector('input[name="report_reason"]:checked');
            let reason = reasonElement ? reasonElement.value : null;
            
            // 🔄 FALLBACK: Ak querySelector nefunguje, skúsime manuálne
            if (!reason) {
                console.log('🔄 First method failed, trying fallback...');
                for (let i = 0; i < allRadios.length; i++) {
                    if (allRadios[i].checked) {
                        reasonElement = allRadios[i];
                        reason = allRadios[i].value;
                        console.log('🎯 Found via fallback:', reason);
                        break;
                    }
                }
            }
            
            console.log('🎯 Final reason value:', reason);
            console.log('🎯 ReasonElement:', reasonElement);
            
            const detailsElement = modal.querySelector('textarea');
            const details = detailsElement ? detailsElement.value : '';
            console.log('Details:', details);
            
            const emailElement = modal.querySelector('input[name="email"]');
            const email = emailElement ? emailElement.value : '';
            console.log('Email:', email);

            if (!reason || reason.trim() === '') {
                console.log('❌ No reason selected, showing error');
                console.log('❌ Debug info: reasonElement=', reasonElement, 'reason=', reason);
                console.log('❌ All radios again:');
                allRadios.forEach((radio, i) => {
                    console.log(`  📻 Radio ${i}: "${radio.value}" checked=${radio.checked} id=${radio.id}`);
                });
                showErrorPopup('Prosím vyberte dôvod nahlásenia.');
                return;
            }
            
            console.log('All data collected, proceeding with request');

            // Disable submit button
            const submitBtn = modal.querySelector('button[onclick="submitReport()"]');
            if (!submitBtn) {
                console.error('Submit button not found');
                return;
            }
            
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Odosielam...';
            submitBtn.disabled = true;

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            console.log('🔐 CSRF token element:', csrfToken);
            console.log('🔐 CSRF token content:', csrfToken ? csrfToken.getAttribute('content') : 'NOT FOUND');
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                showErrorPopup('Chyba: CSRF token nenájdený. Obnovte stránku a skúste znovu.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }

            // Send report
            const requestUrl = `/inzerat/{{ $ad->id }}/nahlas`;
            const requestData = { reason, details, email };
            
            console.log('📡 FINAL REQUEST DETAILS:');
            console.log('  URL:', requestUrl);
            console.log('  Method: POST');
            console.log('  Content-Type: application/json');
            console.log('  CSRF Token:', csrfToken.getAttribute('content').substring(0, 10) + '...');
            console.log('  Request Data:', requestData);
            console.log('Sending request to:', requestUrl);
            console.log('Request data:', requestData);
            
            fetch(`/inzerat/{{ $ad->id }}/nahlas`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    reason: reason,
                    details: details,
                    email: email
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Show success popup
                    modal.innerHTML = `
                        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="this.parentElement.remove()"></div>
                        <div class="relative bg-white dark:bg-slate-900 rounded-xl p-6 m-4 max-w-md w-full">
                            <div class="text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                    <i class="ri-check-line text-green-600 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Úspešne nahlásené!</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">${data.message}</p>
                                <button onclick="this.closest('.fixed').remove()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    OK
                                </button>
                            </div>
                        </div>
                    `;
                    
                    // Auto close after 3 seconds
                    setTimeout(() => {
                        if (modal && modal.parentElement) {
                            modal.remove();
                        }
                    }, 3000);
                } else {
                    // Show error popup
                    modal.innerHTML = `
                        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="this.parentElement.remove()"></div>
                        <div class="relative bg-white dark:bg-slate-900 rounded-xl p-6 m-4 max-w-md w-full">
                            <div class="text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                    <i class="ri-close-line text-red-600 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Chyba!</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">${data.message || 'Nastala chyba pri odosielaní reportu'}</p>
                                <button onclick="this.closest('.fixed').remove()" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    OK
                                </button>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error popup
                modal.innerHTML = `
                    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="this.parentElement.remove()"></div>
                    <div class="relative bg-white dark:bg-slate-900 rounded-xl p-6 m-4 max-w-md w-full">
                        <div class="text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <i class="ri-close-line text-red-600 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Chyba!</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Nastala chyba pri odosielaní reportu</p>
                            <button onclick="this.closest('.fixed').remove()" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                OK
                            </button>
                        </div>
                    </div>
                `;
            });
        }

        function showErrorPopup(message) {
            const errorModal = document.createElement('div');
            errorModal.className = 'fixed inset-0 z-50 overflow-y-auto flex items-center justify-center';
            errorModal.innerHTML = `
                <div class="fixed inset-0 bg-black bg-opacity-50" onclick="this.parentElement.remove()"></div>
                <div class="relative bg-white dark:bg-slate-900 rounded-xl p-6 m-4 max-w-md w-full">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <i class="ri-close-line text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Chyba!</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">${message}</p>
                        <button onclick="this.closest('.fixed').remove()" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            OK
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(errorModal);
        }
    </script>
@endsection 