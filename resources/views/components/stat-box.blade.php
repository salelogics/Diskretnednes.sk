<div class="bg-white border border-gray-200 shadow-lg rounded-xl flex flex-col h-full">
    <div class="p-6 flex-1 flex items-center">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-{{ $color }}-100">
                <i class="{{ $icon }} text-{{ $color }}-600 text-2xl"></i>
            </div>
        </div>
        <div class="ml-4 flex-1">
            <dt class="text-sm font-medium text-gray-600">{{ $title }}</dt>
            <dd class="text-2xl font-bold text-gray-900">{{ $value }}</dd>
            @if(!empty($extra))
                <div class="text-xs text-gray-500">{{ $extra }}</div>
            @endif
        </div>
    </div>
    <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
        <div class="text-sm text-gray-600">
            {{ $subtitle }}
        </div>
    </div>
</div>