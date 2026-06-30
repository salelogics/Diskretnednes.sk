let currentStep = 1;
const totalSteps = 5;

function showStep(step) {
    console.log('Showing step:', step);
    
    // Skryť všetky kroky
    for (let i = 1; i <= totalSteps; i++) {
        const stepEl = document.querySelector(`[data-step="${i}"]`);
        if (stepEl) {
            stepEl.style.display = 'none';
            console.log(`Step ${i} hidden`);
        }
    }
    
    // Zobraziť aktuálny krok
    const currentStepEl = document.querySelector(`[data-step="${step}"]`);
    if (currentStepEl) {
        currentStepEl.style.display = 'block';
        console.log(`Step ${step} shown`);
    } else {
        console.error(`Step ${step} element not found!`);
    }
    
    // Aktualizovať progress bar
    updateProgressBar(step);
    
    // Aktualizovať tlačidlá
    updateButtons(step);
}

function updateProgressBar(step) {
    for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.querySelector(`.step-indicator[data-step="${i}"]`);
        const label = document.querySelector(`.step-label[data-step="${i}"]`);
        const line = document.querySelector(`.step-line[data-step="${i}"]`);
        
        if (indicator) {
            if (i <= step) {
                indicator.className = 'flex items-center justify-center w-8 h-8 bg-pink-600 text-white rounded-full text-sm font-medium step-indicator';
            } else {
                indicator.className = 'flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-500 rounded-full text-sm font-medium step-indicator';
            }
        }
        
        if (label) {
            if (i <= step) {
                label.className = 'ml-2 text-sm font-medium text-gray-900 step-label hidden md:inline';
            } else {
                label.className = 'ml-2 text-sm font-medium text-gray-500 step-label hidden md:inline';
            }
        }
        
        if (line) {
            if (i < step) {
                line.className = 'w-8 md:w-16 h-1 bg-pink-600 step-line flex-shrink-0';
            } else {
                line.className = 'w-8 md:w-16 h-1 bg-gray-200 step-line flex-shrink-0';
            }
        }
    }
}

function updateButtons(step) {
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    
    if (prevBtn) {
        prevBtn.style.display = step > 1 ? 'inline-flex' : 'none';
    }
    if (nextBtn) {
        nextBtn.style.display = step < totalSteps ? 'inline-flex' : 'none';
    }
    if (submitBtn) {
        submitBtn.style.display = step === totalSteps ? 'inline-flex' : 'none';
    }
}

function nextStep() {
    if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

// Inicializácia po načítaní stránky
document.addEventListener('DOMContentLoaded', function() {
    console.log('Multi-step form script loaded');
    
    // Kontrola či sme na stránke s formulárom
    const formSteps = document.querySelectorAll('.form-step');
    console.log('Found form steps:', formSteps.length);
    
    if (formSteps.length > 0) {
        console.log('Initializing multi-step form');
        showStep(1);
        
        // Event listenery pre tlačidlá
        const nextBtn = document.getElementById('next-btn');
        const prevBtn = document.getElementById('prev-btn');
        
        console.log('Buttons found:', {nextBtn, prevBtn});
        
        if (nextBtn) {
            nextBtn.onclick = function(e) {
                e.preventDefault();
                console.log('Next button clicked, current step:', currentStep);
                nextStep();
            };
        }
        
        if (prevBtn) {
            prevBtn.onclick = function(e) {
                e.preventDefault();
                console.log('Prev button clicked, current step:', currentStep);
                prevStep();
            };
        }
    } else {
        console.log('No form steps found on this page');
    }
});

// Export funkcií pre globálne použitie
window.showStep = showStep;
window.nextStep = nextStep;
window.prevStep = prevStep; 