@extends('layouts.admin-dashboard')

@section('header', 'Upraviť Email Template')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.email-templates.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h2 class="text-xl font-semibold text-gray-900">Upraviť Email Template</h2>
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

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.email-templates.update', $emailTemplate) }}" id="template-form">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Hlavný obsah -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📧 Obsah Template</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Názov template</label>
                        <input type="text" id="name" name="name" 
                               value="{{ old('name', $emailTemplate->name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Predmet emailu</label>
                        <input type="text" id="subject" name="subject" 
                               value="{{ old('subject', $emailTemplate->subject) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Napríklad: Vitajte na stránke"
                               required>
                        <p class="mt-1 text-sm text-gray-500">Môžete použiť premenné vo formáte &#123;&#123; variable_name &#125;&#125;</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Obsah emailu</label>
                        <div class="border border-gray-300 rounded-md">
                            <div id="editorjs" class="min-h-[400px] p-4"></div>
                        </div>
                        <input type="hidden" name="content" id="content-input" value="{{ old('content', $emailTemplate->content) }}">
                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                            <span>💡 <strong>Tip:</strong> Použite premenné vo formáte &#123;&#123; variable_name &#125;&#125;</span>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Template je aktívny
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Informácie -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">ℹ️ Informácie</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
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
                        <span class="text-sm font-medium text-gray-500">Vytvorené:</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $emailTemplate->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Aktualizované:</span>
                        <p class="text-sm text-gray-900 mt-1">{{ $emailTemplate->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Dostupné premenné -->
            @if($emailTemplate->variables && count($emailTemplate->getAvailableVariables()) > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">📝 Dostupné premenné</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-2">
                            @foreach($emailTemplate->getVariableLabels() as $variableName => $label)
                                <div class="block w-full text-left px-3 py-2 text-sm bg-gray-50 rounded border">
                                    <div class="flex items-center justify-between">
                                        <code class="text-blue-600 font-semibold select-all">&#123;&#123; {{ $variableName }} &#125;&#125;</code>
                                        <span class="text-gray-500 text-xs">{{ $label }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mt-4">
                            <p class="text-sm text-blue-700">
                                <strong>💡 Tip:</strong> Označte premennú (kliknite na ňu) a skopírujte ju pomocou Ctrl+C, potom ju vložte do editora pomocou Ctrl+V. Premenné sa automaticky nahradia skutočnými hodnotami pri odosielaní emailu.
                            </p>
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
                    <button type="button" 
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 flex items-center justify-center"
                            onclick="openTestModal()">
                        <i class="ri-mail-send-line mr-2"></i>
                        Odoslať test email
                    </button>
                    
                    <a href="{{ route('admin.email-templates.preview', $emailTemplate) }}" 
                       class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center justify-center" target="_blank">
                        <i class="ri-eye-2-line mr-2"></i>
                        Otvoriť náhľad
                    </a>
                    
                    <form method="POST" action="{{ route('admin.email-templates.duplicate', $emailTemplate) }}">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 flex items-center justify-center"
                                onclick="return confirm('Chcete duplikovať tento template?')">
                            <i class="ri-file-copy-line mr-2"></i>
                            Duplikovať
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tlačidlá na uloženie -->
    <div class="flex justify-between items-center mt-6">
        <a href="{{ route('admin.email-templates.index') }}" 
           class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
            Zrušiť
        </a>
        <button type="submit" 
                class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 flex items-center">
            <i class="ri-save-line mr-2"></i>
            Uložiť zmeny
        </button>
    </div>
</form>

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
                    <div class="flex justify-center space-x-3">
                        <button type="button" 
                                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
                                onclick="closeTestModal()">
                            Zrušiť
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Odoslať
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let editor = null;

// Konvertuje HTML obsah na Editor.js formát
function convertHtmlToEditorJs(htmlContent) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = htmlContent.trim();
    
    const blocks = [];
    const children = tempDiv.children;
    
    for (let i = 0; i < children.length; i++) {
        const element = children[i];
        const block = convertElementToBlock(element);
        if (block) {
            blocks.push(block);
        }
    }
    
    // Ak neboli nájdené žiadne bloky, vytvoríme jeden paragraf s čistým textom
    if (blocks.length === 0) {
        const cleanText = tempDiv.textContent || tempDiv.innerText || '';
        if (cleanText.trim()) {
            blocks.push({
                type: 'paragraph',
                data: {
                    text: cleanText.trim()
                }
            });
        }
    }
    
    return {
        blocks: blocks,
        version: '2.22.2'
    };
}

// Konvertuje HTML element na Editor.js blok
function convertElementToBlock(element) {
    const tagName = element.tagName.toLowerCase();
    
    switch (tagName) {
        case 'h1':
        case 'h2':
        case 'h3':
        case 'h4':
        case 'h5':
        case 'h6':
            return {
                type: 'header',
                data: {
                    text: element.textContent.trim(),
                    level: parseInt(tagName.charAt(1))
                }
            };
            
        case 'p':
            return {
                type: 'paragraph',
                data: {
                    text: element.innerHTML.trim()
                }
            };
            
        case 'div':
            // Div môže obsahovať text alebo iné elementy
            const divContent = element.innerHTML.trim();
            if (divContent) {
                return {
                    type: 'paragraph',
                    data: {
                        text: divContent
                    }
                };
            }
            break;
            
        case 'ul':
        case 'ol':
            const items = [];
            const listItems = element.querySelectorAll('li');
            listItems.forEach(li => {
                items.push(li.textContent.trim());
            });
            
            return {
                type: 'list',
                data: {
                    style: tagName === 'ul' ? 'unordered' : 'ordered',
                    items: items
                }
            };
            
        case 'blockquote':
            return {
                type: 'quote',
                data: {
                    text: element.textContent.trim(),
                    caption: ''
                }
            };
            
        case 'hr':
            return {
                type: 'delimiter',
                data: {}
            };
            
        default:
            // Pre ostatné elementy použijeme paragraph s innerHTML
            const content = element.innerHTML.trim();
            if (content) {
                return {
                    type: 'paragraph',
                    data: {
                        text: content
                    }
                };
            }
    }
    
    return null;
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializácia Editor.js
    const existingContent = document.getElementById('content-input').value;
    let editorData = null;
    
    // Pokus o parsovanie existujúceho obsahu ako JSON (Editor.js data)
    try {
        if (existingContent && existingContent.trim() !== '') {
            editorData = JSON.parse(existingContent);
        }
    } catch (e) {
        // Ak nie je JSON, konvertujeme HTML na Editor.js formát
        if (existingContent && existingContent.trim() !== '') {
            editorData = convertHtmlToEditorJs(existingContent);
        } else {
            editorData = null;
        }
    }
    
    editor = new EditorJSComponent('editorjs', editorData);
    
    // Uloženie obsahu pred odoslaním formulára
    document.getElementById('template-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const outputData = await editor.save();
            document.getElementById('content-input').value = JSON.stringify(outputData);
            this.submit();
        } catch (error) {
            console.error('Saving failed: ', error);
            alert('Chyba pri ukladaní obsahu. Skúste to znovu.');
        }
    });
});


// Modal funkcie
function openTestModal() {
    const modal = document.getElementById('testModal');
    if (modal) modal.classList.remove('hidden');
}

function closeTestModal() {
    const modal = document.getElementById('testModal');
    if (modal) modal.classList.add('hidden');
}
</script>
@endpush
@endsection 