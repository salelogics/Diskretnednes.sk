@extends('layouts.user-dashboard')

@section('header', 'Profil')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Profil informácie -->
        <div class="space-y-8">
            <!-- Základné informácie -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <div class="flex items-center space-x-6 mb-6">
                    @if(auth()->user()->photo)
                        <img src="{{ auth()->user()->profile_photo_url }}" class="size-20 rounded-full object-cover" alt="Profilová fotka">
                    @else
                        <div class="size-20 rounded-full bg-pink-500/10 flex items-center justify-center">
                            <i class="ri-user-line text-3xl text-pink-500"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ auth()->user()->name }}</h2>
                        <p class="text-gray-600">{{ auth()->user()->email }}</p>
                        @if(auth()->user()->phone)
                            <p class="text-gray-600">{{ auth()->user()->phone }}</p>
                        @endif
                        @if(auth()->user()->city)
                            <p class="text-gray-600">{{ auth()->user()->city }}</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="text-center p-4 bg-pink-50 rounded-lg">
                        <div class="text-2xl font-bold text-pink-600">{{ auth()->user()->ads()->count() }}</div>
                        <div class="text-sm text-gray-600">Inzeráty</div>
                    </div>
                    <div class="text-center p-4 bg-pink-50 rounded-lg">
                        <div class="text-2xl font-bold text-pink-600">{{ auth()->user()->activeAds()->count() }}</div>
                        <div class="text-sm text-gray-600">Aktívne inzeráty</div>
                    </div>
                </div>
            </div>

            <!-- Užitočné odkazy -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Rýchle akcie</h3>
                <div class="space-y-4">
                    <a href="{{ route('ads.create') }}" class="flex items-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                        <div class="size-10 flex items-center justify-center rounded-full bg-pink-500/10 mr-4">
                            <i class="ri-add-line text-xl text-pink-500"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Pridať inzerát</div>
                            <div class="text-sm text-gray-600">Vytvorte nový inzerát</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('ads.index') }}" class="flex items-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                        <div class="size-10 flex items-center justify-center rounded-full bg-pink-500/10 mr-4">
                            <i class="ri-list-check text-xl text-pink-500"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Moje inzeráty</div>
                            <div class="text-sm text-gray-600">Spravujte svoje inzeráty</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('pricing-dashboard') }}" class="flex items-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                        <div class="size-10 flex items-center justify-center rounded-full bg-pink-500/10 mr-4">
                            <i class="ri-vip-crown-line text-xl text-pink-500"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Predplatné</div>
                            <div class="text-sm text-gray-600">Pozrite si naše balíky</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Formulár na úpravu profilu -->
        <div class="space-y-8">
            <!-- Úprava profilu -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Upraviť profil</h2>
                
                @if(session('status') === 'profile-updated')
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">Profil bol úspešne aktualizovaný.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <!-- Meno -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Meno</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">
                                    Váš email nie je overený.
                                    <button form="send-verification" class="underline text-pink-500 hover:text-pink-600">
                                        Kliknite tu pre opätovné odoslanie overovacieho emailu.
                                    </button>
                                </p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="w-full rounded-md bg-pink-500 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2">
                            Uložiť zmeny
                        </button>
                    </div>
                </form>
            </div>

            <!-- Rozšírené nastavenia -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Rozšírené nastavenia</h2>
                
                <form method="post" action="{{ route('profile.update.extended') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('put')

                    <!-- Profilová fotka -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700">Profilová fotka</label>
                        <div class="mt-2 flex items-center space-x-4">
                            @if(auth()->user()->photo)
                                <img src="{{ auth()->user()->profile_photo_url }}" class="size-12 rounded-full object-cover" alt="Profilová fotka">
                            @else
                                <div class="size-12 rounded-full bg-pink-500/10 flex items-center justify-center">
                                    <i class="ri-user-line text-xl text-pink-500"></i>
                                </div>
                            @endif
                            <input type="file" name="photo" id="photo" accept="image/*"
                                   class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none focus:border-pink-500">
                        </div>
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telefón -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Telefón</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('phone') border-red-300 @enderror"
                               placeholder="+421 xxx xxx xxx">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mesto -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">Mesto</label>
                        <input type="text" name="city" id="city" value="{{ old('city', auth()->user()->city) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('city') border-red-300 @enderror">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- O mne -->
                    <div>
                        <label for="about" class="block text-sm font-medium text-gray-700">O mne</label>
                        <textarea name="about" id="about" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('about') border-red-300 @enderror"
                                  placeholder="Napíšte pár viet o sebe...">{{ old('about', auth()->user()->about) }}</textarea>
                        @error('about')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="w-full rounded-md bg-pink-500 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2">
                            Uložiť rozšírené nastavenia
                        </button>
                    </div>
                </form>
            </div>

            <!-- Zmena hesla -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Zmeniť heslo</h2>
                
                <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <!-- Aktuálne heslo -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Aktuálne heslo</label>
                        <input type="password" name="current_password" id="current_password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('current_password', 'updatePassword') border-red-300 @enderror">
                        @error('current_password', 'updatePassword')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nové heslo -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Nové heslo</label>
                        <input type="password" name="password" id="password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('password', 'updatePassword') border-red-300 @enderror">
                        @error('password', 'updatePassword')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Potvrdenie hesla -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Potvrďte heslo</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('password_confirmation', 'updatePassword') border-red-300 @enderror">
                        @error('password_confirmation', 'updatePassword')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" class="w-full rounded-md bg-pink-500 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2">
                            Zmeniť heslo
                        </button>
                    </div>
                </form>
            </div>

            <!-- Vymazanie účtu -->
            <div class="border border-red-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <h2 class="text-2xl font-semibold text-red-600 mb-6">Vymazať účet</h2>
                <p class="text-gray-600 mb-6">
                    Po vymazaní vášho účtu budú všetky vaše údaje a inzeráty trvalo odstránené. Pred vymazaním si stiahnite všetky údaje alebo informácie, ktoré si chcete ponechať.
                </p>
                
                <button type="button" onclick="confirmDelete()" class="w-full rounded-md bg-red-500 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Vymazať účet
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden forms -->
<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<!-- Delete confirmation modal -->
<div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Vymazať účet</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Ste si istí, že chcete vymazať svoj účet? Táto akcia je nevratná.
                </p>
                <form method="post" action="{{ route('profile.destroy') }}" class="mt-4">
                    @csrf
                    @method('delete')
                    <input type="password" name="password" placeholder="Zadajte heslo pre potvrdenie" required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 mb-4">
                    <div class="flex gap-3">
                        <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Zrušiť
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                            Vymazať
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}
</script>
@endsection
