@foreach($ads as $ad)
    <a href="{{ route('ad.show', $ad->id) }}" class="group">
        <article class="relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 min-h-[450px] sm:min-h-[400px] lg:min-h-[470px] transition-transform group-hover:scale-105">
            @if($ad->verification_image_url)
                <img src="{{ $ad->verification_image_url }}" alt="{{ $ad->nickname }}" class="absolute inset-0 -z-10 size-full object-cover" loading="lazy" decoding="async">
            @elseif(count($ad->gallery_image_urls) > 0)
                <img src="{{ $ad->gallery_image_urls[0] }}" alt="{{ $ad->nickname }}" class="absolute inset-0 -z-10 size-full object-cover" loading="lazy" decoding="async">
            @else
                <div class="absolute inset-0 -z-10 size-full bg-gradient-to-br from-pink-400 to-pink-600"></div>
            @endif
            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40"></div>
            <div class="absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10"></div>

            <!-- Badge Topované v ľavom rohu -->
            @if($ad->top_ad)
                <div class="absolute top-4 left-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-500 text-white">{{ __('app.messages.top_ad') }}</span>
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
                <div class="flex flex-col text-sm/6 text-gray-300">
                    <span>{{ $ad->age }} {{ __('app.general.years') }}</span>
                    <span>{{ $ad->city_label }}@if($ad->street), {{ $ad->street }}@endif</span>
                    <span class="text-pink-300 font-medium">{{ $ad->offer_type_label }}</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs text-gray-400">
                    <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ number_format($ad->views) }} {{ __('app.general.views') }}
                    </span>
                    @if(count($ad->gallery_image_urls) > 0)
                        <span class="flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ count($ad->gallery_image_urls) + 1 }} {{ __('app.general.photos') }}
                        </span>
                    @endif
                </div>
            </div>
        </article>
    </a>
@endforeach 