@extends('layouts.app')

@section('title', 'WhatsApp Messages')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">WhatsApp Messages</h1>
                <p class="text-gray-600 mt-2">Riwayat pesan yang dikirim melalui WhatsApp</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <input type="text" id="searchMessages" placeholder="Cari pesan..." class="w-64 px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                    <option value="">Semua Status</option>
                    <option value="sent">Terkirim</option>
                    <option value="pending">Menunggu</option>
                    <option value="failed">Gagal</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pesan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $messages->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                        <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Terkirim</p>
            <p class="text-2xl font-bold text-gray-900">{{ collect($messages->items())->where('status', 'sent')->count() }}</p>
        </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                        <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Menunggu</p>
            <p class="text-2xl font-bold text-gray-900">{{ collect($messages->items())->where('status', 'pending')->count() }}</p>
        </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                        <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Gagal</p>
            <p class="text-2xl font-bold text-gray-900">{{ collect($messages->items())->where('status', 'failed')->count() }}</p>
        </div>
            </div>
        </div>
    </div>

    <!-- Messages List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Pesan</h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($messages as $message)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $message['phone_number'] ?? 'N/A' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $message['session']['name'] ?? ($message['campaign']['name'] ?? 'N/A') }}
                                        </p>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 ml-4">
                                        @if($message['status'] === 'sent')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                Terkirim
                                            </span>
                                        @elseif($message['status'] === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Menunggu
                                            </span>
                                        @elseif($message['status'] === 'failed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                </svg>
                                                Gagal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Tidak Diketahui
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <p class="text-sm text-gray-900 line-clamp-2">
                                        {{ $message['message'] ?? 'N/A' }}
                                    </p>
                                </div>
                                
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-xs text-gray-500">
                                        {{ $message['sent_at'] ? \Carbon\Carbon::parse($message['sent_at'])->format('d M Y H:i') : '-' }}
                                    </span>
                                    
                                    <div class="flex items-center space-x-1">
                                        @if($message['status'] === 'failed')
                                        <button onclick="retryMessage('{{ $message['original_id'] }}')" class="p-1 text-blue-600 hover:bg-blue-50 rounded transition-colors duration-200" title="Kirim Ulang">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                        </button>
                                        @endif
                                        <button onclick="viewMessage('{{ $message['original_id'] }}', '{{ $message['source_table'] }}')" class="p-1 text-gray-600 hover:bg-gray-50 rounded transition-colors duration-200" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="deleteMessage('{{ $message['original_id'] }}', '{{ $message['source_table'] }}')" class="p-1 text-red-600 hover:bg-red-50 rounded transition-colors duration-200" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pesan</h3>
                <p class="text-gray-500">Belum ada pesan yang dikirim melalui WhatsApp</p>
            </div>
            @endforelse
        </div>
    </div>
    
    @if($messages->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $messages->links() }}
    </div>
    @endif
</div>

<!-- Message Detail Modal -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-8 border w-96 shadow-2xl rounded-2xl bg-white">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Detail Pesan</h3>
            <p class="text-sm text-gray-600">Informasi lengkap tentang pesan ini</p>
        </div>
        <div id="messageDetail" class="space-y-4">
            <!-- Message details will be loaded here -->
        </div>
        <div class="flex justify-end mt-6">
            <button id="closeMessageModal" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('closeMessageModal').addEventListener('click', function() {
    document.getElementById('messageModal').classList.add('hidden');
});

// Close modal when clicking outside
document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

// Search functionality
document.getElementById('searchMessages').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const messageItems = document.querySelectorAll('.divide-y > div');
    
    messageItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

// Status filter functionality
document.getElementById('statusFilter').addEventListener('change', function() {
    const selectedStatus = this.value;
    const messageItems = document.querySelectorAll('.divide-y > div');
    
    messageItems.forEach(item => {
        const statusElement = item.querySelector('[class*="bg-"]');
        if (!selectedStatus || (statusElement && statusElement.textContent.toLowerCase().includes(selectedStatus))) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});

function viewMessage(messageId, sourceTable) {
    // Load message details via AJAX
    fetch(`/api/messages/${messageId}?source=${sourceTable}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const message = data.data;
                document.getElementById('messageDetail').innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor Tujuan</label>
                            <p class="text-sm text-gray-900">${message.to_number || message.phone_number || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Session</label>
                            <p class="text-sm text-gray-900">${message.session?.name || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pesan</label>
                            <p class="text-sm text-gray-900">${message.message || message.content || 'N/A'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="text-sm text-gray-900">${message.status}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Waktu Kirim</label>
                            <p class="text-sm text-gray-900">${message.sent_at || message.timestamp || 'N/A'}</p>
                        </div>
                    </div>
                `;
                document.getElementById('messageModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading message details:', error);
            showError('Gagal memuat detail pesan');
        });
}

function retryMessage(messageId) {
    showConfirm('Apakah Anda yakin ingin mengirim ulang pesan ini?', 'Konfirmasi Kirim Ulang', 'Ya, Kirim Ulang', 'Batal').then((result) => {
        if (result.isConfirmed) {
            showLoading('Mengirim ulang pesan...');
            fetch(`/api/messages/${messageId}/retry?source=blast`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    showSuccess('Pesan berhasil dikirim ulang');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showError('Gagal mengirim ulang pesan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error retrying message:', error);
                Swal.close();
                showError('Terjadi kesalahan saat mengirim ulang pesan');
            });
        }
    });
}

function deleteMessage(messageId, sourceTable) {
    showConfirm('Apakah Anda yakin ingin menghapus pesan ini?', 'Konfirmasi Hapus', 'Ya, Hapus', 'Batal').then((result) => {
        if (result.isConfirmed) {
            showLoading('Menghapus pesan...');
            fetch(`/api/messages/${messageId}?source=${sourceTable}`, {
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
                    showSuccess('Pesan berhasil dihapus');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showError('Gagal menghapus pesan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error deleting message:', error);
                Swal.close();
                showError('Terjadi kesalahan saat menghapus pesan');
            });
        }
    });
}
</script>
@endsection 