@extends('layouts.main')

@section('content')
<div class="bg-gray-900">
    <div class="relative isolate overflow-hidden pt-14">
        <img src="{{ asset('images/uploads/hero-bg.jpg') }}" alt="" class="absolute inset-0 -z-10 size-full object-cover opacity-90">
        <div class="absolute inset-0 -z-10 bg-black/60"></div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-12 sm:py-14 lg:py-16">
                <div class="text-center">
                    <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">Erotické kluby</h1>
                    <p class="mt-6 text-lg leading-8 text-gray-300">Objavte najlepšie nočné kluby a erotické zariadenia na Slovensku. Kvalitné služby, diskrétne prostredie a profesionálny prístup.</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </div>
</div>

<div class="bg-white py-8 sm:py-12" x-data="{ selectedClub: null }">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-3">
            @forelse($clubs as $club)
                <article class="relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 min-h-[450px] sm:min-h-[400px] lg:min-h-[470px]">
                    <img src="{{ $club->image_url ?: asset('images/uploads/hero-bg.jpg') }}" alt="{{ $club->name }}" class="absolute inset-0 -z-10 size-full object-cover">
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40"></div>
                    <div class="absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10"></div>

                    <div class="flex flex-wrap items-center gap-y-1 overflow-hidden text-sm/6 text-gray-300 -mb-5">
                        <div class="flex items-center gap-x-2">
                            @if($club->logo_url)
                                <img src="{{ $club->logo_url }}" alt="" class="size-6 flex-none rounded-full bg-white/10">
                            @endif
                            <span class="text-white font-semibold text-xl">{{ $club->name }}</span>
                        </div>
                    </div>
                    <h3 class="mt-1 text-lg/6 font-semibold text-white">
                        <button @click="selectedClub = '{{ $club->slug }}'" type="button" class="text-left">
                            <span class="absolute inset-0"></span>
                        </button>
                    </h3>
                    <p class="mt-1 text-sm text-gray-300">{{ $club->address }}</p>
                    <p class="mt-2 text-sm text-gray-400">{{ Str::limit($club->description, 80) }}</p>
                    <div class="mt-4 flex items-center text-xs text-gray-400">
                        <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        Kliknite pre viac detailov
                    </div>
                </article>
            @empty
                <div class="col-span-3 text-center py-12">
                    <h3 class="text-lg font-semibold text-gray-900">Žiadne kluby neboli nájdené</h3>
                    <p class="mt-2 text-sm text-gray-500">Momentálne nie sú k dispozícii žiadne erotické kluby.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal pre detail klubu -->
    <div x-show="selectedClub" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="selectedClub" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-50 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="selectedClub" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                    @foreach($clubs as $club)
                        <div x-show="selectedClub === '{{ $club->slug }}'">
                            <!-- Close button -->
                            <div class="absolute right-4 top-4 z-10">
                                <button @click="selectedClub = null" type="button" class="rounded-full bg-white/90 p-2 text-gray-400 hover:text-gray-500 hover:bg-white shadow-lg backdrop-blur-sm transition-all duration-200">
                                    <span class="sr-only">Zavrieť</span>
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Hero image section -->
                            <div class="relative h-64 sm:h-80">
                                <img src="{{ asset(str_starts_with($club->image_path, 'storage/') ? $club->image_path : 'storage/'.ltrim($club->image_path, '/')) }}" alt="{{ $club->name }}" class="h-full w-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                <div class="absolute bottom-6 left-6 right-16">
                                    <h3 class="text-3xl font-bold text-white mb-2" id="modal-title">{{ $club->name }}</h3>
                                    <p class="text-white/90 text-lg">{{ $club->address ?: 'Neuvedené' }}</p>
                                </div>
                            </div>

                            <!-- Content section -->
                            <div class="p-6 sm:p-8">
                                <!-- About section -->
                                <div class="mb-8">
                                    <h4 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        O klube
                                    </h4>
                                    <p class="text-gray-600 leading-relaxed">{{ $club->description }}</p>
                                </div>

                                <!-- Info grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Contact info -->
                                    <div class="bg-gray-50 rounded-xl p-6">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            Kontakt
                                        </h4>
                                        <div class="space-y-4">
                                            @if($club->phone)
                                            <div>
                                                <a href="tel:{{ $club->phone }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    Zavolajte nám
                                                </a>
                                            </div>
                                            @endif
                                            
                                            @if($club->website)
                                            <div>
                                                <a href="{{ $club->website }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                    Zobraziť stránku
                                                </a>
                                            </div>
                                            @endif
                                            
                                            @if($club->email)
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 mb-1">Email</p>
                                                <p class="text-sm text-gray-600">{{ $club->email }}</p>
                                            </div>
                                            @endif
                                            
                                            @if(!$club->phone && !$club->email && !$club->website)
                                            <p class="text-sm text-gray-500 italic">Kontaktné údaje nie sú uvedené</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Opening hours -->
                                    <div class="bg-gray-50 rounded-xl p-6">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Otváracie hodiny
                                        </h4>
                                        @if($club->hours_weekdays || $club->hours_weekend || $club->hours_sunday)
                                            <div class="space-y-3">
                                                @if($club->hours_weekdays)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm font-medium text-gray-900">Po - Štv</span>
                                                    <span class="text-sm text-gray-600">{{ $club->hours_weekdays }}</span>
                                                </div>
                                                @endif
                                                @if($club->hours_weekend)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm font-medium text-gray-900">Pia - Sob</span>
                                                    <span class="text-sm text-gray-600">{{ $club->hours_weekend }}</span>
                                                </div>
                                                @endif
                                                @if($club->hours_sunday)
                                                <div class="flex justify-between items-center">
                                                    <span class="text-sm font-medium text-gray-900">Nedeľa</span>
                                                    <span class="text-sm text-gray-600">{{ $club->hours_sunday }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 italic">Otváracie hodiny nie sú uvedené</p>
                                        @endif
                                    </div>

                                    <!-- Services -->
                                    <div class="bg-gray-50 rounded-xl p-6">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                            </svg>
                                            Služby
                                        </h4>
                                        @if($club->services && count($club->services) > 0)
                                            <div class="space-y-2">
                                                @foreach($club->services as $service)
                                                    <div class="flex items-center">
                                                        <svg class="w-3 h-3 mr-2 text-pink-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <span class="text-sm text-gray-600">{{ $service }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 italic">Žiadne služby nie sú uvedené</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 