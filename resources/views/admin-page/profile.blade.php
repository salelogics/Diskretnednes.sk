@extends('layouts.admin-dashboard')

@section('header', 'Admin Profil')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Admin informácie -->
        <div class="space-y-8">
            <!-- Základné informácie -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <div class="flex items-center space-x-6 mb-6">
                    @if(auth()->user()->photo)
                        <img src="{{ auth()->user()->profile_photo_url }}" class="size-20 rounded-full object-cover" alt="Profilová fotka">
                    @else
                        <div class="size-20 rounded-full bg-pink-500/10 flex items-center justify-center">
                            <i class="ri-shield-user-line text-3xl text-pink-500"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ auth()->user()->name }}</h2>
                        <p class="text-gray-600">{{ auth()->user()->email }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                            Administrátor
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="text-center p-4 bg-pink-50 rounded-lg">
                        <div class="text-2xl font-bold text-pink-600">{{ \App\Models\User::count() }}</div>
                        <div class="text-sm text-gray-600">Používatelia</div>
                    </div>
                    <div class="text-center p-4 bg-pink-50 rounded-lg">
                        <div class="text-2xl font-bold text-pink-600">{{ \App\Models\Ad::count() }}</div>
                        <div class="text-sm text-gray-600">Inzeráty</div>
                    </div>
                </div>
            </div>

            <!-- Rýchle akcie -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Administrácia</h3>
                <div class="space-y-4">
                    <a href="{{ route('admin.pouzivatelia.index') }}" class="flex items-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                        <div class="size-10 flex items-center justify-center rounded-full bg-pink-500/10 mr-4">
                            <i class="ri-user-settings-line text-xl text-pink-500"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Používatelia</div>
                            <div class="text-sm text-gray-600">Spravujte používateľov</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.inzeraty.index') }}" class="flex items-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                        <div class="size-10 flex items-center justify-center rounded-full bg-pink-500/10 mr-4">
                            <i class="ri-advertisement-line text-xl text-pink-500"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Inzeráty</div>
                            <div class="text-sm text-gray-600">Spravujte inzeráty</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.statistiky.index') }}" class="flex items-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                        <div class="size-10 flex items-center justify-center rounded-full bg-pink-500/10 mr-4">
                            <i class="ri-bar-chart-line text-xl text-pink-500"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Štatistiky</div>
                            <div class="text-sm text-gray-600">Pozrite si analýzy</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.platby.index') }}" class="flex items-center p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition">
                        <div class="size-10 flex items-center justify-center rounded-full bg-pink-500/10 mr-4">
                            <i class="ri-money-dollar-circle-line text-xl text-pink-500"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Platby</div>
                            <div class="text-sm text-gray-600">Spravujte platby</div>
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
                
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Meno -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Meno</label>
                        <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                
                <form method="POST" action="{{ route('admin.profile.update.extended') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

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
                
                <form method="post" action="{{ route('admin.password.update') }}" class="space-y-6">
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
        </div>
    </div>
</div>
@endsection 