<?php

/*
|--------------------------------------------------------------------------
| Zážitky (kategórie s farebným kódovaním)
|--------------------------------------------------------------------------
|
| Jeden zdroj pravdy pre výber "Zážitkov" vo formulári (create/edit) aj pre
| farebné zobrazenie na detaile profilu. Hodnoty (kľúče) sa ukladajú do
| JSON stĺpca `practices`, takže žiadna migrácia nie je potrebná.
|
| Použité Tailwind triedy sú literálne, aby ich zachytil build a sú overené,
| že existujú v skompilovanom CSS.
|
*/

return [

    'blue' => [
        'title' => 'Spoločnosť a sprievod',
        'section' => 'border-blue-200 bg-blue-50',
        'heading' => 'text-blue-800',
        'chip' => 'bg-blue-100 text-blue-800',
        'items' => [
            'diskretny-sprievod' => 'Diskrétny sprievod',
            'rozhovor' => 'Rozhovor',
            'spolocnost' => 'Spoločnosť',
            'vecera-drink-kava' => 'Večera, drink, káva',
            'kulturne-podujatia' => 'Kultúrne podujatia',
            'firemne-akcie' => 'Firemné akcie',
            'cestovanie-doprovod' => 'Cestovanie ako doprovod',
            'eventovy-host' => 'Eventový host',
            'relax-chill' => 'Relax & chill',
            'online-spolocnost' => 'Online spoločnosť (chat & video)',
        ],
    ],

    'yellow' => [
        'title' => 'Vzťahy a dohody',
        'section' => 'border-yellow-200 bg-yellow-50',
        'heading' => 'text-yellow-800',
        'chip' => 'bg-yellow-100 text-yellow-800',
        'items' => [
            'kratkodoby-vztah' => 'Krátkodobý vzťah',
            'dlhodoby-vztah' => 'Dlhodobý vzťah',
            'pravidelne-stretnutia' => 'Pravidelné stretnutia',
            'nepravidelne-stretnutia' => 'Nepravidelné stretnutia',
            'mentoring-lifestyle' => 'Mentoring / lifestyle support',
            'diskretny-vztah' => 'Diskrétny vzťah',
            'spolocna-dovolenka' => 'Spoločná dovolenka',
            'nakupovanie' => 'Nakupovanie',
            'individualna-dohoda' => 'Individuálna dohoda',
        ],
    ],

    'green' => [
        'title' => 'Masáže a relax',
        'section' => 'border-green-200 bg-green-50',
        'heading' => 'text-green-800',
        'chip' => 'bg-green-100 text-green-800',
        'items' => [
            'masaz' => 'Masáž',
            'intimna-masaz' => 'Intímna masáž',
            'masaz-telo-na-telo' => 'Masáž telo na telo',
            'tantra' => 'Tantra',
            'nuru' => 'Nuru',
            'spodne-pradlo' => 'Spodné prádlo',
            'footjob' => 'Footjob',
            'ine-po-dohode-masaz' => 'Iné (po dohode)',
        ],
    ],

    'red' => [
        'title' => 'Intímne zážitky',
        'section' => 'border-red-200 bg-red-50',
        'heading' => 'text-red-800',
        'chip' => 'bg-red-100 text-red-800',
        'items' => [
            'intimne-stretnutie-spolocna-noc' => 'Intímne stretnutie / spoločná noc',
            'gfe' => 'GFE (girlfriend experience)',
            'roleplay-dynamika-moci' => 'Roleplay & Dynamika moci',
            'submisivne' => 'Submisívne',
            'dominantne' => 'Dominantne',
            'stretnutie-viacerych-osob' => 'Stretnutie viacerých osôb',
            'ine-po-dohode-intim' => 'Iné (po dohode)',
        ],
    ],

];
