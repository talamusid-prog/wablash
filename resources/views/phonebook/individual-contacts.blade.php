@extends('layouts.app')

@section('title', 'Kontak Individual')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('phonebook.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $group->name }}</h1>
                    <p class="text-gray-600 mt-2">Daftar kontak individual yang tidak tergabung dalam grup</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('phonebook.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Kontak</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $group->group_participants_count }}</h2>
                    <p class="text-gray-600">Total Kontak Individual</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">ID: {{ $group->contact_id }}</p>
                <p class="text-sm text-gray-500">Tipe: Kontak Individual</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('phonebook.individual-contacts') }}" class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kontak..." class="w-64 px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    Cari
                </button>
                
                @if(request('search') || request('status'))
                    <a href="{{ route('phonebook.individual-contacts') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Reset
                    </a>
                @endif
            </div>
            
            <div class="flex items-center space-x-3">
                <button type="button" onclick="exportContacts()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export</span>
                </button>
                <button type="button" onclick="showImportModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span>Import</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Contacts List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Kontak</h3>
                <div class="text-sm text-gray-500">
                    {{ $group->group_participants_count }} kontak individual
                </div>
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($group->participants as $contact)
            <div class="p-6 hover:bg-gray-50 transition-colors duration-200 individual-contact" data-contact-name="{{ $contact->name }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium text-sm">{{ strtoupper(substr($contact->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">{{ $contact->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $contact->phone_number }}</p>
                            @if($contact->email)
                            <p class="text-xs text-gray-400">{{ $contact->email }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($contact->status === 'active') bg-green-100 text-green-800
                            @elseif($contact->status === 'inactive') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($contact->status) }}
                        </span>
                        <div class="flex items-center space-x-1">
                            <button onclick="editContact('{{ $contact->id }}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteContact('{{ $contact->id }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada kontak individual</h3>
                <p class="text-gray-500">Kontak individual akan muncul setelah Anda menggunakan fitur grabber kontak atau menambahkan kontak manual</p>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Pagination -->
    @if($group->participants->hasPages())
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $group->participants->firstItem() ?? 0 }} sampai {{ $group->participants->lastItem() ?? 0 }} dari {{ $group->participants->total() }} kontak
                </div>
                <div class="flex items-center space-x-2">
                    {{ $group->participants->appends(request()->query())->links('pagination::simple-tailwind') }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- JavaScript for functionality -->
<script>
function editContact(contactId) {
    // Implement edit functionality
    console.log('Edit contact:', contactId);
}

function deleteContact(contactId) {
    // Implement delete functionality
    console.log('Delete contact:', contactId);
}

function exportContacts() {
    // Implement export functionality
    console.log('Export contacts');
}

function showImportModal() {
    // Implement import modal
    console.log('Show import modal');
}
</script>
@endsection 