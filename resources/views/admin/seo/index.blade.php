@extends('layouts.admin-dashboard')

@section('header', 'SEO Nastavenia')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">SEO Nastavenia</h1>
            <p class="text-gray-600 mt-1">Spravujte SEO nastavenia vašej stránky</p>
        </div>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('admin.seo.generate-sitemap') }}" class="inline">
                @csrf
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="ri-refresh-line mr-2"></i>
                    Generovať Sitemap
                </button>
            </form>
        </div>
    </div>
    
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="ri-check-circle-fill text-green-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Sitemap Section -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="ri-sitemap-line mr-2 text-blue-600"></i>
                Sitemap
            </h3>
            
            <div class="space-y-4">
                <!-- Sitemap Status -->
                <div class="flex items-center justify-between p-3 bg-white rounded-md border">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($seoSettings['sitemap_exists'])
                                <i class="ri-check-circle-fill text-green-500 text-lg"></i>
                            @else
                                <i class="ri-error-warning-fill text-orange-500 text-lg"></i>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">
                                Sitemap.xml
                            </p>
                            <p class="text-sm text-gray-500">
                                @if($seoSettings['sitemap_exists'])
                                    Sitemap existuje a je dostupná
                                @else
                                    Sitemap nebola nájdená
                                @endif
                            </p>
                        </div>
                    </div>
                    <div>
                        @if($seoSettings['sitemap_exists'])
                            <a href="{{ $seoSettings['sitemap_url'] }}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Zobraziť
                                <i class="ri-external-link-line ml-1"></i>
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">Nedostupná</span>
                        @endif
                    </div>
                </div>
                
                <!-- Sitemap URL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sitemap URL
                    </label>
                    <div class="flex">
                        <input type="text" 
                               value="{{ $seoSettings['sitemap_url'] }}" 
                               readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-gray-500 text-sm">
                        <button type="button" 
                                onclick="copyToClipboard('{{ $seoSettings['sitemap_url'] }}')"
                                class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 transition-colors">
                            <i class="ri-file-copy-line text-gray-600"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default SEO Settings -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="ri-seo-line mr-2 text-green-600"></i>
                Predvolené SEO nastavenia
            </h3>
            
            <form method="POST" action="{{ route('admin.seo.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <!-- Default Title -->
                    <div>
                        <label for="default_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Predvolený SEO Titulok
                            <span class="text-gray-400 text-xs">(max 60 znakov)</span>
                        </label>
                        <textarea name="default_title" 
                                id="default_title" 
                                rows="2"
                                maxlength="60"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('default_title') border-red-500 @enderror">{{ old('default_title', $seoSettings['default_title']) }}</textarea>
                        <div class="flex justify-between mt-1">
                            @error('default_title')
                                <p class="text-red-600 text-xs">{{ $message }}</p>
                            @enderror
                            <span class="text-xs text-gray-400 ml-auto" id="title-counter">0/60</span>
                        </div>
                    </div>

                    <!-- Default Description -->
                    <div>
                        <label for="default_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Predvolený SEO Popis
                            <span class="text-gray-400 text-xs">(max 160 znakov)</span>
                        </label>
                        <textarea name="default_description" 
                                id="default_description" 
                                rows="3"
                                maxlength="160"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('default_description') border-red-500 @enderror">{{ old('default_description', $seoSettings['default_description']) }}</textarea>
                        <div class="flex justify-between mt-1">
                            @error('default_description')
                                <p class="text-red-600 text-xs">{{ $message }}</p>
                            @enderror
                            <span class="text-xs text-gray-400 ml-auto" id="description-counter">0/160</span>
                        </div>
                    </div>

                    <!-- Default Image -->
                    <div>
                        <label for="default_image" class="block text-sm font-medium text-gray-700 mb-1">
                            Predvolený SEO Obrázok
                        </label>
                        
                        <!-- Current Image Preview -->
                        @if($seoSettings['default_image'])
                            <div class="mb-3 p-3 bg-white rounded-md border">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $seoSettings['default_image'] }}" 
                                         alt="SEO Image" 
                                         class="w-16 h-16 object-cover rounded">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Aktuálny obrázok</p>
                                        <p class="text-xs text-gray-500">{{ basename($seoSettings['default_image']) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <input type="file" 
                               name="default_image" 
                               id="default_image"
                               accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('default_image') border-red-500 @enderror">
                        @error('default_image')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            <i class="ri-save-line mr-2"></i>
                            Uložiť SEO nastavenia
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SEO Tips Section -->
    <div class="mt-8 bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
            <i class="ri-lightbulb-line mr-2"></i>
            SEO Tipy
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
            <div class="flex items-start space-x-2">
                <i class="ri-check-line text-blue-600 mt-0.5"></i>
                <p><strong>Titulok:</strong> Udržujte pod 60 znakmi pre optimálne zobrazenie v Google</p>
            </div>
            <div class="flex items-start space-x-2">
                <i class="ri-check-line text-blue-600 mt-0.5"></i>
                <p><strong>Popis:</strong> Ideálne 120-160 znakov pre najlepšie výsledky</p>
            </div>
            <div class="flex items-start space-x-2">
                <i class="ri-check-line text-blue-600 mt-0.5"></i>
                <p><strong>Sitemap:</strong> Pravidelne aktualizujte pre lepšie indexovanie</p>
            </div>
            <div class="flex items-start space-x-2">
                <i class="ri-check-line text-blue-600 mt-0.5"></i>
                <p><strong>Obrázok:</strong> Odporúčané rozmery 1200x630px pre sociálne médiá</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Character counters
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('default_title');
    const descriptionInput = document.getElementById('default_description');
    const titleCounter = document.getElementById('title-counter');
    const descriptionCounter = document.getElementById('description-counter');
    
    function updateCounter(input, counter, max) {
        const length = input.value.length;
        counter.textContent = `${length}/${max}`;
        
        if (length > max * 0.9) {
            counter.classList.add('text-orange-500');
        } else {
            counter.classList.remove('text-orange-500');
        }
        
        if (length > max) {
            counter.classList.add('text-red-500');
        } else {
            counter.classList.remove('text-red-500');
        }
    }
    
    titleInput.addEventListener('input', () => updateCounter(titleInput, titleCounter, 60));
    descriptionInput.addEventListener('input', () => updateCounter(descriptionInput, descriptionCounter, 160));
    
    // Initialize counters
    updateCounter(titleInput, titleCounter, 60);
    updateCounter(descriptionInput, descriptionCounter, 160);
});

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show temporary success message
        const button = event.target.closest('button');
        const icon = button.querySelector('i');
        const originalClass = icon.className;
        
        icon.className = 'ri-check-line text-green-600';
        setTimeout(() => {
            icon.className = originalClass;
        }, 2000);
    });
}
</script>
@endpush
@endsection