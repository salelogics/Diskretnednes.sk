@if(session()->has('impersonating_admin_id'))
    @php
        $adminId = session('impersonating_admin_id');
        $admin = $adminId ? \App\Models\User::find($adminId) : null;
    @endphp
    <div class="bg-yellow-500 border-b border-yellow-600">
        <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between flex-wrap">
                <div class="w-0 flex-1 flex items-center">
                    <span class="flex p-2 rounded-lg bg-yellow-600">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </span>
                    <p class="ml-3 font-medium text-white">
                        <span class="md:hidden">
                            Ste prepnutí na používateľa {{ auth()->user()->name }}
                        </span>
                        <span class="hidden md:inline">
                            Ste prepnutí na používateľa <strong>{{ auth()->user()->name }}</strong> 
                            @if($admin)
                                (pôvodný admin: {{ $admin->name }})
                            @endif
                        </span>
                    </p>
                </div>
                <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                    <form method="POST" action="{{ route('stop-impersonating') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-yellow-600 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                            </svg>
                            Vrátiť sa ako admin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif 