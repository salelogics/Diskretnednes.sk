@props(['isAdmin' => false])

<div class="relative" x-data="notificationBell({{ $isAdmin ? 'true' : 'false' }})" x-init="init()">
    <!-- Notification Bell Button -->
    <button type="button" 
            @click="toggleDropdown()"
            class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors duration-200">
        <span class="absolute -inset-1.5"></span>
        <span class="sr-only">Zobraziť notifikácie</span>
        
        <!-- Bell Icon -->
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        
        <!-- Unread Count Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full min-w-[18px] h-[18px] animate-pulse">
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="closeDropdown()"
         class="absolute right-0 z-50 mt-2 w-96 origin-top-right rounded-lg bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">
                    <span x-text="isAdmin ? 'Admin notifikácie' : 'Notifikácie'"></span>
                </h3>
                <div class="flex items-center space-x-2">
                    <span x-show="unreadCount > 0" 
                          x-text="`${unreadCount} nových`"
                          class="text-xs text-gray-500"></span>
                    <button @click="markAllAsRead()" 
                            x-show="unreadCount > 0"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        Označiť všetko
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="loading">
                <div class="p-4 text-center">
                    <div class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-gray-500">Načítavam...</span>
                    </div>
                </div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="p-6 text-center">
                    <i class="ri-notification-off-line text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Žiadne nové notifikácie</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div class="border-b border-gray-100 last:border-b-0">
                    <div class="p-4 hover:bg-gray-50 transition-colors duration-150 cursor-pointer"
                         @click="handleNotificationClick(notification)"
                         :class="{ 'bg-blue-50': !notification.is_read }">
                        
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                     :class="{
                                         'bg-blue-100 text-blue-600': notification.color === 'blue',
                                         'bg-green-100 text-green-600': notification.color === 'green',
                                         'bg-red-100 text-red-600': notification.color === 'red',
                                         'bg-yellow-100 text-yellow-600': notification.color === 'yellow',
                                         'bg-purple-100 text-purple-600': notification.color === 'purple',
                                         'bg-orange-100 text-orange-600': notification.color === 'orange'
                                     }">
                                    <i :class="notification.icon || 'ri-notification-line'" class="text-sm"></i>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="notification.message"></p>
                                        
                                        <!-- Action Button -->
                                        <template x-if="notification.action_url && notification.action_text">
                                            <button @click.stop="window.location.href = notification.action_url"
                                                    class="mt-2 text-xs font-medium text-blue-600 hover:text-blue-800">
                                                <span x-text="notification.action_text"></span>
                                                <i class="ri-arrow-right-line ml-1"></i>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Priority & Time -->
                                    <div class="flex flex-col items-end ml-2">
                                        <template x-if="notification.priority === 'urgent'">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mb-1">
                                                Urgentné
                                            </span>
                                        </template>
                                        <template x-if="notification.priority === 'high'">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mb-1">
                                                Vysoká
                                            </span>
                                        </template>
                                        
                                        <span class="text-xs text-gray-500" x-text="notification.time_ago"></span>
                                        
                                        <!-- Unread indicator -->
                                        <template x-if="!notification.is_read">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-1"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
            <div class="flex items-center justify-between">
                <a :href="isAdmin ? '/notifikacie?admin=true' : '/notifikacie'" 
                   class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    Zobraziť všetky
                </a>
                <button @click="deleteReadNotifications()" 
                        class="text-sm text-gray-500 hover:text-gray-700">
                    Vymazať prečítané
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function notificationBell(isAdmin = false) {
    return {
        isOpen: false,
        isAdmin: isAdmin,
        notifications: [],
        unreadCount: 0,
        loading: false,
        refreshInterval: null,

        init() {
            this.loadNotifications();
            this.startAutoRefresh();
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications();
            }
        },

        closeDropdown() {
            this.isOpen = false;
        },

        async loadNotifications() {
            this.loading = true;
            try {
                const response = await fetch(`/api/notifikacie?admin=${this.isAdmin}&limit=10`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                }
            } catch (error) {
                console.error('Chyba pri načítavaní notifikácií:', error);
            } finally {
                this.loading = false;
            }
        },

        async markAllAsRead() {
            try {
                const response = await fetch(`/notifikacie/oznacit-vsetky?admin=${this.isAdmin}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Chyba pri označovaní notifikácií:', error);
            }
        },

        async handleNotificationClick(notification) {
            // Mark as read if not already
            if (!notification.is_read) {
                try {
                    await fetch(`/notifikacie/${notification.id}/precitat`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    notification.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch (error) {
                    console.error('Chyba pri označovaní notifikácie:', error);
                }
            }

            // Navigate to action URL if exists
            if (notification.action_url) {
                window.location.href = notification.action_url;
            }
            
            this.closeDropdown();
        },

        async deleteReadNotifications() {
            try {
                const formData = new FormData();
                if (this.isAdmin) {
                    formData.append('admin', 'true');
                }

                const response = await fetch(`/notifikacie/vymazat-precitane`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.notifications = this.notifications.filter(n => !n.is_read);
                    console.log(`Vymazaných ${data.deleted_count} prečítaných notifikácií`);
                }
            } catch (error) {
                console.error('Chyba pri mazaní notifikácií:', error);
            }
        },

        startAutoRefresh() {
            // Refresh every 30 seconds
            this.refreshInterval = setInterval(() => {
                if (!this.isOpen) {
                    this.loadNotifications();
                }
            }, 30000);
        },

        destroy() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
        }
    }
}
</script> 