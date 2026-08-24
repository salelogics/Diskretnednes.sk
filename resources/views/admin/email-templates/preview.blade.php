@extends('layouts.admin-dashboard')

@section('header', 'Náhľad Email Template')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.email-templates.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h2 class="text-xl font-semibold text-gray-900">Náhľad Email Template</h2>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" 
           class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 flex items-center">
            <i class="ri-edit-line text-sm mr-2"></i>
            Upraviť
        </a>
        <a href="{{ route('admin.email-templates.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            Späť na zoznam
        </a>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Hlavný obsah -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">📧 Email Náhľad</h3>
                <div class="flex space-x-2">
                    <button type="button" id="preview-mode" 
                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        👁️ Náhľad
                    </button>
                    <button type="button" id="code-mode" 
                            class="px-3 py-1 text-xs bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        💻 HTML kód
                    </button>
                    <button type="button" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center"
                            onclick="openTestModal()">
                        <i class="ri-mail-send-line text-sm mr-2"></i>
                        Odoslať test
                    </button>
                </div>
            </div>
            <div class="p-0">
                <!-- Email Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Od:</span>
                            <p class="text-sm text-gray-900">{{ config('mail.from.name', 'DiskretneDnes.sk') }} &lt;{{ config('mail.from.address', 'info@diskretnednes.sk') }}&gt;</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Typ:</span>
                            <div class="mt-1">
                                @if($emailTemplate->type === 'user')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="ri-user-line text-xs mr-1"></i>
                                        Pre používateľa
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                        <i class="ri-admin-line text-xs mr-1"></i>
                                        Pre admina
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-sm font-medium text-gray-500">Predmet:</span>
                        <p class="text-sm text-gray-900">{{ $subject }}</p>
                    </div>
                </div>
                
                <!-- Email Content Preview -->
                <div id="preview-content" class="px-6 py-4">
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <iframe src="{{ route('admin.email-templates.preview-raw', $emailTemplate) }}" 
                                class="w-full border-none bg-white"
                                style="min-height: 600px; max-height: 800px;"
                                onload="this.style.height=(this.contentWindow.document.body.scrollHeight + 50) + 'px';">
                        </iframe>
                    </div>
                </div>
                
                <!-- HTML Code View -->
                <div id="code-content" class="hidden px-6 py-4">
                    <div class="bg-gray-900 text-green-400 rounded-lg p-4 overflow-auto" style="max-height: 600px;">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-medium text-gray-300">HTML Zdrojový kód</h4>
                            <button type="button" onclick="copyHtmlCode()" 
                                    class="px-3 py-1 text-xs bg-gray-700 text-white rounded-md hover:bg-gray-600">
                                📋 Kopírovať
                            </button>
                        </div>
                        <pre class="text-sm font-mono whitespace-pre-wrap" id="html-code">{{ $content }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Informácie o Template -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">ℹ️ Informácie o Template</h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Názov:</span>
                    <p class="text-sm text-gray-900 mt-1">{{ $emailTemplate->name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Kľúč:</span>
                    <code class="block text-sm bg-gray-100 px-2 py-1 rounded mt-1">{{ $emailTemplate->key }}</code>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Typ:</span>
                    <div class="mt-1">
                        @if($emailTemplate->type === 'user')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="ri-user-line text-xs mr-1"></i>
                                Používateľ
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                <i class="ri-admin-line text-xs mr-1"></i>
                                Admin
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Stav:</span>
                    <div class="mt-1">
                        @if($emailTemplate->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="ri-check-line text-xs mr-1"></i>
                                Aktívny
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i class="ri-close-line text-xs mr-1"></i>
                                Neaktívny
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Vytvorené:</span>
                    <p class="text-sm text-gray-900 mt-1">{{ $emailTemplate->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Aktualizované:</span>
                    <p class="text-sm text-gray-900 mt-1">{{ $emailTemplate->updated_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Použité ukážkové dáta -->
        @if(isset($sampleData) && count($sampleData) > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📊 Ukážkové dáta</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-2">
                        @foreach($sampleData as $key => $value)
                            <div class="flex items-center justify-between">
                                <code class="text-sm bg-gray-100 px-2 py-1 rounded text-blue-600">{{ $key }}</code>
                                <span class="text-sm text-gray-700 ml-2 truncate">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mt-4">
                        <p class="text-sm text-yellow-700">
                            <strong>Poznámka:</strong> Toto sú ukážkové dáta použité pre náhľad. Skutočné emaily budú obsahovať reálne hodnoty.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Premenné dostupné v template -->
        @if($emailTemplate->variables && count($emailTemplate->getAvailableVariables()) > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📝 Dostupné premenné</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-2">
                        @foreach($emailTemplate->getAvailableVariables() as $variable)
                            <div class="flex items-center justify-between">
                                <code class="text-sm bg-gray-100 px-2 py-1 rounded text-blue-600">@{{ '{{ ' . $variable . ' }}' }}</code>
                                @if(isset($sampleData[$variable]))
                                    <span class="text-sm text-gray-700">{{ $sampleData[$variable] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Akcie -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">🔧 Akcie</h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" 
                   class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 flex items-center justify-center">
                    <i class="ri-edit-line mr-2"></i>
                    Upraviť template
                </a>
                
                <button type="button" 
                        class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center justify-center"
                        onclick="openTestModal()">
                    <i class="ri-mail-send-line mr-2"></i>
                    Odoslať test email
                </button>
                
                <form method="POST" action="{{ route('admin.email-templates.duplicate', $emailTemplate) }}">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 flex items-center justify-center"
                            onclick="return confirm('Chcete duplikovať tento template?')">
                        <i class="ri-file-copy-line mr-2"></i>
                        Duplikovať template
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal -->
<div id="testModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="ri-mail-send-line text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Test Email Template</h3>
            <div class="mt-4 px-7 py-3">
                <form method="POST" action="{{ route('admin.email-templates.test', $emailTemplate) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="test_email" class="block text-sm font-medium text-gray-700 mb-2">Email adresa</label>
                        <input type="email" id="test_email" name="test_email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="vas@email.com" required>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4">
                        <p class="text-sm text-blue-700">Email bude odoslaný s ukážkovými dátami a predmetom začínajúcim "[TEST]"</p>
                    </div>
                    <div class="flex justify-center space-x-3">
                        <button type="button" 
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
                                onclick="closeTestModal()">
                            Zrušiť
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Odoslať test
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let isPreviewMode = true;

document.addEventListener('DOMContentLoaded', function() {
    // Prepínanie medzi náhľadom a kódom
    document.getElementById('preview-mode').addEventListener('click', function() {
        switchToPreviewMode();
    });
    
    document.getElementById('code-mode').addEventListener('click', function() {
        switchToCodeMode();
    });
});

function switchToPreviewMode() {
    isPreviewMode = true;
    
    // Aktualizácia tlačidiel
    document.getElementById('preview-mode').classList.remove('bg-gray-600');
    document.getElementById('preview-mode').classList.add('bg-blue-600');
    document.getElementById('code-mode').classList.remove('bg-blue-600');
    document.getElementById('code-mode').classList.add('bg-gray-600');
    
    // Zobrazenie náhľadu
    document.getElementById('preview-content').classList.remove('hidden');
    document.getElementById('code-content').classList.add('hidden');
}

function switchToCodeMode() {
    isPreviewMode = false;
    
    // Aktualizácia tlačidiel
    document.getElementById('code-mode').classList.remove('bg-gray-600');
    document.getElementById('code-mode').classList.add('bg-blue-600');
    document.getElementById('preview-mode').classList.remove('bg-blue-600');
    document.getElementById('preview-mode').classList.add('bg-gray-600');
    
    // Zobrazenie kódu
    document.getElementById('code-content').classList.remove('hidden');
    document.getElementById('preview-content').classList.add('hidden');
}

function copyHtmlCode() {
    const htmlCode = document.getElementById('html-code').textContent;
    navigator.clipboard.writeText(htmlCode).then(function() {
        // Dočasne zmeniť text tlačidla
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = '✅ Skopírované';
        button.classList.add('bg-green-600');
        
        setTimeout(function() {
            button.textContent = originalText;
            button.classList.remove('bg-green-600');
        }, 2000);
    }).catch(function(err) {
        console.error('Chyba pri kopírovaní: ', err);
        alert('Nepodarilo sa skopírovať obsah');
    });
}

function openTestModal() {
    document.getElementById('testModal').classList.remove('hidden');
}

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
}

// Automatické prispôsobenie výšky iframe
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.querySelector('iframe');
    if (iframe) {
        iframe.onload = function() {
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const height = iframeDoc.body.scrollHeight;
                iframe.style.height = Math.min(height + 50, 800) + 'px';
            } catch (e) {
                // Fallback pre cross-origin issues
                iframe.style.height = '600px';
            }
        };
    }
});
</script>
@endpush
@endsection 