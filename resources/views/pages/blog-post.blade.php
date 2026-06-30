@extends('layouts.main')

@section('content')
<div class="bg-gray-900">
    <div class="relative isolate overflow-hidden pt-14">
        <img src="{{ $post->image }}" alt="{{ $post->title }}" class="absolute inset-0 -z-10 size-full object-cover opacity-90">
        <div class="absolute inset-0 -z-10 bg-black/60"></div>
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-12 sm:py-14 lg:py-16">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-x-2 text-xs leading-6 text-gray-300">
                        <time datetime="{{ $post->published_at->format('Y-m-d') }}">{{ $post->published_at->format('d. F Y') }}</time>
                        <div class="h-4 w-px bg-gray-400"></div>
                        <div>{{ $post->category }}</div>
                    </div>
                    <h1 class="mt-6 text-4xl font-semibold tracking-tight text-white sm:text-5xl">{{ $post->title }}</h1>
                    @if($post->excerpt)
                        <p class="mt-6 text-lg leading-8 text-gray-300">{{ $post->excerpt }}</p>
                    @endif
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
            <div class="prose prose-lg prose-pink dark:prose-invert mx-auto">
                {!! $post->content !!}
            </div>

            <!-- Tlačidlá na zdieľanie -->
            <div class="mt-8 flex items-center gap-x-6">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Zdieľať článok:</span>
                <div class="flex gap-x-4">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                        <span class="sr-only">Zdieľať na Facebooku</span>
                        <i class="ri-facebook-circle-fill text-2xl"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $post->title }}" target="_blank" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                        <span class="sr-only">Zdieľať na X (Twitter)</span>
                        <i class="ri-twitter-x-fill text-2xl"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}&title={{ $post->title }}" target="_blank" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                        <span class="sr-only">Zdieľať na LinkedIn</span>
                        <i class="ri-linkedin-box-fill text-2xl"></i>
                    </a>
                    <button onclick="navigator.clipboard.writeText(window.location.href)" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                        <span class="sr-only">Kopírovať odkaz</span>
                        <i class="ri-link text-2xl"></i>
                    </button>
                </div>
            </div>

            @if($post->tags)
                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <span class="inline-flex items-center rounded-full bg-gray-50 dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Súvisiace články -->
            <div class="mt-24">
                <div class="mx-auto max-w-2xl lg:mx-0">
                    <h2 class="text-pretty text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">Súvisiace články</h2>
                    <p class="mt-2 text-lg/8 text-gray-600 dark:text-gray-400">Prečítajte si ďalšie zaujímavé články z našej sekcie {{ $post->category }}.</p>
                </div>
                <div class="mx-auto mt-10 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 border-t border-gray-200 dark:border-gray-700 pt-10 sm:mt-16 sm:pt-16 lg:mx-0 lg:max-w-none lg:grid-cols-2">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="flex max-w-xl flex-col items-start justify-between">
                            <div class="flex items-center gap-x-4 text-xs">
                                <time datetime="{{ $relatedPost->published_at->format('Y-m-d') }}" class="text-gray-500 dark:text-gray-400">
                                    {{ $relatedPost->published_at->format('d. F Y') }}
                                </time>
                                <span class="relative z-10 rounded-full bg-gray-50 dark:bg-gray-800 px-3 py-1.5 font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    {{ $relatedPost->category }}
                                </span>
                            </div>
                            <div class="group relative">
                                <h3 class="mt-3 text-lg/6 font-semibold text-gray-900 dark:text-gray-100 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                    <a href="{{ route('blog.show', $relatedPost->slug) }}">
                                        <span class="absolute inset-0"></span>
                                        {{ $relatedPost->title }}
                                    </a>
                                </h3>
                                <p class="mt-5 line-clamp-3 text-sm/6 text-gray-600 dark:text-gray-400">{{ $relatedPost->excerpt }}</p>
                            </div>
                            <div class="relative mt-8 flex items-center gap-x-4">
                                <img src="{{ $relatedPost->author_image ?? asset('images/default-avatar.jpg') }}" alt="" class="size-10 rounded-full bg-gray-50 dark:bg-gray-800">
                                <div class="text-sm/6">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">
                                        <span>{{ $relatedPost->author_name ?? 'Erotikon' }}</span>
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $relatedPost->author_role ?? 'Redaktor' }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 