@extends($isAdmin ? 'layouts.admin-dashboard' : 'layouts.user-dashboard')

@section('header', $isAdmin ? 'Admin notifikácie' : 'Notifikácie')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <!-- Mobile Header -->
            <div class="sm:hidden">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    {{ $isAdmin ? 'Admin notifikácie' : 'Notifikácie' }}
                </h1>
                <p class="text-sm text-gray-600 mb-4">
                    Spravujte svoje notifikácie a zostaňte informovaní
                </p>
                
                <!-- Mobile Actions -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        @if($unreadCount > 0)
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                                @csrf
                                @if($isAdmin)
                                    <input type="hidden" name="admin" value="true">
                                @endif
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors duration-200">
                                    <i class="ri-check-double-line mr-1 text-xs"></i>
                                    Označiť ({{ $unreadCount }})
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('notifications.delete-read') }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            @if($isAdmin)
                                <input type="hidden" name="admin" value="true">
                            @endif
                            <button type="submit" 
                                    onclick="return confirm('Vymazať prečítané notifikácie?')"
                                    class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors duration-200">
                                <i class="ri-delete-bin-line mr-1 text-xs"></i>
                                Vymazať
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Desktop Header -->
            <div class="hidden sm:flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ $isAdmin ? 'Admin notifikácie' : 'Notifikácie' }}
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Spravujte svoje notifikácie a zostaňte informovaní o dôležitých udalostiach
                    </p>
                </div>
                
                <!-- Actions - Desktop -->
                <div class="flex items-center space-x-3">
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                            @csrf
                            @if($isAdmin)
                                <input type="hidden" name="admin" value="true">
                            @endif
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <i class="ri-check-double-line mr-2"></i>
                                Označiť všetky ({{ $unreadCount }})
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('notifications.delete-read') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        @if($isAdmin)
                            <input type="hidden" name="admin" value="true">
                        @endif
                        <button type="submit" 
                                onclick="return confirm('Naozaj chcete vymazať všetky prečítané notifikácie?')"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            <i class="ri-delete-bin-line mr-2"></i>
                            Vymazať prečítané
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-3 sm:grid-cols-1 md:grid-cols-3 gap-3 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 p-3 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="ri-notification-line text-blue-600 text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-2 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Celkom</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $notifications->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 p-3 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="ri-notification-badge-line text-red-600 text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-2 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Nové</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $unreadCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 p-3 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="ri-check-line text-green-600 text-sm sm:text-base"></i>
                        </div>
                    </div>
                    <div class="ml-2 sm:ml-4 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Prečítané</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $notifications->total() - $unreadCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($notifications->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($notifications as $notification)
                        <div class="p-4 sm:p-6 {{ !$notification->is_read ? 'bg-blue-50' : 'hover:bg-gray-50' }} transition-colors duration-150">
                            <!-- Mobile Layout -->
                            <div class="sm:hidden">
                                <div class="flex items-start space-x-3">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                                            @if($notification->color === 'blue') bg-blue-100 text-blue-600
                                            @elseif($notification->color === 'green') bg-green-100 text-green-600
                                            @elseif($notification->color === 'red') bg-red-100 text-red-600
                                            @elseif($notification->color === 'yellow') bg-yellow-100 text-yellow-600
                                            @elseif($notification->color === 'purple') bg-purple-100 text-purple-600
                                            @elseif($notification->color === 'orange') bg-orange-100 text-orange-600
                                            @else bg-gray-100 text-gray-600
                                            @endif">
                                            <i class="{{ $notification->icon ?? 'ri-notification-line' }} text-sm"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1 pr-2">
                                                <h3 class="text-sm font-semibold text-gray-900 leading-tight mb-1">{{ $notification->title }}</h3>
                                                
                                                <!-- Badges - Mobile stacked -->
                                                <div class="flex flex-wrap gap-1 mb-2">
                                                    @if($notification->priority === 'urgent')
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="ri-error-warning-line mr-1"></i>
                                                            Urgentné
                                                        </span>
                                                    @elseif($notification->priority === 'high')
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                            <i class="ri-alert-line mr-1"></i>
                                                            Vysoká
                                                        </span>
                                                    @endif
                                                    
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $notification->type_label }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Unread Indicator - Mobile -->
                                            @if(!$notification->is_read)
                                                <div class="flex-shrink-0 mt-1">
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <p class="text-sm text-gray-700 mb-2 leading-relaxed">{{ $notification->message }}</p>
                                        
                                        <!-- Time info - Mobile compact -->
                                        <div class="flex items-center text-xs text-gray-500 mb-3">
                                            <i class="ri-time-line mr-1"></i>
                                            <span>{{ $notification->time_ago }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $notification->created_at->format('d.m.Y H:i') }}</span>
                                        </div>
                                        
                                        <!-- Actions - Mobile improved -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                @if($notification->action_url && $notification->action_text)
                                                    <a href="{{ $notification->action_url }}" 
                                                       class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors duration-200">
                                                        <i class="ri-arrow-right-line mr-1 text-xs"></i>
                                                        {{ Str::limit($notification->action_text, 15) }}
                                                    </a>
                                                @endif
                                            </div>
                                            
                                            <div class="flex items-center space-x-1">
                                                @if(!$notification->is_read)
                                                    <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="p-1.5 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors duration-200"
                                                                title="Označiť ako prečítané">
                                                            <i class="ri-check-line text-xs"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('Vymazať notifikáciu?')"
                                                            class="p-1.5 bg-red-100 text-red-600 rounded hover:bg-red-200 transition-colors duration-200"
                                                            title="Vymazať">
                                                        <i class="ri-delete-bin-line text-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Desktop Layout -->
                            <div class="hidden sm:flex items-start space-x-4">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                                        @if($notification->color === 'blue') bg-blue-100 text-blue-600
                                        @elseif($notification->color === 'green') bg-green-100 text-green-600
                                        @elseif($notification->color === 'red') bg-red-100 text-red-600
                                        @elseif($notification->color === 'yellow') bg-yellow-100 text-yellow-600
                                        @elseif($notification->color === 'purple') bg-purple-100 text-purple-600
                                        @elseif($notification->color === 'orange') bg-orange-100 text-orange-600
                                        @else bg-gray-100 text-gray-600
                                        @endif">
                                        <i class="{{ $notification->icon ?? 'ri-notification-line' }} text-lg"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $notification->title }}</h3>
                                                
                                                <!-- Priority Badge -->
                                                @if($notification->priority === 'urgent')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="ri-error-warning-line mr-1"></i>
                                                        Urgentné
                                                    </span>
                                                @elseif($notification->priority === 'high')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                        <i class="ri-alert-line mr-1"></i>
                                                        Vysoká priorita
                                                    </span>
                                                @endif
                                                
                                                <!-- Type Badge -->
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $notification->type_label }}
                                                </span>
                                            </div>
                                            
                                            <p class="text-gray-700 mb-3">{{ $notification->message }}</p>
                                            
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="flex items-center">
                                                    <i class="ri-time-line mr-1"></i>
                                                    {{ $notification->time_ago }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="ri-calendar-line mr-1"></i>
                                                    {{ $notification->created_at->format('d.m.Y H:i') }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Actions - Desktop -->
                                        <div class="flex items-center space-x-2 ml-4">
                                            @if($notification->action_url && $notification->action_text)
                                                <a href="{{ $notification->action_url }}" 
                                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                                    {{ $notification->action_text }}
                                                    <i class="ri-arrow-right-line ml-1"></i>
                                                </a>
                                            @endif
                                            
                                            @if(!$notification->is_read)
                                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                                        <i class="ri-check-line mr-1"></i>
                                                        Označiť
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        onclick="return confirm('Naozaj chcete vymazať túto notifikáciu?')"
                                                        class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Unread Indicator - Desktop -->
                                        @if(!$notification->is_read)
                                            <div class="flex-shrink-0 ml-2">
                                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $notifications->appends(request()->query())->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="ri-notification-off-line text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Žiadne notifikácie</h3>
                    <p class="text-gray-500">
                        {{ $isAdmin ? 'Zatiaľ nemáte žiadne admin notifikácie.' : 'Zatiaľ nemáte žiadne notifikácie.' }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 