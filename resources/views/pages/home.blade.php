@extends('layouts.main')

@section('content')
<style>
/* Inline CSS pre filter scrollbars */
#cityOptions .overflow-y-auto::-webkit-scrollbar,
#ad_typeOptions::-webkit-scrollbar,
#offer_typeOptions::-webkit-scrollbar,
#ageOptions::-webkit-scrollbar {
    width: 8px;
}

#cityOptions .overflow-y-auto::-webkit-scrollbar-track,
#ad_typeOptions::-webkit-scrollbar-track,
#offer_typeOptions::-webkit-scrollbar-track,
#ageOptions::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 4px;
}

#cityOptions .overflow-y-auto::-webkit-scrollbar-thumb,
#ad_typeOptions::-webkit-scrollbar-thumb,
#offer_typeOptions::-webkit-scrollbar-thumb,
#ageOptions::-webkit-scrollbar-thumb {
    background: #f472b6;
    border-radius: 4px;
}

#cityOptions .overflow-y-auto::-webkit-scrollbar-thumb:hover,
#ad_typeOptions::-webkit-scrollbar-thumb:hover,
#offer_typeOptions::-webkit-scrollbar-thumb:hover,
#ageOptions::-webkit-scrollbar-thumb:hover {
    background: #ec4899;
}

/* Search input styling */
#citySearchInput:focus {
    box-shadow: 0 0 0 2px rgba(244, 114, 182, 0.1);
}
</style>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap" rel="stylesheet">

    <!-- Age verification modal -->
    <div id="age-verification-modal" class="fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <div class="relative flex flex-col bg-white dark:bg-gray-800 shadow-lg rounded-xl w-full max-w-md m-3 opacity-0 transform -translate-y-4 transition-all duration-300" style="min-height: 340px;">
            <div class="absolute top-4 end-4">
                <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none" onclick="closeModal()">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 p-4 sm:p-8 text-center flex flex-col items-center">
                <img src="{{ asset('images/uploads/erotikon-logo.webp') }}" alt="Erotikon" class="w-36 mb-6">
                
                <div class="space-y-4 text-left max-w-sm mx-auto">
                    <p class="text-gray-800 dark:text-gray-200 font-medium">
                        {{ __('app.age_verification.content_18_plus') }}
                    </p>
                    
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        {{ __('app.age_verification.confirmation_text') }}
                    </p>

                    <div class="pt-2">
                        <h4 class="text-gray-800 dark:text-gray-200 font-medium mb-1">{{ __('app.age_verification.cookies_title') }}</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                            {{ __('app.age_verification.cookies_text') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center" style="font-family: 'Roboto', sans-serif;">
                <button 
                    type="button" 
                    class="w-full py-5 px-4 inline-flex justify-center items-center text-base font-normal rounded-bl-xl bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:pointer-events-none border-t border-r border-gray-200 dark:border-gray-600" 
                    onclick="window.location.href='https://www.google.com'"
                >
                    {{ __('app.age_verification.disagree') }}
                </button>
                <button 
                    type="button" 
                    class="w-full py-5 px-4 inline-flex justify-center items-center text-base font-normal rounded-br-xl bg-pink-500 text-white hover:bg-pink-600 disabled:opacity-50 disabled:pointer-events-none border-t border-gray-200 dark:border-gray-600" 
                    onclick="verifyAge()"
                >
                    {{ __('app.age_verification.agree') }}
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

        // Multi-select filters functionality
        let filterTimeout;
        
        function toggleMobileFilters() {
            const mobileButtons = document.getElementById('mobileFilterButtons');
            const mobileReset = document.getElementById('mobileResetButton');
            
            if (mobileButtons) {
                mobileButtons.classList.toggle('hidden');
                mobileButtons.classList.toggle('grid');
            }
            
            if (mobileReset) {
                mobileReset.classList.toggle('hidden');
            }
        }
        
        function toggleFilter(filterType) {
            const options = document.getElementById(filterType + 'Options');
            const chevron = document.getElementById(filterType + 'Chevron');
            
            // Close all other filters first
            const allFilters = ['city', 'ad_type', 'offer_type', 'age'];
            allFilters.forEach(filter => {
                if (filter !== filterType) {
                    const otherOptions = document.getElementById(filter + 'Options');
                    const otherChevron = document.getElementById(filter + 'Chevron');
                    if (otherOptions && !otherOptions.classList.contains('hidden')) {
                        otherOptions.classList.add('hidden');
                        if (otherChevron) otherChevron.classList.remove('rotate-180');
                    }
                }
            });
            
            // Toggle current filter
            options.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
            
            // Clear search when closing city filter
            if (filterType === 'city' && options.classList.contains('hidden')) {
                const searchInput = document.getElementById('citySearchInput');
                if (searchInput) {
                    searchInput.value = '';
                    filterCities('');
                }
            }
            
            // Focus search input when opening city filter
            if (filterType === 'city' && !options.classList.contains('hidden')) {
                setTimeout(() => {
                    const searchInput = document.getElementById('citySearchInput');
                    if (searchInput) searchInput.focus();
                }, 100);
            }
        }
        
        function filterCities(searchTerm) {
            const cityOptions = document.querySelectorAll('.city-option');
            const searchLower = searchTerm.toLowerCase().trim();
            const noResultsMsg = document.getElementById('noCitiesFound');
            let visibleCount = 0;
            
            cityOptions.forEach(option => {
                const cityName = option.getAttribute('data-city-name');
                if (searchLower === '' || cityName.includes(searchLower)) {
                    option.style.display = 'flex';
                    visibleCount++;
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            if (noResultsMsg) {
                if (visibleCount === 0 && searchLower !== '') {
                    noResultsMsg.classList.remove('hidden');
                } else {
                    noResultsMsg.classList.add('hidden');
                }
            }
        }
        
        function updateFilters() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                updateSelectedCounts();
                performAjaxFilter();
            }, 300);
        }
        
        function updateSelectedCounts() {
            const filterTypes = ['city', 'ad_type', 'offer_type', 'age'];
            let totalSelected = 0;
            
            filterTypes.forEach(type => {
                let selector;
                if (type === 'city') selector = 'input[name="cities[]"]:checked';
                else if (type === 'ad_type') selector = 'input[name="ad_types[]"]:checked';
                else if (type === 'offer_type') selector = 'input[name="offer_types[]"]:checked';
                else if (type === 'age') selector = 'input[name="age_ranges[]"]:checked';
                
                const selected = document.querySelectorAll(selector);
                const count = selected.length;
                const countElement = document.getElementById(type + 'SelectedCount');
                
                if (countElement) {
                    if (count > 0) {
                        countElement.textContent = count + ' vybraných';
                        countElement.classList.add('text-pink-600');
                        countElement.classList.remove('text-gray-500');
                        totalSelected += count;
                    } else {
                        countElement.textContent = 'Všetky';
                        countElement.classList.remove('text-pink-600');
                        countElement.classList.add('text-gray-500');
                    }
                }
            });
            
            // Update mobile filter count
            const mobileCount = document.getElementById('activeFiltersCount');
            if (totalSelected > 0) {
                mobileCount.textContent = totalSelected;
                mobileCount.classList.remove('hidden');
            } else {
                mobileCount.classList.add('hidden');
            }
        }
        
        function performAjaxFilter() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const searchParams = new URLSearchParams(formData);
            
            // Show loading
            document.getElementById('loadingSpinner').classList.remove('hidden');
            
            // Update URL
            const newUrl = window.location.pathname + '?' + searchParams.toString();
            history.replaceState(null, '', newUrl);
            
            // Perform AJAX request
            fetch(newUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update ads grid
                document.getElementById('adsGrid').innerHTML = data.ads_html;
                
                // Update pagination
                const paginationContainer = document.getElementById('paginationContainer');
                if (paginationContainer) {
                    if (data.pagination_html && data.pagination_html.trim() !== '') {
                        paginationContainer.innerHTML = data.pagination_html;
                        paginationContainer.style.display = 'flex';
                    } else {
                        paginationContainer.style.display = 'none';
                    }
                }
                
                // Hide loading
                document.getElementById('loadingSpinner').classList.add('hidden');
            })
            .catch(error => {
                console.error('Filter error:', error);
                // Fallback to page reload
                window.location.href = newUrl;
            });
        }
        
        function clearAllFilters() {
            // Uncheck all checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            updateSelectedCounts();
            
            // Redirect to clean URL
            window.location.href = window.location.pathname;
        }
        
        // Close filters when clicking outside
        document.addEventListener('click', function(event) {
            const allFilters = ['city', 'ad_type', 'offer_type', 'age'];
            
            allFilters.forEach(filterType => {
                const filterGroup = event.target.closest('.filter-group');
                const options = document.getElementById(filterType + 'Options');
                const chevron = document.getElementById(filterType + 'Chevron');
                
                // If click is outside this filter group and options are visible
                if (!filterGroup || !filterGroup.contains(document.getElementById(filterType + 'Options'))) {
                    if (options && !options.classList.contains('hidden')) {
                        options.classList.add('hidden');
                        if (chevron) chevron.classList.remove('rotate-180');
                    }
                }
            });
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedCounts();
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="bg-gray-900">
        <div class="relative isolate overflow-hidden pt-14 min-h-[60vh]">
            <img src="{{ asset('images/uploads/hero-bg.jpg') }}" alt="" class="absolute inset-0 -z-10 size-full object-cover opacity-90">
            <div class="absolute inset-0 -z-10 bg-black/60"></div>
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl py-16 sm:py-20 lg:py-24">
                    <div class="text-center">
                        <h1 class="text-balance text-5xl font-semibold tracking-tight text-white sm:text-7xl">{{ __('app.hero.title') }}</h1>
                        <div class="mt-8 flex justify-center">
                            <div class="relative rounded-full px-3 py-1 text-sm/6 text-gray-400 ring-1 ring-white/10 hover:ring-white/20">
                                {{ __('app.hero.subtitle') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </div>
    </div>

        <!-- Top Ads Carousel -->
    <!-- Debug info (dočasne pre zistenie problému na produkcii) -->
    @if(config('app.debug'))
        <div class="bg-yellow-100 p-4 text-sm text-gray-800 mb-4">
            <strong>Debug Info:</strong> Topované inzeráty celkovo: {{ $topAds->count() }} | 
            Top ads: {{ $topAds->where('top_ad', true)->count() }} | 
            Featured: {{ $topAds->where('featured', true)->count() }}
        </div>
    @endif
    
    @if($topAds->count() > 0)
    <div class="relative -mt-24 mb-6 z-10">
<div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="bg-gradient-to-r from-pink-500 via-pink-600 to-pink-700 rounded-2xl p-4 shadow-2xl">
                <div class="relative overflow-hidden rounded-2xl">
                    <div id="carousel-container" class="flex transition-transform duration-300 ease-in-out">
                        @foreach($topAds as $ad)
                        <div class="flex-shrink-0 w-28 md:w-32 lg:w-36 mr-3">
                            <a href="{{ route('ad.show', $ad->id) }}" class="block relative group">
                                <div class="w-full h-36 md:h-42 lg:h-48 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                                    @if($ad->verification_image_url)
                                        <img src="{{ $ad->verification_image_url }}" 
                                             alt="{{ $ad->nickname }}" 
                                             class="w-full h-full object-cover object-center rounded-xl">
                                    @elseif(count($ad->gallery_image_urls) > 0)
                                        <img src="{{ $ad->gallery_image_urls[0] }}" 
                                             alt="{{ $ad->nickname }}" 
                                             class="w-full h-full object-cover object-center rounded-xl">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-pink-300 to-purple-400 flex items-center justify-center rounded-xl">
                                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <!-- Gradient overlay na bottom -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                                    
                                    <!-- Info overlay -->
                                    <div class="absolute bottom-0 left-0 right-0 p-2 text-white">
                                        <div class="text-xs font-semibold truncate">{{ $ad->nickname ?: 'Anonymný' }}</div>
                                        <div class="text-xs opacity-90">{{ $ad->age }} rokov</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters section -->
    <div class="bg-white dark:bg-slate-900 py-6 border-b border-gray-200 dark:border-gray-700">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            
            <!-- Filter Toggle Button (Mobile) -->
            <div class="md:hidden mb-4">
                <button type="button" 
                        onclick="toggleMobileFilters()" 
                        class="w-full flex items-center justify-center px-4 py-3 bg-pink-600 text-white rounded-xl font-medium hover:bg-pink-700 transition-colors">
                    <i class="ri-equalizer-line text-lg mr-2"></i>
                    Filtre
                    <span id="activeFiltersCount" class="ml-2 px-2 py-1 bg-pink-700 text-xs rounded-full hidden">0</span>
                </button>
            </div>

            <!-- Filters Container -->
            <div id="filtersContainer" class="block">
                <form id="filterForm" method="GET" action="{{ route('home') }}">
                    
                    <!-- Header s filtrami a akčnými tlačidlami -->
                    <div class="flex flex-col gap-4 mb-6">
                        <!-- Mobile Filter Buttons (Visible only on mobile) -->
                        <div id="mobileFilterButtons" class="hidden grid-cols-2 gap-3 md:hidden">
                            <!-- Mesto Button -->
                            <button type="button" 
                                    onclick="openMobileFilter('city')" 
                                    class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-900">Mesto</div>
                                        <div id="cityMobileCount" class="text-xs text-pink-600">Všetky</div>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>

                            <!-- Typ inzerátu Button -->
                            <button type="button" 
                                    onclick="openMobileFilter('adType')" 
                                    class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-900">Typ profilu</div>
                                        <div id="adTypeMobileCount" class="text-xs text-pink-600">Všetky</div>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>

                            <!-- Ponuka Button -->
                            <button type="button" 
                                    onclick="openMobileFilter('offerType')" 
                                    class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-900">Typ stretnutia</div>
                                        <div id="offerTypeMobileCount" class="text-xs text-pink-600">Všetky</div>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>

                            <!-- Vek Button -->
                            <button type="button" 
                                    onclick="openMobileFilter('age')" 
                                    class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-900">Vek</div>
                                        <div id="ageMobileCount" class="text-xs text-pink-600">Všetky</div>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Mobile Reset Button (mimo grid kontajnera) -->
                        <div id="mobileResetButton" class="hidden md:hidden">
                            <button type="button" 
                                    onclick="clearAllFilters()" 
                                    class="w-full flex items-center justify-center px-4 py-3 bg-pink-600 text-white rounded-xl font-medium hover:bg-pink-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Resetovať filtre
                            </button>
                        </div>

                        <!-- Mobile Reset Button (SKRYTÉ - používa sa len desktop reset) -->

                        <!-- Filtre a vymazať tlačidlo (Desktop - jeden riadok na celú šírku) -->
                        <div class="hidden md:flex md:flex-row md:items-center md:gap-4 md:w-full">
                            <!-- Filtre kontajner -->
                            <div class="flex-1 flex flex-row gap-4">
                            
                            <!-- Lokalita Filter -->
                            <div class="filter-group relative flex-1">
                                <button type="button" 
                                        onclick="toggleFilter('city')" 
                                        class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div class="text-left">
                                            <div class="text-sm font-medium text-gray-900">Mesto</div>
                                            <div id="citySelectedCount" class="text-xs text-pink-600">Všetky</div>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" id="cityChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div id="cityOptions" class="hidden absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg z-50" style="max-height: 250px; scrollbar-width: thin; scrollbar-color: #f472b6 #f3f4f6;">
                                    <!-- Vyhľadávanie -->
                                    <div class="p-3 border-b border-gray-200 dark:border-gray-600">
                                        <div class="relative">
                                            <input type="text" 
                                                   id="citySearchInput"
                                                   placeholder="Vyhľadať mesto..."
                                                   onkeyup="filterCities(this.value)"
                                                   onclick="event.stopPropagation()"
                                                   class="w-full px-3 py-2 pl-8 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-pink-500 focus:border-pink-500 dark:bg-slate-700 dark:text-gray-300">
                                            <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Zoznam miest -->
                                    <div class="p-3 overflow-y-auto" style="max-height: 180px;">
                                        @foreach($stats['cities'] as $cityValue => $cityLabel)
                                            <label class="city-option flex items-center py-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded cursor-pointer" data-city-name="{{ strtolower($cityLabel) }}">
                                                <input type="checkbox" 
                                                       name="cities[]" 
                                                       value="{{ $cityValue }}" 
                                                       {{ in_array($cityValue, request('cities', [])) ? 'checked' : '' }}
                                                       onchange="updateFilters()"
                                                       class="w-4 h-4 text-pink-600 border-gray-300 dark:border-gray-600 rounded-full focus:ring-pink-500 dark:focus:ring-pink-400">
                                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ $cityLabel }}</span>
                                            </label>
                                        @endforeach
                                        
                                        <!-- No results message -->
                                        <div id="noCitiesFound" class="hidden text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            Žiadne mesto sa nenašlo
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Typ inzerátu Filter -->
                            <div class="filter-group relative flex-1">
                                <button type="button" 
                                        onclick="toggleFilter('ad_type')" 
                                        class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        <div class="text-left">
                                            <div class="text-sm font-medium text-gray-900">Typ profilu</div>
                                            <div id="ad_typeSelectedCount" class="text-xs text-pink-600">Všetky</div>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" id="ad_typeChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div id="ad_typeOptions" class="hidden absolute top-full left-0 right-0 mt-2 p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg z-50" style="max-height: 180px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #f472b6 #f3f4f6;">
                                    @foreach($stats['ad_types'] as $adTypeValue => $count)
                                        <label class="flex items-center py-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded cursor-pointer">
                                            <input type="checkbox" 
                                                   name="ad_types[]" 
                                                   value="{{ $adTypeValue }}" 
                                                   {{ in_array($adTypeValue, request('ad_types', [])) ? 'checked' : '' }}
                                                   onchange="updateFilters()"
                                                   class="w-4 h-4 text-pink-600 border-gray-300 dark:border-gray-600 rounded focus:ring-pink-500 dark:focus:ring-pink-400">
                                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('app.ad_types.' . ($adTypeValue === 'individual' ? 'zena' : $adTypeValue)) }} ({{ $count }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Typ ponuky Filter -->
                            <div class="filter-group relative flex-1">
                                <button type="button" 
                                        onclick="toggleFilter('offer_type')" 
                                        class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        <div class="text-left">
                                            <div class="text-sm font-medium text-gray-900">Typ stretnutia</div>
                                            <div id="offer_typeSelectedCount" class="text-xs text-pink-600">Všetky</div>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" id="offer_typeChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div id="offer_typeOptions" class="hidden absolute top-full left-0 right-0 mt-2 p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg z-50" style="max-height: 180px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #f472b6 #f3f4f6;">
                                    @foreach($stats['offer_types'] as $offerTypeValue => $count)
                                        <label class="flex items-center py-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded cursor-pointer">
                                            <input type="checkbox" 
                                                   name="offer_types[]" 
                                                   value="{{ $offerTypeValue }}" 
                                                   {{ in_array($offerTypeValue, request('offer_types', [])) ? 'checked' : '' }}
                                                   onchange="updateFilters()"
                                                   class="w-4 h-4 text-pink-600 border-gray-300 dark:border-gray-600 rounded focus:ring-pink-500 dark:focus:ring-pink-400">
                                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('app.offer_types.' . $offerTypeValue) }} ({{ $count }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Vek Filter -->
                            <div class="filter-group relative flex-1">
                                <button type="button" 
                                        onclick="toggleFilter('age')" 
                                        class="w-full flex items-center justify-between p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <div class="text-left">
                                            <div class="text-sm font-medium text-gray-900">Vek</div>
                                            <div id="ageSelectedCount" class="text-xs text-pink-600">Všetky</div>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" id="ageChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div id="ageOptions" class="hidden absolute top-full left-0 right-0 mt-2 p-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg z-50" style="max-height: 180px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #f472b6 #f3f4f6;">
                                    <label class="flex items-center py-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               name="age_ranges[]" 
                                               value="18-25" 
                                               {{ in_array('18-25', request('age_ranges', [])) ? 'checked' : '' }}
                                               onchange="updateFilters()"
                                               class="w-4 h-4 text-pink-600 border-gray-300 dark:border-gray-600 rounded focus:ring-pink-500 dark:focus:ring-pink-400">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('app.age_ranges.18-25') }}</span>
                                    </label>
                                    <label class="flex items-center py-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               name="age_ranges[]" 
                                               value="26-35" 
                                               {{ in_array('26-35', request('age_ranges', [])) ? 'checked' : '' }}
                                               onchange="updateFilters()"
                                               class="w-4 h-4 text-pink-600 border-gray-300 dark:border-gray-600 rounded focus:ring-pink-500 dark:focus:ring-pink-400">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('app.age_ranges.26-35') }}</span>
                                    </label>
                                    <label class="flex items-center py-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               name="age_ranges[]" 
                                               value="36-45" 
                                               {{ in_array('36-45', request('age_ranges', [])) ? 'checked' : '' }}
                                               onchange="updateFilters()"
                                               class="w-4 h-4 text-pink-600 border-gray-300 dark:border-gray-600 rounded focus:ring-pink-500 dark:focus:ring-pink-400">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('app.age_ranges.36-45') }}</span>
                                    </label>
                                    <label class="flex items-center py-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               name="age_ranges[]" 
                                               value="46-55" 
                                               {{ in_array('46-55', request('age_ranges', [])) ? 'checked' : '' }}
                                               onchange="updateFilters()"
                                               class="w-4 h-4 text-pink-600 border-gray-300 dark:border-gray-600 rounded focus:ring-pink-500 dark:focus:ring-pink-400">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('app.age_ranges.46-55') }}</span>
                                    </label>
                                    <label class="flex items-center py-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               name="age_ranges[]" 
                                               value="56-65" 
                                               {{ in_array('56-65', request('age_ranges', [])) ? 'checked' : '' }}
                                               onchange="updateFilters()"
                                               class="w-4 h-4 text-pink-600 border-gray-300 dark:border-gray-600 rounded focus:ring-pink-500 dark:focus:ring-pink-400">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('app.age_ranges.56-65') }}</span>
                                    </label>
                                    <label class="flex items-center py-2 hover:bg-gray-50 dark:hover:bg-slate-700 rounded cursor-pointer">
                                        <input type="checkbox" 
                                               name="age_ranges[]" 
                                               value="65+" 
                                               {{ in_array('65+', request('age_ranges', [])) ? 'checked' : '' }}
                                               onchange="updateFilters()"
                                               class="w-4 h-4 text-pink-600 border-gray-300 dark:border-gray-600 rounded focus:ring-pink-500 dark:focus:ring-pink-400">
                                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ __('app.age_ranges.65+') }}</span>
                                    </label>
                                </div>
                            </div>

                            </div>
                            
                            <!-- Resetovať tlačidlo s medzerou (IBA DESKTOP) -->
                            <button type="button" 
                                    onclick="clearAllFilters()" 
                                    class="hidden md:flex items-center justify-center px-6 py-4 h-[68px] bg-pink-600 hover:bg-pink-700 rounded-xl transition-colors text-white font-medium shadow-sm hover:shadow-md whitespace-nowrap ml-4">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Resetovať
                            </button>
                        </div>
                        
                        <!-- Loading spinner -->
                        <div class="flex justify-center">
                            <div class="hidden" id="loadingSpinner">
                                <svg class="animate-spin w-5 h-5 text-pink-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Inzeráty section -->
    <div class="bg-white dark:bg-slate-950 py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            @if($ads->count() > 0)
                <!-- Inzeráty grid -->
                <div class="mx-auto grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4" id="adsGrid">
                    @include('partials.ad-grid', ['ads' => $ads])
                </div>

            @else
                <!-- Prázdny stav -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20.4a7.962 7.962 0 01-5-1.691c-2.598-2.11-3.292-5.731-1.691-8.329 2.11-2.598 5.731-3.292 8.329-1.691 1.556 1.263 2.524 3.187 2.524 5.291z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('app.messages.no_ads_found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if(request()->hasAny(['city', 'ad_type', 'offer_type']))
                            {{ __('app.messages.no_ads_for_filters') }}
                        @else
                            {{ __('app.messages.no_active_ads') }}
                        @endif
                    </p>
                    @if(request()->hasAny(['city', 'ad_type', 'offer_type']))
                        <div class="mt-6">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700">
                                {{ __('app.messages.show_all_ads') }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif
            
            <!-- Paginácia (vždy prítomná pre AJAX updates) -->
            <div id="paginationContainer" class="mt-12 flex justify-center" @if(!$ads->count() > 0 || !$ads->hasPages()) style="display: none;" @endif>
                @if($ads->count() > 0 && $ads->hasPages())
                    {{ $ads->appends(request()->query())->links() }}
                @endif
            </div>
        </div>
    </div>

    <!-- Mobile Filter Modals -->
    <!-- City Filter Modal -->
    <div id="cityMobileModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-5 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeMobileFilter('city')"></div>
            
            <div class="inline-block align-bottom bg-white rounded-t-3xl px-6 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all w-[400px] max-w-[400px] sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:rounded-3xl">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Vyberte mesto</h3>
                    <button type="button" onclick="closeMobileFilter('city')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Search -->
                <div class="mb-4">
                    <div class="relative">
                        <input type="text" 
                               id="cityMobileSearchInput"
                               placeholder="Vyhľadať mesto..."
                               onkeyup="filterMobileCities(this.value)"
                               class="w-full px-4 py-3 pl-12 text-base border border-gray-300 rounded-xl focus:ring-pink-500 focus:border-pink-500">
                        <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Cities List -->
                <div class="max-h-96 overflow-y-auto">
                    @foreach($stats['cities'] as $cityValue => $cityLabel)
                        <label class="city-mobile-option flex items-center py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50" data-city-name="{{ strtolower($cityLabel) }}">
                            <input type="checkbox" 
                                   name="cities[]" 
                                   value="{{ $cityValue }}" 
                                   {{ in_array($cityValue, request('cities', [])) ? 'checked' : '' }}
                                   onchange="updateMobileFilters()"
                                   class="w-5 h-5 text-pink-600 border-gray-300 rounded-full focus:ring-pink-500">
                            <span class="ml-4 text-base text-gray-700">{{ $cityLabel }}</span>
                        </label>
                    @endforeach
                    
                    <!-- No results message -->
                    <div id="noCitiesMobileFound" class="hidden text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <p>Žiadne mesto sa nenašlo</p>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeMobileFilter('city')" class="w-full px-4 py-3 text-white bg-pink-600 rounded-xl hover:bg-pink-700 transition-colors">
                        Hotovo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ad Type Filter Modal -->
    <div id="adTypeMobileModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-5 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeMobileFilter('adType')"></div>
            
            <div class="inline-block align-bottom bg-white rounded-t-3xl px-6 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all w-[400px] max-w-[400px] sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:rounded-3xl">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Vyberte typ profilu</h3>
                    <button type="button" onclick="closeMobileFilter('adType')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Options List -->
                <div class="max-h-96 overflow-y-auto">
                    @foreach($stats['ad_types'] as $adTypeValue => $count)
                        <label class="flex items-center justify-between py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="ad_types[]" 
                                       value="{{ $adTypeValue }}" 
                                       {{ in_array($adTypeValue, request('ad_types', [])) ? 'checked' : '' }}
                                       onchange="updateMobileFilters()"
                                       class="w-5 h-5 text-pink-600 border-gray-300 rounded-full focus:ring-pink-500">
                                <span class="ml-4 text-base text-gray-700">{{ __('app.ad_types.' . $adTypeValue) }}</span>
                            </div>
                            <span class="text-sm text-gray-500">({{ $count }})</span>
                        </label>
                    @endforeach
                </div>

                <!-- Footer Buttons -->
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeMobileFilter('adType')" class="w-full px-4 py-3 text-white bg-pink-600 rounded-xl hover:bg-pink-700 transition-colors">
                        Hotovo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Offer Type Filter Modal -->
    <div id="offerTypeMobileModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-5 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeMobileFilter('offerType')"></div>
            
            <div class="inline-block align-bottom bg-white rounded-t-3xl px-6 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all w-[400px] max-w-[400px] sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:rounded-3xl">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Vyberte typ stretnutia</h3>
                    <button type="button" onclick="closeMobileFilter('offerType')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Options List -->
                <div class="max-h-96 overflow-y-auto">
                    @foreach($stats['offer_types'] as $offerTypeValue => $count)
                        <label class="flex items-center justify-between py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="offer_types[]" 
                                       value="{{ $offerTypeValue }}" 
                                       {{ in_array($offerTypeValue, request('offer_types', [])) ? 'checked' : '' }}
                                       onchange="updateMobileFilters()"
                                       class="w-5 h-5 text-pink-600 border-gray-300 rounded-full focus:ring-pink-500">
                                <span class="ml-4 text-base text-gray-700">{{ __('app.offer_types.' . $offerTypeValue) }}</span>
                            </div>
                            <span class="text-sm text-gray-500">({{ $count }})</span>
                        </label>
                    @endforeach
                </div>

                <!-- Footer Buttons -->
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeMobileFilter('offerType')" class="w-full px-4 py-3 text-white bg-pink-600 rounded-xl hover:bg-pink-700 transition-colors">
                        Hotovo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Age Filter Modal -->
    <div id="ageMobileModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-5 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeMobileFilter('age')"></div>
            
            <div class="inline-block align-bottom bg-white rounded-t-3xl px-6 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all w-[400px] max-w-[400px] sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:rounded-3xl">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Vyberte vek</h3>
                    <button type="button" onclick="closeMobileFilter('age')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Options List -->
                <div class="max-h-96 overflow-y-auto">
                    <label class="flex items-center py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" 
                               name="age_ranges[]" 
                               value="18-25" 
                               {{ in_array('18-25', request('age_ranges', [])) ? 'checked' : '' }}
                               onchange="updateMobileFilters()"
                               class="w-5 h-5 text-pink-600 border-gray-300 rounded-full focus:ring-pink-500">
                        <span class="ml-4 text-base text-gray-700">{{ __('app.age_ranges.18-25') }}</span>
                    </label>
                    <label class="flex items-center py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" 
                               name="age_ranges[]" 
                               value="26-35" 
                               {{ in_array('26-35', request('age_ranges', [])) ? 'checked' : '' }}
                               onchange="updateMobileFilters()"
                               class="w-5 h-5 text-pink-600 border-gray-300 rounded-full focus:ring-pink-500">
                        <span class="ml-4 text-base text-gray-700">{{ __('app.age_ranges.26-35') }}</span>
                    </label>
                    <label class="flex items-center py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" 
                               name="age_ranges[]" 
                               value="36-45" 
                               {{ in_array('36-45', request('age_ranges', [])) ? 'checked' : '' }}
                               onchange="updateMobileFilters()"
                               class="w-5 h-5 text-pink-600 border-gray-300 rounded-full focus:ring-pink-500">
                        <span class="ml-4 text-base text-gray-700">{{ __('app.age_ranges.36-45') }}</span>
                    </label>
                    <label class="flex items-center py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" 
                               name="age_ranges[]" 
                               value="46-55" 
                               {{ in_array('46-55', request('age_ranges', [])) ? 'checked' : '' }}
                               onchange="updateMobileFilters()"
                               class="w-5 h-5 text-pink-600 border-gray-300 rounded-full focus:ring-pink-500">
                        <span class="ml-4 text-base text-gray-700">{{ __('app.age_ranges.46-55') }}</span>
                    </label>
                    <label class="flex items-center py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" 
                               name="age_ranges[]" 
                               value="56-65" 
                               {{ in_array('56-65', request('age_ranges', [])) ? 'checked' : '' }}
                               onchange="updateMobileFilters()"
                               class="w-5 h-5 text-pink-600 border-gray-300 rounded-full focus:ring-pink-500">
                        <span class="ml-4 text-base text-gray-700">{{ __('app.age_ranges.56-65') }}</span>
                    </label>
                    <label class="flex items-center py-4 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" 
                               name="age_ranges[]" 
                               value="65+" 
                               {{ in_array('65+', request('age_ranges', [])) ? 'checked' : '' }}
                               onchange="updateMobileFilters()"
                               class="w-5 h-5 text-pink-600 border-gray-300 rounded-full focus:ring-pink-500">
                        <span class="ml-4 text-base text-gray-700">{{ __('app.age_ranges.65+') }}</span>
                    </label>
                </div>

                <!-- Footer Buttons -->
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="closeMobileFilter('age')" class="w-full px-4 py-3 text-white bg-pink-600 rounded-xl hover:bg-pink-700 transition-colors">
                        Hotovo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // TOP Ads Carousel funkcionalita - len auto-scroll
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('carousel-container');
            
            if (container) {
                let currentPosition = 0;
                const itemWidth = 124; // nové rozmery: w-28 (112px) + mr-3 (12px) = 124px
                const visibleItems = Math.floor(container.parentElement.offsetWidth / itemWidth);
                const totalItems = container.children.length;
                const maxPosition = Math.max(0, (totalItems - visibleItems) * itemWidth);
                
                // Auto-scroll každých 4 sekundy
                let autoScrollInterval = setInterval(() => {
                    if (currentPosition >= maxPosition) {
                        currentPosition = 0;
                    } else {
                        currentPosition += itemWidth;
                    }
                    container.style.transform = `translateX(-${currentPosition}px)`;
                }, 4000);
                
                // Pause auto-scroll on hover
                container.addEventListener('mouseenter', () => {
                    clearInterval(autoScrollInterval);
                });
                
                container.addEventListener('mouseleave', () => {
                    autoScrollInterval = setInterval(() => {
                        if (currentPosition >= maxPosition) {
                            currentPosition = 0;
                        } else {
                            currentPosition += itemWidth;
                        }
                        container.style.transform = `translateX(-${currentPosition}px)`;
                    }, 4000);
                });
            }
        });

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

        // Mobile Filter Modal Functions
        function openMobileFilter(filterType) {
            const modal = document.getElementById(filterType + 'MobileModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';  // Disable body scroll
            }
        }

        function closeMobileFilter(filterType) {
            const modal = document.getElementById(filterType + 'MobileModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';  // Re-enable body scroll
            }
        }

        function clearMobileFilter(filterType) {
            let selector;
            if (filterType === 'city') selector = 'input[name="cities[]"]';
            else if (filterType === 'adType') selector = 'input[name="ad_types[]"]';
            else if (filterType === 'offerType') selector = 'input[name="offer_types[]"]';
            else if (filterType === 'age') selector = 'input[name="age_ranges[]"]';
            
            const checkboxes = document.querySelectorAll(selector);
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            updateMobileFilters();
        }

        function updateMobileFilters() {
            // Update desktop checkboxes to match mobile selections
            const filterTypes = ['city', 'adType', 'offerType', 'age'];
            
            filterTypes.forEach(type => {
                let selector;
                if (type === 'city') selector = 'input[name="cities[]"]';
                else if (type === 'adType') selector = 'input[name="ad_types[]"]';
                else if (type === 'offerType') selector = 'input[name="offer_types[]"]';
                else if (type === 'age') selector = 'input[name="age_ranges[]"]';
                
                const mobileCheckboxes = document.querySelectorAll(`#${type}MobileModal ${selector}`);
                const desktopCheckboxes = document.querySelectorAll(`#${type}Options ${selector}`);
                
                // Sync mobile selections to desktop
                mobileCheckboxes.forEach((mobileCheckbox, index) => {
                    if (desktopCheckboxes[index]) {
                        desktopCheckboxes[index].checked = mobileCheckbox.checked;
                    }
                });
            });
            
            // Update counts and perform filter
            updateSelectedCounts();
            updateMobileCounts();
            performAjaxFilter();
        }

        function updateMobileCounts() {
            const filterTypes = [
                { type: 'city', displayName: 'Mesto', countElement: 'cityMobileCount' },
                { type: 'ad_type', displayName: 'Typ', countElement: 'adTypeMobileCount' },
                { type: 'offer_type', displayName: 'Ponuka', countElement: 'offerTypeMobileCount' },
                { type: 'age', displayName: 'Vek', countElement: 'ageMobileCount' }
            ];
            
            filterTypes.forEach(filter => {
                let selector;
                if (filter.type === 'city') selector = 'input[name="cities[]"]:checked';
                else if (filter.type === 'ad_type') selector = 'input[name="ad_types[]"]:checked';
                else if (filter.type === 'offer_type') selector = 'input[name="offer_types[]"]:checked';
                else if (filter.type === 'age') selector = 'input[name="age_ranges[]"]:checked';
                
                const selected = document.querySelectorAll(selector);
                const countElement = document.getElementById(filter.countElement);
                
                if (countElement) {
                    if (selected.length > 0) {
                        countElement.textContent = `${selected.length} vybraných`;
                        countElement.classList.add('text-pink-600');
                        countElement.classList.remove('text-gray-500');
                    } else {
                        countElement.textContent = 'Všetky';
                        countElement.classList.remove('text-pink-600');
                        countElement.classList.add('text-gray-500');
                    }
                }
            });
        }

        function filterMobileCities(searchTerm) {
            const cityOptions = document.querySelectorAll('.city-mobile-option');
            const searchLower = searchTerm.toLowerCase().trim();
            const noResultsMsg = document.getElementById('noCitiesMobileFound');
            let visibleCount = 0;
            
            cityOptions.forEach(option => {
                const cityName = option.getAttribute('data-city-name');
                if (searchLower === '' || cityName.includes(searchLower)) {
                    option.style.display = 'flex';
                    visibleCount++;
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            if (noResultsMsg) {
                if (visibleCount === 0 && searchLower !== '') {
                    noResultsMsg.classList.remove('hidden');
                } else {
                    noResultsMsg.classList.add('hidden');
                }
            }
        }

        // Initialize mobile counts on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateMobileCounts();
        });
    </script>
@endsection
