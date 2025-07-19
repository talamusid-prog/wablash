@extends('layouts.app')

@section('title', 'Detail Kontak')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Kontak</h1>
                <p class="text-gray-600 mt-2">Informasi lengkap kontak</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('phonebook.edit', $phonebook) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit</span>
                </a>
                <a href="{{ route('phonebook.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Contact Details -->
    <div class="max-w-4xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Kontak</h3>
            </div>
            
            <div class="p-6">
                <div class="flex items-start space-x-6">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">{{ strtoupper(substr($phonebook->name, 0, 1)) }}</span>
                        </div>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="flex-1">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $phonebook->name }}</h2>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-lg text-gray-900">{{ $phonebook->phone_number }}</span>
                                            <button onclick="copyToClipboard('{{ $phonebook->phone_number }}')" class="p-1 text-gray-400 hover:text-gray-600" title="Salin nomor">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    @if($phonebook->email)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-lg text-gray-900">{{ $phonebook->email }}</span>
                                            <button onclick="copyToClipboard('{{ $phonebook->email }}')" class="p-1 text-gray-400 hover:text-gray-600" title="Salin email">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($phonebook->group)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Grup</label>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ $phonebook->group }}
                                        </span>
                                    </div>
                                    @endif
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                        @if($phonebook->is_active)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ditambahkan</label>
                                        <span class="text-lg text-gray-900">{{ $phonebook->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Diupdate</label>
                                        <span class="text-lg text-gray-900">{{ $phonebook->updated_at->format('d M Y H:i') }}</span>
                                    </div>
                                    
                                    @if($phonebook->notes)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-gray-900 whitespace-pre-wrap">{{ $phonebook->notes }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button onclick="sendTestMessage()" class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Pesan Test
                    </button>
                    
                    <button onclick="addToCampaign()" class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah ke Kampanye
                    </button>
                    
                    <button onclick="deleteContact()" class="flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Kontak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = `
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        `;
        button.classList.add('text-green-600');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('text-green-600');
        }, 2000);
    });
}

function sendTestMessage() {
    // Redirect to test-send page with pre-filled phone number
    window.location.href = `{{ route('test-send') }}?phone={{ $phonebook->phone_number }}`;
}

function addToCampaign() {
    // Redirect to campaigns page
    window.location.href = `{{ route('campaigns') }}`;
}

function deleteContact() {
    showConfirm('Apakah Anda yakin ingin menghapus kontak ini? Tindakan ini tidak dapat dibatalkan.', 'Konfirmasi Hapus', 'Ya, Hapus', 'Batal').then((result) => {
        if (result.isConfirmed) {
            showLoading('Menghapus kontak...');
            fetch(`/api/phonebook/{{ $phonebook->id }}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    showSuccess('Kontak berhasil dihapus');
                    setTimeout(() => {
                        window.location.href = '{{ route("phonebook.index") }}';
                    }, 1500);
                } else {
                    showError('Gagal menghapus kontak: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error deleting contact:', error);
                Swal.close();
                showError('Terjadi kesalahan saat menghapus kontak');
            });
        }
    });
}
</script>
@endsection 