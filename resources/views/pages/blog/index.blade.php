@extends('layouts.main')

@section('content')
<div class="bg-gray-900">
    <div class="relative isolate overflow-hidden pt-14">
        <img src="{{ asset('images/uploads/hero-bg.jpg') }}" alt="" class="absolute inset-0 -z-10 size-full object-cover opacity-90">
        <div class="absolute inset-0 -z-10 bg-black/60"></div>
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-12 sm:py-14 lg:py-16">
                <div class="text-center">
                    <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">{{ __('app.navigation.blog') }}</h1>
                    <p class="mt-6 text-lg leading-8 text-gray-300">Najnovšie články, rady a tipy zo sveta erotiky.</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-950 py-8 sm:py-12 transition-colors duration-300">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-3">
            @foreach($posts as $post)
                <article class="relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 min-h-[450px] sm:min-h-[400px] lg:min-h-[470px]">
                    <img src="{{ $post->image_url ?: asset('images/uploads/hero-bg.jpg') }}" alt="{{ $post->title }}" class="absolute inset-0 -z-10 size-full object-cover">
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40"></div>
                    <div class="absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10"></div>

                    <div class="flex flex-wrap items-center gap-y-1 overflow-hidden text-sm/6 text-gray-300">
                        <time datetime="{{ $post->published_at->format('Y-m-d') }}" class="mr-8">{{ $post->published_at->format('d. F Y') }}</time>
                    </div>
                    <h3 class="mt-3 text-lg/6 font-semibold text-white">
                        <a href="{{ route('blog.show', $post) }}">
                            <span class="absolute inset-0"></span>
                            {{ $post->title }}
                        </a>
                    </h3>
                </article>
            @endforeach
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8 mt-16">
        {{ $posts->links() }}
    </div>
</div>
@endsection 