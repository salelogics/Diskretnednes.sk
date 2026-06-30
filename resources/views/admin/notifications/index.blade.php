@extends('layouts.admin-dashboard')

@section('header', 'Admin Notifikácie')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Flash správy -->
    @if(session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Štatistiky notifikácií -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <!-- Celkom notifikácií -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Celkom notifikácií</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $notifications->total() }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    všetky admin notifikácie
                </div>
            </div>
        </div>

        <!-- Neprečítané -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Neprečítané</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $unreadCount }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    čakajú na prečítanie
                </div>
            </div>
        </div>

        <!-- Prečítané -->
        <div class="bg-white border border-gray-200 shadow-lg rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dt class="text-sm font-medium text-gray-600">Prečítané</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $notifications->total() - $unreadCount }}</dd>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    úspešne spracované
                </div>
            </div>
        </div>
    </div>

    <!-- Zoznam notifikácií -->
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Admin Notifikácie</h3>
                    <p class="text-sm text-gray-500 mt-1">Správa systémových notifikácií pre administrátorov</p>
                </div>
                <div class="flex space-x-2">
                    @if($unreadCount > 0)
                        <button onclick="markAllAsRead()" class="inline-flex items-center px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white font-medium rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Označiť všetky ako prečítané
                        </button>
                    @endif
                    <button onclick="deleteReadNotifications()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Vymazať prečítané
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                @if($notifications->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-pink-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notifikácia</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Typ</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorita</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vytvorené</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcie</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($notifications as $notification)
                                <tr class="hover:bg-pink-50 transition-colors {{ $notification->is_read ? '' : 'bg-blue-50/30' }}" data-id="{{ $notification->id }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <!-- Icon -->
                                            <div class="flex-shrink-0 mr-4">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center
                                                    @if($notification->priority === 'urgent') bg-red-100 text-red-600
                                                    @elseif($notification->priority === 'high') bg-orange-100 text-orange-600
                                                    @elseif($notification->type === 'payment') bg-green-100 text-green-600
                                                    @elseif($notification->type === 'support') bg-blue-100 text-blue-600
                                                    @else bg-pink-100 text-pink-600
                                                    @endif">
                                                    @if($notification->icon)
                                                        <i class="{{ $notification->icon }} text-base"></i>
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900 truncate">{{ $notification->title }}</div>
                                                <div class="text-sm text-gray-500 mt-1 max-w-md line-clamp-2">{{ $notification->message }}</div>
                                                @if($notification->action_url && $notification->action_text)
                                                    <div class="mt-2">
                                                        <a href="{{ $notification->action_url }}" class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                                                            {{ $notification->action_text }} →
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $notification->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($notification->priority === 'urgent')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="ri-error-warning-line text-base mr-1"></i> Urgentné
                                            </span>
                                        @elseif($notification->priority === 'high')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="ri-flashlight-line text-base mr-1"></i> Vysoká
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="ri-information-line text-base mr-1"></i> Normálna
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $notification->created_at->format('d.m.Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $notification->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(!$notification->is_read)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="ri-star-line text-base mr-1"></i> Nové
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="ri-check-line text-base mr-1"></i> Prečítané
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <div>
                                                <button type="button" @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                                                    Akcie
                                                    <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" style="display: none;">
                                                <div class="py-1">
                                                    @if($notification->action_url && $notification->action_text)
                                                        <!-- Akčný odkaz -->
                                                        <a href="{{ $notification->action_url }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                            </svg>
                                                            {{ $notification->action_text }}
                                                        </a>
                                                    @endif
                                                    
                                                    @if(!$notification->is_read)
                                                        <!-- Označiť ako prečítané -->
                                                        <button onclick="markAsRead({{ $notification->id }})" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                            <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Označiť ako prečítané
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Vymazať -->
                                                    <button onclick="deleteNotification({{ $notification->id }})" class="group flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50 w-full text-left">
                                                        <svg class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Vymazať
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-8 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-pink-100 rounded-3xl flex items-center justify-center mb-6">
                                <svg class="w-10 h-10 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Žiadne notifikácie</h3>
                            <p class="text-gray-500 max-w-md">Momentálne nemáte žiadne admin notifikácie. Nové notifikácie sa zobrazia automaticky.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Paginácia -->
    @if($notifications->hasPages())
        <div class="mt-8">
            <div class="bg-white rounded-2xl border border-pink-500/20 p-4">
                {{ $notifications->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Custom Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999]">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Vymazať notifikáciu</h3>
                        <p class="text-sm text-gray-600">Táto akcia sa nedá vrátiť späť</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6" id="modalMessage">Naozaj chcete vymazať túto notifikáciu?</p>
                <div class="flex space-x-3">
                    <button onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                        Zrušiť
                    </button>
                    <button onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        Vymazať
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
let currentDeleteId = null;
let currentDeleteType = 'single'; // 'single' or 'read'

function markAsRead(notificationId) {
    const notificationElement = document.querySelector(`[data-id="${notificationId}"]`);
    
    // Okamžite aktualizuj UI
    if (notificationElement) {
        notificationElement.classList.remove('bg-blue-50/30');
        
        // Aktualizuj status badge
        const statusBadge = notificationElement.querySelector('td:nth-child(5) span');
        if (statusBadge) {
            statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
            statusBadge.innerHTML = '✅ Prečítané';
        }
        
        // Odstráň "Označiť ako prečítané" tlačidlo z dropdown
        const readButton = notificationElement.querySelector('button[onclick*="markAsRead"]');
        if (readButton) {
            readButton.remove();
        }
        
        // Aktualizuj počítadlá
        updateCounters(-1, 1);
    }
    
    // Pošli request na server
    fetch(`/admin/notifikacie/${notificationId}/precitat`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('✅ Notifikácia označená ako prečítaná', 'success');
        } else {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('❌ Chyba pri označovaní notifikácie', 'error');
        location.reload();
    });
}

function markAllAsRead() {
    const unreadNotifications = document.querySelectorAll('.bg-blue-50\\/30');
    const unreadCount = unreadNotifications.length;
    
    if (unreadCount === 0) {
        showToast('ℹ️ Žiadne neprečítané notifikácie', 'info');
        return;
    }
    
    // Okamžite aktualizuj UI
    unreadNotifications.forEach(notification => {
        notification.classList.remove('bg-blue-50/30');
        
        // Aktualizuj status badge
        const statusBadge = notification.querySelector('td:nth-child(5) span');
        if (statusBadge) {
            statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
            statusBadge.innerHTML = '✅ Prečítané';
        }
        
        // Odstráň "Označiť ako prečítané" tlačidlo
        const readButton = notification.querySelector('button[onclick*="markAsRead"]');
        if (readButton) {
            readButton.remove();
        }
    });
    
    // Aktualizuj počítadlá
    updateCounters(-unreadCount, unreadCount);
    
    // Skry "Označiť všetky" tlačidlo
    const markAllButton = document.querySelector('button[onclick="markAllAsRead()"]');
    if (markAllButton) {
        markAllButton.style.display = 'none';
    }
    
    fetch('{{ route('admin.notifications.mark-all-read') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`✅ Označených ${data.marked_count} notifikácií ako prečítaných`, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('❌ Chyba pri označovaní notifikácií', 'error');
        location.reload();
    });
}

function deleteNotification(notificationId) {
    currentDeleteId = notificationId;
    currentDeleteType = 'single';
    
    const notification = document.querySelector(`[data-id="${notificationId}"]`);
    const title = notification?.querySelector('.text-sm.font-medium')?.textContent || 'túto notifikáciu';
    
    document.getElementById('modalMessage').textContent = `Naozaj chcete vymazať notifikáciu "${title}"?`;
    showModal();
}

function deleteReadNotifications() {
    const readNotifications = document.querySelectorAll('tr:not(.bg-blue-50\\/30)');
    const readCount = readNotifications.length - 1; // -1 pre header row
    
    if (readCount <= 0) {
        showToast('ℹ️ Žiadne prečítané notifikácie na vymazanie', 'info');
        return;
    }
    
    currentDeleteType = 'read';
    document.getElementById('modalMessage').textContent = `Naozaj chcete vymazať všetky prečítané notifikácie (${readCount})?`;
    showModal();
}

function showModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    currentDeleteId = null;
    currentDeleteType = 'single';
}

function confirmDelete() {
    if (currentDeleteType === 'single' && currentDeleteId) {
        performSingleDelete(currentDeleteId);
    } else if (currentDeleteType === 'read') {
        performReadDelete();
    }
    closeModal();
}

function performSingleDelete(notificationId) {
    const notificationElement = document.querySelector(`[data-id="${notificationId}"]`);
    const isUnread = notificationElement?.classList.contains('bg-blue-50/30');
    
    // Okamžite odstráň z UI
    if (notificationElement) {
        notificationElement.remove();
        
        // Aktualizuj počítadlá
        if (isUnread) {
            updateCounters(-1, 0, -1);
        } else {
            updateCounters(0, -1, -1);
        }
        
        checkEmptyState();
    }
    
    fetch(`/admin/notifikacie/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('🗑️ Notifikácia vymazaná', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('❌ Chyba pri mazaní notifikácie', 'error');
        location.reload();
    });
}

function performReadDelete() {
    const readNotifications = document.querySelectorAll('tbody tr:not(.bg-blue-50\\/30)');
    const readCount = readNotifications.length;
    
    // Okamžite odstráň z UI
    readNotifications.forEach(notification => {
        notification.remove();
    });
    
    // Aktualizuj počítadlá
    updateCounters(0, -readCount, -readCount);
    checkEmptyState();
    
    fetch('{{ route('admin.notifications.delete-read') }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`🗑️ Vymazaných ${data.deleted_count} prečítaných notifikácií`, 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('❌ Chyba pri mazaní notifikácií', 'error');
        location.reload();
    });
}

function updateCounters(unreadChange, readChange, totalChange = null) {
    // Aktualizuj počítadlá v štatistikách
    const totalElement = document.querySelector('.text-2xl.font-bold.text-gray-900');
    const unreadElement = document.querySelectorAll('.text-2xl.font-bold.text-gray-900')[1];
    const readElement = document.querySelectorAll('.text-2xl.font-bold.text-gray-900')[2];
    
    if (totalElement && totalChange !== null) {
        const currentTotal = parseInt(totalElement.textContent);
        totalElement.textContent = Math.max(0, currentTotal + totalChange);
    }
    
    if (unreadElement && unreadChange !== 0) {
        const currentUnread = parseInt(unreadElement.textContent);
        unreadElement.textContent = Math.max(0, currentUnread + unreadChange);
    }
    
    if (readElement && readChange !== 0) {
        const currentRead = parseInt(readElement.textContent);
        readElement.textContent = Math.max(0, currentRead + readChange);
    }
}

function checkEmptyState() {
    const tableBody = document.querySelector('tbody');
    
    if (!tableBody || tableBody.children.length === 0) {
        // Zobraz prázdny stav
        const tableContainer = document.querySelector('.overflow-x-auto');
        const emptyState = `
            <div class="px-8 py-16 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-pink-100 rounded-3xl flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Žiadne notifikácie</h3>
                    <p class="text-gray-500 max-w-md">Momentálne nemáte žiadne admin notifikácie. Nové notifikácie sa zobrazia automaticky.</p>
                </div>
            </div>
        `;
        
        tableContainer.innerHTML = emptyState;
    }
}

function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    const icons = {
        success: '<i class="ri-checkbox-circle-line text-lg mr-2"></i>',
        error: '<i class="ri-close-circle-line text-lg mr-2"></i>',
        info: '<i class="ri-information-line text-lg mr-2"></i>',
        delete: '<i class="ri-delete-bin-line text-lg mr-2"></i>'
    };
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        delete: 'bg-red-500'
    };
    toast.className = `${colors[type] || colors.info} text-white px-6 py-4 rounded-xl shadow-lg max-w-sm transition-all duration-300 transform translate-x-full`;
    toast.innerHTML = `
        <div class="flex items-center">
            ${icons[type] || icons.info}
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 100);
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, 5000);
}

// Zatvorenie modalu pri kliknutí mimo
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// ESC key pre zatvorenie modalu
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection