@extends('layouts.app')

@section('title', 'WhatsApp Sessions')

<style>
.loading-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid #ffffff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.slide-out {
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
}

/* WhatsApp-style design */
.whatsapp-bg {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
}

.whatsapp-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.whatsapp-button {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    border: none;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.whatsapp-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
}

.whatsapp-button-secondary {
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid #25D366;
    color: #25D366;
    font-weight: 600;
    transition: all 0.3s ease;
}

.whatsapp-button-secondary:hover {
    background: #25D366;
    color: white;
    transform: translateY(-2px);
}

/* Status indicators */
.status-connected {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
    border: none;
}

.status-connecting {
    background: linear-gradient(135deg, #FFA726 0%, #FF9800 100%);
    color: white;
    border: none;
}

.status-disconnected {
    background: linear-gradient(135deg, #EF5350 0%, #D32F2F 100%);
    color: white;
    border: none;
}

/* QR Modal WhatsApp style */
.qr-modal-whatsapp {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.qr-container {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

/* Animasi untuk status badge */
.status-badge {
    transition: all 0.3s ease;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge:hover {
    transform: scale(1.05);
}

/* Animasi untuk stats cards */
.stats-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

/* Session card WhatsApp style */
.session-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    transition: all 0.3s ease;
}

.session-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

/* Pulse animation untuk status connected */
@keyframes pulse-green {
    0%, 100% { 
        opacity: 1; 
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
    }
    50% { 
        opacity: 0.8; 
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(37, 211, 102, 0);
    }
}

.status-badge.status-connected {
    animation: pulse-green 2s ease-in-out;
}

/* Animasi untuk session card ketika terhubung */
@keyframes session-connected {
    0% { 
        transform: scale(1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    50% { 
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
    }
    100% { 
        transform: scale(1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
}

/* WhatsApp-style header */
.whatsapp-header {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: white;
    border-radius: 0 0 30px 30px;
    padding: 30px 0;
    margin-bottom: 30px;
}

/* WhatsApp-style icons */
.whatsapp-icon {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-bottom: 20px;
}

/* Responsive design */
@media (max-width: 768px) {
    .whatsapp-header {
        border-radius: 0 0 20px 20px;
        padding: 20px 0;
    }
    
    .session-card {
        margin-bottom: 15px;
    }
}
</style>

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">WhatsApp Sessions</h1>
                <p class="text-gray-600 mt-2">Kelola sesi WhatsApp untuk blast pesan</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="location.reload()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-xl flex items-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="font-semibold">Refresh</span>
                </button>
                <button onclick="cleanupOrphanedSessions()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-xl flex items-center gap-2 shadow-lg hover:shadow-xl transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span class="font-semibold">Cleanup</span>
                </button>
            <a href="{{ route('sessions.create') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-xl flex items-center gap-3 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="font-semibold">Buat Sesi Baru</span>
            </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Sesi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sessions->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Terhubung</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sessions->where('status', 'connected')->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Menghubungkan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sessions->where('status', 'connecting')->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Terputus</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $sessions->where('status', 'disconnected')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($sessions as $session)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $session->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $session->phone_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="refreshSessionStatus('{{ $session->session_id }}')" class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-200" title="Refresh Status">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                        @if($session->status !== 'connected')
                        <button onclick="connectSession('{{ $session->session_id }}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200" title="Hubungkan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </button>
                        @endif
                        @if($session->status === 'disconnected' || $session->status === 'error')
                        <button onclick="reconnectSession('{{ $session->session_id }}')" class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors duration-200" title="Reconnect">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                        @endif
                        <button onclick="deleteSession('{{ $session->session_id }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="space-y-3" data-session-id="{{ $session->session_id }}">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        @if($session->status === 'connected')
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800" data-current-status="connected">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Terhubung
                            </span>
                        @elseif(in_array($session->status, ['connecting','qr_ready','authenticated']))
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800" data-current-status="{{ $session->status }}">
                                <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ $session->status === 'qr_ready' ? 'QR Ready' : ($session->status === 'authenticated' ? 'Terverifikasi' : 'Menghubungkan') }}
                            </span>
                        @else
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800" data-current-status="disconnected">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Terputus
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Pesan Terkirim</span>
                        <span class="text-sm font-medium text-gray-900">{{ $session->messages()->count() }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Dibuat</span>
                        <span class="text-sm text-gray-900">{{ $session->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada sesi WhatsApp</h3>
                <p class="text-gray-500 mb-6">Buat sesi baru untuk memulai blast pesan</p>
                <a href="{{ route('sessions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    Buat Sesi Pertama
                </a>
            </div>
        </div>
        @endforelse
    </div>
    
    @if($sessions->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $sessions->links() }}
    </div>
    @endif
</div>

<!-- QR Code Modal -->
<div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-8 border w-96 shadow-2xl rounded-2xl bg-white">
        <div class="text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Scan QR Code</h3>
            <div id="qrCode" class="flex justify-center mb-6 p-4 bg-gray-50 rounded-xl"></div>
            <p class="text-sm text-gray-600 mb-4">Buka WhatsApp di HP Anda dan scan QR code di atas</p>
            <div id="qrStatus" class="text-xs text-blue-600 mb-6 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memantau status koneksi...
            </div>
            <div class="flex space-x-3">
                <button id="closeQrModal" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    Tutup
                </button>
                <button id="forceRefreshStatus" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    Refresh Status
                </button>
            </div>
        </div>
    </div>
</div>



<script>
let currentSessionId = null;

// Initialize session polling intervals object
window.sessionPollingIntervals = {};

// Modal management
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error('Modal not found:', modalId);
        return;
    }
    
    // Simple show without animation
    modal.style.display = 'block';
    modal.classList.remove('hidden');
    
    // Focus on first input if exists
    const firstInput = modal.querySelector('input');
    if (firstInput) {
        setTimeout(() => firstInput.focus(), 100);
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        console.error('Modal not found:', modalId);
        return;
    }
    
    // Simple hide without animation
    modal.style.display = 'none';
    modal.classList.add('hidden');
    
    // Jika ini adalah QR modal, bersihkan polling dan reset status
    if (modalId === 'qrModal') {
        // Hentikan semua polling yang sedang berjalan
        if (currentSessionId && window.sessionPollingIntervals[currentSessionId]) {
            clearInterval(window.sessionPollingIntervals[currentSessionId]);
            delete window.sessionPollingIntervals[currentSessionId];
        }
        
        // Reset QR status
        const qrStatus = document.getElementById('qrStatus');
        if (qrStatus) {
            qrStatus.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memantau status koneksi...
            `;
        }
        
        // Reset QR code container
        const qrContainer = document.getElementById('qrCode');
        if (qrContainer) {
            qrContainer.innerHTML = '';
        }
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    

    
    const closeQrModal = document.getElementById('closeQrModal');
    if (closeQrModal) {
        closeQrModal.addEventListener('click', function() {
            hideModal('qrModal');
            // Hentikan semua polling yang sedang berjalan
            if (currentSessionId && window.sessionPollingIntervals[currentSessionId]) {
                clearInterval(window.sessionPollingIntervals[currentSessionId]);
                delete window.sessionPollingIntervals[currentSessionId];
            }
        });
    }
    
    const forceRefreshStatus = document.getElementById('forceRefreshStatus');
    if (forceRefreshStatus) {
        forceRefreshStatus.addEventListener('click', function() {
            if (currentSessionId) {
                console.log('Manual refresh status for session:', currentSessionId);
                // Force check status and update
                fetch(`/api/sessions/${currentSessionId}/status`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Manual status check response:', data);
                    if (data.success && data.data.status === 'connected') {
                        showSuccess('Status: Terhubung! Menutup QR modal...');
                        closeQRModalAndRefresh();
                    } else {
                        showWarning('Status: ' + (data.data?.status || 'Unknown'));
                    }
                })
                .catch(error => {
                    console.error('Error in manual status check:', error);
                    showError('Gagal memeriksa status: ' + error.message);
                });
            } else {
                showError('Tidak ada session yang aktif');
            }
        });
    }
    

    
    const qrModal = document.getElementById('qrModal');
    if (qrModal) {
        qrModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal('qrModal');
                // Hentikan semua polling yang sedang berjalan
                if (currentSessionId && window.sessionPollingIntervals[currentSessionId]) {
                    clearInterval(window.sessionPollingIntervals[currentSessionId]);
                    delete window.sessionPollingIntervals[currentSessionId];
                }
            }
        });
    }
});



function pollForQRCode(sessionId) {
    console.log('Starting QR code polling for session:', sessionId);
    
    // Hentikan interval polling sebelumnya jika ada
    if (window.sessionPollingIntervals[sessionId]) {
        clearInterval(window.sessionPollingIntervals[sessionId]);
    }
    
    const pollInterval = setInterval(() => {
        console.log('Polling QR code for session:', sessionId);
        
        fetch(`/api/sessions/${sessionId}/qr`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.text().then(text => {
                try {
                    const parsed = JSON.parse(text);
                    return parsed;
                } catch (e) {
                    console.error('Failed to parse JSON from QR polling:', e);
                    console.error('Response text was:', text);
                    throw new Error('Invalid JSON response from server');
                }
            });
        })
        .then(data => {
            console.log('QR polling response:', data);
            
            if (data.success && (data.data.qr_code || data.data.qrCode)) {
                console.log('QR code ready, showing QR modal');
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                showQRCode(data.data.qr_code || data.data.qrCode, sessionId);
                showSuccess('QR Code siap! Silakan scan dengan WhatsApp.');
            } else if (data.success && data.data.status === 'connected') {
                console.log('Session already connected during QR polling');
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                // Close QR modal automatically when connected
                showSuccess('Sesi sudah terhubung! QR Code ditutup otomatis.');
                closeQRModalAndRefresh();
                return; // Exit early to prevent further polling
            } else if (data.success && data.data.status === 'qr_ready') {
                // QR code is ready, check if we have QR code data
                if (data.data.qrCode || data.data.qr_code) {
                    console.log('QR code ready, showing QR modal');
                    clearInterval(pollInterval);
                    delete window.sessionPollingIntervals[sessionId];
                    showQRCode(data.data.qrCode || data.data.qr_code, sessionId);
                    showSuccess('QR Code siap! Silakan scan dengan WhatsApp.');
                }
            } else if (data.success && data.data.status === 'authenticated') {
                console.log('Session authenticated, starting connection status polling');
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                // Start connection status polling for authenticated status
                startConnectionStatusPolling(sessionId);
                showSuccess('Terverifikasi! Menyelesaikan koneksi...');
            } else if (data.success && data.data.status === 'connecting') {
                console.log('Session connecting, continue polling...');
                // Session is connecting, continue polling...
            } else if (data.success) {
                console.log('QR code not ready yet, continue polling...');
                // QR code not ready yet, continue polling...
            } else {
                console.log('QR polling failed, continue silently');
                // QR polling failed, continue silently
            }
        })
        .catch(error => {
            console.error('Error polling QR code:', error);
            // Don't clear interval on first few errors, might be temporary
            if (error.message.includes('Invalid JSON') || error.message.includes('HTTP error')) {
                // Server error detected, will retry...
            } else {
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                showError('Gagal mendapatkan QR code: ' + error.message);
            }
        });
    }, 1000); // Poll every 1 second for very fast QR detection
    
    // Store the interval for this session
    window.sessionPollingIntervals[sessionId] = pollInterval;
    
    // Stop polling after 45 seconds
    setTimeout(() => {
        clearInterval(pollInterval);
        delete window.sessionPollingIntervals[sessionId];
        // Check if QR modal is still open before showing error
        const modal = document.getElementById('qrModal');
        if (modal && !modal.classList.contains('hidden')) {
            showError('Timeout: QR code tidak muncul dalam 45 detik. Pastikan engine Node.js berjalan.');
        }
    }, 45000);
}

// Fungsi baru yang lebih sederhana untuk polling status koneksi
function startConnectionStatusPolling(sessionId) {
    console.log('Starting connection status polling for session:', sessionId);
    
    // Hentikan interval polling sebelumnya jika ada
    if (window.sessionPollingIntervals[sessionId]) {
        clearInterval(window.sessionPollingIntervals[sessionId]);
    }
    
    const pollInterval = setInterval(() => {
        console.log('Polling status for session:', sessionId);
        
        fetch(`/api/sessions/${sessionId}/status`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Status polling response:', data);
            
            // Update status text in QR modal
            const qrStatus = document.getElementById('qrStatus');
            if (qrStatus) {
                if (data.success && data.data.status === 'connected') {
                    qrStatus.innerHTML = `
                        <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-green-600">Terhubung! Menutup QR Code...</span>
                    `;
                } else if (data.success && data.data.status === 'authenticated') {
                    qrStatus.innerHTML = `
                        <svg class="w-4 h-4 mr-2 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-blue-600">Terverifikasi, menyelesaikan koneksi...</span>
                    `;
                } else if (data.success && data.data.status === 'connecting') {
                    qrStatus.innerHTML = `
                        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Menghubungkan...</span>
                    `;
                } else if (data.success && data.data.status === 'qr_ready') {
                    qrStatus.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>QR Code siap, silakan scan</span>
                    `;
                }
            }
            
            // Check for connected status and handle it immediately
            if (data.success && data.data.status === 'connected') {
                console.log('Session connected! Closing QR modal...');
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                // Close QR modal automatically when connected
                showSuccess('Sesi berhasil terhubung! QR Code ditutup otomatis.');
                closeQRModalAndRefresh();
                return; // Exit early to prevent further polling
            } else if (data.success && data.data.status === 'authenticated') {
                // Continue polling for authenticated status, but increase frequency
                console.log('Session authenticated, continuing to poll for connected status...');
            } else if (data.success && (data.data.status === 'disconnected' || data.data.status === 'error')) {
                console.log('Session disconnected or error:', data.data.status);
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                showError('Sesi terputus atau terjadi error. Silakan coba lagi.');
                return; // Exit early to prevent further polling
            }
            // Continue polling for other statuses (connecting, qr_ready, etc.)
        })
        .catch(error => {
            console.error('Error polling status:', error);
            // Don't clear interval on network errors, might be temporary
        });
    }, 500); // Poll every 500ms for faster detection
    
    // Store the interval for this session
    window.sessionPollingIntervals[sessionId] = pollInterval;
    
    // Add a more aggressive polling for connected status
    const aggressivePollInterval = setInterval(() => {
        console.log('Aggressive polling for connected status:', sessionId);
        
        fetch(`/api/sessions/${sessionId}/status`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Aggressive polling response:', data);
            if (data.success && data.data.status === 'connected') {
                console.log('Connected status detected in aggressive polling!');
                clearInterval(aggressivePollInterval);
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                showSuccess('Sesi berhasil terhubung! QR Code ditutup otomatis.');
                closeQRModalAndRefresh();
            }
        })
        .catch(error => {
            console.error('Error in aggressive polling:', error);
        });
    }, 2000); // Check every 2 seconds as backup
    
    // Stop all polling after 60 seconds
    setTimeout(() => {
        if (window.sessionPollingIntervals[sessionId]) {
            console.log('Timeout reached for session:', sessionId);
            clearInterval(pollInterval);
            clearInterval(aggressivePollInterval);
            delete window.sessionPollingIntervals[sessionId];
            // Don't show error message if QR modal is already closed or session is connected
            const modal = document.getElementById('qrModal');
            if (modal && !modal.classList.contains('hidden')) {
                // Check if session is actually connected before showing timeout error
                fetch(`/api/sessions/${sessionId}/status`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success || data.data.status !== 'connected') {
                        showError('Timeout: Koneksi tidak berhasil dalam 60 detik. Silakan coba lagi.');
                    }
                })
                .catch(() => {
                    // If we can't check status, don't show error
                });
            }
        }
    }, 60000);
    

}

function updateSessionStatus(statusElement, newStatus) {
    if (!statusElement) return;
    
    // Add transition animation
    statusElement.style.transition = 'all 0.3s ease';
    
    // Add a brief flash effect when status changes
    statusElement.style.transform = 'scale(1.1)';
    setTimeout(() => {
        statusElement.style.transform = 'scale(1)';
    }, 150);
    
    // Remove existing classes
    statusElement.className = 'status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium';
    
    // Update based on status
    if (newStatus === 'connected') {
        statusElement.classList.add('bg-green-100', 'text-green-800');
        statusElement.innerHTML = `
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            Terhubung
        `;
    } else if (newStatus === 'connecting' || newStatus === 'qr_ready' || newStatus === 'authenticated') {
        statusElement.classList.add('bg-yellow-100', 'text-yellow-800');
        statusElement.innerHTML = `
            <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            ${newStatus === 'qr_ready' ? 'QR Ready' : newStatus === 'authenticated' ? 'Terverifikasi' : 'Menghubungkan'}
        `;
    } else if (newStatus === 'disconnected' || newStatus === 'error' || newStatus === 'auth_failed') {
        statusElement.classList.add('bg-red-100', 'text-red-800');
        statusElement.innerHTML = `
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            ${newStatus === 'disconnected' ? 'Terputus' : newStatus === 'error' ? 'Error' : 'Auth Failed'}
        `;
    } else {
        statusElement.classList.add('bg-gray-100', 'text-gray-800');
        statusElement.innerHTML = `
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}
        `;
    }
}

function showQRCode(qrCodeData, sessionId = null) {
    console.log('Showing QR code for session:', sessionId);
    
    const qrContainer = document.getElementById('qrCode');
    if (qrContainer) {
        qrContainer.innerHTML = `<img src="${qrCodeData}" alt="QR Code" class="w-64 h-64 rounded-lg shadow-lg" onerror="console.error('QR image failed to load')">`;
        
        // Ensure modal is visible
        const modal = document.getElementById('qrModal');
        if (modal) {
            modal.style.display = 'block';
            modal.classList.remove('hidden');
            modal.style.zIndex = '9999';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            console.log('QR modal is now visible');
        } else {
            console.error('QR modal not found');
        }
        
        // Start polling for connection status if sessionId is provided
        if (sessionId) {
            console.log('Starting connection status polling for session:', sessionId);
            // Hentikan polling sebelumnya jika ada
            if (window.sessionPollingIntervals[sessionId]) {
                clearInterval(window.sessionPollingIntervals[sessionId]);
                delete window.sessionPollingIntervals[sessionId];
            }
            startConnectionStatusPolling(sessionId);
        }
    } else {
        console.error('QR container not found');
    }
}

function cleanupOrphanedSessions() {
    showConfirm('Apakah Anda yakin ingin membersihkan session yang tidak sinkron? Session yang tidak ada di WhatsApp Engine akan dihapus.', 'Konfirmasi Cleanup', 'Ya, Cleanup', 'Batal').then((result) => {
        if (result.isConfirmed) {
            const btn = event.target.closest('button');
            const originalHTML = btn?.innerHTML || '';
            
            // Show loading state
            if (btn) {
                btn.innerHTML = '<div class="loading-spinner inline-block mr-2"></div>Cleaning up...';
                btn.disabled = true;
            }

            // Get all sessions and check their status
            const sessions = document.querySelectorAll('[data-session-id]');
            let cleanedCount = 0;
            let totalSessions = sessions.length;

            sessions.forEach((sessionElement, index) => {
                const sessionId = sessionElement.getAttribute('data-session-id');
                
                setTimeout(() => {
                    fetch(`/api/sessions/${sessionId}/status`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // If session not found in engine, delete it
                        if (!data.success && data.error && data.error.includes('Session not found')) {
                            return fetch(`/api/sessions/${sessionId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                }
                            });
                        }
                        return Promise.resolve();
                    })
                    .then(deleteResponse => {
                        if (deleteResponse) {
                            cleanedCount++;
                        }
                        
                        // Check if all sessions processed
                        if (index === totalSessions - 1) {
                            if (cleanedCount > 0) {
                                showSuccess(`Cleanup selesai! ${cleanedCount} session yang tidak sinkron telah dihapus.`);
                                setTimeout(() => location.reload(), 2000);
                            } else {
                                showSuccess('Tidak ada session yang perlu dibersihkan.');
                            }
                        }
                    })
                    .catch(error => {
                        // Error during cleanup, continue silently
                    });
                }, index * 1000); // Process one session per second
            });

            // Reset button after timeout
            setTimeout(() => {
                if (btn) {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            }, (totalSessions + 5) * 1000);
        }
    });
}

function refreshSessionStatus(sessionId) {
    const btn = event.target.closest('button');
    const originalHTML = btn?.innerHTML || '';
    
    // Show loading state
    if (btn) {
        btn.innerHTML = '<div class="loading-spinner inline-block mr-2"></div>Refreshing...';
        btn.disabled = true;
    }

    fetch(`/api/sessions/${sessionId}/status`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Refresh status response:', data);
        if (data.success && data.data.status) {
            // Update the status badge
            const sessionElement = document.querySelector(`[data-session-id="${sessionId}"]`);
            const statusElement = sessionElement?.querySelector('.status-badge');
            
            if (statusElement) {
                const currentStatus = statusElement.getAttribute('data-current-status') || '';
                const newStatus = data.data.status;
                
                updateSessionStatus(statusElement, newStatus);
                statusElement.setAttribute('data-current-status', newStatus);
                
                // Update tombol aksi
                updateSessionActions(sessionId, newStatus);
                
                // Update stats cards jika status berubah
                if (newStatus !== currentStatus) {
                    updateStatsCards();
                    
                    if (newStatus === 'connected') {
                        showSuccess('Status berhasil diperbarui: Terhubung!');
                    } else {
                        showSuccess('Status berhasil diperbarui: ' + newStatus);
                    }
                } else {
                    showSuccess('Status sudah up-to-date: ' + newStatus);
                }
            } else {
                showError('Element status tidak ditemukan');
            }
        } else {
            showError('Gagal mendapatkan status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error refreshing status:', error);
        showError('Terjadi kesalahan saat refresh status: ' + error.message);
    })
    .finally(() => {
        if (btn) {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    });
}

function connectSession(sessionId) {
    const btn = event.target.closest('button');
    const originalHTML = btn?.innerHTML || '';
    
    // Show loading state
    if (btn) {
        btn.innerHTML = '<div class="loading-spinner inline-block mr-2"></div>Menghubungkan...';
        btn.disabled = true;
    }

    fetch(`/api/sessions/${sessionId}/connect`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            currentSessionId = sessionId; // Set current session ID
            if (data.qr_code || data.data?.qr_code || data.data?.qrCode) {
                const qrCode = data.qr_code || data.data?.qr_code || data.data?.qrCode;
                showQRCode(qrCode, sessionId);
                showSuccess('QR Code berhasil dibuat!');
            } else {
                // Start polling for QR code
                pollForQRCode(sessionId);
                showSuccess('Menunggu QR code...');
            }
        } else {
            showError('Gagal menghubungkan sesi: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        showError('Terjadi kesalahan saat menghubungkan sesi. Pastikan server berjalan dengan baik.');
    })
    .finally(() => {
        if (btn) {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    });
}

function reconnectSession(sessionId) {
    const btn = event.target.closest('button');
    const originalHTML = btn?.innerHTML || '';
    
    // Show loading state
    if (btn) {
        btn.innerHTML = '<div class="loading-spinner inline-block mr-2"></div>Reconnecting...';
        btn.disabled = true;
    }

    fetch(`/api/sessions/${sessionId}/reconnect`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            currentSessionId = sessionId; // Set current session ID
            showSuccess('Reconnect session berhasil diinisiasi!');
            // Start polling for status update
            pollForStatusUpdate(sessionId);
        } else {
            showError('Gagal reconnect session: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        showError('Terjadi kesalahan saat reconnect session. Pastikan server berjalan dengan baik.');
    })
    .finally(() => {
        if (btn) {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    });
}

function pollForStatusUpdate(sessionId) {
    // Hentikan interval polling sebelumnya jika ada
    if (window.sessionPollingIntervals[sessionId]) {
        clearInterval(window.sessionPollingIntervals[sessionId]);
    }
    
    const pollInterval = setInterval(() => {
        fetch(`/api/sessions/${sessionId}/status`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data.status === 'connected') {
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                // Close QR modal automatically when connected
                showSuccess('Session berhasil terhubung kembali! QR Code ditutup otomatis.');
                closeQRModalAndRefresh();
                return; // Exit early to prevent further polling
            } else if (data.success && data.data.status === 'authenticated') {
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                // Start connection status polling for authenticated status
                startConnectionStatusPolling(sessionId);
                showSuccess('Terverifikasi! Menyelesaikan koneksi...');
                return; // Exit early to prevent further polling
            } else if (data.success && data.data.status === 'qr_ready') {
                clearInterval(pollInterval);
                delete window.sessionPollingIntervals[sessionId];
                // Start QR code polling
                pollForQRCode(sessionId);
                return; // Exit early to prevent further polling
            } else if (data.success && data.data.status === 'connecting') {
                // Session is connecting, continue polling...
            } else if (data.success && (data.data.status === 'disconnected' || data.data.status === 'error')) {
                // Session still disconnected, continue polling...
            } else {
                // Status polling failed, continue silently
            }
        })
        .catch(error => {
            clearInterval(pollInterval);
            delete window.sessionPollingIntervals[sessionId];
            showError('Gagal mendapatkan status session: ' + error.message);
        });
    }, 1000); // Poll every 1 second for faster status detection
    
    // Store the interval for this session
    window.sessionPollingIntervals[sessionId] = pollInterval;
    
    // Stop polling after 45 seconds
    setTimeout(() => {
        clearInterval(pollInterval);
        delete window.sessionPollingIntervals[sessionId];
        // Check if QR modal is still open before showing error
        const modal = document.getElementById('qrModal');
        if (modal && !modal.classList.contains('hidden')) {
            showError('Timeout: Session tidak berhasil terhubung dalam 45 detik.');
        }
    }, 45000);
}

function deleteSession(sessionId) {
    showConfirm('Apakah Anda yakin ingin menghapus sesi ini?', 'Konfirmasi Hapus', 'Ya, Hapus', 'Batal').then((result) => {
        if (result.isConfirmed) {
            const btn = event.target.closest('button');
            const originalHTML = btn?.innerHTML || '';
            
            // Show loading state
            if (btn) {
                btn.innerHTML = '<div class="loading-spinner inline-block mr-2"></div>Menghapus...';
                btn.disabled = true;
            }

            fetch(`/api/sessions/${sessionId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Sesi berhasil dihapus!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showError('Gagal menghapus sesi: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                showError('Terjadi kesalahan saat menghapus sesi');
            })
            .finally(() => {
                if (btn) {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            });
        }
    });
}

// Notification functions
function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showWarning(message) {
    showNotification(message, 'warning');
}

function showNotification(message, type) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    let bgColor, icon;
    
    if (type === 'success') {
        bgColor = 'bg-green-500 text-white';
        icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
    } else if (type === 'warning') {
        bgColor = 'bg-yellow-500 text-white';
        icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>';
    } else {
        bgColor = 'bg-red-500 text-white';
        icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
    }
    
    notification.className = `notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${bgColor}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icon}
            </svg>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('slide-out');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function showConfirm(message, title, confirmText, cancelText) {
    return new Promise((resolve) => {
        // Remove existing confirm dialogs
        const existingDialogs = document.querySelectorAll('.confirm-dialog');
        existingDialogs.forEach(dialog => dialog.remove());

        // Create confirm dialog
        const dialog = document.createElement('div');
        dialog.className = 'confirm-dialog fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        dialog.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                </div>
                <p class="text-gray-600 mb-6">${message}</p>
                <div class="flex space-x-3">
                    <button class="cancel-btn flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        ${cancelText || 'Batal'}
                    </button>
                    <button class="confirm-btn flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        ${confirmText || 'Ya'}
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(dialog);

        // Add event listeners
        const confirmBtn = dialog.querySelector('.confirm-btn');
        const cancelBtn = dialog.querySelector('.cancel-btn');

        confirmBtn.addEventListener('click', () => {
            dialog.remove();
            resolve({ isConfirmed: true });
        });

        cancelBtn.addEventListener('click', () => {
            dialog.remove();
            resolve({ isConfirmed: false });
        });

        // Close on outside click
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) {
                dialog.remove();
                resolve({ isConfirmed: false });
            }
        });
    });
}





// Start polling when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Mulai polling status setelah delay singkat
    setTimeout(() => {
        startStatusPolling();
        // Update stats cards sekali di awal
        updateStatsCards();
        
        // Inisialisasi tombol aksi berdasarkan status awal
        const sessions = document.querySelectorAll('[data-session-id]');
        sessions.forEach(sessionElement => {
            const sessionId = sessionElement.getAttribute('data-session-id');
            const statusElement = sessionElement.querySelector('.status-badge');
            if (statusElement) {
                const currentStatus = statusElement.getAttribute('data-current-status') || 'disconnected';
                updateSessionActions(sessionId, currentStatus);
            }
        });
    }, 1000);
});

// Clean up intervals when page is unloaded
window.addEventListener('beforeunload', function() {
    if (window.sessionPollingIntervals) {
        Object.keys(window.sessionPollingIntervals).forEach(sessionId => {
            if (window.sessionPollingIntervals[sessionId]) {
                clearInterval(window.sessionPollingIntervals[sessionId]);
                delete window.sessionPollingIntervals[sessionId];
            }
        });
    }
});

// Fungsi helper untuk menutup QR modal dan update status real-time
function closeQRModalAndRefresh() {
    console.log('closeQRModalAndRefresh called for session:', currentSessionId);
    
    // Pastikan QR modal tertutup
    hideModal('qrModal');
    console.log('QR modal hidden');
    
    // Hentikan semua polling yang sedang berjalan
    if (currentSessionId && window.sessionPollingIntervals[currentSessionId]) {
        clearInterval(window.sessionPollingIntervals[currentSessionId]);
        delete window.sessionPollingIntervals[currentSessionId];
        console.log('Polling stopped for session:', currentSessionId);
    }
    
    // Sinkronkan status session yang baru dengan memanggil API status
    if (currentSessionId) {
        const sessionElement = document.querySelector(`[data-session-id="${currentSessionId}"]`);
        const statusElement = sessionElement?.querySelector('.status-badge');
        
        console.log('Session element found:', !!sessionElement);
        console.log('Status element found:', !!statusElement);
        
        if (statusElement) {
            fetch(`/api/sessions/${currentSessionId}/status`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const newStatus = data?.data?.status || 'connecting';
                updateSessionStatus(statusElement, newStatus);
                statusElement.setAttribute('data-current-status', newStatus);
                console.log('Status updated to', newStatus);
                
                // Update stats & tombol aksi
                updateStatsCards();
                updateSessionActions(currentSessionId, newStatus);
                
                // Feedback visual jika benar-benar connected
                if (newStatus === 'connected') {
                    sessionElement.style.animation = 'session-connected 1s ease-in-out';
                    setTimeout(() => { sessionElement.style.animation = ''; }, 1000);
                }
            })
            .catch(err => {
                console.error('Failed to sync status after closing QR modal:', err);
            });
        } else {
            console.error('Status element not found for session:', currentSessionId);
        }
    } else {
        console.error('No current session ID available');
    }
    
    // Reset current session ID
    currentSessionId = null;
    
    console.log('Status sync initiated after QR modal close');
}

// Fungsi untuk update tombol aksi berdasarkan status
function updateSessionActions(sessionId, status) {
    const sessionElement = document.querySelector(`[data-session-id="${sessionId}"]`);
    if (!sessionElement) return;
    
    const actionButtons = sessionElement.querySelectorAll('button');
    
    actionButtons.forEach(button => {
        const title = button.getAttribute('title');
        
        if (title === 'Hubungkan') {
            if (status === 'connected') {
                // Sembunyikan tombol hubungkan jika sudah terhubung
                button.style.display = 'none';
            } else {
                // Tampilkan tombol hubungkan jika tidak terhubung
                button.style.display = 'inline-flex';
            }
        } else if (title === 'Reconnect') {
            if (status === 'connected') {
                // Sembunyikan tombol reconnect jika sudah terhubung
                button.style.display = 'none';
            } else if (status === 'disconnected' || status === 'error') {
                // Tampilkan tombol reconnect jika terputus atau error
                button.style.display = 'inline-flex';
            } else {
                // Sembunyikan tombol reconnect untuk status lainnya
                button.style.display = 'none';
            }
        }
    });
}

// Auto-polling untuk update status session
function startStatusPolling() {
    const sessions = document.querySelectorAll('[data-session-id]');
    
    sessions.forEach(sessionElement => {
        const sessionId = sessionElement.getAttribute('data-session-id');
        const statusElement = sessionElement.querySelector('.status-badge');
        
        if (sessionId && statusElement) {
            // Poll status setiap 3 detik untuk responsivitas yang lebih baik
            setInterval(() => {
                fetch(`/api/sessions/${sessionId}/status`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.status) {
                            const newStatus = data.data.status;
                            const currentStatus = statusElement.getAttribute('data-current-status') || '';
                            
                            // Update status badge jika status berubah
                            if (newStatus !== currentStatus) {
                                updateSessionStatus(statusElement, newStatus);
                                statusElement.setAttribute('data-current-status', newStatus);
                                
                                // Update tombol aksi berdasarkan status baru
                                updateSessionActions(sessionId, newStatus);
                                
                                // Jika status berubah menjadi connected, update stats cards dan berikan notifikasi
                                if (newStatus === 'connected') {
                                    updateStatsCards();
                                    // Berikan notifikasi visual yang lebih jelas
                                    showSuccess(`Sesi ${sessionId} berhasil terhubung!`);
                                } else if (newStatus === 'disconnected' && currentStatus === 'connected') {
                                    // Notifikasi ketika terputus
                                    showWarning(`Sesi ${sessionId} terputus`);
                                }
                            }
                        }
                    })
                    .catch(error => {
                        // Error polling session status, continue silently
                    });
            }, 3000); // Poll setiap 3 detik untuk update yang lebih cepat
        }
    });
}

// Fungsi untuk update stats cards
function updateStatsCards() {
    fetch('/api/sessions', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.sessions) {
            const sessions = data.sessions;
            const totalSessions = sessions.length;
            const connectedSessions = sessions.filter(s => s.status === 'connected').length;
            const connectingSessions = sessions.filter(s => s.status === 'connecting').length;
            const disconnectedSessions = sessions.filter(s => s.status === 'disconnected').length;
            
            // Update stats cards dengan animasi smooth
            const statsCards = document.querySelectorAll('.grid-cols-1.md\\:grid-cols-4 > div');
            if (statsCards.length >= 4) {
                // Total Sesi
                const totalElement = statsCards[0].querySelector('.text-2xl');
                if (totalElement) {
                    totalElement.style.transition = 'all 0.3s ease';
                    totalElement.textContent = totalSessions;
                }
                
                // Terhubung
                const connectedElement = statsCards[1].querySelector('.text-2xl');
                if (connectedElement) {
                    connectedElement.style.transition = 'all 0.3s ease';
                    connectedElement.textContent = connectedSessions;
                }
                
                // Menghubungkan
                const connectingElement = statsCards[2].querySelector('.text-2xl');
                if (connectingElement) {
                    connectingElement.style.transition = 'all 0.3s ease';
                    connectingElement.textContent = connectingSessions;
                }
                
                // Terputus
                const disconnectedElement = statsCards[3].querySelector('.text-2xl');
                if (disconnectedElement) {
                    disconnectedElement.style.transition = 'all 0.3s ease';
                    disconnectedElement.textContent = disconnectedSessions;
                }
            }
        }
    })
    .catch(error => {
        // Error updating stats, continue silently
        console.log('Error updating stats cards:', error);
    });
}
</script>
@endsection 