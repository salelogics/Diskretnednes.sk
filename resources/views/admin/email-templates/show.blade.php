@extends('layouts.admin-dashboard')

@section('header', 'Detail Email Template')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.email-templates.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h2 class="text-xl font-semibold text-gray-900">Detail Email Template</h2>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" 
           class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 flex items-center">
            <i class="ri-edit-line text-sm mr-2"></i>
            Upraviť
        </a>
        <a href="{{ route('admin.email-templates.preview', $emailTemplate) }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
            <i class="ri-eye-2-line text-sm mr-2"></i>
            Náhľad
        </a>
        <a href="{{ route('admin.email-templates.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            Späť na zoznam
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Hlavný obsah -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Obsah Template</h3>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div>
                    <h6 class="text-sm font-medium text-gray-500 mb-2">Predmet emailu:</h6>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="text-gray-900">{{ $emailTemplate->subject }}</p>
                    </div>
                </div>

                <div>
                    <h6 class="text-sm font-medium text-gray-500 mb-2">HTML obsah:</h6>
                    <div class="bg-gray-50 p-4 rounded-md overflow-x-auto" style="max-height: 400px;">
                        <pre class="text-sm text-gray-900"><code>{{ $emailTemplate->content }}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Základné informácie -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">ℹ️ Základné informácie</h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
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
        </div>

        <!-- Dostupné premenné -->
        @if($emailTemplate->variables)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">📝 Dostupné premenné</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-2">
                        @foreach($emailTemplate->getAvailableVariables() as $variable)
                            <div class="flex items-center">
                                <code class="text-sm bg-gray-100 px-2 py-1 rounded text-blue-600">@{{ '{{ ' . $variable . ' }}' }}</code>
                            </div>
                        @endforeach
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mt-4">
                        <p class="text-sm text-blue-700">
                            <strong>Tip:</strong> Tieto premenné sa automaticky nahradia skutočnými hodnotami pri odosielaní emailu.
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
                <a href="{{ route('admin.email-templates.edit', $emailTemplate) }}" 
                   class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 flex items-center justify-center">
                    <i class="ri-edit-line mr-2"></i>
                    Upraviť template
                </a>
                
                <a href="{{ route('admin.email-templates.preview', $emailTemplate) }}" 
                   class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center justify-center">
                    <i class="ri-eye-2-line mr-2"></i>
                    Zobraziť náhľad
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
                        Duplikovať
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal -->
<div id="testModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Test Email - {{ $emailTemplate->name }}</h3>
                <button type="button" onclick="closeTestModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.email-templates.test', $emailTemplate) }}">
                @csrf
                <div class="mb-4">
                    <label for="test_email" class="block text-sm font-medium text-gray-700 mb-2">Email adresa</label>
                    <input type="email" id="test_email" name="test_email" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="test@example.com" required>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4">
                    <p class="text-sm text-blue-700">Email bude odoslaný s ukážkovými dátami a predmetom začínajúcim "[TEST]"</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeTestModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300">
                        Zrušiť
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        <i class="ri-mail-send-line mr-2"></i>Odoslať test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTestModal() {
    document.getElementById('testModal').classList.remove('hidden');
}

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('testModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTestModal();
    }
});
</script>
@endsection 