@props(['darkMode' => false])

<style>
    /* Ensure mobile menu icons are properly positioned */
    @media (max-width: 1023px) {
        .mobile-menu-container {
            margin-left: auto;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.25rem;
            flex-shrink: 0;
            min-width: fit-content;
        }
        
        .mobile-menu-icon {
            flex-shrink: 0;
            padding: 0.5rem;
            border-radius: 0.375rem;
            white-space: nowrap;
        }
        
        /* Zabezpečiť že nav container má správne flexbox správanie */
        nav .flex {
            flex-wrap: nowrap;
        }
        
        /* Logo má flex-grow: 0 aby nezaberal príliš veľa miesta */
        .flex.lg\\:flex-1 {
            flex: 0 0 auto;
            max-width: calc(100% - 120px); /* Rezervovať priestor pre mobile menu ikony */
        }
    }
</style>

<header 
    x-data="{ 
        isOpen: false,
        isScrolled: false,
        darkMode: false,
        favoritesCount: 0
    }" 
    x-init="
        window.addEventListener('scroll', () => { isScrolled = window.pageYOffset > 0 });
        
        // Načítanie dark mode z localStorage
        const savedDarkMode = localStorage.getItem('darkMode') === 'true';
        darkMode = savedDarkMode;
        
        // Aplikovanie dark mode pri inicializácii
        const htmlElement = document.documentElement;
        if (darkMode) {
            htmlElement.classList.add('dark');
        } else {
            htmlElement.classList.remove('dark');
        }
        
        console.log('Header dark mode initialized:', darkMode, 'HTML classes:', htmlElement.className);
        
        $watch('darkMode', value => {
            localStorage.setItem('darkMode', value);
            const htmlElement = document.documentElement;
            if (value) {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }
            console.log('Dark mode changed to:', value, 'HTML classes:', htmlElement.className);
        });
        
        // Načítanie počtu obľúbených
        fetch('/oblubene/count')
            .then(response => response.json())
            .then(data => favoritesCount = data.count)
            .catch(error => console.error('Error loading favorites count:', error));
        
        // Počúvanie na aktualizácie obľúbených
        window.addEventListener('favorites-updated', (event) => {
            favoritesCount = event.detail.count;
        });
    "
    :class="{ 'bg-white shadow-lg dark:bg-slate-900': isScrolled }"
    class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
>
    <nav class="flex items-center justify-between p-4 lg:px-8" aria-label="Global">
        <div class="flex lg:flex-1">
            <a href="{{ route('home') }}" class="-m-1.5 p-1.5">
                <span class="sr-only">{{ config('app.name') }}</span>
                {{-- White logo over the transparent (dark hero) header; black logo once scrolled onto the white background --}}
                <img
                    class="h-8 w-auto"
                    src="{{ asset('images/uploads/diskretne-dnes-logo-white.png') }}"
                    :src="isScrolled ? '{{ asset('images/uploads/diskretne-dnes-logo-black.png') }}' : '{{ asset('images/uploads/diskretne-dnes-logo-white.png') }}'"
                    alt="{{ config('app.name') }}"
                >
            </a>
        </div>
        <div class="flex lg:hidden items-center justify-end ml-auto mobile-menu-container">
            <!-- Favorites Button for Mobile -->
            <a href="{{ route('favorites.index') }}" class="mobile-menu-icon transition-colors flex-shrink-0" :class="favoritesCount > 0 ? 'text-red-500 hover:text-red-600' : (isScrolled ? 'text-gray-700 dark:text-gray-300 hover:text-gray-500' : 'text-gray-400 hover:text-gray-300')">
                <i class="ri-heart-3-fill size-6" x-show="favoritesCount > 0"></i>
                <i class="ri-heart-3-line size-6" x-show="favoritesCount === 0"></i>
            </a>
            <!-- Hamburger Menu Button -->
            <button @click="isOpen = !isOpen" type="button" class="mobile-menu-icon inline-flex items-center justify-center transition-colors flex-shrink-0" :class="isScrolled ? 'text-gray-700 dark:text-gray-300 hover:text-gray-500' : 'text-gray-400 hover:text-gray-300'">
                <span class="sr-only">{{ __('app.navigation.menu') }}</span>
                <i class="ri-menu-line size-6" x-show="!isOpen"></i>
                <i class="ri-close-line size-6" x-show="isOpen"></i>
            </button>
        </div>
        <div class="hidden lg:flex lg:gap-x-8">
            {{-- <a href="{{ route('erotic-clubs') }}" class="text-sm font-light" :class="isScrolled ? 'text-gray-900 hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300' : 'text-white hover:text-gray-300'">{{ __('app.navigation.erotic_clubs') }}</a> --}}
            <a href="{{ route('tantra') }}" class="text-sm font-light" :class="isScrolled ? 'text-gray-900 hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300' : 'text-white hover:text-gray-300'">{{ __('app.navigation.tantra_massage') }}</a>
            <a href="{{ route('pricing') }}" class="text-sm font-light" :class="isScrolled ? 'text-gray-900 hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300' : 'text-white hover:text-gray-300'">{{ __('app.navigation.pricing') }}</a>
            <a href="{{ route('blog.index') }}" class="text-sm font-light" :class="isScrolled ? 'text-gray-900 hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300' : 'text-white hover:text-gray-300'">{{ __('app.navigation.blog') }}</a>
            <a href="{{ route('contact') }}" class="text-sm font-light" :class="isScrolled ? 'text-gray-900 hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300' : 'text-white hover:text-gray-300'">{{ __('app.navigation.contact') }}</a>
        </div>
        <div class="hidden lg:flex lg:items-center lg:justify-end lg:flex-1 lg:gap-x-4">
            <!-- Dark Mode Toggle -->
            <button 
                @click="darkMode = !darkMode" 
                class="transition-colors" 
                :class="isScrolled ? 'text-gray-900 hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300' : 'text-white hover:text-gray-300'"
            >
                <i x-show="!darkMode" class="ri-sun-line size-5"></i>
                <i x-show="darkMode" class="ri-moon-line size-5"></i>
            </button>

            @auth
                <a href="{{ route('favorites.index') }}" class="transition-colors" :class="favoritesCount > 0 ? 'text-red-500 hover:text-red-600' : (isScrolled ? 'text-gray-900 hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300' : 'text-white hover:text-gray-300')">
                    <i class="ri-heart-3-fill size-5" x-show="favoritesCount > 0"></i>
                    <i class="ri-heart-3-line size-5" x-show="favoritesCount === 0"></i>
                </a>
                <div class="h-6 w-px" :class="isScrolled ? 'bg-gray-200 dark:bg-gray-600' : 'bg-gray-600'"></div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-x-2" :class="isScrolled ? 'text-gray-900 dark:text-gray-100' : 'text-white'">
                        @if(Auth::user()->photo)
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="Profilová fotka">
                        @else
                            <div class="h-8 w-8 rounded-full bg-pink-500 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="text-sm font-light">{{ Auth::user()->name }}</span>
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 rounded-md bg-white dark:bg-slate-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-600 focus:outline-none">
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.nastenka') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">Nástenka</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">Nástenka</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">Odhlásiť sa</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('favorites.index') }}" class="transition-colors" :class="favoritesCount > 0 ? 'text-red-500 hover:text-red-600' : (isScrolled ? 'text-gray-900 hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300' : 'text-white hover:text-gray-300')">
                    <i class="ri-heart-3-fill size-5" x-show="favoritesCount > 0"></i>
                    <i class="ri-heart-3-line size-5" x-show="favoritesCount === 0"></i>
                </a>
                <div class="h-6 w-px" :class="isScrolled ? 'bg-gray-200 dark:bg-gray-600' : 'bg-gray-600'"></div>
                <a href="{{ route('login') }}" class="text-sm font-light" :class="isScrolled ? 'text-gray-900 hover:text-gray-600 dark:text-gray-100 dark:hover:text-gray-300' : 'text-white hover:text-gray-300'">Prihlásiť sa</a>
                <a href="{{ route('register') }}" class="text-sm font-light rounded-md bg-pink-500 px-3 py-2 text-white shadow-sm hover:bg-pink-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-500">Registrovať sa</a>
            @endauth
        </div>
    </nav>

    <!-- Mobile menu -->
    <div x-show="isOpen" class="lg:hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 z-50"></div>
        <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-slate-950 px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-white/10">
            <div class="flex items-center justify-between min-h-[48px]">
                <a href="{{ route('home') }}" class="-m-1.5 p-1.5 flex-1">
                    <span class="sr-only">{{ config('app.name') }}</span>
                    {{-- Mobile menu panel has a dark background, so the white logo is always used here --}}
                    <img class="h-8 w-auto" src="{{ asset('images/uploads/diskretne-dnes-logo-white.png') }}" alt="{{ config('app.name') }}">
                </a>
                <button @click="isOpen = false" type="button" class="p-2 rounded-md text-gray-400 hover:text-white flex-shrink-0 transition-colors">
                    <span class="sr-only">Zavrieť menu</span>
                    <i class="ri-close-line size-6"></i>
                </button>
            </div>
            <div class="mt-6 flow-root">
                <div class="-my-6 divide-y divide-gray-500/25">
                    <div class="space-y-2 py-6">
                        {{-- <a href="{{ route('erotic-clubs') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-light text-white hover:bg-slate-800">Erotické kluby</a> --}}
                        <a href="{{ route('tantra') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-light text-white hover:bg-slate-800">Tantra masáže</a>
                        <a href="{{ route('pricing') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-light text-white hover:bg-slate-800">Cenník</a>
                        <a href="{{ route('blog.index') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-light text-white hover:bg-slate-800">Blog</a>
                        <a href="{{ route('contact') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-light text-white hover:bg-slate-800">Kontakt</a>
                    </div>
                    <div class="py-6">
                        <!-- Dark Mode Toggle -->
                        <button 
                            @click="darkMode = !darkMode" 
                            class="flex items-center gap-x-2 text-white mb-4"
                        >
                            <i x-show="!darkMode" class="ri-sun-line size-5"></i>
                            <i x-show="darkMode" class="ri-moon-line size-5"></i>
                            <span class="text-sm font-light" x-text="darkMode ? '{{ __('app.dark_mode.dark_mode') }}' : '{{ __('app.dark_mode.light_mode') }}'"></span>
                        </button>

                        @guest
                            <a href="{{ route('login') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-light text-white hover:bg-slate-800">{{ __('app.navigation.login') }}</a>
                            <a href="{{ route('register') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-light text-white hover:bg-slate-800">{{ __('app.navigation.register') }}</a>
                        @endguest
                        @auth
                            <a href="{{ route('profile.edit') }}" class="-mx-3 flex rounded-lg px-3 py-2.5 text-base font-light text-white hover:bg-slate-800 items-center gap-x-2">
                                @if(Auth::user()->photo)
                                    <img class="h-5 w-5 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="Profilová fotka">
                                @else
                                    <div class="h-5 w-5 rounded-full bg-pink-500 flex items-center justify-center">
                                        <span class="text-xs font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                {{ Auth::user()->name }}
                            </a>
                            
                            <!-- Notifikácie v mobile menu -->
                            <a href="{{ route('notifications.index') }}" class="-mx-3 flex rounded-lg px-3 py-2.5 text-base font-light text-white hover:bg-slate-800 items-center gap-x-2">
                                <i class="ri-notification-line size-5"></i>
                                Notifikácie
                            </a>
                            
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.nastenka') }}" class="-mx-3 flex rounded-lg px-3 py-2.5 text-base font-light text-white hover:bg-slate-800 items-center gap-x-2">
                                    <i class="ri-dashboard-line size-5"></i>
                                    {{ __('app.navigation.dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="-mx-3 flex rounded-lg px-3 py-2.5 text-base font-light text-white hover:bg-slate-800 items-center gap-x-2">
                                    <i class="ri-dashboard-line size-5"></i>
                                    {{ __('app.navigation.dashboard') }}
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="-mx-3 flex rounded-lg px-3 py-2.5 text-base font-light text-white hover:bg-slate-800 w-full text-left items-center gap-x-2">
                                    <i class="ri-logout-box-line size-5"></i>
                                    {{ __('app.navigation.logout') }}
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</header> 