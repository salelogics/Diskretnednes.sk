@extends('layouts.admin-dashboard')

@section('header', 'Pridať klub')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Breadcrumbs a header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <!-- Breadcrumbs -->
                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-3">
                    <a href="{{ route('admin.nastenka') }}" class="hover:text-pink-600 transition-colors">Admin</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="{{ route('admin.kluby.index') }}" class="hover:text-pink-600 transition-colors">Kluby</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Pridať klub</span>
                </div>
                
                <!-- Nadpis -->
                <h1 class="text-3xl font-bold text-gray-900">Pridať nový klub</h1>
                <p class="mt-2 text-gray-600">Vytvorte nový erotický klub v systéme</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.kluby.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Späť na zoznam
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Nový klub</h3>
                    <p class="text-sm text-gray-500 mt-1">Vyplňte informácie o novom klube</p>
                </div>
            </div>
        </div>
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800 border border-red-300">
            <strong>Chyba:</strong> {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-yellow-100 px-4 py-3 text-yellow-800 border border-yellow-300">
            <strong>Chyby vo formulári:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <form method="POST" action="{{ route('admin.kluby.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Názov klubu</label>
            <input type="text" name="name" id="name" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('name') }}">
            @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Popis</label>
            <textarea name="description" id="description" rows="4" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400">{{ old('description') }}</textarea>
            @error('description') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresa</label>
                <input type="text" name="address" id="address" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('address') }}">
                @error('address') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefón</label>
                <input type="tel" name="phone" id="phone" required placeholder="+421 905 905 905" pattern="^\+421 ?9[0-9]{2} ?[0-9]{3} ?[0-9]{3}$" maxlength="17" autocomplete="tel" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('phone') }}">
                @error('phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400 font-normal text-xs">(nepovinné)</span></label>
                <input type="email" name="email" id="email" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('email') }}">
                @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Webová stránka <span class="text-gray-400 font-normal text-xs">(nepovinné)</span></label>
                <input type="url" name="website" id="website" placeholder="https://example.com" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('website') }}">
                @error('website') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div x-data="{
                logoPreview: null,
                updatePreview(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (ev) => this.logoPreview = ev.target.result;
                        reader.readAsDataURL(file);
                    } else {
                        this.logoPreview = null;
                    }
                }
            }">
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo (voliteľné)</label>
                <input type="file" name="logo" id="logo" accept="image/*" @change="updatePreview" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <template x-if="logoPreview">
                    <img :src="logoPreview" alt="Náhľad loga" class="mt-2 h-12 w-12 object-cover rounded">
                </template>
                @error('logo') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div x-data="{
                imagePreview: null,
                updatePreview(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (ev) => this.imagePreview = ev.target.result;
                        reader.readAsDataURL(file);
                    } else {
                        this.imagePreview = null;
                    }
                }
            }">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Hlavný obrázok</label>
                <input type="file" name="image" id="image" accept="image/*" required @change="updatePreview" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <template x-if="imagePreview">
                    <img :src="imagePreview" alt="Náhľad obrázka" class="mt-2 h-12 w-12 object-cover rounded">
                </template>
                @error('image') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="hours_weekdays" class="block text-sm font-medium text-gray-700 mb-1">Otváracie hodiny (Po-Štv) <span class='text-gray-400 font-normal text-xs'>(nepovinné)</span></label>
                <input type="text" name="hours_weekdays" id="hours_weekdays" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('hours_weekdays') }}">
                @error('hours_weekdays') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="hours_weekend" class="block text-sm font-medium text-gray-700 mb-1">Otváracie hodiny (Pia-So) <span class='text-gray-400 font-normal text-xs'>(nepovinné)</span></label>
                <input type="text" name="hours_weekend" id="hours_weekend" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('hours_weekend') }}">
                @error('hours_weekend') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="hours_sunday" class="block text-sm font-medium text-gray-700 mb-1">Otváracie hodiny (Nedeľa) <span class='text-gray-400 font-normal text-xs'>(nepovinné)</span></label>
                <input type="text" name="hours_sunday" id="hours_sunday" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('hours_sunday') }}">
                @error('hours_sunday') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div x-data="{
            services: ['strip', 'bar', 'escort', 'jacuzzi'],
            newService: '',
            addService() {
                if (this.newService && !this.services.includes(this.newService)) {
                    this.services.push(this.newService)
                    this.newService = ''
                }
            },
            removeService(idx) {
                this.services.splice(idx, 1)
            }
        }">
            <label class="block text-sm font-medium text-gray-700 mb-2">Služby (voliteľné)</label>
            <div class="flex flex-wrap gap-4">
                <template x-for="(service, idx) in services" :key="service">
                    <div class="flex items-center gap-2 bg-gray-50 px-2 py-1 rounded">
                        <input type="checkbox" :id="'service_' + idx" :value="service" name="services[]" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <label :for="'service_' + idx" class="text-sm text-gray-700" x-text="service"></label>
                        <button type="button" @click="removeService(idx)" class="ml-1 text-red-500 hover:text-red-700 text-xs">&times;</button>
                    </div>
                </template>
            </div>
            <div class="mt-4 flex gap-2">
                <input type="text" x-model="newService" @keyup.enter.prevent="addService()" placeholder="Pridať novú službu..." class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400">
                <button type="button" @click="addService()" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition">Pridať</button>
            </div>
            <p class="mt-2 text-xs text-gray-500">Zaškrtnite všetky služby, ktoré klub ponúka. Môžete pridať aj vlastné.</p>
        </div>
        <div class="flex items-center gap-4" x-data="{ isActive: {{ old('is_active', true) ? 'true' : 'false' }} }">
            <span class="text-sm text-gray-700">Stav:</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="is_active" name="is_active" class="sr-only peer" x-model="isActive">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:bg-indigo-600 transition"></div>
                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition peer-checked:translate-x-5"></div>
            </label>
            <span class="ml-2 text-sm text-gray-700" x-text="isActive ? 'Aktívny' : 'Neaktívny'"></span>
        </div>
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.kluby.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Zrušiť
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Pridať klub
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 