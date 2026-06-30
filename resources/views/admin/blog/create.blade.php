@extends('layouts.admin-dashboard')

@section('header', 'Vytvoriť článok')

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
                    <a href="{{ route('admin.clanky.index') }}" class="hover:text-pink-600 transition-colors">Články</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Vytvoriť článok</span>
                </div>
                
                <!-- Nadpis -->
                <h1 class="text-3xl font-bold text-gray-900">Vytvoriť nový článok</h1>
                <p class="mt-2 text-gray-600">Napíšte nový článok pre blog</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.clanky.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
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
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Nový článok</h3>
                    <p class="text-sm text-gray-500 mt-1">Vytvorte nový článok pre blog</p>
                </div>
                <a href="{{ route('admin.clanky.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Späť na zoznam
                </a>
            </div>
        </div>

        <form action="{{ route('admin.clanky.store') }}" method="POST" enctype="multipart/form-data" id="article-form">
            @csrf
            
            @if ($errors->has('general'))
                <div class="mx-6 mt-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <strong>Chyba:</strong> {{ $errors->first('general') }}
                </div>
            @endif
            
            <div class="p-6 space-y-6">
                <!-- Názov článku -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Názov článku</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 @error('title') border-red-500 @enderror"
                           placeholder="Zadajte názov článku..." required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Úvodný text -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Úvodný text (voliteľné)</label>
                    <textarea name="excerpt" id="excerpt" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 @error('excerpt') border-red-500 @enderror"
                              placeholder="Krátky popis článku...">{{ old('excerpt', '') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hlavný obrázok -->
                <div>
                    <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Hlavný obrázok (voliteľné)</label>
                    <input type="file" name="featured_image" id="featured_image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 @error('featured_image') border-red-500 @enderror">
                    @error('featured_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Editor.js obsah -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Obsah článku</label>
                    <div class="border border-gray-300 rounded-md">
                        <div id="editorjs" class="min-h-[400px] p-4"></div>
                    </div>
                    <input type="hidden" name="content" id="content-input" value="{{ old('content') }}">
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stav -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Stav</label>
                    <select name="status" id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Koncept</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Publikovaný</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tlačidlá -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.clanky.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                    Zrušiť
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Vytvoriť článok
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializácia Editor.js
    const editor = new EditorJSComponent('editorjs');
    
    // Uloženie obsahu pred odoslaním formulára
    document.getElementById('article-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            // Čakáme kým bude editor pripravený
            await editor.isReady();
            
            const outputData = await editor.save();
            console.log('Editor content to save:', outputData);
            
            // Overíme, že máme nejaký obsah
            if (outputData && outputData.blocks && outputData.blocks.length > 0) {
                document.getElementById('content-input').value = JSON.stringify(outputData);
            } else {
                // Ak nemáme obsah, nastavíme prázdny JSON
                document.getElementById('content-input').value = JSON.stringify({blocks: []});
            }
            
            // Odošleme formulár
            this.submit();
        } catch (error) {
            console.error('Saving failed: ', error);
            // Skúsime uložiť aspoň prázdny obsah
            document.getElementById('content-input').value = JSON.stringify({blocks: []});
            alert('Chyba pri ukladaní obsahu editora, ale formulár sa odošle s prázdnym obsahom.');
            this.submit();
        }
    });
});
</script>
@endpush
@endsection 