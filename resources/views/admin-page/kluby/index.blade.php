@extends('layouts.admin-dashboard')

@section('header', 'Erotické kluby')

@section('content')
<div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <!-- Breadcrumbs a header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <!-- Breadcrumbs -->
                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-3">
                    <a href="{{ route('admin.nastenka') }}" class="hover:text-pink-600 transition-colors">Admin</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-medium">Erotické kluby</span>
                </div>
                
                <!-- Nadpis -->
                <h1 class="text-3xl font-bold text-gray-900">Erotické kluby</h1>
                <p class="mt-2 text-gray-600">Spravujte erotické kluby v systéme</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.kluby.create') }}" class="inline-flex items-center px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Pridať klub
                </a>
            </div>
        </div>
    </div>

<div x-data="{
    showDeleteModal: false,
    deleteClubId: null,
    deleteClubName: '',
    confirmDelete(id, name) {
        this.deleteClubId = id;
        this.deleteClubName = name;
        this.showDeleteModal = true;
    },
    closeModal() {
        this.showDeleteModal = false;
        this.deleteClubId = null;
        this.deleteClubName = '';
    }
}">
    <!-- Zoznam klubov -->
    <div class="bg-white shadow-xl rounded-2xl border border-pink-500/20">
        <div class="px-6 py-6 border-b border-pink-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Všetky kluby</h3>
                    <p class="text-sm text-gray-500 mt-1">Prehľad a správa erotických klubov</p>
                </div>
                <div class="flex space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        Aktívne
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                        Neaktívne
                    </span>
                </div>
            </div>
        </div>
        
        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-pink-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klub</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vytvorené</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stav</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcie</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($clubs as $club)
                            <tr class="hover:bg-pink-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 mr-3 flex-shrink-0">
                                            @if($club->logo_path && !empty($club->logo_path) && file_exists(public_path($club->logo_path)))
                                                <img class="w-10 h-10 rounded-full object-cover" src="{{ asset($club->logo_path) }}" alt="{{ $club->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center" style="display: none;">
                                                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                </div>
                                            @elseif($club->image_path && !empty($club->image_path) && file_exists(public_path(str_starts_with($club->image_path, 'storage/') ? $club->image_path : 'storage/'.ltrim($club->image_path,'/'))))
                                                <img class="w-10 h-10 rounded-full object-cover" src="{{ asset(str_starts_with($club->image_path, 'storage/') ? $club->image_path : 'storage/'.ltrim($club->image_path,'/')) }}" alt="{{ $club->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center" style="display: none;">
                                                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $club->name }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($club->address, 30) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $club->created_at->format('d.m.Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $club->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($club->is_active) bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $club->is_active ? 'Aktívny' : 'Neaktívny' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.kluby.edit', $club) }}" class="text-pink-600 hover:text-pink-900 font-medium mr-3">
                                        Upraviť
                                    </a>
                                    <button @click.prevent="confirmDelete({{ $club->id }}, '{{ addslashes($club->name) }}')" class="text-red-600 hover:text-red-900 font-medium">
                                        Vymazať
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Žiadne kluby</h3>
                                    <p class="mt-1 text-sm text-gray-500">Zatiaľ neboli vytvorené žiadne kluby.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginácia -->
    @if($clubs->hasPages())
        <div class="mt-6">
            {{ $clubs->links() }}
        </div>
    @endif

<!-- Modal -->
<div x-show="showDeleteModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto p-6">
      <div class="sm:flex sm:items-start">
        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
          <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
          <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Vymazať klub</h3>
          <div class="mt-2">
            <p class="text-sm text-gray-500">Naozaj chcete vymazať klub <span class="font-semibold text-gray-900" x-text="deleteClubName"></span>? Táto akcia je nevratná.</p>
          </div>
        </div>
      </div>
      <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
        <form id="deleteClubForm" action="{{ route('admin.kluby.destroy', ['club' => 0]) }}" method="POST" @submit.prevent="
          if(deleteClubId){
            $event.target.action = '{{ url('/admin/kluby') }}/' + deleteClubId;
            $event.target.submit();
          }
        ">
          @csrf
          @method('DELETE')
          <input type="hidden" name="club_id" :value="deleteClubId">
          <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Vymazať</button>
        </form>
        <button type="button" @click="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Zrušiť</button>
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection 