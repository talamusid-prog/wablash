@extends('layouts.app')

@section('title', 'Edit Kontak')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Kontak</h1>
                <p class="text-gray-600 mt-2">Edit informasi kontak</p>
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
                <h3 class="text-lg font-semibold text-gray-900">Informasi Kontak</h3>
            </div>
            
            <form action="{{ route('phonebook.update', $phonebook) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $phonebook->name) }}" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                               placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon *</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $phonebook->phone_number) }}" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                               placeholder="Contoh: 08123456789">
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $phonebook->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                               placeholder="email@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="group" class="block text-sm font-medium text-gray-700 mb-2">Grup</label>
                        <div class="relative">
                            <input type="text" id="group" name="group" value="{{ old('group', $phonebook->group) }}" list="groups"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                   placeholder="Pilih atau buat grup baru">
                            <datalist id="groups">
                                @foreach($groups as $group)
                                    <option value="{{ $group }}">
                                @endforeach
                            </datalist>
                        </div>
                        @error('group')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea id="notes" name="notes" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                              placeholder="Tambahkan catatan tentang kontak ini">{{ old('notes', $phonebook->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $phonebook->is_active) ? 'checked' : '' }}
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
                    <button type="submit" 
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        Update Kontak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
});

// Auto-suggest groups
document.getElementById('group').addEventListener('input', function() {
    const input = this.value.toLowerCase();
    const datalist = document.getElementById('groups');
    const options = datalist.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value.toLowerCase().includes(input)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
});
</script>
@endsection 