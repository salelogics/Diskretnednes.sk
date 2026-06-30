@extends('layouts.admin-dashboard')

@section('header', 'Upraviť článok')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

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
                    <span class="text-gray-900 font-medium">Upraviť článok</span>
                </div>
                
                <!-- Nadpis -->
                <h1 class="text-3xl font-bold text-gray-900">Upraviť článok</h1>
                <p class="mt-2 text-gray-600">Upravte obsah článku</p>
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
                    <h3 class="text-xl font-semibold text-gray-900">Upraviť článok</h3>
                    <p class="text-sm text-gray-500 mt-1">Upravte obsah článku</p>
                </div>
                <a href="{{ route('admin.clanky.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Späť na zoznam
                </a>
            </div>
        </div>

        <form action="{{ route('admin.clanky.update', $article) }}" method="POST" enctype="multipart/form-data" id="article-form">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <!-- Chybové hlásenia -->
                @if($errors->has('general'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ $errors->first('general') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Názov článku -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Názov článku</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" 
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
                              placeholder="Krátky popis článku...">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hlavný obrázok -->
                <div>
                    <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Hlavný obrázok (voliteľné)</label>
                    @if($article->image_url)
                        <div class="mb-3">
                            <img src="{{ $article->image_url }}" alt="Aktuálny obrázok" class="w-32 h-32 object-cover rounded-lg">
                            <p class="text-sm text-gray-500 mt-1">Aktuálny obrázok {{ $article->is_external_image ? '(externý)' : '(lokálny)' }}</p>
                        </div>
                    @endif
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
                    <input type="hidden" name="content" id="content-input" value="{{ old('content', $article->content) }}">
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stav -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Stav</label>
                    <select name="status" id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 @error('status') border-red-500 @enderror">
                        <option value="draft" {{ old('status', $article->is_published ? 'published' : 'draft') === 'draft' ? 'selected' : '' }}>Koncept</option>
                        <option value="published" {{ old('status', $article->is_published ? 'published' : 'draft') === 'published' ? 'selected' : '' }}>Publikovaný</option>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Uložiť zmeny
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    // Inicializácia Editor.js s existujúcim obsahom
    const editor = new EditorJSComponent('editorjs');
    
    // Načítanie existujúceho obsahu
    const existingContent = document.getElementById('content-input').value;
    if (existingContent && existingContent.trim() !== '') {
        try {
            const contentData = JSON.parse(existingContent);
            console.log('Loading existing content:', contentData);
            
            // Čakáme kým bude editor pripravený pred renderovaním
            await editor.isReady();
            await editor.render(contentData);
            
        } catch (error) {
            console.error('Error parsing existing content:', error);
            // Ak sa nepodarí parsovať JSON, skúsime text ako plain content
            if (existingContent.trim().length > 0) {
                try {
                    await editor.isReady();
                    await editor.render({
                        blocks: [{
                            type: 'paragraph',
                            data: {
                                text: existingContent
                            }
                        }]
                    });
                } catch (renderError) {
                    console.error('Error rendering fallback content:', renderError);
                }
            }
        }
    } else {
        console.log('No existing content found, editor will be empty');
    }
    
    // Uloženie obsahu pred odoslaním formulára
    document.getElementById('article-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
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