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
                    <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">{{ __('app.navigation.pricing') }}</h1>
                    <p class="mt-6 text-lg leading-8 text-gray-300">Vyberte si cenovo dostupný balík s najlepšími funkciami pre oslovenie vašej cieľovej skupiny, vytvorenie lojality zákazníkov a zvýšenie predaja.</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </div>
</div>

<div class="relative isolate bg-white dark:bg-slate-950 px-6 py-24 sm:py-32 lg:px-8 transition-colors duration-300">
    <div class="absolute inset-x-0 -top-3 -z-10 transform-gpu overflow-hidden px-36 blur-3xl" aria-hidden="true">
        <div class="mx-auto aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>

    <div class="mx-auto max-w-4xl text-center">
        <h2 class="text-base/7 font-semibold text-indigo-600 dark:text-indigo-400">{{ __('app.navigation.pricing') }}</h2>
        <p class="mt-2 text-balance text-5xl font-semibold tracking-tight text-gray-900 dark:text-gray-100 sm:text-6xl">Vyberte si ideálny balík pre vás</p>
    </div>

    <p class="mx-auto mt-6 max-w-2xl text-pretty text-center text-lg font-medium text-gray-600 dark:text-gray-400 sm:text-xl/8">
        Vyberte si cenovo dostupný balík s najlepšími funkciami pre oslovenie vašej cieľovej skupiny, vytvorenie lojality zákazníkov a zvýšenie predaja.
    </p>

    <div class="mx-auto mt-16 grid max-w-lg grid-cols-1 items-stretch gap-y-6 sm:mt-20 sm:gap-y-0 lg:max-w-6xl lg:grid-cols-2 lg:gap-x-8">
        <!-- Classic balík -->
        <div class="relative bg-white dark:bg-slate-900 p-8 shadow-2xl ring-1 ring-gray-900/10 dark:ring-gray-700/20 sm:p-10 rounded-3xl flex flex-col">
            <h3 id="tier-classic" class="text-2xl font-semibold text-left text-gray-900 dark:text-gray-100">CLASSIC</h3>

            <ul role="list" class="mt-8 space-y-3 text-base text-gray-600 dark:text-gray-400">
                <li class="flex gap-x-3">
                    <svg class="h-6 w-5 flex-none text-pink-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                    Dostupný cenový balíček
                </li>
                <li class="flex gap-x-3">
                    <svg class="h-6 w-5 flex-none text-pink-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                    Štandardné zobrazenie v zozname podľa poradia
                </li>
                <li class="flex gap-x-3">
                    <svg class="h-6 w-5 flex-none text-pink-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                    Viditeľný pre všetkých používateľov stránky bez dodatočného zvýraznenia
                </li>
            </ul>

            <p class="mt-8 text-sm text-gray-600 dark:text-gray-400">
                Tento inzerát sa zobrazuje medzi ostatnými inzerátmi v zozname a jeho pozícia závisí od času pridania. Nemá zvýraznenie ani prioritné umiestnenie, ale je plne viditeľný pre všetkých návštevníkov stránky.
            </p>

            <div class="mt-8">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">Obdobie</th>
                            <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">Cena</th>
                            <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">Topovanie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">5 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">10€</td>
                            <td class="py-2 text-left text-gray-500 dark:text-gray-400">-</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">7 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">13€</td>
                            <td class="py-2 text-left text-gray-500 dark:text-gray-400">-</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">30 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">25€</td>
                            <td class="py-2 text-left text-gray-500 dark:text-gray-400">-</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">90 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">70€</td>
                            <td class="py-2 text-left text-gray-500 dark:text-gray-400">-</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">365 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">200€</td>
                            <td class="py-2 text-left text-gray-500 dark:text-gray-400">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <a href="{{ route('register') }}" class="mt-auto block rounded-md bg-pink-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow hover:bg-pink-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600">
                Registrácia
            </a>
        </div>

        <!-- Premium balík -->
        <div class="relative rounded-3xl bg-white dark:bg-slate-900 p-8 shadow-2xl ring-1 ring-gray-900/10 dark:ring-gray-700/20 sm:p-10 flex flex-col">
            <h3 id="tier-premium" class="text-2xl font-semibold text-left text-gray-900 dark:text-gray-100">PREMIUM TOPOVANÉ</h3>

            <ul role="list" class="mt-8 space-y-3 text-base text-gray-600 dark:text-gray-400">
                <li class="flex gap-x-3">
                    <svg class="h-6 w-5 flex-none text-pink-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                    Neustále na vrchu zoznamu inzerátov bez ohľadu na čas pridania
                </li>
                <li class="flex gap-x-3">
                    <svg class="h-6 w-5 flex-none text-pink-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                    Zvýraznený a označený ako TOPovaný, čo zaručuje, že ho uvidí viac návštevníkov
                </li>
                <li class="flex gap-x-3">
                    <svg class="h-6 w-5 flex-none text-pink-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                    Ideálny pre tých, ktorí chcú rýchlo a efektívne zaujať
                </li>
            </ul>

            <p class="mt-8 text-sm text-gray-600 dark:text-gray-400">
                Prémiový inzerát sa zobrazuje na vrchu zoznamu inzerátov a je označený ako TOPovaný, čím získava maximálnu viditeľnosť. Vďaka svojmu prioritnému umiestneniu výrazne zvyšuje šancu zaujať potenciálnych záujemcov.
            </p>

            <div class="mt-8">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">Obdobie</th>
                            <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">Cena</th>
                            <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">Topovanie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">5 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">20€</td>
                            <td class="py-2 text-left text-pink-600">✓</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">7 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">25€</td>
                            <td class="py-2 text-left text-pink-600">✓</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">30 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">40€</td>
                            <td class="py-2 text-left text-pink-600">✓</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">90 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">90€</td>
                            <td class="py-2 text-left text-pink-600">✓</td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-900 dark:text-gray-100">365 dní</td>
                            <td class="py-2 text-left font-semibold text-gray-900 dark:text-gray-100">240€</td>
                            <td class="py-2 text-left text-pink-600">✓</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <a href="{{ route('register') }}" class="mt-auto block rounded-md bg-pink-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow hover:bg-pink-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600">
                Registrácia
            </a>
        </div>
    </div>

    <!-- Platobné metódy -->
    <div class="mx-auto mt-20 max-w-4xl text-center">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Možnosti platby</h2>
        <div class="mt-8 flex justify-center">
            <img src="{{ asset('images/uploads/moznosti-platby.webp') }}" alt="Možnosti platby" class="max-w-full h-auto">
        </div>
    </div>
</div>

<div class="bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:mx-0">
            <h2 class="text-pretty text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">Prečo inzerovať na portáli Erotikon?</h2>
            <p class="mt-6 text-lg/8 text-gray-600">Sme lídrom v oblasti erotických služieb na Slovensku. S našou platformou získate prístup k najväčšej komunite potenciálnych záujemcov a profesionálne nástroje pre váš úspech.</p>
        </div>
        <dl class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 text-base/7 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3">
            <div>
                <dt class="font-semibold text-gray-900">Nový Erotický portál na Slovensku</dt>
                <dd class="mt-1 text-gray-600">Máme dlhoročné skúsenosti v oblasti erotických služieb. Naše know-how využívame pre váš úspech a maximálnu spokojnosť.</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-900">Významná návštevnosť</dt>
                <dd class="mt-1 text-gray-600">Denne privítame viac ako 10 000 návštevníkov, čo mesačne predstavuje vyše 300 000 unikátnych používateľov. Vaše inzeráty uvidia tisíce potenciálnych záujemcov.</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-900">Aktívna propagácia</dt>
                <dd class="mt-1 text-gray-600">Neustále investujeme do reklamy a hľadáme nové možnosti, ako prilákať ešte viac záujemcov na náš portál. Váš inzerát tak získa maximálnu viditeľnosť.</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-900">Používateľská prívetivosť</dt>
                <dd class="mt-1 text-gray-600">Naša platforma je navrhnutá tak, aby bola jednoduchá na používanie, prehľadná a efektívna. Intuitívne rozhranie vám umožní ľahko spravovať vaše inzeráty.</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-900">Univerzálny prístup</dt>
                <dd class="mt-1 text-gray-600">Erotikon.sk je optimalizovaný pre všetky zariadenia – od mobilov, tabletov, a smart televízorov až po bežné počítače a notebooky. Vaše inzeráty budú perfektne zobrazené všade.</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-900">Dôkladná kontrola inzercie</dt>
                <dd class="mt-1 text-gray-600">Každý inzerát a fotografia prechádza dôslednou kontrolou, aby sme zabezpečili kvalitu a dôveryhodnosť obsahu. Duplicitné a nepravdivé inzeráty u nás nemajú miesto.</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-900">Profesionalita a podpora</dt>
                <dd class="mt-1 text-gray-600">Ponúkame rýchlu a spoľahlivú komunikáciu a technickú podporu pre našich inzerentov. Sme tu pre vás, keď nás potrebujete.</dd>
            </div>
            <div>
                <dt class="font-semibold text-gray-900">Rýchla odozva</dt>
                <dd class="mt-1 text-gray-600">Vaše otázky zodpovieme promptne a s maximálnou ochotou. Váš úspech je našou prioritou.</dd>
            </div>
        </dl>
    </div>
</div>

@endsection 