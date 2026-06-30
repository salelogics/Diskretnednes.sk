@extends('layouts.admin-dashboard')

@section('header', 'Pridať nového používateľa')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Nový používateľ</h3>
                    <p class="text-sm text-gray-500 mt-1">Vytvorte nový používateľský alebo admin účet</p>
                </div>
                <a href="{{ route('admin.pouzivatelia.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Späť na zoznam
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pouzivatelia.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Meno -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Meno <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 @error('email') border-red-500 @enderror"
                       required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Heslo -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Heslo <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       id="password" 
                       name="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 @error('password') border-red-500 @enderror"
                       required>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Potvrdenie hesla -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Potvrdenie hesla <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500"
                       required>
            </div>

            <!-- Nastavenia účtu -->
            <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                <h4 class="text-sm font-medium text-gray-900">Nastavenia účtu</h4>
                
                <!-- Admin práva -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_admin" 
                           name="is_admin" 
                           value="1"
                           {{ old('is_admin') ? 'checked' : '' }}
                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="is_admin" class="ml-2 block text-sm text-gray-700">
                        Administrátorské práva
                    </label>
                </div>

                <!-- Overený email -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="email_verified" 
                           name="email_verified" 
                           value="1"
                           {{ old('email_verified', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="email_verified" class="ml-2 block text-sm text-gray-700">
                        Email je overený (používateľ nemusí potvrdzovať email)
                    </label>
                </div>
            </div>

            <!-- Tlačidlá -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.pouzivatelia.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    Zrušiť
                </a>
                <button type="submit" 
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Vytvoriť používateľa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 