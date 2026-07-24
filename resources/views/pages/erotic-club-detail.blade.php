@extends('layouts.main')

@section('title', $club->name . ' - Erotické kluby | Diskrétne Dnes')

@section('meta')
    <meta name="description" content="{{ Str::limit(strip_tags($club->description ?: config('seo.default_description')), 160) }}">
    <meta name="keywords" content="erotický klub, {{ $club->city }}, {{ $club->name }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $club->name }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($club->description ?: config('seo.default_description')), 160) }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($club->image_url)
        <meta property="og:image" content="{{ $club->image_url }}">
    @endif

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $club->name }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($club->description ?: config('seo.default_description')), 160) }}">
@endsection

@section('content')
<div class="bg-gray-900">
    <div class="relative isolate overflow-hidden pt-14">
        <img src="{{ $club->image_url ?: asset('images/uploads/hero-bg.jpg') }}" alt="{{ $club->name }}" class="absolute inset-0 -z-10 size-full object-cover opacity-90">
        <div class="absolute inset-0 -z-10 bg-black/60"></div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-12 sm:py-14 lg:py-16">
                <div class="text-center">
                    @if($club->logo_url)
                        <img src="{{ $club->logo_url }}" alt="" class="mx-auto mb-4 size-16 rounded-full bg-white/10">
                    @endif
                    <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">{{ $club->name }}</h1>
                    <p class="mt-6 text-lg leading-8 text-gray-300">{{ $club->address ?: 'Neuvedené' }}</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </div>
</div>

<div class="bg-white py-8 sm:py-12">
    <div class="mx-auto max-w-4xl px-6 lg:px-8">
        <a href="{{ route('erotic-clubs') }}" class="inline-flex items-center text-sm font-medium text-pink-600 hover:text-pink-700 mb-8">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Späť na erotické kluby
        </a>

        <!-- About section -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                O klube
            </h2>
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
                        <a href="{{ $club->website }}" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition-colors duration-200">
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
@endsection
