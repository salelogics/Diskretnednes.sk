@extends('layouts.main')

@section('content')
<div class="bg-white dark:bg-slate-950 transition-colors duration-300">
    <div class="relative isolate overflow-hidden pt-14">
        <img src="{{ asset('images/uploads/hero-bg.jpg') }}" alt="" class="absolute inset-0 -z-10 size-full object-cover opacity-90">
        <div class="absolute inset-0 -z-10 bg-black/60"></div>
        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
            <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-12 sm:py-14 lg:py-16">
                <div class="text-center">
                    <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">{{ __('app.navigation.contact') }}</h1>
                    <p class="mt-6 text-lg leading-8 text-gray-300">{{ __('app.contact.subtitle') }}</p>
                </div>
            </div>
        </div>
        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Kontaktné informácie -->
            <div class="space-y-12">
                <div class="border border-pink-500/20 dark:border-pink-500/30 rounded-2xl p-8 bg-white dark:bg-slate-900 shadow-sm">
                    <div class="text-center">
                        <div class="mx-auto size-16 flex items-center justify-center rounded-full bg-pink-500/10 dark:bg-pink-500/20 mb-4">
                            <i class="ri-mail-line text-2xl text-pink-500"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">info@diskretnednes.sk</h3>
                    </div>
                </div>

                <div class="border border-pink-500/20 dark:border-pink-500/30 rounded-2xl p-8 bg-white dark:bg-slate-900 shadow-sm">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('app.contact.follow_us') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-8">{{ __('app.contact.description') }}</p>
                    <div class="flex gap-4">
                        <a href="https://www.instagram.com/diskretnednes.sk/" target="_blank" rel="noopener noreferrer" class="text-gray-400 dark:text-gray-500 hover:text-pink-500 dark:hover:text-pink-400 transition">
                            <i class="ri-instagram-fill text-3xl"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kontaktný formulár -->
            <div class="border border-pink-500/20 dark:border-pink-500/30 rounded-2xl p-8 bg-white dark:bg-slate-900 shadow-sm">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('app.contact.form_title') }}</h2>
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6" id="contact-form">
                    @csrf
                    
                    <!-- ANTI-SPAM: Honeypot polia (pre boty) -->
                    <div style="position: absolute; left: -9999px; visibility: hidden;">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                        <input type="text" name="url" tabindex="-1" autocomplete="off">
                        <input type="text" name="homepage" tabindex="-1" autocomplete="off">
                    </div>
                    
                    <!-- ANTI-SPAM: Time-based protection -->
                    <input type="hidden" name="form_start_time" id="form_start_time" value="{{ time() }}">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.contact.name_label') }}</label>
                        <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-pink-500 focus:ring-pink-500" value="{{ old('name') }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.contact.email_label') }}</label>
                        <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-pink-500 focus:ring-pink-500" value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.contact.subject_label') }}</label>
                        <input type="text" name="subject" id="subject" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-pink-500 focus:ring-pink-500" value="{{ old('subject') }}">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.contact.message_label') }}</label>
                        <textarea name="message" id="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-pink-500 focus:ring-pink-500">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- ANTI-SPAM: Simple Math CAPTCHA -->
                    <div>
                        <label for="math_answer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Bezpečnostná otázka: <span id="math_question_display"></span> = ?
                        </label>
                        <input type="number" name="math_answer" id="math_answer" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-slate-800 dark:text-gray-100 shadow-sm focus:border-pink-500 focus:ring-pink-500" placeholder="Zadajte výsledok">
                        <input type="hidden" name="math_question" id="math_question">
                        @error('math_answer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 dark:bg-slate-800 text-pink-500 focus:ring-pink-500">
                        </div>
                        <div class="ml-3">
                            <label for="terms" class="text-sm text-gray-700 dark:text-gray-300">
                                {{ __('app.contact.terms_label') }} <a href="{{ route('terms') }}" class="font-medium text-pink-500 hover:text-pink-600 dark:hover:text-pink-400">{{ __('app.contact.terms_link') }}</a> {{ __('app.contact.and') }} <a href="{{ route('privacy') }}" class="font-medium text-pink-500 hover:text-pink-600 dark:hover:text-pink-400">{{ __('app.contact.privacy_link') }}</a>
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
                        <button type="submit" class="w-full rounded-md bg-pink-500 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                            {{ __('app.contact.send_button') }}
                        </button>
                    </div>
                </form>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="modal-title">Ďakujeme za vašu správu!</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="modal-message">
                    Vaša správa bola úspešne odoslaná. Odpovieme vám čo najskôr.
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
    // Generuj matematickú otázku
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
    
    // Nastav čas začatia formulára  
    document.getElementById('form_start_time').value = Math.floor(Date.now() / 1000);
    
    // Generuj matematickú otázku
    generateMathQuestion();
    
    // AJAX form submission
    const form = document.getElementById('contact-form');
    const modal = document.getElementById('success-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const modalClose = document.getElementById('modal-close');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Zobrazenie loading stavu
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Odosielam...';
        
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
        .then(response => {
            // Log response for debugging
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.indexOf('application/json') !== -1) {
                return response.json();
            } else {
                // If not JSON, get text and try to parse
                return response.text().then(text => {
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response: ' + text.substring(0, 100));
                });
            }
        })
        .then(data => {
            if (data.success) {
                // Zobrazenie success modalu
                modalTitle.textContent = 'Ďakujeme za vašu správu!';
                modalMessage.textContent = 'Vaša správa bola úspešne odoslaná. Odpovieme vám čo najskôr.';
                modal.classList.remove('hidden');
                
                // Reset formulára
                form.reset();
                // Regeneruj math otázku
                generateMathQuestion();
                document.getElementById('form_start_time').value = Math.floor(Date.now() / 1000);
            } else {
                // Zobrazenie chybovej správy
                alert(data.message || 'Nastala chyba pri odosielaní správy. Skúste to znovu.');
            }
        })
        .catch(error => {
            console.error('Contact form error:', error);
            alert('Nastala chyba pri odosielaní správy: ' + error.message);
        })
        .finally(() => {
            // Obnovenie pôvodného stavu tlačidla
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
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
        </div>
    </div>
</div>
@endsection 