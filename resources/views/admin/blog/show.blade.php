@extends('layouts.admin-dashboard')

@section('header', 'Detail článku')

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
                    <span class="text-gray-900 font-medium">Detail článku</span>
                </div>
                
                <!-- Nadpis -->
                <h1 class="text-3xl font-bold text-gray-900">{{ $article->title }}</h1>
                <p class="mt-2 text-gray-600">Detail článku</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.clanky.edit', $article) }}" class="inline-flex items-center px-4 py-2 bg-pink-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-pink-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Upraviť
                </a>
                <a href="{{ route('admin.clanky.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Späť na zoznam
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Hlavný obsah -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
                <!-- Hlavička článku -->
                <div class="px-6 py-6 border-b border-pink-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $article->title }}</h2>
                            @if($article->excerpt)
                                <p class="text-gray-600 mt-2">{{ $article->excerpt }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($article->is_published) bg-green-100 text-green-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ $article->is_published ? 'Publikovaný' : 'Koncept' }}
                        </span>
                    </div>
                </div>

                <!-- Hlavný obrázok -->
                @if($article->image_url)
                    <div class="px-6 py-4">
                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-64 object-cover rounded-lg">
                    </div>
                @endif

                <!-- Obsah článku -->
                <div class="px-6 py-6">
                    <div class="prose max-w-none">
                        @if($article->content)
                            <div id="article-content"></div>
                        @else
                            <p class="text-gray-500 italic">Článok nemá obsah.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar s informáciami -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
                <div class="px-6 py-6 border-b border-pink-100">
                    <h3 class="text-lg font-semibold text-gray-900">Informácie o článku</h3>
                </div>
                
                <div class="px-6 py-6 space-y-4">
                    <!-- Autor -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Autor</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->author->name }}</dd>
                    </div>

                    <!-- Slug -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">URL slug</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $article->slug }}</dd>
                    </div>

                    <!-- Stav -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stav</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($article->is_published) bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $article->is_published ? 'Publikovaný' : 'Koncept' }}
                            </span>
                        </dd>
                    </div>

                    <!-- Dátum vytvorenia -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vytvorené</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->created_at->format('d.m.Y H:i') }}</dd>
                    </div>

                    <!-- Dátum poslednej úpravy -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Posledná úprava</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $article->updated_at->format('d.m.Y H:i') }}</dd>
                    </div>

                    <!-- Dátum publikácie -->
                    @if($article->published_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Publikované</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $article->published_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    @endif

                    <!-- WordPress informácie -->
                    @if($article->wp_id)
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">WordPress Import</h4>
                            
                            <div class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">WordPress ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $article->wp_id }}</dd>
                                </div>

                                @if($article->wp_modified_at)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">WP posledná zmena</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $article->wp_modified_at->format('d.m.Y H:i') }}</dd>
                                    </div>
                                @endif

                                @if($article->wp_categories)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">WP kategórie</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ implode(', ', $article->wp_categories) }}</dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Typ obsahu</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            WordPress HTML
                                        </span>
                                    </dd>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="pt-4 border-t border-gray-200">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Typ obsahu</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Editor.js JSON
                                    </span>
                                </dd>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Akcie -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('admin.clanky.edit', $article) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-pink-600 hover:bg-pink-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Upraviť článok
                        </a>
                        
                        <form method="POST" action="{{ route('admin.clanky.destroy', $article) }}" onsubmit="return confirm('Naozaj chcete vymazať tento článok?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Vymazať článok
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Zobrazenie obsahu článku ak existuje
    const contentContainer = document.getElementById('article-content');
    const articleContent = @json($article->content ?? '');
    const isWordPress = {{ $article->wp_id ? 'true' : 'false' }};
    
    if (articleContent && contentContainer) {
        try {
            if (isWordPress) {
                // WordPress článok - HTML obsah
                if (typeof articleContent === 'string' && articleContent.trim()) {
                    contentContainer.innerHTML = articleContent;
                } else {
                    contentContainer.innerHTML = '<p class="text-gray-500 italic">WordPress článok nemá obsah.</p>';
                }
            } else {
                // Lokálny článok - Editor.js JSON
                const contentData = JSON.parse(articleContent);
                
                // Jednoduché zobrazenie Editor.js obsahu
                if (contentData.blocks && contentData.blocks.length > 0) {
                    let html = '';
                    contentData.blocks.forEach(block => {
                        switch(block.type) {
                            case 'paragraph':
                                html += `<p>${block.data.text || ''}</p>`;
                                break;
                            case 'header':
                                const level = block.data.level || 2;
                                html += `<h${level}>${block.data.text || ''}</h${level}>`;
                                break;
                            case 'list':
                                const listType = block.data.style === 'ordered' ? 'ol' : 'ul';
                                html += `<${listType}>`;
                                if (block.data.items) {
                                    block.data.items.forEach(item => {
                                        html += `<li>${item}</li>`;
                                    });
                                }
                                html += `</${listType}>`;
                                break;
                            case 'quote':
                                html += `<blockquote>${block.data.text || ''}</blockquote>`;
                                break;
                            case 'image':
                                if (block.data.file && block.data.file.url) {
                                    html += `<img src="${block.data.file.url}" alt="${block.data.caption || ''}" class="max-w-full h-auto">`;
                                    if (block.data.caption) {
                                        html += `<p class="text-sm text-gray-500 mt-2">${block.data.caption}</p>`;
                                    }
                                }
                                break;
                            default:
                                // Pre neznáme typy blokov zobrazíme raw text
                                if (block.data.text) {
                                    html += `<p>${block.data.text}</p>`;
                                }
                        }
                    });
                    contentContainer.innerHTML = html;
                } else {
                    contentContainer.innerHTML = '<p class="text-gray-500 italic">Článok nemá obsah.</p>';
                }
            }
        } catch (error) {
            console.error('Error parsing article content:', error, 'Content:', articleContent);
            // Fallback - pokús sa zobraziť ako HTML
            if (typeof articleContent === 'string' && articleContent.trim()) {
                contentContainer.innerHTML = articleContent;
            } else {
                contentContainer.innerHTML = '<p class="text-red-500">Chyba pri zobrazovaní obsahu článku.</p>';
            }
        }
    }
});
</script>
@endpush
@endsection 