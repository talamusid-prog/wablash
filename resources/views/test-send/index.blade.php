@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-6">Test Kirim Pesan WhatsApp</h2>
                    
                    @if($sessions->count() > 0)
                    <form id="testSendForm" class="space-y-6">
                        <!-- Session Selection -->
                        <div>
                            <label for="session_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Session WhatsApp
                            </label>
                            <select id="session_id" name="session_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih session...</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->session_id }}">
                                        {{ $session->name }} ({{ $session->phone_number }}) - {{ $session->status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Recipient Number -->
                        <div>
                            <label for="to_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Tujuan
                            </label>
                            <input type="text" id="to_number" name="to_number" placeholder="Contoh: 85159205506" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Masukkan nomor tanpa awalan 0 atau 62</p>
                        </div>

                        <!-- Message Type -->
                        <div>
                            <label for="message_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Pesan
                            </label>
                            <select id="message_type" name="message_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="text">Text</option>
                                <option value="image">Image</option>
                                <option value="video">Video</option>
                                <option value="audio">Audio</option>
                                <option value="document">Document</option>
                            </select>
                        </div>

                        <!-- Message Content -->
                        <div id="textMessageField">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                Isi Pesan
                            </label>
                            <textarea id="message" name="message" rows="4" placeholder="Tulis pesan Anda di sini..." 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <!-- Image Upload Field -->
                        <div id="imageUploadField" class="hidden">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Gambar
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload file</span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 10MB</p>
                                </div>
                            </div>
                            <div id="imagePreview" class="mt-3 hidden">
                                <img id="previewImg" src="" alt="Preview" class="max-w-xs rounded-lg shadow-sm">
                            </div>
                        </div>

                        <!-- Video Upload Field -->
                        <div id="videoUploadField" class="hidden">
                            <label for="video" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Video
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="video" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload file</span>
                                            <input id="video" name="video" type="file" class="sr-only" accept="video/*">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">MP4, AVI, MOV hingga 50MB</p>
                                </div>
                            </div>
                            <div id="videoPreview" class="mt-3 hidden">
                                <video id="previewVideo" controls class="max-w-xs rounded-lg shadow-sm">
                                    <source src="" type="video/mp4">
                                    Browser Anda tidak mendukung tag video.
                                </video>
                            </div>
                        </div>

                        <!-- Audio Upload Field -->
                        <div id="audioUploadField" class="hidden">
                            <label for="audio" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Audio
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="audio" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload file</span>
                                            <input id="audio" name="audio" type="file" class="sr-only" accept="audio/*">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">MP3, WAV, OGG hingga 20MB</p>
                                </div>
                            </div>
                            <div id="audioPreview" class="mt-3 hidden">
                                <audio id="previewAudio" controls class="w-full">
                                    <source src="" type="audio/mpeg">
                                    Browser Anda tidak mendukung tag audio.
                                </audio>
                            </div>
                        </div>

                        <!-- Document Upload Field -->
                        <div id="documentUploadField" class="hidden">
                            <label for="document" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Dokumen
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-200">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="document" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload file</span>
                                            <input id="document" name="document" type="file" class="sr-only" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, DOC, TXT, XLS hingga 10MB</p>
                                </div>
                            </div>
                            <div id="documentPreview" class="mt-3 hidden">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <svg class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div>
                                        <p id="documentName" class="text-sm font-medium text-gray-900"></p>
                                        <p id="documentSize" class="text-xs text-gray-500"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Send Button -->
                        <div>
                            <button type="button" id="sendButton" 
                                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Kirim Pesan Test
                            </button>
                        </div>
                    </form>

                    <!-- Recent Messages -->
                    <div class="bg-white rounded-lg shadow p-6 mt-8">
                        <h3 class="text-lg font-semibold mb-4">Pesan Terbaru</h3>
                        <div id="recentMessages" class="space-y-2">
                            <p class="text-gray-500 text-sm">Belum ada pesan terkirim</p>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <div class="text-gray-500 mb-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Session WhatsApp</h3>
                        <p class="text-gray-500 mb-4">Anda perlu membuat session WhatsApp terlebih dahulu.</p>
                        <a href="{{ route('sessions.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Buat Session
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Berhasil!</h3>
            </div>
            <p id="successMessage" class="text-gray-600 mb-4"></p>
            <button onclick="document.getElementById('successModal').classList.add('hidden')" class="w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
                Tutup
            </button>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Error!</h3>
            </div>
            <p id="errorMessage" class="text-gray-600 mb-4"></p>
            <button onclick="document.getElementById('errorModal').classList.add('hidden')" class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const form = document.getElementById('testSendForm');
    const sendButton = document.getElementById('sendButton');
    const successModal = document.getElementById('successModal');
    const errorModal = document.getElementById('errorModal');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const recentMessages = document.getElementById('recentMessages');

    // Message type change handler
    const messageTypeSelect = document.getElementById('message_type');
    messageTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        // Hide all upload fields
        document.getElementById('textMessageField').classList.add('hidden');
        document.getElementById('imageUploadField').classList.add('hidden');
        document.getElementById('videoUploadField').classList.add('hidden');
        document.getElementById('audioUploadField').classList.add('hidden');
        document.getElementById('documentUploadField').classList.add('hidden');
        
        // Show selected field
        switch(selectedType) {
            case 'text':
                document.getElementById('textMessageField').classList.remove('hidden');
                break;
            case 'image':
                document.getElementById('imageUploadField').classList.remove('hidden');
                break;
            case 'video':
                document.getElementById('videoUploadField').classList.remove('hidden');
                break;
            case 'audio':
                document.getElementById('audioUploadField').classList.remove('hidden');
                break;
            case 'document':
                document.getElementById('documentUploadField').classList.remove('hidden');
                break;
        }
    });

    // File preview handlers
    const imageInput = document.getElementById('image');
    const videoInput = document.getElementById('video');
    const audioInput = document.getElementById('audio');
    const documentInput = document.getElementById('document');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    videoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewVideo').src = e.target.result;
                document.getElementById('videoPreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    audioInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewAudio').src = e.target.result;
                document.getElementById('audioPreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    documentInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('documentName').textContent = file.name;
            document.getElementById('documentSize').textContent = formatFileSize(file.size);
            document.getElementById('documentPreview').classList.remove('hidden');
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Auto-format phone number
    const toNumberInput = document.getElementById('to_number');
    toNumberInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Remove leading zeros except for country code
        if (value.length > 0 && value[0] === '0') {
            value = value.substring(1);
        }
        
        // Limit to 15 digits
        if (value.length > 15) {
            value = value.substring(0, 15);
        }
        
        e.target.value = value;
    });

    // Simple approach: Just handle button click directly
    sendButton.onclick = function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const sessionId = formData.get('session_id');
        const toNumber = formData.get('to_number');
        const message = formData.get('message');
        const messageType = formData.get('message_type');

        if (!sessionId || !toNumber || toNumber.trim() === '') {
            showError('Session dan nomor tujuan harus diisi');
            return;
        }

        // Validate phone number format
        const cleanNumber = toNumber.replace(/\D/g, '');
        if (cleanNumber.length < 10) {
            showError('Nomor telepon minimal 10 digit');
            return;
        }

        // Validate based on message type
        if (messageType === 'text') {
            if (!message || message.trim() === '') {
                showError('Pesan text harus diisi');
                return;
            }
        } else {
            // For media types, treat as text for now
            // TODO: Implement proper media handling
        }

        // Show loading state
        const originalText = sendButton.innerHTML;
        sendButton.innerHTML = '<div class="loading-spinner inline-block mr-2"></div>Mengirim...';
        sendButton.disabled = true;

        // Prepare request data
        const requestData = new FormData();
        requestData.append('to_number', toNumber);
        requestData.append('message_type', messageType);
        
        if (messageType === 'text') {
            requestData.append('message', message);
        } else {
            // For media types, we need to implement file upload handling
            // For now, just send the message as caption
            if (message) {
                requestData.append('message', message); // Use message field for caption
            }
            // TODO: Implement file upload for media types
            // For now, just send as text message
            requestData.set('message_type', 'text');
        }

        // Send request
        fetch(`/api/sessions/${sessionId}/test-send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: requestData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                showSuccess(data.message || 'Pesan berhasil dikirim!');
                if (data.data) {
                    addMessageToRecent(data.data);
                }
                form.reset();
            } else {
                showError(data.message || data.error || 'Gagal mengirim pesan');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            showError('Terjadi kesalahan saat mengirim pesan: ' + error.message);
        })
        .finally(() => {
            sendButton.innerHTML = originalText;
            sendButton.disabled = false;
        });
    };

    function showSuccess(message) {
        successMessage.textContent = message;
        successModal.classList.remove('hidden');
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorModal.classList.remove('hidden');
    }

    function addMessageToRecent(messageData) {
        let contentDisplay = '';
        
        if (messageData.message_type === 'text') {
            contentDisplay = `<div class="text-sm text-gray-500 truncate">${messageData.content || messageData.message}</div>`;
        } else {
            const mediaType = messageData.message_type;
            const mediaIcon = {
                'image': '🖼️',
                'video': '🎥',
                'audio': '🎵',
                'document': '📄'
            }[mediaType] || '📎';
            
            contentDisplay = `
                <div class="text-sm text-gray-500 truncate">
                    ${mediaIcon} ${mediaType ? mediaType.charAt(0).toUpperCase() + mediaType.slice(1) : 'Media'}
                    ${messageData.content ? `: ${messageData.content}` : ''}
                </div>
            `;
        }
        
        const messageHtml = `
            <div class="border border-gray-200 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">${messageData.to_number}</div>
                        ${contentDisplay}
                    </div>
                    <div class="ml-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Terkirim
                        </span>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-400">
                    ${new Date(messageData.sent_at || new Date()).toLocaleString('id-ID')}
                </div>
            </div>
        `;

        // Remove "no messages" placeholder if exists
        const noMessages = recentMessages.querySelector('.text-gray-500');
        if (noMessages) {
            noMessages.remove();
        }

        // Add new message at the top
        recentMessages.insertAdjacentHTML('afterbegin', messageHtml);

        // Keep only last 5 messages
        const messages = recentMessages.querySelectorAll('.border');
        if (messages.length > 5) {
            messages[messages.length - 1].remove();
        }
    }

});
</script>

<style>
.loading-spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection 