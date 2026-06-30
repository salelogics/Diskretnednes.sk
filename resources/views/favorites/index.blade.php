@extends('layouts.main')

@section('content')
    <div class="bg-gray-900">
        <div class="relative isolate overflow-hidden pt-14">
            <img src="{{ asset('images/uploads/hero-bg.jpg') }}" alt="" class="absolute inset-0 -z-10 size-full object-cover opacity-90">
            <div class="absolute inset-0 -z-10 bg-black/60"></div>
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl py-12 sm:py-14 lg:py-16">
                    <div class="text-center">
                        <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">Obľúbené inzeráty</h1>
                        <p class="mt-6 text-lg leading-8 text-gray-300">Vaše obľúbené inzeráty na jednom mieste.</p>
                    </div>
                </div>
            </div>
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </div>
    </div>

    <!-- Obľúbené inzeráty section -->
    <div class="bg-white dark:bg-slate-900 py-24 sm:py-32">
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
                                    <div class="absolute inset-0 -z-10 size-full bg-gradient-to-br from-pink-400 to-pink-600"></div>
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
                                    <button onclick="event.preventDefault(); toggleFavorite({{ $ad->id }}, this)" class="favorite-btn p-2 rounded-full bg-white/90 hover:bg-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" viewBox="0 0 20 20">
                                            <path class="heart-path text-red-500" fill="currentColor" fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
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
                                        <span class="text-pink-300 font-medium">{{ $ad->offer_type_label }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-400">
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ number_format($ad->views) }}
                                        </span>
                                        @if(count($ad->gallery_photos ?? []) > 0)
                                            <span class="flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ count($ad->gallery_photos) + 1 }}
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
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Žiadne obľúbené inzeráty</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Zatiaľ ste si nepridali žiadne inzeráty do obľúbených.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700">
                            Prehliadať inzeráty
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
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
                        // Ak sme na stránke obľúbených, odstránime kartu
                        if (window.location.pathname === '/oblubene') {
                            const card = button.closest('a');
                            if (card) {
                                card.remove();
                                // Ak už nie sú žiadne karty, zobrazíme prázdny stav
                                const grid = document.querySelector('.grid');
                                if (grid && grid.children.length === 0) {
                                    location.reload();
                                }
                            }
                        }
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