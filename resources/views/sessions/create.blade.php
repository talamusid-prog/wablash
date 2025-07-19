@extends('layouts.app')

@section('title', 'Buat Sesi WhatsApp Baru')

<style>


.form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e5e7eb;
}

.form-input {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.form-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.step-indicator {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.step-line {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    height: 3px;
    border-radius: 2px;
}

.step-inactive {
    background: #e5e7eb;
    color: #9ca3af;
}

.step-line-inactive {
    background: #e5e7eb;
}

.loading-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #ffffff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
</style>

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Buat Sesi WhatsApp Baru</h1>
                <p class="text-gray-600 mt-2">Langkah demi langkah untuk menghubungkan WhatsApp</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Back Button -->
                <a href="{{ route('sessions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200" title="Kembali ke daftar sesi">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali
                </a>
                
                <!-- WhatsApp Icon -->
                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        <!-- Progress Steps -->
        <div class="flex justify-center mb-8">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="step-indicator" id="step1">
                        <span>1</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Informasi Sesi</p>
                        <p class="text-xs text-gray-500">Masukkan nama dan nomor</p>
                    </div>
                </div>
                <div class="step-line w-16" id="line1"></div>
                <div class="flex items-center">
                    <div class="step-indicator step-inactive" id="step2">
                        <span>2</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">QR Code</p>
                        <p class="text-xs text-gray-400">Scan dengan WhatsApp</p>
                    </div>
                </div>
                <div class="step-line step-line-inactive w-16" id="line2"></div>
                <div class="flex items-center">
                    <div class="step-indicator step-inactive" id="step3">
                        <span>3</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Selesai</p>
                        <p class="text-xs text-gray-400">Sesi terhubung</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="max-w-2xl mx-auto">
            <div class="form-card p-8">
                <!-- Step 1: Session Information -->
                <div id="step1-content">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Informasi Sesi WhatsApp</h2>
                        <p class="text-gray-600">Masukkan nama dan nomor telepon untuk sesi WhatsApp baru</p>
                    </div>

                    <form id="createSessionForm" class="space-y-6">
                        <div>
                            <label for="sessionName" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Sesi <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="sessionName" 
                                name="sessionName"
                                class="form-input w-full px-4 py-3 text-gray-900 placeholder-gray-400"
                                placeholder="Contoh: Sesi Utama, Sesi Marketing, dll"
                                required
                            >
                            <p class="text-xs text-gray-500 mt-1">Nama ini akan digunakan untuk mengidentifikasi sesi WhatsApp Anda</p>
                        </div>

                        <div>
                            <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                id="phoneNumber" 
                                name="phoneNumber"
                                class="form-input w-full px-4 py-3 text-gray-900 placeholder-gray-400"
                                placeholder="081234567890 atau +6281234567890"
                                required
                            >
                            <p class="text-xs text-gray-500 mt-1">Format: 081234567890 atau +6281234567890</p>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-blue-800">Tips Penting</h3>
                                    <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                        <li>• Pastikan nomor telepon aktif dan terdaftar di WhatsApp</li>
                                        <li>• Siapkan HP untuk scan QR code di langkah selanjutnya</li>
                                        <li>• Pastikan koneksi internet stabil</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-4 pt-4">
                            <a href="{{ route('sessions.index') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg text-center font-medium transition-colors duration-200">
                                Batal
                            </a>
                            <button type="button" id="submitBtn" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                <span id="submitText">Lanjut ke QR Code</span>
                                <span id="submitLoading" class="hidden">
                                    <div class="loading-spinner inline-block mr-2"></div>
                                    Membuat Sesi...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: QR Code -->
                <div id="step2-content" class="hidden">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Scan QR Code</h2>
                        <p class="text-gray-600">Buka WhatsApp di HP Anda dan scan QR code di bawah ini</p>
                    </div>

                    <div class="text-center">
                        <div id="qrCodeContainer" class="bg-gray-50 rounded-lg p-8 mb-6">
                            <div id="qrLoading" class="flex items-center justify-center">
                                <div class="loading-spinner mr-3"></div>
                                <span class="text-gray-600">Mempersiapkan QR Code...</span>
                            </div>
                            <div id="qrCode" class="hidden"></div>
                        </div>

                        <div id="qrStatus" class="text-sm text-gray-600 mb-6">
                            <div class="flex items-center justify-center">
                                <div class="loading-spinner mr-2"></div>
                                <span>Memantau status koneksi...</span>
                            </div>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-green-800">Cara Scan QR Code</h3>
                                    <ol class="text-sm text-green-700 mt-1 space-y-1">
                                        <li>1. Buka aplikasi WhatsApp di HP Anda</li>
                                        <li>2. Tap Menu (3 titik) → WhatsApp Web</li>
                                        <li>3. Arahkan kamera ke QR code di atas</li>
                                        <li>4. Tunggu hingga terhubung otomatis</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <button onclick="goBackToStep1()" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg font-medium transition-colors duration-200">
                                Kembali
                            </button>
                            <button id="refreshQrBtn" onclick="refreshQRCode()" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                Refresh QR Code
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Success -->
                <div id="step3-content" class="hidden">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Sesi Berhasil Dibuat!</h2>
                        <p class="text-gray-600 mb-6">WhatsApp Anda telah terhubung dan siap digunakan untuk blast pesan</p>

                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <div class="text-left">
                                <h3 class="font-medium text-gray-900 mb-2">Detail Sesi:</h3>
                                <div class="space-y-1 text-sm text-gray-600">
                                    <p><strong>Nama:</strong> <span id="sessionNameDisplay"></span></p>
                                    <p><strong>Nomor:</strong> <span id="sessionPhoneDisplay"></span></p>
                                    <p><strong>Status:</strong> <span class="text-green-600 font-medium">Terhubung</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            <a href="{{ route('sessions.index') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg text-center font-medium transition-colors duration-200">
                                Lihat Semua Sesi
                            </a>
                            <button onclick="createAnotherSession()" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                Buat Sesi Lain
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
let currentSessionId = null;
let qrPollingInterval = null;

// Notification functions (define first)
function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
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

// Debug: Check if functions are defined
console.log('Script loaded, checking functions...');
console.log('showError defined:', typeof showError);
console.log('showSuccess defined:', typeof showSuccess);
console.log('showNotification defined:', typeof showNotification);

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    try {
        console.log('DOM loaded, initializing form...');
        const form = document.getElementById('createSessionForm');
        console.log('Form element:', form);
        
        if (form) {
            form.addEventListener('submit', handleFormSubmit);
            console.log('Form submit event listener added');
        } else {
            console.error('Form element not found!');
        }
        
        // Also add click event listener to submit button as backup
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Submit button clicked!');
                try {
                    handleFormSubmit(e);
                } catch (error) {
                    console.error('Error in handleFormSubmit:', error);
                    showError('Terjadi kesalahan saat memproses form');
                }
            });
            console.log('Submit button click event listener added');
        } else {
            console.error('Submit button not found!');
        }
        
        // Also add form submit event listener as backup
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submit event triggered!');
                handleFormSubmit(e);
            });
            console.log('Form submit event listener added as backup');
        }
    } catch (error) {
        console.error('Error initializing form:', error);
    }
});

function handleFormSubmit(e) {
    e.preventDefault();
    console.log('Form submitted!');
    
    // Check if all required elements exist
    const sessionNameInput = document.getElementById('sessionName');
    const phoneNumberInput = document.getElementById('phoneNumber');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitLoading = document.getElementById('submitLoading');
    
    console.log('Session Name Input:', sessionNameInput);
    console.log('Phone Number Input:', phoneNumberInput);
    console.log('Submit Button:', submitBtn);
    console.log('Submit Text:', submitText);
    console.log('Submit Loading:', submitLoading);
    
    if (!sessionNameInput || !phoneNumberInput || !submitBtn || !submitText || !submitLoading) {
        console.error('Required elements not found!');
        showError('Terjadi kesalahan: Elemen form tidak ditemukan');
        return;
    }
    
    const sessionName = sessionNameInput.value.trim();
    const phoneNumber = phoneNumberInput.value.trim();

    console.log('Session Name:', sessionName);
    console.log('Phone Number:', phoneNumber);

    // Validation
    if (!sessionName) {
        showError('Nama sesi harus diisi!');
        document.getElementById('sessionName').focus();
        return;
    }

    if (!phoneNumber) {
        showError('Nomor telepon harus diisi!');
        document.getElementById('phoneNumber').focus();
        return;
    }

    // Validate phone number format
    const phoneRegex = /^[0-9+\-\s()]+$/;
    if (!phoneRegex.test(phoneNumber)) {
        showError('Format nomor telepon tidak valid!');
        document.getElementById('phoneNumber').focus();
        return;
    }

    console.log('Validation passed, preparing request...');

    // Show loading state
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    submitLoading.classList.remove('hidden');

    console.log('Sending request to /api/sessions...');
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF Token:', csrfToken);

    // Create session
    fetch('/api/sessions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || '',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name: sessionName,
            phone_number: phoneNumber
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                console.error('Response text:', text);
                throw new Error('Invalid JSON response');
            }
        });
    })
    .then(data => {
        console.log('API Response:', data);
        if (data.success) {
            currentSessionId = data.session.session_id;
            showSuccess('Sesi berhasil dibuat! Menunggu QR code...');
            goToStep2();
            startQRCodePolling();
        } else {
            showError('Gagal membuat sesi: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error creating session:', error);
        showError('Terjadi kesalahan saat membuat sesi. Pastikan server berjalan dengan baik.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        submitLoading.classList.add('hidden');
    });
}

function goToStep2() {
    currentStep = 2;
    updateStepIndicators();
    document.getElementById('step1-content').classList.add('hidden');
    document.getElementById('step2-content').classList.remove('hidden');
    document.getElementById('step3-content').classList.add('hidden');
}

function goBackToStep1() {
    currentStep = 1;
    updateStepIndicators();
    document.getElementById('step1-content').classList.remove('hidden');
    document.getElementById('step2-content').classList.add('hidden');
    document.getElementById('step3-content').classList.add('hidden');
    
    // Stop polling
    if (qrPollingInterval) {
        clearInterval(qrPollingInterval);
        qrPollingInterval = null;
    }
}

function goToStep3() {
    currentStep = 3;
    updateStepIndicators();
    document.getElementById('step1-content').classList.add('hidden');
    document.getElementById('step2-content').classList.add('hidden');
    document.getElementById('step3-content').classList.remove('hidden');
    
    // Stop polling
    if (qrPollingInterval) {
        clearInterval(qrPollingInterval);
        qrPollingInterval = null;
    }
    
    // Display session info
    const sessionName = document.getElementById('sessionName').value;
    const phoneNumber = document.getElementById('phoneNumber').value;
    document.getElementById('sessionNameDisplay').textContent = sessionName;
    document.getElementById('sessionPhoneDisplay').textContent = phoneNumber;
}

function updateStepIndicators() {
    // Update step indicators
    const steps = ['step1', 'step2', 'step3'];
    const lines = ['line1', 'line2'];
    
    steps.forEach((step, index) => {
        const element = document.getElementById(step);
        if (index + 1 <= currentStep) {
            element.classList.remove('step-inactive');
            element.classList.add('step-indicator');
        } else {
            element.classList.add('step-inactive');
            element.classList.remove('step-indicator');
        }
    });
    
    lines.forEach((line, index) => {
        const element = document.getElementById(line);
        if (index + 1 < currentStep) {
            element.classList.remove('step-line-inactive');
            element.classList.add('step-line');
        } else {
            element.classList.add('step-line-inactive');
            element.classList.remove('step-line');
        }
    });
}

function startQRCodePolling() {
    if (qrPollingInterval) {
        clearInterval(qrPollingInterval);
    }
    
    qrPollingInterval = setInterval(() => {
        if (!currentSessionId) return;
        
        fetch(`/api/sessions/${currentSessionId}/qr`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('QR polling response:', data);
            
            if (data.success && (data.data.qr_code || data.data.qrCode)) {
                showQRCode(data.data.qr_code || data.data.qrCode);
            } else if (data.success && data.data.status === 'connected') {
                showSuccess('WhatsApp berhasil terhubung!');
                goToStep3();
            } else if (data.success && data.data.status === 'authenticated') {
                updateQRStatus('Terverifikasi, menyelesaikan koneksi...', 'blue');
            } else if (data.success && data.data.status === 'connecting') {
                updateQRStatus('Menghubungkan...', 'yellow');
            }
        })
        .catch(error => {
            console.error('Error polling QR:', error);
        });
    }, 1000);
    
    // Also poll for status
    const statusPollingInterval = setInterval(() => {
        if (!currentSessionId) {
            clearInterval(statusPollingInterval);
            return;
        }
        
        fetch(`/api/sessions/${currentSessionId}/status`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.status === 'connected') {
                showSuccess('WhatsApp berhasil terhubung!');
                clearInterval(statusPollingInterval);
                goToStep3();
            }
        })
        .catch(error => {
            console.error('Error polling status:', error);
        });
    }, 2000);
}

function showQRCode(qrCodeData) {
    const qrLoading = document.getElementById('qrLoading');
    const qrCode = document.getElementById('qrCode');
    
    qrLoading.classList.add('hidden');
    qrCode.classList.remove('hidden');
    qrCode.innerHTML = `<img src="${qrCodeData}" alt="QR Code" class="w-64 h-64 mx-auto rounded-lg shadow-lg">`;
    
    updateQRStatus('QR Code siap, silakan scan dengan WhatsApp', 'green');
}

function updateQRStatus(message, color) {
    const qrStatus = document.getElementById('qrStatus');
    const colorClasses = {
        'green': 'text-green-600',
        'blue': 'text-blue-600',
        'yellow': 'text-yellow-600'
    };
    
    qrStatus.innerHTML = `
        <div class="flex items-center justify-center ${colorClasses[color] || 'text-gray-600'}">
            <div class="loading-spinner mr-2"></div>
            <span>${message}</span>
        </div>
    `;
}

function refreshQRCode() {
    const qrLoading = document.getElementById('qrLoading');
    const qrCode = document.getElementById('qrCode');
    
    qrCode.classList.add('hidden');
    qrLoading.classList.remove('hidden');
    updateQRStatus('Memperbarui QR Code...', 'yellow');
    
    // Restart polling
    if (qrPollingInterval) {
        clearInterval(qrPollingInterval);
    }
    startQRCodePolling();
}

function createAnotherSession() {
    // Reset form and go back to step 1
    document.getElementById('createSessionForm').reset();
    currentSessionId = null;
    goBackToStep1();
}


</script>
@endsection 