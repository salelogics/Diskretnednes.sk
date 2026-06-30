@extends('layouts.main')

@section('content')
<div class="bg-gray-900">
    <div class="relative isolate overflow-hidden pt-14">
        <img src="{{ $post->image_url ?: asset('images/uploads/hero-bg.jpg') }}" alt="" class="absolute inset-0 -z-10 size-full object-cover opacity-90">
        <div class="absolute inset-0 -z-10 bg-black/60"></div>
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-12 sm:py-14 lg:py-16">
                <div class="text-center">
                    <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">{{ $post->title }}</h1>
                    <p class="mt-6 text-lg leading-8 text-gray-300">{{ $post->excerpt }}</p>
                    <div class="mt-6 flex items-center justify-center gap-x-4 text-sm text-gray-400">
                        <time datetime="{{ $post->published_at->format('Y-m-d') }}">
                            {{ $post->published_at->format('d. F Y') }}
                        </time>
                        <span>•</span>
                        <span>{{ $post->author->name }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-950 py-24 sm:py-32 transition-colors duration-300">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-3xl">
            <div class="prose prose-lg prose-pink dark:prose-invert mx-auto" id="article-content">
                <!-- Obsah sa načíta cez JavaScript -->
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const contentElement = document.getElementById('article-content');
                const contentData = @json($post->content);
                const isWordPress = {{ $post->wp_id ? 'true' : 'false' }};
                
                try {
                    if (isWordPress) {
                        // WordPress článok - HTML obsah
                        if (typeof contentData === 'string' && contentData.trim()) {
                            contentElement.innerHTML = contentData;
                        } else {
                            contentElement.innerHTML = '<p>Obsah WordPress článku je prázdny.</p>';
                        }
                    } else {
                        // Lokálny článok - Editor.js JSON
                        const parsedData = typeof contentData === 'string' ? JSON.parse(contentData) : contentData;
                        
                        if (parsedData && parsedData.blocks) {
                            let htmlContent = '';
                            
                            parsedData.blocks.forEach(block => {
                                switch(block.type) {
                                    case 'paragraph':
                                        htmlContent += `<p>${block.data.text || ''}</p>`;
                                        break;
                                    case 'header':
                                        const level = block.data.level || 2;
                                        htmlContent += `<h${level}>${block.data.text || ''}</h${level}>`;
                                        break;
                                    case 'list':
                                        const listType = block.data.style === 'ordered' ? 'ol' : 'ul';
                                        const items = block.data.items.map(item => `<li>${item}</li>`).join('');
                                        htmlContent += `<${listType}>${items}</${listType}>`;
                                        break;
                                    case 'image':
                                        const imageUrl = block.data.file?.url || '';
                                        const caption = block.data.caption || '';
                                        htmlContent += `
                                            <figure>
                                                <img src="${imageUrl}" alt="${caption}" class="w-full rounded-lg">
                                                ${caption ? `<figcaption>${caption}</figcaption>` : ''}
                                            </figure>
                                        `;
                                        break;
                                    case 'quote':
                                        htmlContent += `
                                            <blockquote>
                                                <p>${block.data.text || ''}</p>
                                                ${block.data.caption ? `<cite>${block.data.caption}</cite>` : ''}
                                            </blockquote>
                                        `;
                                        break;
                                    default:
                                        // Pre neznáme typy blokov zobraz aspoň text
                                        if (block.data.text) {
                                            htmlContent += `<p>${block.data.text}</p>`;
                                        }
                                }
                            });
                            
                            contentElement.innerHTML = htmlContent;
                        } else {
                            contentElement.innerHTML = '<p>Obsah článku sa nepodarilo načítať.</p>';
                        }
                    }
                } catch (error) {
                    console.error('Error parsing article content:', error, 'Content:', contentData);
                    // Fallback - pokús sa zobraziť ako HTML
                    if (typeof contentData === 'string' && contentData.trim()) {
                        contentElement.innerHTML = contentData;
                    } else {
                        contentElement.innerHTML = '<p>Chyba pri načítavaní obsahu článku.</p>';
                    }
                }
            });
        </script>

        <div class="mx-auto max-w-3xl mt-16">
            <a href="{{ route('blog.index') }}" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                <span aria-hidden="true">&larr;</span> {{ __('app.blog.back_to_blog') }}
            </a>
        </div>
    </div>
</div>
@endsection 