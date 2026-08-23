@extends('layouts.main')

@section('content')
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">

    <!-- Age verification modal -->
    <div id="age-verification-modal" class="fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <div class="relative flex flex-col bg-white shadow-lg rounded-xl w-full max-w-md m-3 opacity-0 transform -translate-y-4 transition-all duration-300" style="min-height: 340px;">
            <div class="absolute top-4 end-4">
                <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none" onclick="closeModal()">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 p-4 sm:p-8 text-center flex flex-col items-center">
                <img src="{{ asset('images/uploads/diskretne-dnes-logo-black.png') }}" alt="Diskrétne Dnes" class="w-36 mb-6">
                
                <div class="space-y-4 text-left max-w-sm mx-auto">
                    <p class="text-gray-800 font-medium">
                        Stránky sú určené výhradne pre osoby staršie ako 18 rokov.
                    </p>
                    
                    <p class="text-gray-600 text-sm">
                        Vstupom na webstránku a kliknutím na tlačidlo Súhlasím potvrdzujem, že mám viac ako 18 rokov a že tieto webové stránky nikdy nebudem ukazovať deťom a maloletým.
                    </p>

                    <div class="pt-2">
                        <h4 class="text-gray-800 font-medium mb-1">Cookies</h4>
                        <p class="text-gray-600 text-sm">
                            Kliknutím na tlačidlo Súhlasím, povoliť cookies sa uložia technické a analytické súbory cookie, súbory cookie používame na analýzu údajov o našich návštevníkoch, meranie funkčnosti a na zlepšenie našich webových stránok, aby sme vám poskytli skvelý zážitok z webu.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center" style="font-family: 'Roboto', sans-serif;">
                <button 
                    type="button" 
                    class="w-full py-5 px-4 inline-flex justify-center items-center text-base font-normal rounded-bl-xl bg-gray-50 text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none border-t border-r border-gray-200" 
                    onclick="window.location.href='https://www.google.com'"
                >
                    Nesúhlasím
                </button>
                <button 
                    type="button" 
                    class="w-full py-5 px-4 inline-flex justify-center items-center text-base font-normal rounded-br-xl bg-pink-500 text-white hover:bg-pink-600 disabled:opacity-50 disabled:pointer-events-none border-t border-gray-200" 
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
            // Set cookie for 30 days
            let date = new Date();
            date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
            document.cookie = 'ageVerified=true; expires=' + date.toUTCString() + '; path=/';
            
            // Save to localStorage
            localStorage.setItem('ageVerified', 'true');
            
            // Close modal
            closeModal();
        }

        // Show modal on page load if not verified
        document.addEventListener('DOMContentLoaded', function() {
            if (!localStorage.getItem('ageVerified')) {
                showModal();
            }
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="bg-white">
        <div class="relative isolate overflow-hidden pt-14 min-h-[22rem] sm:min-h-[28rem] lg:min-h-[34rem]">
            {{-- Táto sekcia nemá vlastnú výšku, len padding okolo krátkeho textu -
                 na širokej obrazovke to s object-cover orezávalo cez 3/4 fotky
                 (kontajner bol príliš nízky vzhľadom na jeho šírku). min-h nižšie
                 dáva fotke viac priestoru, aby bolo vidieť podstatne viac z nej. --}}
            <img src="{{ asset('images/uploads/tantra-hero.jpg') }}" alt="" class="absolute inset-0 -z-10 size-full object-cover opacity-90">
            <div class="absolute inset-0 -z-10 bg-black/60"></div>
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl py-12 sm:py-14 lg:py-16">
                    <div class="text-center">
                        <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">Tantra masáže</h1>
                        <p class="mt-6 text-lg leading-8 text-gray-300">Relax, dotyk a uvoľnenie v príjemnej atmosfére.</p>
                    </div>
                </div>
            </div>
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </div>
    </div>

    <!-- Intro section -->
    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-4xl">
                <h2 class="text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl mb-8">Relax, dotyk a uvoľnenie v príjemnej atmosfére.</h2>

                <p class="text-lg leading-8 text-gray-600">
                    Kategória masáží zahŕňa relaxačné a zmyselné procedúry pre dospelých klientov. Rozsah služieb je vždy vecou individuálnej dohody a prebieha v súkromí a diskrétnom prostredí.
                </p>
            </div>
        </div>
    </div>

    <!-- Inzeráty section -->
    <div class="bg-white py-8 sm:py-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            @if($ads->count() > 0)
                <!-- Inzeráty grid -->
                <div class="mx-auto grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($ads as $ad)
                        <a href="{{ route('ad.show', $ad->id) }}" class="group">
                            <article class="relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 min-h-[450px] sm:min-h-[400px] lg:min-h-[470px] transition-transform group-hover:scale-105">
                                @if($ad->verification_image_url)
                                    <img src="{{ $ad->verification_image_url }}" alt="{{ $ad->nickname }}" class="absolute inset-0 -z-10 size-full object-cover">
                                @else
                                    <div class="absolute inset-0 -z-10 size-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40"></div>
                                <div class="absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10"></div>

                                <!-- Badge Topované v ľavom rohu -->
                                @if($ad->top_ad)
                                    <div class="absolute top-4 left-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-500 text-white">Topované</span>
                                    </div>
                                @endif

                                <!-- Obľúbené srdce v pravom rohu v bielom krúžku -->
                                <div class="absolute top-4 right-4">
                                    <button onclick="event.preventDefault(); toggleFavorite({{ $ad->id }}, this)" class="favorite-btn p-2 rounded-full bg-white/90 hover:bg-white transition-all shadow-sm" data-ad-id="{{ $ad->id }}">
                                        <svg class="w-4 h-4" viewBox="0 0 20 20">
                                            <path class="heart-path text-gray-400" fill="currentColor" fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xl font-semibold text-white">{{ $ad->nickname ?: 'Anonymný' }}</h3>
                                        @if($ad->phone_verified)
                                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </div>
                                    @if($ad->current_availability)
                                        <div class="flex items-center gap-1 mb-1">
                                            <div class="w-2 h-2 rounded-full {{ $ad->availability_color }}"></div>
                                            <span class="text-xs font-medium text-white">{{ $ad->current_availability }}</span>
                                        </div>
                                    @endif
                                    <div class="flex flex-col text-sm/6 text-gray-300">
                                        <span>{{ $ad->age }} rokov</span>
                                        <span>{{ $ad->city_label }}@if($ad->street), {{ $ad->street }}@endif</span>
                                        <span class="text-purple-300 font-medium">Tantra masáž</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-400">
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ number_format($ad->views) }}
                                        </span>
                                        @if(count($ad->gallery_image_urls) > 0)
                                            <span class="flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ count($ad->gallery_image_urls) + 1 }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Prázdny stav -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20.4a7.962 7.962 0 01-5-1.691c-2.598-2.11-3.292-5.731-1.691-8.329 2.11-2.598 5.731-3.292 8.329-1.691 1.556 1.263 2.524 3.187 2.524 5.291z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Žiadne tantra masáže</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Momentálne nie sú k dispozícii žiadne aktívne tantra masáže.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Načítanie stavu obľúbených pri načítaní stránky
        document.addEventListener('DOMContentLoaded', function() {
            const favoriteButtons = document.querySelectorAll('.favorite-btn');
            favoriteButtons.forEach(button => {
                const adId = button.dataset.adId;
                if (adId) {
                    fetch(`/oblubene/${adId}/check`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            const heartPath = button.querySelector('.heart-path');
                            if (heartPath && data.is_favorite) {
                                heartPath.classList.add('text-red-500');
                                heartPath.classList.remove('text-gray-400');
                            }
                        })
                        .catch(error => {
                            console.error('Error loading favorites:', error);
                        });
                }
            });
        });

        function toggleFavorite(adId, button) {
            // Kontrola CSRF tokenu
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Chyba: CSRF token nenájdený. Obnovte stránku a skúste znovu.');
                return;
            }

            // Zabránenie viacnásobného kliknutia
            if (button.disabled) {
                return;
            }
            button.disabled = true;

            fetch(`/oblubene/${adId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
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
                if (heartPath) {
                    if (data.is_favorite) {
                        heartPath.classList.add('text-red-500');
                        heartPath.classList.remove('text-gray-400');
                    } else {
                        heartPath.classList.add('text-gray-400');
                        heartPath.classList.remove('text-red-500');
                    }
                }
                // Aktualizácia počtu obľúbených v headeri
                if (window.updateFavoritesCount) {
                    window.updateFavoritesCount();
                }
            })
            .catch(error => {
                console.error('Error toggling favorite:', error);
                alert('Chyba pri pridávaní do obľúbených. Skúste to znovu.');
            })
            .finally(() => {
                // Obnova funkčnosti tlačidla
                button.disabled = false;
            });
        }
    </script>
@endsection
