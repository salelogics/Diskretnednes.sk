<!DOCTYPE html>
<html class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Reset hesla') }}</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Tag Manager -->
</head>
<body class="h-full">
    <div class="flex min-h-full">
        <!-- Obrazok na lavej strane -->
        <div class="relative w-0 flex-1 hidden lg:block">
            <img class="absolute inset-0 size-full object-cover" src="{{ asset('images/uploads/auth-img.jpg') }}" alt="Erotikon background">
        </div>

        <!-- Formular na pravej strane -->
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <img class="h-10 w-auto" src="{{ asset('images/uploads/erotikon-logo.webp') }}" alt="{{ config('app.name') }}">
                    <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900">Reset hesla</h2>
                    <p class="mt-2 text-sm/6 text-gray-500">
                        Zadajte nové heslo pre váš účet.
                    </p>
                </div>

                <div class="mt-10">
                    <div>
                        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                            @csrf

                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div>
                                <label for="email" class="block text-sm/6 font-medium text-gray-900">Emailová adresa</label>
                                <div class="mt-2">
                                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', $request->email) }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm/6 font-medium text-gray-900">Nové heslo</label>
                                <div class="mt-2">
                                    <input id="password" name="password" type="password" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-900">Potvrďte nové heslo</label>
                                <div class="mt-2">
                                    <input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Resetovať heslo</button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-10">
                        <div class="relative flex justify-center text-sm/6 font-medium">
                            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Späť na prihlásenie</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
