@extends('layouts.user-dashboard')

@section('header', 'Support Ticket #' . $ticket->id)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Support Ticket #{{ $ticket->id }}
                    </h1>
                    <p class="mt-2 text-gray-600">
                        {{ $ticket->subject }}
                    </p>
                </div>
                
                <!-- Status Badge -->
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($ticket->status === 'new') bg-blue-100 text-blue-800
                        @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-800
                        @elseif($ticket->status === 'resolved') bg-green-100 text-green-800
                        @elseif($ticket->status === 'closed') bg-gray-100 text-gray-800
                        @endif">
                        {{ $ticket->status_label }}
                    </span>
                    
                    <a href="{{ route('support.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <i class="ri-arrow-left-line mr-2"></i>
                        Späť na podporu
                    </a>
                </div>
            </div>
        </div>

        <!-- Ticket Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Informácie o tickete</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Typ problému</label>
                        <p class="text-gray-900">{{ $ticket->problem_type_label }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <p class="text-gray-900">{{ $ticket->status_label }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meno</label>
                        <p class="text-gray-900">{{ $ticket->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900">{{ $ticket->email }}</p>
                    </div>
                    @if($ticket->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefón</label>
                        <p class="text-gray-900">{{ $ticket->phone }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vytvorené</label>
                        <p class="text-gray-900">{{ $ticket->created_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Konverzácia</h2>
            </div>
            <div class="p-6">
                <!-- Original Message -->
                <div class="mb-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="ri-user-line text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $ticket->name }}</h3>
                                <span class="text-xs text-gray-500">{{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $ticket->message }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Response -->
                @if($ticket->admin_response)
                <div class="mb-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="ri-admin-line text-green-600"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="text-sm font-semibold text-gray-900">Admin</h3>
                                <span class="text-xs text-gray-500">{{ $ticket->responded_at?->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $ticket->admin_response }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Reply Form -->
        @if($ticket->status !== 'closed')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    @if($ticket->admin_response)
                        Odpoveď na admin správu
                    @else
                        Pridať informácie
                    @endif
                </h2>
            </div>
            <div class="p-6">
                <form id="replyForm" action="{{ route('support.ticket.reply', $ticket) }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Vaša správa
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="6" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                  placeholder="Napíšte vašu odpoveď alebo doplňujúce informácie..."
                                  required></textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Vaša odpoveď bude pridaná k existujúcej konverzácii.
                        </p>
                        
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            <i class="ri-send-plane-line mr-2"></i>
                            Odoslať odpoveď
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 text-center">
            <i class="ri-lock-line text-3xl text-gray-400 mb-2"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Ticket je uzavretý</h3>
            <p class="text-gray-600">
                Tento support ticket bol uzavretý a nie je možné pridávať ďalšie odpovede.
            </p>
        </div>
        @endif
    </div>
</div>

<script>
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Disable button and show loading
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Odosielam...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const successDiv = document.createElement('div');
            successDiv.className = 'mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg';
            successDiv.innerHTML = '<i class="ri-check-line mr-2"></i>' + data.message;
            form.parentNode.insertBefore(successDiv, form);
            
            // Clear form
            form.reset();
            
            // Reload page after 2 seconds to show the new reply
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Nastala chyba');
        }
    })
    .catch(error => {
        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg';
        errorDiv.innerHTML = '<i class="ri-error-warning-line mr-2"></i>' + error.message;
        form.parentNode.insertBefore(errorDiv, form);
    })
    .finally(() => {
        // Re-enable button
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});
</script>
@endsection 