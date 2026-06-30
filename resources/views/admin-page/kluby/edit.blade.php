@extends('layouts.admin-dashboard')

@section('content')
<div class="max-w-4xl w-full mx-auto px-4 py-8 sm:px-8 lg:px-12">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Upraviť erotický klub</h1>
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
    <form method="POST" action="{{ route('admin.kluby.update', $club) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Názov klubu</label>
            <input type="text" name="name" id="name" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('name', $club->name) }}">
            @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Popis</label>
            <textarea name="description" id="description" rows="4" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400">{{ old('description', $club->description) }}</textarea>
            @error('description') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Adresa</label>
                <input type="text" name="address" id="address" required class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('address', $club->address) }}">
                @error('address') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefón</label>
                <input type="tel" name="phone" id="phone" required placeholder="+421 905 905 905" pattern="^\+421 ?9[0-9]{2} ?[0-9]{3} ?[0-9]{3}$" maxlength="17" autocomplete="tel" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('phone', $club->phone) }}">
                @error('phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400 font-normal text-xs">(nepovinné)</span></label>
                <input type="email" name="email" id="email" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('email', $club->email) }}">
                @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Webová stránka <span class="text-gray-400 font-normal text-xs">(nepovinné)</span></label>
                <input type="url" name="website" id="website" placeholder="https://example.com" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('website', $club->website) }}">
                @error('website') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div x-data="{
                logoPreview: @if($club->logo_path && !empty($club->logo_path)) '{{ asset($club->logo_path) }}' @else null @endif,
                updatePreview(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (ev) => this.logoPreview = ev.target.result;
                        reader.readAsDataURL(file);
                    } else {
                        this.logoPreview = @if($club->logo_path && !empty($club->logo_path)) '{{ asset($club->logo_path) }}' @else null @endif;
                    }
                }
            }">
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo (voliteľné)</label>
                <input type="file" name="logo" id="logo" accept="image/*" @change="updatePreview" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <template x-if="logoPreview">
                    <img :src="logoPreview" alt="Náhľad loga" class="mt-2 h-12 w-12 object-cover rounded">
                </template>
                @if($club->logo_path && !empty($club->logo_path))
                    <p class="mt-1 text-xs text-gray-500">Aktuálne logo: {{ basename($club->logo_path) }}</p>
                @endif
                @error('logo') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div x-data="{
                imagePreview: @if($club->image_path && !empty($club->image_path)) '{{ asset(str_starts_with($club->image_path, 'storage/') ? $club->image_path : 'storage/'.ltrim($club->image_path,'/')) }}' @else null @endif,
                updatePreview(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (ev) => this.imagePreview = ev.target.result;
                        reader.readAsDataURL(file);
                    } else {
                        this.imagePreview = @if($club->image_path && !empty($club->image_path)) '{{ asset(str_starts_with($club->image_path, 'storage/') ? $club->image_path : 'storage/'.ltrim($club->image_path,'/')) }}' @else null @endif;
                    }
                }
            }">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Hlavný obrázok</label>
                <input type="file" name="image" id="image" accept="image/*" @change="updatePreview" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <template x-if="imagePreview">
                    <img :src="imagePreview" alt="Náhľad obrázka" class="mt-2 h-12 w-12 object-cover rounded">
                </template>
                @if($club->image_path && !empty($club->image_path))
                    <p class="mt-1 text-xs text-gray-500">Aktuálny obrázok: {{ basename($club->image_path) }}</p>
                @endif
                @error('image') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="hours_weekdays" class="block text-sm font-medium text-gray-700 mb-1">Otváracie hodiny (Po-Štv) <span class='text-gray-400 font-normal text-xs'>(nepovinné)</span></label>
                <input type="text" name="hours_weekdays" id="hours_weekdays" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('hours_weekdays', $club->hours_weekdays) }}">
                @error('hours_weekdays') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="hours_weekend" class="block text-sm font-medium text-gray-700 mb-1">Otváracie hodiny (Pia-So) <span class='text-gray-400 font-normal text-xs'>(nepovinné)</span></label>
                <input type="text" name="hours_weekend" id="hours_weekend" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('hours_weekend', $club->hours_weekend) }}">
                @error('hours_weekend') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="hours_sunday" class="block text-sm font-medium text-gray-700 mb-1">Otváracie hodiny (Nedeľa) <span class='text-gray-400 font-normal text-xs'>(nepovinné)</span></label>
                <input type="text" name="hours_sunday" id="hours_sunday" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition placeholder:text-gray-400" value="{{ old('hours_sunday', $club->hours_sunday) }}">
                @error('hours_sunday') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
        <div x-data="{
            services: @json(old('services', $club->services ?? [])),
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
                        <input type="checkbox" :id="'service_' + idx" :value="service" name="services[]" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" :checked="true">
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
        <div class="flex items-center gap-4" x-data="{ isActive: {{ old('is_active', $club->is_active) ? 'true' : 'false' }} }">
            <span class="text-sm text-gray-700">Stav:</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="is_active" name="is_active" class="sr-only peer" x-model="isActive">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:bg-indigo-600 transition"></div>
                <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition peer-checked:translate-x-5"></div>
            </label>
            <span class="ml-2 text-sm text-gray-700" x-text="isActive ? 'Aktívny' : 'Neaktívny'"></span>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.kluby.index') }}" class="inline-flex items-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition">Zrušiť</a>
            <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition">Uložiť zmeny</button>
        </div>
    </form>
</div>
@endsection 