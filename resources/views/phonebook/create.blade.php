@extends('layouts.app')

@section('title', 'Tambah Kontak')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Kontak</h1>
                <p class="text-gray-600 mt-2">Tambahkan kontak baru ke phonebook</p>
            </div>
            <a href="{{ route('phonebook.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <!-- Import Options -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pilihan Import</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button type="button" onclick="showManualForm()" id="manualBtn" class="p-4 border-2 border-purple-200 rounded-lg hover:border-purple-400 transition-colors duration-200 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Input Manual</h4>
                                <p class="text-sm text-gray-500">Tambah kontak secara manual</p>
                            </div>
                        </div>
                    </button>
                    
                    <button type="button" onclick="showGrabberForm()" id="grabberBtn" class="p-4 border-2 border-gray-200 rounded-lg hover:border-green-400 transition-colors duration-200 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Grabber WhatsApp</h4>
                                <p class="text-sm text-gray-500">Ambil kontak dari WhatsApp</p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Manual Form -->
        <div id="manualForm" class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Kontak</h3>
            </div>
            
            @if($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('phonebook.store') }}" method="POST" class="p-6 space-y-6" id="contactForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 @error('name') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap"
                               minlength="1">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
                        <div class="relative">
                            <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required 
                                   class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 @error('phone_number') border-red-500 @enderror"
                                   placeholder="Contoh: 08123456789"
                                   minlength="10">
                            <button type="button" id="clearPhoneBtn" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors duration-200"
                                    title="Hapus nomor telepon (Ctrl+Backspace)">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 @error('email') border-red-500 @enderror"
                               placeholder="email@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="group" class="block text-sm font-medium text-gray-700 mb-2">Grup</label>
                        <select id="group" name="group" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 @error('group') border-red-500 @enderror">
                            <option value="">Pilih grup (opsional)</option>
                            @foreach($groups as $group)
                                <option value="{{ $group }}" {{ old('group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                            @endforeach
                        </select>
                        @error('group')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea id="notes" name="notes" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 @error('notes') border-red-500 @enderror"
                              placeholder="Tambahkan catatan tentang kontak ini">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Kontak aktif (dapat digunakan untuk kampanye)
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('phonebook.index') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        Simpan Kontak
                    </button>
                </div>
            </form>
        </div>

        <!-- Grabber Form -->
        <div id="grabberForm" class="bg-white rounded-lg shadow-sm border border-gray-200 hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Grabber Kontak WhatsApp</h3>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Grabber Kontak WhatsApp</h4>
                    <p class="text-gray-500 mb-6">Pilih session WhatsApp yang terhubung untuk mengambil kontak</p>
                    <a href="{{ route('phonebook.grabber') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Buka Grabber Kontak
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form toggle functions
function showManualForm() {
    document.getElementById('manualForm').classList.remove('hidden');
    document.getElementById('grabberForm').classList.add('hidden');
    document.getElementById('manualBtn').classList.add('border-purple-200', 'hover:border-purple-400');
    document.getElementById('manualBtn').classList.remove('border-gray-200', 'hover:border-green-400');
    document.getElementById('grabberBtn').classList.add('border-gray-200', 'hover:border-green-400');
    document.getElementById('grabberBtn').classList.remove('border-purple-200', 'hover:border-purple-400');
}

function showGrabberForm() {
    document.getElementById('grabberForm').classList.remove('hidden');
    document.getElementById('manualForm').classList.add('hidden');
    document.getElementById('grabberBtn').classList.add('border-green-200', 'hover:border-green-400');
    document.getElementById('grabberBtn').classList.remove('border-gray-200', 'hover:border-green-400');
    document.getElementById('manualBtn').classList.add('border-gray-200', 'hover:border-purple-400');
    document.getElementById('manualBtn').classList.remove('border-purple-200', 'hover:border-purple-400');
}

// Auto-format phone number
document.getElementById('phone_number').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    
    // Remove leading zeros except for country code
    if (value.startsWith('0')) {
        value = value.substring(1);
    }
    
    // Add country code if not present
    if (!value.startsWith('62')) {
        value = '62' + value;
    }
    
    this.value = value;
    
    // Update border color based on validation
    if (value.length >= 10) {
        this.classList.remove('border-red-500');
        this.classList.add('border-green-500');
    } else if (value.length > 0) {
        this.classList.remove('border-green-500', 'border-red-500');
    } else {
        this.classList.remove('border-green-500');
    }
});

// Real-time validation for name field
document.getElementById('name').addEventListener('input', function() {
    const value = this.value.trim();
    
    if (value.length > 0) {
        this.classList.remove('border-red-500');
        this.classList.add('border-green-500');
    } else {
        this.classList.remove('border-green-500');
    }
});

// Initialize with manual form
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DEBUG: DOM Content Loaded ===');
    showManualForm();
    
    // Form validation
    const contactForm = document.getElementById('contactForm');
    console.log('Contact form found:', contactForm);
    
    if (contactForm) {
        console.log('=== DEBUG: Adding form submit listener ===');
        
        contactForm.addEventListener('submit', function(e) {
            console.log('=== DEBUG: Form submission triggered ===');
            console.log('Event:', e);
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            
            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('phone_number').value.trim();
            const email = document.getElementById('email').value.trim();
            const group = document.getElementById('group').value;
            const notes = document.getElementById('notes').value.trim();
            const isActive = document.getElementById('is_active').checked;
            
            console.log('=== DEBUG: Form Data ===');
            console.log('Name:', name);
            console.log('Phone:', phone);
            console.log('Email:', email);
            console.log('Group:', group);
            console.log('Notes:', notes);
            console.log('Is Active:', isActive);
            
            let errors = [];
            
            if (!name) {
                errors.push('Nama wajib diisi');
                document.getElementById('name').classList.add('border-red-500');
                console.log('DEBUG: Name validation failed');
            } else {
                document.getElementById('name').classList.remove('border-red-500');
                console.log('DEBUG: Name validation passed');
            }
            
            if (!phone) {
                errors.push('Nomor telepon wajib diisi');
                document.getElementById('phone_number').classList.add('border-red-500');
                console.log('DEBUG: Phone validation failed - empty');
            } else if (phone.length < 10) {
                errors.push('Nomor telepon minimal 10 digit');
                document.getElementById('phone_number').classList.add('border-red-500');
                console.log('DEBUG: Phone validation failed - too short');
            } else {
                document.getElementById('phone_number').classList.remove('border-red-500');
                console.log('DEBUG: Phone validation passed');
            }
            
            console.log('=== DEBUG: Validation Results ===');
            console.log('Errors found:', errors);
            console.log('Errors length:', errors.length);
            
            if (errors.length > 0) {
                console.log('DEBUG: Preventing form submission due to validation errors');
                e.preventDefault();
                
                // Show error message using SweetAlert if available, otherwise use alert
                if (typeof Swal !== 'undefined') {
                    console.log('DEBUG: Using SweetAlert for error display');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errors.map(error => `<div class="text-left">• ${error}</div>`).join(''),
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    console.log('DEBUG: SweetAlert not available, using alert');
                    alert('Validasi gagal:\n' + errors.join('\n'));
                }
                return false;
            }
            
            console.log('DEBUG: Validation passed, proceeding with form submission');
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            console.log('Submit button found:', submitBtn);
            
            if (submitBtn) {
                console.log('DEBUG: Setting loading state on submit button');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>Menyimpan...';
            }
            
            console.log('DEBUG: Allowing form to submit normally');
            console.log('DEBUG: Form will submit to:', this.action);
            
            // Allow form to submit normally
            return true;
        });
        
        console.log('=== DEBUG: Form submit listener added successfully ===');
    } else {
        console.error('ERROR: Contact form not found!');
    }
    
    // Debug submit button click
    const submitBtn = document.getElementById('submitBtn');
    console.log('Submit button found:', submitBtn);
    
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            console.log('=== DEBUG: Submit button clicked ===');
            console.log('Button event:', e);
            console.log('Button disabled:', this.disabled);
            console.log('Button type:', this.type);
            console.log('Form:', this.form);
            
            // Prevent default button behavior to handle manually
            e.preventDefault();
            console.log('DEBUG: Prevented default button behavior');
            
            // Manually trigger form submission
            const form = this.form;
            if (form) {
                console.log('DEBUG: Manually submitting form');
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                
                // Validate form data first
                const name = document.getElementById('name').value.trim();
                const phone = document.getElementById('phone_number').value.trim();
                
                console.log('DEBUG: Validating form data');
                console.log('Name:', name);
                console.log('Phone:', phone);
                
                let errors = [];
                
                if (!name) {
                    errors.push('Nama wajib diisi');
                    document.getElementById('name').classList.add('border-red-500');
                } else {
                    document.getElementById('name').classList.remove('border-red-500');
                }
                
                if (!phone) {
                    errors.push('Nomor telepon wajib diisi');
                    document.getElementById('phone_number').classList.add('border-red-500');
                } else if (phone.length < 10) {
                    errors.push('Nomor telepon minimal 10 digit');
                    document.getElementById('phone_number').classList.add('border-red-500');
                } else {
                    document.getElementById('phone_number').classList.remove('border-red-500');
                }
                
                console.log('DEBUG: Validation errors:', errors);
                
                if (errors.length > 0) {
                    console.log('DEBUG: Validation failed, showing errors');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: errors.map(error => `<div class="text-left">• ${error}</div>`).join(''),
                            confirmButtonColor: '#ef4444'
                        });
                    } else {
                        alert('Validasi gagal:\n' + errors.join('\n'));
                    }
                    return false;
                }
                
                console.log('DEBUG: Validation passed, submitting form');
                
                // Show loading state
                this.disabled = true;
                this.innerHTML = '<div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>Menyimpan...';
                
                // Submit the form
                console.log('DEBUG: Calling form.submit()');
                
                // Check CSRF token
                const csrfToken = document.querySelector('input[name="_token"]');
                console.log('DEBUG: CSRF token found:', csrfToken ? csrfToken.value : 'NOT FOUND');
                
                // Log all form data before submission
                const formData = new FormData(form);
                console.log('DEBUG: Form data before submission:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                
                form.submit();
            } else {
                console.error('ERROR: Form not found for submit button');
            }
        });
    }
    
    // Clear phone number function
    const clearPhoneBtn = document.getElementById('clearPhoneBtn');
    const phoneInput = document.getElementById('phone_number');
    
    if (clearPhoneBtn && phoneInput) {
        clearPhoneBtn.addEventListener('click', function() {
            if (phoneInput.value.length > 0) {
                const confirmMessage = typeof Swal !== 'undefined' ? 
                    Swal.fire({
                        title: 'Hapus Nomor Telepon?',
                        text: `Apakah Anda yakin ingin menghapus nomor "${phoneInput.value}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }) :
                    confirm(`Apakah Anda yakin ingin menghapus nomor "${phoneInput.value}"?`);
                
                if (typeof confirmMessage === 'object') {
                    confirmMessage.then((result) => {
                        if (result.isConfirmed) {
                            clearPhoneNumber();
                        }
                    });
                } else if (confirmMessage) {
                    clearPhoneNumber();
                }
            }
        });
        
        function clearPhoneNumber() {
            phoneInput.value = '';
            phoneInput.focus();
            phoneInput.classList.remove('border-green-500', 'border-red-500');
            
            // Tambahkan animasi feedback
            clearPhoneBtn.classList.add('text-red-500');
            setTimeout(() => {
                clearPhoneBtn.classList.remove('text-red-500');
            }, 200);
            
            // Tampilkan notifikasi sukses
            if (typeof Swal !== 'undefined') {
                Swal.fire(
                    'Terhapus!',
                    'Nomor telepon berhasil dihapus.',
                    'success'
                );
            } else {
                alert('Nomor telepon berhasil dihapus.');
            }
        }
        
        // Show/hide clear button based on input value
        phoneInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearPhoneBtn.style.display = 'block';
            } else {
                clearPhoneBtn.style.display = 'none';
            }
        });
        
        // Initial state
        if (phoneInput.value.length > 0) {
            clearPhoneBtn.style.display = 'block';
        } else {
            clearPhoneBtn.style.display = 'none';
        }
        
        // Keyboard shortcut untuk menghapus nomor (Ctrl+Backspace)
        phoneInput.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Backspace') {
                e.preventDefault();
                if (this.value.length > 0) {
                    const confirmMessage = typeof Swal !== 'undefined' ? 
                        Swal.fire({
                            title: 'Hapus Nomor Telepon?',
                            text: `Apakah Anda yakin ingin menghapus nomor "${this.value}"?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }) :
                        confirm(`Apakah Anda yakin ingin menghapus nomor "${this.value}"?`);
                    
                    if (typeof confirmMessage === 'object') {
                        confirmMessage.then((result) => {
                            if (result.isConfirmed) {
                                this.value = '';
                                this.classList.remove('border-green-500', 'border-red-500');
                                
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Nomor telepon berhasil dihapus.',
                                        'success'
                                    );
                                } else {
                                    alert('Nomor telepon berhasil dihapus.');
                                }
                            }
                        });
                    } else if (confirmMessage) {
                        this.value = '';
                        this.classList.remove('border-green-500', 'border-red-500');
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire(
                                'Terhapus!',
                                'Nomor telepon berhasil dihapus.',
                                'success'
                            );
                        } else {
                            alert('Nomor telepon berhasil dihapus.');
                        }
                    }
                }
            }
        });
    }
    
    console.log('=== DEBUG: All event listeners added successfully ===');
});
</script>
@endsection 