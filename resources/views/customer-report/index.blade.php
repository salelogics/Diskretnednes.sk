@extends('layouts.user-dashboard')

@section('header', 'Nahlásenie zákazníka')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Informačná sekcia -->
        <div class="space-y-8">
            <!-- Úvodné informácie -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <div class="text-center mb-6">
                    <div class="mx-auto size-16 flex items-center justify-center rounded-full bg-pink-500/10 mb-4">
                        <i class="ri-shield-check-line text-2xl text-pink-500"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Bezpečnosť komunity</h2>
                    <p class="text-gray-600">Pomôžte chrániť našu komunitu nahlásením problematických zákazníkov</p>
                </div>
                
                <!-- Štatistiky -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-pink-500">{{ $totalReportedNumbers }}</div>
                        <div class="text-sm text-gray-600">Nahlásených čísel</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-pink-500">{{ $totalReports }}</div>
                        <div class="text-sm text-gray-600">Celkom nahlásení</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-pink-500">{{ $dangerousNumbers }}</div>
                        <div class="text-sm text-gray-600">Nebezpečných</div>
                    </div>
                </div>
            </div>

            <!-- Vyhľadávanie -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Vyhľadať číslo</h3>
                <p class="text-gray-600 mb-6">Skontrolujte si, či telefónne číslo nie je v našej databáze nahlásených čísel</p>
                
                <div class="relative">
                    <input type="text" 
                           id="phone_search" 
                           placeholder="Zadajte telefónne číslo (min. 3 znaky)..." 
                           class="w-full rounded-md border-gray-300 pl-10 pr-4 py-3 focus:border-pink-500 focus:ring-pink-500 text-lg">
                    <svg class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                
                <!-- Výsledky vyhľadávania -->
                <div id="searchResults" class="mt-4 hidden">
                    <div class="rounded-lg border border-pink-200 bg-pink-50 shadow-sm">
                        <div id="searchResultsContent"></div>
                    </div>
                </div>
            </div>

            <!-- FAQ sekcia -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Často kladené otázky</h3>
                <div class="space-y-4">
                    <div class="border-l-4 border-pink-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-1">Kedy nahlásiť zákazníka?</h4>
                        <p class="text-sm text-gray-600">Nahlaste zákazníka, ak sa správa nevhodne, je agresívny, neplatí alebo porušuje pravidlá.</p>
                    </div>
                    
                    <div class="border-l-4 border-pink-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-1">Je nahlásenie anonymné?</h4>
                        <p class="text-sm text-gray-600">Áno, všetky nahlásenia sú úplne anonymné. Vaše údaje sa nikde nezobrazujú.</p>
                    </div>
                    
                    <div class="border-l-4 border-pink-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-1">Čo sa stane po nahlásení?</h4>
                        <p class="text-sm text-gray-600">Číslo sa pridá do databázy a ostatní používatelia budú upozornení na potenciálny problém.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulár a tabuľka -->
        <div class="space-y-8">
            <!-- Flash správy -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulár pre nahlásenie -->
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900">Nahlásiť zákazníka</h2>
                    <div class="size-12 flex items-center justify-center rounded-full bg-pink-500/10">
                        <i class="ri-alarm-warning-line text-xl text-pink-500"></i>
                    </div>
                </div>
                
                <form action="{{ route('customer-report.report') }}" method="POST" class="space-y-6" id="report-form">
                    @csrf
                    
                    <div>
                        <label for="report_phone" class="block text-sm font-medium text-gray-700">Telefónne číslo</label>
                        <input type="text" 
                               id="report_phone" 
                               name="phone_number" 
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('phone_number') border-red-300 @enderror"
                               placeholder="Napr. 0901234567"
                               value="{{ old('phone_number') }}">
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="report_reason" class="block text-sm font-medium text-gray-700">Dôvod nahlásenia</label>
                        <textarea id="report_reason" 
                                  name="reason" 
                                  rows="4" 
                                  required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('reason') border-red-300 @enderror"
                                  placeholder="Detailne opíšte problém s týmto zákazníkom...">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="anonymous" name="anonymous" type="checkbox" checked class="h-4 w-4 rounded border-gray-300 text-pink-500 focus:ring-pink-500">
                        </div>
                        <div class="ml-3">
                            <label for="anonymous" class="text-sm text-gray-700">
                                Nahlásenie bude anonymné (odporúčané)
                            </label>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit" class="w-full rounded-md bg-pink-500 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2" id="submit-btn">
                            <span id="submit-text">Nahlásiť zákazníka</span>
                            <span id="submit-loading" class="hidden">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Nahlasuje...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabuľka s nahláseniami -->
            <div class="border border-pink-500/20 rounded-2xl bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-pink-100">
                    <h3 class="text-lg font-medium text-gray-900">Nahlásené telefónne čísla</h3>
                    <p class="mt-1 text-sm text-gray-500">Zoznam všetkých nahlásených čísel s úrovňou nebezpečenstva</p>
                </div>
                
                @if(count($allReports) > 0)
                    <div>
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-pink-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Telefónne číslo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Dátum</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Počet</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Úroveň</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allReports as $report)
                                    <tr class="hover:bg-pink-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $report['number'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $report['date'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                                {{ $report['reports'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($report['level'] === 'very_dangerous')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-600 text-white shadow-sm">
                                                    <i class="ri-error-warning-line mr-1"></i>
                                                    Veľmi nebezpečné
                                                </span>
                                            @elseif($report['level'] === 'dangerous')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-500 text-white shadow-sm">
                                                    <i class="ri-alert-line mr-1"></i>
                                                    Nebezpečné
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-500 text-white shadow-sm">
                                                    <i class="ri-question-line mr-1"></i>
                                                    Podozrivé
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto size-12 flex items-center justify-center rounded-full bg-pink-100 mb-4">
                            <i class="ri-shield-check-line text-xl text-pink-500"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">Žiadne nahlásenia</h3>
                        <p class="text-sm text-gray-500">Zatiaľ neboli nahlásené žiadne problematické čísla.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="modal-title">Ďakujeme za nahlásenie!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="modal-message">
                    Vaše nahlásenie bolo úspešne odoslané. Pomáhate tak chrániť našu komunitu.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="modal-close" class="px-4 py-2 bg-pink-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300">
                    Zavrieť
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Vyhľadávanie
let searchTimeout;
document.getElementById('phone_search').addEventListener('input', function(e) {
    const query = e.target.value;
    const resultsDiv = document.getElementById('searchResults');
    const contentDiv = document.getElementById('searchResultsContent');
    
    clearTimeout(searchTimeout);
    
    if (query.length < 3) {
        resultsDiv.classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 300);
});

function performSearch(query) {
    fetch('{{ route("customer-report.search") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ phone_number: query })
    })
    .then(response => response.json())
    .then(data => {
        const resultsDiv = document.getElementById('searchResults');
        const contentDiv = document.getElementById('searchResultsContent');
        
        if (data.is_dangerous && data.found_number) {
            const levelText = {
                'very_dangerous': 'Veľmi nebezpečné',
                'dangerous': 'Nebezpečné',
                'suspicious': 'Podozrivé'
            };
            
            const levelColors = {
                'very_dangerous': 'bg-red-600 text-white shadow-sm',
                'dangerous': 'bg-orange-500 text-white shadow-sm',
                'suspicious': 'bg-amber-500 text-white shadow-sm'
            };

            const levelIcons = {
                'very_dangerous': 'ri-error-warning-line',
                'dangerous': 'ri-alert-line',
                'suspicious': 'ri-question-line'
            };
            
            contentDiv.innerHTML = `
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg font-medium text-gray-900">${data.found_number}</p>
                            <p class="text-sm text-gray-500">${data.reports_count} nahlásení</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${levelColors[data.level]}">
                            <i class="${levelIcons[data.level]} mr-1"></i>
                            ${levelText[data.level]}
                        </span>
                    </div>
                    <div class="mt-4 p-3 bg-red-50 rounded-lg">
                        <p class="text-sm text-red-800">
                            <i class="ri-warning-line mr-1"></i>
                            Toto číslo bolo nahlásené ako problematické. Buďte opatrní!
                        </p>
                    </div>
                </div>
            `;
            resultsDiv.classList.remove('hidden');
        } else {
            contentDiv.innerHTML = `
                <div class="p-6 text-center">
                    <div class="mx-auto size-12 flex items-center justify-center rounded-full bg-green-100 mb-3">
                        <i class="ri-shield-check-line text-xl text-green-500"></i>
                    </div>
                    <p class="text-sm text-gray-900 font-medium">Číslo nie je nahlásené</p>
                    <p class="text-sm text-gray-500">Toto číslo nebolo nájdené v databáze problematických čísel.</p>
                </div>
            `;
            resultsDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const resultsDiv = document.getElementById('searchResults');
        const contentDiv = document.getElementById('searchResultsContent');
        contentDiv.innerHTML = `
            <div class="p-6 text-center">
                <p class="text-sm text-red-500">Nastala chyba pri vyhľadávaní.</p>
            </div>
        `;
        resultsDiv.classList.remove('hidden');
    });
}

// AJAX formulár
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('report-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');
    const modal = document.getElementById('success-modal');
    const modalClose = document.getElementById('modal-close');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Zobrazenie loading stavu
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');
        
        // Vytvorenie FormData objektu
        const formData = new FormData(form);
        
        // AJAX request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Zobrazenie success modalu
                modal.classList.remove('hidden');
                
                // Reset formulára
                form.reset();
                
                // Refresh stránky po 2 sekundách
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                alert('Nastala chyba pri nahlasovaní. Skúste to znovu.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Nastala chyba pri nahlasovaní. Skúste to znovu.');
        })
        .finally(() => {
            // Obnovenie pôvodného stavu tlačidla
            submitBtn.disabled = false;
            submitText.classList.remove('hidden');
            submitLoading.classList.add('hidden');
        });
    });

    // Zatvorenie modalu
    modalClose.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Zatvorenie modalu kliknutím mimo neho
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

// Zatvorenie výsledkov vyhľadávania pri kliknutí mimo
document.addEventListener('click', function(e) {
    const searchInput = document.getElementById('phone_search');
    const searchResults = document.getElementById('searchResults');
    
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.classList.add('hidden');
    }
});
</script>
@endsection 