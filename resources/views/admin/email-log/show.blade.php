@extends('layouts.admin-dashboard')

@section('header', 'Detail emailu #' . $emailLog->id)

@section('content')
<div class="space-y-6">
    <!-- Základné informácie -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Základné informácie</h3>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.email-log.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 flex items-center">
                        <i class="ri-arrow-left-line text-sm mr-2"></i>
                        Späť na zoznam
                    </a>
                    <form method="POST" action="{{ route('admin.email-log.destroy', $emailLog->id) }}" class="inline" onsubmit="return confirm('Naozaj chcete vymazať tento email log?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 flex items-center">
                            <i class="ri-delete-bin-line text-sm mr-2"></i>
                            Vymazať
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Príjemca</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $emailLog->to_email }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Odosielateľ</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $emailLog->from_email ?? 'Niet zadané' }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Typ emailu</dt>
                    <dd class="mt-1">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $emailLog->type_name }}
                        </span>
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @if($emailLog->status === 'sent')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="ri-check-line text-xs mr-1"></i>
                                {{ $emailLog->status_name }}
                            </span>
                        @elseif($emailLog->status === 'failed')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="ri-close-line text-xs mr-1"></i>
                                {{ $emailLog->status_name }}
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="ri-time-line text-xs mr-1"></i>
                                {{ $emailLog->status_name }}
                            </span>
                        @endif
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dátum odoslania</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $emailLog->sent_at ? $emailLog->sent_at->format('d.m.Y H:i:s') : 'Neodoslaný' }}
                    </dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dátum vytvorenia</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $emailLog->created_at->format('d.m.Y H:i:s') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Predmet a obsah -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Obsah emailu</h3>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500 mb-2">Predmet</dt>
                <dd class="text-sm text-gray-900 bg-gray-50 rounded p-3">{{ $emailLog->subject }}</dd>
            </div>
            
            @if($emailLog->body)
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-2">Obsah emailu</dt>
                    <dd class="text-sm text-gray-900 bg-gray-50 rounded p-3">
                        @if(strpos($emailLog->body, '<') !== false)
                            <!-- HTML obsah -->
                            <div class="prose max-w-none">
                                {!! $emailLog->body !!}
                            </div>
                        @else
                            <!-- Textový obsah -->
                            <pre class="whitespace-pre-wrap font-sans">{{ $emailLog->body }}</pre>
                        @endif
                    </dd>
                </div>
            @else
                <div class="text-center py-4 text-gray-500">
                    <i class="ri-file-text-line text-2xl"></i>
                    <p class="mt-2">Obsah emailu nie je uložený</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Chybová správa -->
    @if($emailLog->error_message)
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-red-600">
                    <i class="ri-error-warning-line mr-2"></i>
                    Chybová správa
                </h3>
            </div>
            <div class="px-6 py-4">
                <div class="bg-red-50 border border-red-200 rounded p-4">
                    <pre class="text-sm text-red-800 whitespace-pre-wrap">{{ $emailLog->error_message }}</pre>
                </div>
            </div>
        </div>
    @endif

    <!-- Dodatočné informácie -->
    @if($emailLog->metadata || $emailLog->headers)
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Dodatočné informácie</h3>
            </div>
            <div class="px-6 py-4 space-y-6">
                @if($emailLog->metadata)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">Metadata</dt>
                        <dd class="text-sm text-gray-900">
                            <div class="bg-gray-50 rounded p-3">
                                <pre class="text-xs">{{ json_encode($emailLog->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </dd>
                    </div>
                @endif
                
                @if($emailLog->headers)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-2">Email hlavičky</dt>
                        <dd class="text-sm text-gray-900">
                            <div class="bg-gray-50 rounded p-3">
                                <pre class="text-xs">{{ json_encode($emailLog->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </dd>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection 