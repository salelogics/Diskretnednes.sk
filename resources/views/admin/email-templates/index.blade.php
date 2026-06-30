@extends('layouts.admin-dashboard')

@section('header', 'Správa Email Templates')

@section('content')
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

<!-- Email Templates tabuľka -->
<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">📧 Email Templates</h3>
    </div>
    <div class="overflow-x-auto">
        @if($templates->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Názov</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kľúč</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Predmet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcie</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($templates as $template)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($template->type === 'user')
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
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $template->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $template->key }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($template->subject, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($template->is_active)
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.email-templates.edit', $template) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" title="Upraviť">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <a href="{{ route('admin.email-templates.preview', $template) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="Náhľad">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <button type="button" 
                                            class="text-green-600 hover:text-green-900" 
                                            title="Test email"
                                            onclick="openTestModal('{{ $template->id }}', '{{ $template->name }}')">
                                        <i class="ri-mail-send-line"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.email-templates.duplicate', $template) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-yellow-600 hover:text-yellow-900" 
                                                title="Duplikovať"
                                                onclick="return confirm('Chcete duplikovať tento template?')">
                                            <i class="ri-file-copy-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="ri-mail-line text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">Žiadne email templates</h3>
                <p class="mt-1 text-sm text-gray-500">Zatiaľ nie sú vytvorené žiadne email templates.</p>
            </div>
        @endif
    </div>
</div>

<!-- Informácie -->
<div class="bg-white shadow rounded-lg mt-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">ℹ️ Informácie</h3>
    </div>
    <div class="px-6 py-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h6 class="text-sm font-medium text-gray-900 mb-3">📝 Dostupné premenné:</h6>
                <ul class="space-y-1 text-sm text-gray-600">
                    <li><code class="bg-gray-100 px-2 py-1 rounded text-xs">@{{ '{{ user_name }}' }}</code> - Meno používateľa</li>
                    <li><code class="bg-gray-100 px-2 py-1 rounded text-xs">@{{ '{{ user_email }}' }}</code> - Email používateľa</li>
                    <li><code class="bg-gray-100 px-2 py-1 rounded text-xs">@{{ '{{ ad_id }}' }}</code> - ID inzerátu</li>
                    <li><code class="bg-gray-100 px-2 py-1 rounded text-xs">@{{ '{{ ad_nickname }}' }}</code> - Prezývka inzerátu</li>
                    <li><code class="bg-gray-100 px-2 py-1 rounded text-xs">@{{ '{{ ad_views }}' }}</code> - Počet zobrazení</li>
                </ul>
            </div>
            <div>
                <h6 class="text-sm font-medium text-gray-900 mb-3">🔧 Funkcie:</h6>
                <ul class="space-y-1 text-sm text-gray-600">
                    <li><i class="ri-edit-line text-indigo-500 mr-2"></i><strong>Upraviť</strong> - Zmeniť obsah template</li>
                    <li><i class="ri-eye-line text-blue-500 mr-2"></i><strong>Náhľad</strong> - Zobraziť ako bude email vyzerať</li>
                    <li><i class="ri-mail-send-line text-green-500 mr-2"></i><strong>Test</strong> - Odoslať test email</li>
                    <li><i class="ri-file-copy-line text-yellow-500 mr-2"></i><strong>Duplikovať</strong> - Vytvoriť kópiu template</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal -->
<div id="testModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Test Email</h3>
                <button type="button" onclick="closeTestModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
            <form id="testForm" method="POST">
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
function openTestModal(templateId, templateName) {
    document.getElementById('modalTitle').textContent = 'Test Email - ' + templateName;
    document.getElementById('testForm').action = '{{ url("admin/email-templates") }}/' + templateId + '/test';
    document.getElementById('test_email').value = '';
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