@extends('layouts.admin-dashboard')

@section('header', 'Nová faktúra')

@section('content')
<div class="mx-auto max-w-4xl px-6 py-8 lg:px-8">
    <div class="mb-8">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">Vytvoriť faktúru</h2>
        <p class="text-gray-600 mt-2">Manuálne vytvorenie faktúry pre používateľa</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('platby.store') }}" method="POST" x-data="invoiceForm()">
        @csrf

        <div class="bg-white border border-gray-200 shadow-lg rounded-xl p-6 mb-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Používateľ</label>
                    <select id="user_id" name="user_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                        <option value="">Vyberte používateľa</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="due_days" class="block text-sm font-medium text-gray-700 mb-2">Splatnosť (dní)</label>
                    <input type="number" id="due_days" name="due_days" min="1" max="365" value="{{ old('due_days', 14) }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-lg rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Položky faktúry</h3>
                <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Pridať položku
                </button>
            </div>

            <template x-for="(item, index) in items" :key="index">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 items-end border-b border-gray-100 pb-4 mb-4 last:border-b-0 last:pb-0 last:mb-0">
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Názov položky</label>
                        <input type="text" :name="`items[${index}][name]`" x-model="item.name" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Množstvo</label>
                        <input type="number" step="1" min="1" :name="`items[${index}][quantity]`" x-model="item.quantity" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cena/ks (€)</label>
                        <input type="number" step="0.01" min="0" :name="`items[${index}][unit_price]`" x-model="item.unit_price" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">DPH (%)</label>
                        <input type="number" step="0.01" min="0" max="100" :name="`items[${index}][tax_percentage]`" x-model="item.tax_percentage" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="inline-flex items-center justify-center w-full px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg transition-colors">
                            Odstrániť
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div class="bg-white border border-gray-200 shadow-lg rounded-xl p-6 mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Poznámka</label>
            <textarea id="notes" name="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500">{{ old('notes') }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('platby.index') }}" class="px-6 py-3 rounded-xl text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">Zrušiť</a>
            <button type="submit" class="bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white px-6 py-3 rounded-xl text-sm font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                Vytvoriť faktúru
            </button>
        </div>
    </form>
</div>

<script>
    function invoiceForm() {
        return {
            items: [
                { name: '', quantity: 1, unit_price: '', tax_percentage: 0 }
            ],
            addItem() {
                this.items.push({ name: '', quantity: 1, unit_price: '', tax_percentage: 0 });
            },
            removeItem(index) {
                this.items.splice(index, 1);
            }
        };
    }
</script>
@endsection
