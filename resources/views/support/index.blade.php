@extends('layouts.user-dashboard')

@section('header', 'Podpora')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Kontaktné informácie -->
        <div class="space-y-12">
            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <div class="flex justify-center">
                    <div class="text-center">
                        <div class="mx-auto size-16 flex items-center justify-center rounded-full bg-pink-500/10 mb-4">
                            <i class="ri-mail-line text-2xl text-pink-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">info@diskretnednes.sk</h3>
                    </div>
                </div>
            </div>

            <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Potrebujete pomoc?</h2>
                <p class="text-gray-600 mb-8">Sme tu pre vás 24/7. Vyplňte formulár a náš tím vám odpovie čo najskôr. Diskrétne a profesionálne riešenie vašich problémov.</p>
                
                <!-- FAQ sekcia -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Často kladené otázky</h3>
                    <div class="space-y-4">
                        <div class="border-l-4 border-pink-500 pl-4">
                            <h4 class="font-medium text-gray-900 mb-1">Ako môžem pridať inzerát?</h4>
                            <p class="text-sm text-gray-600">Prejdite do sekcie "Inzeráty" a kliknite na "Pridať nový inzerát". Vyplňte všetky potrebné údaje a nahrajte fotografie.</p>
                        </div>
                        
                        <div class="border-l-4 border-pink-500 pl-4">
                            <h4 class="font-medium text-gray-900 mb-1">Ako funguje predplatné?</h4>
                            <p class="text-sm text-gray-600">Predplatné vám umožňuje neobmedzené pridávanie inzerátov a prístup k prémiové funkciám. Pozrite si náš cenník pre viac detailov.</p>
                        </div>
                        
                        <div class="border-l-4 border-pink-500 pl-4">
                            <h4 class="font-medium text-gray-900 mb-1">Sú moje údaje v bezpečí?</h4>
                            <p class="text-sm text-gray-600">Áno, všetky vaše údaje sú šifrované a chránené podľa GDPR. Diskrétnosť je naša priorita.</p>
                        </div>
                        
                        <div class="border-l-4 border-pink-500 pl-4">
                            <h4 class="font-medium text-gray-900 mb-1">Ako môžem zrušiť predplatné?</h4>
                            <p class="text-sm text-gray-600">Predplatné môžete kedykoľvek zrušiť v sekcii "Platby" alebo nás kontaktujte cez tento formulár.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <a href="#" class="text-gray-400 hover:text-pink-500 transition">
                        <i class="ri-facebook-circle-fill text-3xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-pink-500 transition">
                        <i class="ri-instagram-fill text-3xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-pink-500 transition">
                        <i class="ri-twitter-x-fill text-3xl"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Support formulár -->
        <div class="border border-pink-500/20 rounded-2xl p-8 bg-white shadow-sm">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Napíšte nám</h2>
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
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

            <form action="{{ route('support.submit') }}" method="POST" class="space-y-6" id="support-form">
                @csrf
                
                <!-- ANTI-SPAM: Honeypot polia (pre boty) -->
                <div style="position: absolute; left: -9999px; visibility: hidden;">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                    <input type="text" name="url" tabindex="-1" autocomplete="off">
                    <input type="text" name="homepage" tabindex="-1" autocomplete="off">
                </div>
                
                <!-- ANTI-SPAM: Time-based protection -->
                <input type="hidden" name="form_start_time" id="form_start_time" value="{{ time() }}">
                
                <!-- Typ problému -->
                <div>
                    <label for="problem_type" class="block text-sm font-medium text-gray-700">Typ problému</label>
                    <select id="problem_type" name="problem_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('problem_type') border-red-300 @enderror">
                        <option value="">Vyberte typ problému</option>
                        <option value="inzercia" {{ old('problem_type') == 'inzercia' ? 'selected' : '' }}>Inzercia</option>
                        <option value="predplatne" {{ old('problem_type') == 'predplatne' ? 'selected' : '' }}>Predplatné</option>
                        <option value="ina_chyba" {{ old('problem_type') == 'ina_chyba' ? 'selected' : '' }}>Iná chyba</option>
                    </select>
                    @error('problem_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meno (automaticky vyplnené) -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Meno</label>
                    <input type="text" name="name" id="name" value="{{ auth()->user()->name }}" readonly 
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                </div>

                <!-- Email (automaticky vyplnený) -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ auth()->user()->email }}" readonly 
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                </div>

                <!-- Telefón -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Telefón</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('phone') border-red-300 @enderror"
                           placeholder="+421 xxx xxx xxx">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Predmet -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Predmet</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('subject') border-red-300 @enderror"
                           placeholder="Stručne opíšte váš problém">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Správa -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Správa</label>
                    <textarea name="message" id="message" rows="4" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 @error('message') border-red-300 @enderror"
                              placeholder="Detailne opíšte váš problém...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ANTI-SPAM: Simple Math CAPTCHA -->
                <div>
                    <label for="math_answer" class="block text-sm font-medium text-gray-700">
                        Bezpečnostná otázka: <span id="math_question_display"></span> = ?
                    </label>
                    <input type="number" name="math_answer" id="math_answer" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500" placeholder="Zadajte výsledok">
                    <input type="hidden" name="math_question" id="math_question">
                    @error('math_answer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 rounded border-gray-300 text-pink-500 focus:ring-pink-500">
                    </div>
                    <div class="ml-3">
                        <label for="terms" class="text-sm text-gray-700">
                            Súhlasím so <a href="{{ route('terms') }}" class="font-medium text-pink-500 hover:text-pink-600">spracovaním osobných údajov</a> a <a href="{{ route('privacy') }}" class="font-medium text-pink-500 hover:text-pink-600">ochranou súkromia</a>
                        </label>
                    </div>
                </div>
                
                @if ($errors->has('spam'))
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-800">{{ $errors->first('spam') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <button type="submit" class="w-full rounded-md bg-pink-500 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2" id="submit-btn">
                        <span id="submit-text">Odoslať správu</span>
                        <span id="submit-loading" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Odosielam...
                        </span>
                    </button>
                </div>
            </form>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="modal-title">Ďakujeme za vašu správu!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="modal-message">
                    Vaša správa bola úspešne odoslaná. Náš tím vám odpovie čo najskôr.
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
document.addEventListener('DOMContentLoaded', function() {
    // ANTI-SPAM: Generuj matematickú otázku
    function generateMathQuestion() {
        const num1 = Math.floor(Math.random() * 10) + 1;
        const num2 = Math.floor(Math.random() * 10) + 1;
        const operations = ['+', '-'];
        const operation = operations[Math.floor(Math.random() * operations.length)];
        
        let question, answer;
        if (operation === '+') {
            question = `${num1} + ${num2}`;
            answer = num1 + num2;
        } else {
            // Pre odčítanie, zabezpeč že výsledok nie je záporný
            if (num1 >= num2) {
                question = `${num1} - ${num2}`;
                answer = num1 - num2;
            } else {
                question = `${num2} - ${num1}`;
                answer = num2 - num1;
            }
        }
        
        document.getElementById('math_question_display').textContent = question;
        document.getElementById('math_question').value = btoa(question); // base64 encode
    }
    
    // ANTI-SPAM: Nastav čas začatia formulára  
    document.getElementById('form_start_time').value = Math.floor(Date.now() / 1000);
    
    // ANTI-SPAM: Generuj matematickú otázku
    generateMathQuestion();

    const form = document.getElementById('support-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');
    const modal = document.getElementById('success-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
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
                modalTitle.textContent = data.message;
                modalMessage.textContent = data.details;
                modal.classList.remove('hidden');
                
                // Reset formulára
                form.reset();
            } else {
                // Zobrazenie chybovej správy
                alert('Nastala chyba pri odosielaní správy. Skúste to znovu.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Nastala chyba pri odosielaní správy. Skúste to znovu.');
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
</script>

@endsection 