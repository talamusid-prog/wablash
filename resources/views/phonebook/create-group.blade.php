@extends('layouts.app')

@section('title', 'Buat Grup Baru')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Buat Grup Baru</h1>
                <p class="text-gray-600 mt-2">Buat grup baru untuk mengelompokkan kontak</p>
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Grup</h3>
            </div>
            
            <form action="{{ route('phonebook.store-group') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Grup *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                           placeholder="Masukkan nama grup">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                              placeholder="Tambahkan deskripsi tentang grup ini">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Warna Grup</label>
                    <div class="flex items-center space-x-4">
                        <input type="color" id="color" name="color" value="{{ old('color', '#6366f1') }}"
                               class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer">
                        <input type="text" id="colorText" value="{{ old('color', '#6366f1') }}"
                               class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                               placeholder="#6366f1">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Pilih warna untuk mengidentifikasi grup</p>
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('phonebook.index') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        Buat Grup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Sync color picker dengan text input
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('colorText').value = this.value;
});

document.getElementById('colorText').addEventListener('input', function() {
    const colorValue = this.value;
    if (/^#[0-9A-F]{6}$/i.test(colorValue)) {
        document.getElementById('color').value = colorValue;
    }
});

// Auto-format color input
document.getElementById('colorText').addEventListener('blur', function() {
    let value = this.value;
    
    // Add # if not present
    if (!value.startsWith('#')) {
        value = '#' + value;
    }
    
    // Ensure it's a valid hex color
    if (/^#[0-9A-F]{6}$/i.test(value)) {
        this.value = value.toUpperCase();
    } else {
        this.value = '#6366f1'; // Default color
        document.getElementById('color').value = '#6366f1';
    }
});
</script>
@endsection 