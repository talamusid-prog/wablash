@extends('layouts.app')

@section('title', 'Peserta Grup - ' . $group->name)

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <a href="{{ route('phonebook.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $group->name }}</h1>
                </div>
                <p class="text-gray-600">Daftar peserta dengan nomor telepon</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('phonebook.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>Kembali ke Phonebook</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Group Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900">{{ $group->name }}</h2>
                <div class="flex items-center space-x-6 mt-2 text-sm text-gray-500">
                    <span>ID: {{ $group->contact_id }}</span>
                    <span>{{ $group->group_participants_count ?? 0 }} total peserta</span>
                    <span>{{ $group->participants->count() }} dengan nomor telepon</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="relative">
                    <input type="text" id="searchParticipants" placeholder="Cari peserta..." class="w-64 px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="blocked">Diblokir</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-3">
                <button onclick="exportParticipants()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Participants List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Peserta</h3>
                <div class="text-sm text-gray-500">
                    {{ $group->participants->count() }} peserta dengan nomor telepon
                </div>
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($group->participants as $participant)
            <div class="p-6 hover:bg-gray-50 transition-colors duration-200 participant-item" data-participant-name="{{ $participant->name }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium text-sm">{{ strtoupper(substr($participant->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">{{ $participant->name }}</h4>
                            <p class="text-sm text-gray-500 font-mono">{{ $participant->phone_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($participant->is_admin)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Admin
                        </span>
                        @endif
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($participant->status === 'active') bg-green-100 text-green-800
                            @elseif($participant->status === 'inactive') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($participant->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada peserta dengan nomor telepon</h3>
                <p class="text-gray-500">Peserta grup mungkin belum di-grab atau tidak memiliki nomor telepon</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchParticipants').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    document.querySelectorAll('.participant-item').forEach(item => {
        const participantName = item.querySelector('h4').textContent.toLowerCase();
        const participantPhone = item.querySelector('.text-sm').textContent.toLowerCase();
        const hasMatch = participantName.includes(searchTerm) || participantPhone.includes(searchTerm);
        item.style.display = hasMatch ? 'block' : 'none';
    });
});

// Status filter functionality
document.getElementById('statusFilter').addEventListener('change', function(e) {
    const selectedStatus = e.target.value;
    
    if (!selectedStatus) {
        document.querySelectorAll('.participant-item').forEach(item => {
            item.style.display = 'block';
        });
        return;
    }
    
    document.querySelectorAll('.participant-item').forEach(item => {
        const statusElement = item.querySelector('.rounded-full');
        const status = statusElement.textContent.toLowerCase();
        item.style.display = status === selectedStatus ? 'block' : 'none';
    });
});

function exportParticipants() {
    // Implement export functionality
    console.log('Export participants for group: {{ $group->id }}');
}
</script>
@endsection 