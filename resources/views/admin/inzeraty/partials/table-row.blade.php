<tr class="hover:bg-pink-50 transition-colors">
    <td class="px-2 sm:px-3 py-2 whitespace-nowrap">
        <input type="checkbox" class="ad-row-checkbox h-4 w-4 rounded border-gray-300 text-pink-600 focus:ring-pink-500" value="{{ $ad->id }}">
    </td>
    <td class="px-2 sm:px-3 py-2 whitespace-nowrap overflow-hidden">
        <div class="flex items-center min-w-0">
            <!-- Fotka inzerátu -->
            <div class="w-9 h-9 rounded-lg overflow-hidden mr-2 bg-gray-100 flex-shrink-0">
                @if($ad->verification_image_url)
                    <img src="{{ $ad->verification_image_url }}" alt="Overovacia fotka" class="w-full h-full object-cover">
                @elseif($ad->gallery_image_urls && count($ad->gallery_image_urls) > 0)
                    <img src="{{ $ad->gallery_image_urls[0] }}" alt="Galéria" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="min-w-0">
                <div class="text-sm font-medium text-gray-900 truncate">#{{ $ad->id }}</div>
                <div class="text-sm text-gray-500 truncate">{{ $ad->nickname ?: $ad->offer_type_label }}</div>
                @if($ad->gallery_image_urls && count($ad->gallery_image_urls) > 0 || $ad->verification_image_url)
                    <div class="text-xs text-gray-400 mt-1 truncate">
                        {{ count($ad->gallery_image_urls ?? []) + ($ad->verification_image_url ? 1 : 0) }} fotiek
                    </div>
                @endif
            </div>
        </div>
    </td>
    <td class="hidden md:table-cell px-3 py-2 overflow-hidden">
        @if($ad->user)
            <div class="flex items-center min-w-0">
                <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center mr-2 flex-shrink-0">
                    <span class="text-xs font-medium text-gray-600">{{ substr($ad->user->name, 0, 1) }}</span>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">{{ $ad->user->name }}</div>
                    <div class="text-sm text-gray-500 truncate">{{ $ad->user->email }}</div>
                </div>
            </div>
        @else
            <div class="text-sm text-gray-500 truncate">Používateľ neexistuje</div>
        @endif
    </td>
    <td class="hidden lg:table-cell px-3 py-2 overflow-hidden">
        <span class="inline-block max-w-full truncate align-middle px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800" title="{{ $ad->offer_type_label }}">
            {{ $ad->offer_type_label }}
        </span>
    </td>
    <td class="hidden lg:table-cell px-3 py-2 whitespace-nowrap">
        <div class="text-sm text-gray-900">{{ $ad->created_at ? $ad->created_at->format('d.m.Y') : 'Neuvedené' }}</div>
        <div class="text-xs text-gray-500">{{ $ad->created_at ? $ad->created_at->format('H:i') : '' }}</div>
    </td>
    <td class="px-2 sm:px-3 py-2 overflow-hidden">
        <div class="flex items-center gap-1 min-w-0">
            <!-- Stav inzerátu -->
            <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                @if($ad->status === 'active' && $ad->isSubscriptionActive()) bg-green-100 text-green-800
                @elseif($ad->status === 'active' && !$ad->isSubscriptionActive()) bg-orange-100 text-orange-800
                @elseif($ad->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($ad->status === 'inactive') bg-gray-100 text-gray-800
                @else bg-red-100 text-red-800
                @endif">
                @if($ad->status === 'active' && $ad->isSubscriptionActive()) Aktívny
                @elseif($ad->status === 'active' && !$ad->isSubscriptionActive()) Neviditeľný (Expirované)
                @elseif($ad->status === 'pending') Čakajúci
                @elseif($ad->status === 'inactive') Neaktívny
                @else Zamietnutý
                @endif
            </span>
            
            <!-- Ikonka predplatného -->
            @if($ad->isSubscriptionActive())
                <div class="flex items-center flex-shrink-0" title="Aktívne predplatné do {{ $ad->subscription_expires_at ? $ad->subscription_expires_at->format('d.m.Y') : '' }}">
                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
            @elseif($ad->isSubscriptionExpired())
                <div class="flex items-center flex-shrink-0" title="Predplatné vypršalo">
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            @else
                <div class="flex items-center flex-shrink-0" title="Bez predplatného">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            @endif
        </div>
    </td>
    <td class="px-2 sm:px-3 py-2 text-right text-sm font-medium">
        <div class="relative inline-block text-left" x-data="{ open: false }">
            <div>
                <button type="button" @click="open = !open" class="inline-flex items-center px-2 py-1.5 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    Akcie
                    <svg class="-mr-1 ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" style="display: none;">
                <div class="py-1">
                    <!-- Zobraziť -->
                    <a href="{{ route('admin.inzeraty.show', $ad) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Zobraziť detail
                    </a>

                    <!-- Upraviť -->
                    <a href="{{ route('ads.edit', $ad) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" target="_blank">
                        <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Upraviť inzerát
                    </a>

                    <!-- Aktivovať/Deaktivovať -->
                    @if($ad->status === 'active')
                        <form method="POST" action="{{ route('admin.inzeraty.update-status', $ad) }}" class="block">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="inactive">
                            <button type="submit" class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364L18.364 5.636" />
                                </svg>
                                Deaktivovať
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.inzeraty.update-status', $ad) }}" class="block">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="active">
                            <button type="submit" class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <svg class="mr-3 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Aktivovať
                            </button>
                        </form>
                    @endif

                    <!-- Divider -->
                    <div class="border-t border-gray-100"></div>

                    <!-- Vymazať -->
                    <form method="POST" action="{{ route('admin.inzeraty.destroy', $ad) }}" class="block" onsubmit="return confirm('Naozaj chcete vymazať tento inzerát?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="group flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                            <svg class="mr-3 h-4 w-4 text-red-400 group-hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Vymazať
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </td>
</tr>
