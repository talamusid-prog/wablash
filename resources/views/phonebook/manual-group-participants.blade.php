@extends('layouts.app')

@section('title', 'Peserta Grup Manual - ' . $groupName)

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
                    <h1 class="text-3xl font-bold text-gray-900">{{ $groupName }}</h1>
                </div>
                <p class="text-gray-600">Daftar kontak dalam grup manual</p>
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
            <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900">{{ $groupName }}</h2>
                <div class="flex items-center space-x-6 mt-2 text-sm text-gray-500">
                    <span>Tipe: Grup Manual</span>
                    <span>{{ $contacts->count() }} total kontak</span>
                    <span>{{ $contacts->where('is_active', true)->count() }} kontak aktif</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="relative">
                    <input type="text" id="searchContacts" placeholder="Cari kontak..." class="w-64 px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-3">
                <button onclick="exportContacts()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Contacts List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Kontak</h3>
                <div class="text-sm text-gray-500">
                    {{ $contacts->count() }} kontak dalam grup
                </div>
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($contacts as $contact)
            <div class="p-6 hover:bg-gray-50 transition-colors duration-200 contact-item" data-contact-name="{{ $contact->name }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium text-sm">{{ strtoupper(substr($contact->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">{{ $contact->name }}</h4>
                            <p class="text-sm text-gray-500 font-mono">{{ $contact->phone_number }}</p>
                            @if($contact->email)
                                <p class="text-xs text-gray-400">{{ $contact->email }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($contact->is_active) bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $contact->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                        @if($contact->notes)
                            <span class="text-xs text-gray-500" title="{{ $contact->notes }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </span>
                        @endif
                        <button onclick="deleteContact({{ $contact->id }}, '{{ $contact->name }}')" 
                                class="text-red-600 hover:text-red-800 transition-colors duration-200 p-2 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" 
                                title="Hapus kontak (Ctrl+Delete)"
                                data-contact-id="{{ $contact->id }}"
                                data-contact-name="{{ $contact->name }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @if($contact->notes)
                    <div class="mt-3 pl-16">
                        <p class="text-sm text-gray-600">{{ $contact->notes }}</p>
                    </div>
                @endif
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada kontak dalam grup ini</h3>
                <p class="text-gray-500">Tambahkan kontak ke grup ini melalui halaman tambah kontak</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchContacts').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    document.querySelectorAll('.contact-item').forEach(item => {
        const contactName = item.querySelector('h4').textContent.toLowerCase();
        const contactPhone = item.querySelector('.font-mono').textContent.toLowerCase();
        const contactEmail = item.querySelector('.text-xs.text-gray-400') ? item.querySelector('.text-xs.text-gray-400').textContent.toLowerCase() : '';
        const hasMatch = contactName.includes(searchTerm) || contactPhone.includes(searchTerm) || contactEmail.includes(searchTerm);
        item.style.display = hasMatch ? 'block' : 'none';
    });
});

// Status filter functionality
document.getElementById('statusFilter').addEventListener('change', function(e) {
    const selectedStatus = e.target.value;
    
    if (!selectedStatus) {
        document.querySelectorAll('.contact-item').forEach(item => {
            item.style.display = 'block';
        });
        return;
    }
    
    document.querySelectorAll('.contact-item').forEach(item => {
        const statusElement = item.querySelector('.rounded-full');
        const status = statusElement.textContent.toLowerCase();
        item.style.display = status === selectedStatus ? 'block' : 'none';
    });
});

function exportContacts() {
    // Implement export functionality
    console.log('Export contacts for manual group: {{ $groupName }}');
}

function deleteContact(contactId, contactName) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus kontak "${contactName}" dari grup ini?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading state
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Buat form untuk delete request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("phonebook.delete-manual-group-contact", ["groupName" => $groupName, "contactId" => ":contactId"]) }}'.replace(':contactId', contactId);
            
            // Tambahkan CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Tambahkan method override untuk DELETE
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            // Tambahkan form ke body dan submit
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection 