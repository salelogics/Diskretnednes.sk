<!DOCTYPE html>
<html class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stránka nenájdená</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Tag Manager -->
</head>
<body class="h-full">
    <main class="relative min-h-full flex items-center justify-center">
        <img src="{{ asset('images/uploads/hero-bg.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover object-top">
        <div class="absolute inset-0" style="background-color: rgb(0 0 0 / 59%);"></div>
        <div class="relative z-10 px-6 py-32 text-center sm:py-40 lg:px-8">
            <h1 class="text-balance text-5xl tracking-tight text-white sm:text-7xl">Stránka nenájdená</h1>
            <p class="mt-6 text-pretty text-lg text-white sm:text-xl/8">Ľutujeme, ale stránku ktorú hľadáte sa nepodarilo nájsť.</p>
            <div class="mt-10">
                <a href="{{ route('home') }}" class="text-sm/7 text-white hover:text-white/80 transition-colors">
                    <span aria-hidden="true">&larr;</span> Späť na domovskú stránku
                </a>
            </div>
        </div>
    </main>
</body>
</html> 