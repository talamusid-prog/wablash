@extends('layouts.app')

@section('title', 'Buat Kampanye Baru')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="/campaigns" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Buat Kampanye Baru</h1>
                            
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="clearAllCampaignData()" class="flex items-center px-4 py-2 text-sm text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-lg transition-all duration-200 font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Bersihkan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step Navigation -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4">
                <div class="flex items-center justify-center space-x-8">
                    <!-- Step 1: Recipients -->
                    <div class="flex items-center space-x-3" id="step1-indicator">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">1</span>
                        </div>
                        <span class="text-blue-600 font-medium">Recipients</span>
                    </div>
                    
                    <!-- Step 2: Content -->
                    <div class="flex items-center space-x-3" id="step2-indicator">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 font-semibold text-sm">2</span>
                        </div>
                        <span class="text-gray-500 font-medium">Content</span>
                    </div>
                    
                    <!-- Step 3: Preview -->
                    <div class="flex items-center space-x-3" id="step3-indicator">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 font-semibold text-sm">3</span>
                        </div>
                        <span class="text-gray-500 font-medium">Preview</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Step 1: Recipients -->
        <div id="step1" class="step-content">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="max-w-4xl mx-auto space-y-8">
                    <!-- Broadcast Name -->
                    <div class="form-group flex items-center mb-6">
                        <div class="w-1/3">
                            <label class="form-label text-lg font-semibold">Broadcast Name</label>
                        </div>
                        <div class="w-2/3">
                            <div class="relative">
                                <input type="text" id="broadcastName" class="form-input text-base border border-gray-300 px-4 py-3 bg-white hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 rounded-xl" placeholder="Masukkan nama kampanye broadcast Anda..." value="">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Nama ini akan digunakan untuk mengidentifikasi kampanye Anda</p>
                        </div>
                    </div>

                    <!-- Sender Number -->
                    <div class="form-group flex items-start mb-6">
                        <div class="w-1/3">
                            <label class="form-label text-lg font-semibold">Nomor Pengirim</label>
                            <p class="text-sm text-gray-500 mt-1">Pilih nomor WhatsApp yang akan digunakan</p>
                        </div>
                        <div class="w-2/3 space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="sendAllOnline" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mr-2" checked>
                                <label for="sendAllOnline" class="text-sm font-medium text-gray-900">Kirim dengan Semua Nomor Online</label>
                            </div>
                            <div id="senderNumbersList" class="space-y-2">
                                <!-- Sender numbers will be loaded here dynamically -->
                                <div class="flex items-center justify-center py-4 text-gray-500">
                                    <svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Memuat nomor WhatsApp...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recipients List -->
                    <div class="form-group flex items-start mb-6">
                        <div class="w-1/3">
                            <label class="form-label text-lg font-semibold">Daftar Penerima</label>
                            <p class="text-sm text-gray-500 mt-1">Pilih grup atau kontak yang akan menerima pesan</p>
                        </div>
                        <div class="w-2/3">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-blue-600 font-semibold text-lg" id="selectedCount">487 selected contacts</div>
                                            <div class="text-sm text-blue-500">Kontak yang akan menerima broadcast</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-blue-500">Total Available</div>
                                        <div class="text-sm font-medium text-blue-700">1,644 contacts</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                <!-- Search and Select All -->
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 border-b border-gray-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <button type="button" id="selectAllBtn" class="flex items-center justify-center text-sm text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-lg transition-all duration-200 font-medium">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                                            </svg>
                                            Pilih Semua
                                        </button>
                                        <div class="relative w-full sm:w-80">
                                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </div>
                                            <input type="text" id="searchContacts" class="w-full pl-12 pr-12 py-3 text-base border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white shadow-sm" placeholder="Cari grup atau kontak...">
                                            <button type="button" id="clearSearchBtn" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden p-1 rounded-full hover:bg-gray-100 transition-all duration-200" onclick="clearSearch()">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Contact Groups List -->
                                <div class="p-4 space-y-3 max-h-80 overflow-y-auto" id="contactGroupsList">
                                    @if($individualContactsCount > 0)
                                    <div class="flex items-center bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                                        <input type="checkbox" class="contact-group-checkbox w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 mr-4" data-count="{{ $individualContactsCount }}" data-type="individual">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <label class="text-sm font-semibold text-gray-900 cursor-pointer">Kontak Individual</label>
                                            </div>
                                            <div class="flex items-center space-x-3 text-xs text-gray-600">
                                                <span>{{ $individualContactsCount }} contacts</span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @forelse($groups as $group)
                                    <div class="flex items-center bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                                        <input type="checkbox" class="contact-group-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mr-4" data-count="{{ $group['participant_count'] }}" data-group-id="{{ $group['contact_id'] }}" data-type="group">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <label class="text-sm font-semibold text-gray-900 cursor-pointer">{{ $group['name'] }}</label>
                                            </div>
                                            <div class="flex items-center space-x-3 text-xs text-gray-600">
                                                <span>{{ $group['participant_count'] }} contacts</span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Grup</span>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-2">Belum ada grup kontak yang tersedia</p>
                                        <p class="text-xs text-gray-400">Kontak akan muncul di sini setelah Anda menambahkan grup</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Content -->
        <div id="step2" class="step-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Panel - Content Blocks & Tips -->
                <div class="space-y-6">
                    <!-- Content Templates -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Content Templates</h3>
                            <button type="button" id="addContentBtn" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Content List -->
                        <div id="contentList" class="space-y-3">
                            <!-- Content A -->
                            <div class="content-item bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md" data-content-id="content-a">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900">Content A</h4>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                        <button type="button" class="text-gray-400 hover:text-blue-500 transition-colors duration-200" onclick="editContent('content-a')" title="Edit Content">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" class="text-gray-400 hover:text-red-500 transition-colors duration-200" onclick="deleteContent('content-a')" title="Delete Content">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 line-clamp-2">Halo {name}, selamat datang di layanan kami...</p>
                            </div>
                            
                            <!-- Content B -->
                            <div class="content-item bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md" data-content-id="content-b">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900">Content B</h4>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                                        <button type="button" class="text-gray-400 hover:text-blue-500 transition-colors duration-200" onclick="editContent('content-b')" title="Edit Content">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" class="text-gray-400 hover:text-red-500 transition-colors duration-200" onclick="deleteContent('content-b')" title="Delete Content">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 line-clamp-2">Terima kasih telah menggunakan layanan kami...</p>
                            </div>
                        </div>
                    </div>



                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" id="saveTemplateBtn" class="flex items-center justify-center px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Save Template
                            </button>
                            <button type="button" id="loadTemplateBtn" class="flex items-center justify-center px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Load Template
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Middle Panel - Message Composer -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <!-- Enhanced Formatting Bar -->
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-4 mb-6 border border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-700">Message Formatting</h4>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500">Auto-save: <span id="autoSaveStatus" class="text-green-600">ON</span></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 flex-wrap">
                                <button type="button" id="emojiBtn" class="flex items-center px-3 py-2 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 rounded-lg border border-gray-200 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-7.536 5.879a1 1 0 001.415 0 3 3 0 014.242 0 1 1 0 001.415-1.415 5 5 0 00-7.072 0 1 1 0 000 1.415z" clip-rule="evenodd"></path>
                                </svg>
                                    Emoji
                            </button>
                                <button type="button" id="boldBtn" class="flex items-center px-3 py-2 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 rounded-lg border border-gray-200 transition-all duration-200 font-bold">
                                    B
                                </button>
                                <button type="button" id="italicBtn" class="flex items-center px-3 py-2 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 rounded-lg border border-gray-200 transition-all duration-200 italic">
                                    I
                                </button>
                                <button type="button" id="strikeBtn" class="flex items-center px-3 py-2 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 rounded-lg border border-gray-200 transition-all duration-200 line-through">
                                    S
                                </button>
                                <button type="button" id="linkBtn" class="flex items-center px-3 py-2 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 rounded-lg border border-gray-200 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    Link
                                </button>
                                <select id="variableSelect" class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">📝 Variables</option>
                                    <option value="{name}">👤 Nama</option>
                                    <option value="{phone}">📞 Telepon</option>
                                    <option value="{email}">📧 Email</option>
                                    <option value="{group}">👥 Grup</option>
                                    <option value="{company}">🏢 Perusahaan</option>
                                    <option value="{date}">📅 Tanggal</option>
                            </select>
                            </div>
                        </div>

                        <!-- Enhanced Message Text Area -->
                        <div class="mb-6">
                            <div class="relative">
                                <textarea id="messageContent" rows="10" class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-gray-700 placeholder-gray-400 transition-all duration-200" placeholder="✍️ Tulis pesan WhatsApp Anda di sini...&#10;&#10;💡 Variabel Personalisasi:&#10;• {name} - Nama kontak&#10;• {phone} - Nomor telepon&#10;• {email} - Email kontak&#10;• {group} - Nama grup&#10;• {company} - Nama perusahaan&#10;• {date} - Tanggal hari ini&#10;&#10;Contoh: Halo {name}, selamat datang di layanan kami!"></textarea>
                                <div class="absolute bottom-3 right-3 flex items-center space-x-3">
                                    <div class="flex items-center space-x-1 text-xs text-gray-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span id="charCount">0/2000</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Attachment Section -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <label class="text-sm font-semibold text-gray-700">📎 Attachments (Optional)</label>
                                <button type="button" id="clearAttachmentBtn" class="text-xs text-red-500 hover:text-red-700 transition-colors duration-200 hidden">
                                    Clear All
                                </button>
                            </div>
                            <div id="attachmentArea" class="space-y-3">
                            <div class="flex items-center space-x-4">
                                    <button type="button" id="fileUploadBtn" class="flex items-center px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg border-2 border-dashed border-blue-200 hover:border-blue-300 transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    Pilih File
                                </button>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">Drag & drop file atau klik untuk memilih</p>
                                        <p class="text-xs text-gray-400 mt-1">Maksimal: Gambar 1MB, Dokumen 5MB</p>
                            </div>
                                </div>
                                <div id="filePreview" class="hidden">
                                    <!-- File previews will be shown here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <!-- Step 3: Preview -->
        <div id="step3" class="step-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Panel - Message Preview -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Message Preview</h3>
                    <div class="bg-gray-100 rounded-lg p-6 min-h-96 relative overflow-hidden">
                        <!-- WhatsApp Background Pattern -->
                        <div class="absolute inset-0 opacity-5">
                            <div class="grid grid-cols-8 gap-4 h-full">
                                <div class="flex items-center justify-center">💬</div>
                                <div class="flex items-center justify-center">⏰</div>
                                <div class="flex items-center justify-center">📷</div>
                                <div class="flex items-center justify-center">🎤</div>
                                <div class="flex items-center justify-center">✉️</div>
                                <div class="flex items-center justify-center">🛵</div>
                                <div class="flex items-center justify-center">❤️</div>
                                <div class="flex items-center justify-center">📱</div>
                            </div>
                        </div>
                        
                        <!-- Message Bubble -->
                        <div class="relative z-10">
                            <div class="bg-white rounded-lg p-4 shadow-sm max-w-xs">
                                <div class="text-sm text-gray-900" id="previewMessage">Pesan preview akan muncul di sini...</div>
                                <div class="text-xs text-gray-500 mt-1">13:40</div>
                            </div>
                            <div class="mt-2 text-xs text-gray-400 text-center">
                                💡 Preview dengan data contoh: John Doe
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Campaign Settings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Campaign Settings</h3>
                    
                    <!-- Information Alert -->
                    <div class="bg-purple-100 border border-purple-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-purple-800">
                                    This broadcast is a "Draft". When it sends, it will be received by <span class="font-semibold" id="previewRecipientCount">487</span> contacts.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="space-y-6">
                        <!-- Sender -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Sender</span>
                            </div>
                            <span class="text-sm text-gray-600">: +6285242766676 - -</span>
                        </div>

                        <!-- Sending Speed -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Sending Speed</span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="sendingSpeed" value="auto" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-900">Auto</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="sendingSpeed" value="custom" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-900">Custom</span>
                                </label>
                            </div>
                        </div>

                        <!-- Allow Unsubscribe -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Allow Unsubscribe</span>
                                <button type="button" class="ml-2 text-blue-400 hover:text-blue-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="relative">
                                <input type="checkbox" id="unsubscribeToggle" class="sr-only" checked>
                                <label for="unsubscribeToggle" class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <div class="w-10 h-4 bg-gray-200 rounded-full shadow-inner"></div>
                                        <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -top-1 -left-1 transition transform translate-x-6"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Perfect Timing -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Perfect Timing</span>
                                <button type="button" class="ml-2 text-blue-400 hover:text-blue-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="relative">
                                <input type="checkbox" id="timingToggle" class="sr-only" checked>
                                <label for="timingToggle" class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <div class="w-10 h-4 bg-gray-200 rounded-full shadow-inner"></div>
                                        <div class="dot absolute w-6 h-6 bg-white rounded-full shadow -top-1 -left-1 transition transform translate-x-6"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Time to Send -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Waktu Kirim</span>
                            </div>
                            <select id="timeToSendSelect" class="text-sm border border-gray-300 rounded px-3 py-2 bg-white">
                                <option value="immediately">Segera</option>
                                <option value="schedule">Jadwalkan</option>
                                <option value="custom">Waktu Kustom</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4 mt-8">
                        <button type="button" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Send Preview
                        </button>
                        <button type="button" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Save As Draft
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Card -->
    <div id="bottomNavigation" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center justify-center space-x-4">
                    <button type="button" id="prevBtn" class="flex items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Prev
                    </button>
                    <button type="button" id="nextBtn" class="flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium transition-colors duration-200">
                        Next
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 3;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateStepIndicators();
    updateNavigationButtons();
    setupEventListeners();
    updateSelectedCount();
    
    // Initialize tips in Indonesian by default
    // showTips('id');
    
    // Load any saved content
    loadSavedContent();
    
    // Load campaign settings
    loadCampaignSettings();
    
    // Initialize toggle UI colors
    const unsubscribeToggle = document.getElementById('unsubscribeToggle');
    const timingToggle = document.getElementById('timingToggle');
    if (unsubscribeToggle) updateToggleUI(unsubscribeToggle);
    if (timingToggle) updateToggleUI(timingToggle);
    
    // Set up auto-save indicator
    const autoSaveStatus = document.getElementById('autoSaveStatus');
    if (autoSaveStatus) {
        autoSaveStatus.textContent = 'ON';
        autoSaveStatus.className = 'text-green-600';
    }
    
    // Initialize content selection
    const firstContent = document.querySelector('.content-item');
    if (firstContent) {
        firstContent.click(); // Select first content by default
    }
    
    // Initialize search functionality
    setTimeout(() => {
        filterContacts(); // Initial filter to show all items
        console.log('Search functionality initialized'); // Debug log
    }, 100);
    
    // Load active WhatsApp numbers
    loadActiveWhatsAppNumbers();
    
    // Load saved attachments
    loadSavedAttachments();
    
    // Load saved draft if exists
    loadSavedDraft();
    
    // Check available contacts for debugging - REMOVED
    // setTimeout(() => {
    //     checkAvailableContacts();
    // }, 1000);
});

function setupEventListeners() {
    // Navigation buttons
    document.getElementById('prevBtn').addEventListener('click', previousStep);
    document.getElementById('nextBtn').addEventListener('click', nextStep);
    
    // Contact group checkboxes
    document.querySelectorAll('.contact-group-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Select all button
    document.getElementById('selectAllBtn').addEventListener('click', toggleSelectAll);
    
    // Search functionality
    const searchInput = document.getElementById('searchContacts');
    if (searchInput) {
        searchInput.addEventListener('input', filterContacts);
        searchInput.addEventListener('keyup', filterContacts);
        searchInput.addEventListener('change', filterContacts);
        
        // Add clear search functionality
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                filterContacts();
                this.blur();
            }
        });
        
        // console.log('Search input event listeners attached'); // Debug log
    } else {
        console.error('Search input not found'); // Debug log
    }
    
    // Clear search button
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', clearSearch);
        // console.log('Clear search button event listener attached'); // Debug log
    }
    
    // Character count
    document.getElementById('messageContent').addEventListener('input', updateCharCount);
    
    // Real-time preview update
    document.getElementById('messageContent').addEventListener('input', updatePreview);
    
    // Auto-save functionality
    document.getElementById('messageContent').addEventListener('input', debounce(autoSaveMessage, 1000));
    
    // Toggle switches with enhanced styling
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.id.includes('Toggle')) {
                updateToggleUI(this);
                
                // Save settings to localStorage
                saveCampaignSettings();
            }
        });
    });
    
    // Radio buttons for sending speed
    document.querySelectorAll('input[name="sendingSpeed"]').forEach(radio => {
        radio.addEventListener('change', function() {
            saveCampaignSettings();
            updateSendingSpeedUI(true);
        });
    });
    
    // Time to Send dropdown
    const timeToSendSelect = document.getElementById('timeToSendSelect');
    if (timeToSendSelect) {
        timeToSendSelect.addEventListener('change', function() {
            saveCampaignSettings();
            updateTimeToSendUI(true);
        });
    }
    
    // Help buttons
    document.querySelectorAll('button').forEach(button => {
        if (button.innerHTML.includes('fill-rule="evenodd"') && button.closest('div').querySelector('span')) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const settingType = this.closest('div').querySelector('span').textContent.toLowerCase();
                showSettingHelp(settingType);
            });
        }
    });
    
    // Action buttons
    const sendPreviewBtn = Array.from(document.querySelectorAll('button')).find(btn => btn.textContent.includes('Send Preview'));
    const saveDraftBtn = Array.from(document.querySelectorAll('button')).find(btn => btn.textContent.includes('Save As Draft'));
    
    if (sendPreviewBtn) {
        sendPreviewBtn.addEventListener('click', sendPreview);
    }
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', saveAsDraft);
    }
    // Content template functionality
    setupContentTemplates();
    // Tips language toggle
    // Quick actions
    setupQuickActions();
    // Enhanced file upload functionality
    setupEnhancedFileUpload();
    
    // Auto-save attachments when files are added
    setupAutoSaveAttachments();
    // Enhanced text formatting functionality
    setupEnhancedTextFormatting();
    // API numbers functionality
    setupApiNumbers();
    // Content item selection
    setupContentSelection();
}

function updateStepIndicators() {
    // Reset all indicators
    for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.getElementById(`step${i}-indicator`);
        const circle = indicator.querySelector('div');
        const text = indicator.querySelector('span:last-child');
        
        if (i < currentStep) {
            // Completed step
            circle.className = 'w-8 h-8 bg-green-500 rounded-full flex items-center justify-center';
            circle.innerHTML = '<svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>';
            text.className = 'text-green-600 font-medium';
        } else if (i === currentStep) {
            // Current step
            circle.className = 'w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center';
            circle.innerHTML = `<span class="text-white font-semibold text-sm">${i}</span>`;
            text.className = 'text-blue-600 font-medium';
        } else {
            // Future step
            circle.className = 'w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center';
            circle.innerHTML = `<span class="text-gray-600 font-semibold text-sm">${i}</span>`;
            text.className = 'text-gray-500 font-medium';
        }
    }
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    // Update prev button
    if (currentStep === 1) {
        prevBtn.disabled = true;
        prevBtn.className = 'flex items-center px-6 py-3 bg-gray-600 text-gray-400 rounded-lg font-medium cursor-not-allowed';
    } else {
        prevBtn.disabled = false;
        prevBtn.className = 'flex items-center px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors duration-200';
    }
    
    // Update next button
    if (currentStep === totalSteps) {
        nextBtn.innerHTML = `
            Send
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
        `;
    } else {
        nextBtn.innerHTML = `
            Next
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        `;
    }
}

function showStep(step) {
    currentStep = step;
    
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show current step
    document.getElementById(`step${step}`).classList.remove('hidden');
    
    // Update indicators and buttons
    updateStepIndicators();
    updateNavigationButtons();
}

// These functions are now replaced by enhanced versions above

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.contact-group-checkbox:checked');
    let totalCount = 0;
    
    checkboxes.forEach(checkbox => {
        totalCount += parseInt(checkbox.dataset.count);
    });
    
    document.getElementById('selectedCount').textContent = `${totalCount} kontak terpilih`;
    document.getElementById('previewRecipientCount').textContent = totalCount;
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.contact-group-checkbox');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
    
    // Update button text
    if (selectAllBtn) {
        if (allChecked) {
            selectAllBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                </svg>
                Pilih Semua
            `;
        } else {
            selectAllBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Hapus Semua
            `;
        }
    }
    
    updateSelectedCount();
}

function updateCharCount() {
    const textarea = document.getElementById('messageContent');
    const charCount = document.getElementById('charCount');
    const count = textarea.value.length;
    charCount.textContent = `${count}/2000`;
    
    if (count > 1800) {
        charCount.className = 'text-sm text-red-500';
    } else if (count > 1500) {
        charCount.className = 'text-sm text-yellow-500';
    } else {
        charCount.className = 'text-sm text-gray-500';
    }
}

// Function to handle message personalization
function personalizeMessage(message, contactData = {}) {
    // Default contact data for preview
    const defaultData = {
        name: 'John Doe',
        phone: '+6281234567890',
        email: 'john@example.com',
        group: 'Grup Utama',
        company: 'Perusahaan ABC',
        date: new Date().toLocaleDateString('id-ID')
    };
    
    // Merge with provided contact data
    const data = { ...defaultData, ...contactData };
    
    // Replace placeholders with actual data
    return message
        .replace(/\{name\}/g, data.name)
        .replace(/\{phone\}/g, data.phone)
        .replace(/\{email\}/g, data.email)
        .replace(/\{group\}/g, data.group)
        .replace(/\{company\}/g, data.company)
        .replace(/\{date\}/g, data.date);
}

// Enhanced updatePreview function with personalization and file attachments
function updatePreview() {
    const message = document.getElementById('messageContent').value || 'Pesan preview akan muncul di sini...';
    const previewElement = document.getElementById('previewMessage');
    
    console.log('updatePreview() called'); // Debug log
    
    if (previewElement) {
        // Personalize the message
        const personalizedMessage = personalizeMessage(message);
        
        // Format message for better preview
        let formattedMessage = personalizedMessage
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/_(.*?)_/g, '<em>$1</em>')
            .replace(/~(.*?)~/g, '<del>$1</del>')
            .replace(/\n/g, '<br>');
        
        // Add file attachments to preview
        const savedFiles = JSON.parse(localStorage.getItem('campaign_attachments') || '[]');
        console.log('updatePreview - savedFiles from localStorage:', savedFiles); // Debug log
        
        if (savedFiles.length > 0) {
            console.log(`updatePreview - Adding ${savedFiles.length} files to preview`); // Debug log
            formattedMessage += '<div class="mt-4 space-y-2">';
            savedFiles.forEach((file, index) => {
                console.log(`updatePreview - Processing file ${index + 1}:`, file.name, file.type); // Debug log
                if (file.type.startsWith('image/')) {
                    console.log(`updatePreview - Adding image: ${file.name}`); // Debug log
                    formattedMessage += `
                        <div class="bg-gray-50 rounded-lg p-2">
                            <img src="${file.data}" alt="${file.name}" class="max-w-full h-auto rounded" style="max-height: 200px;">
                            <p class="text-xs text-gray-500 mt-1">${file.name}</p>
                        </div>
                    `;
                } else {
                    console.log(`updatePreview - Adding document: ${file.name}`); // Debug log
                    formattedMessage += `
                        <div class="bg-gray-50 rounded-lg p-3 flex items-center space-x-3">
                            <span class="text-2xl">📄</span>
                            <div>
                                <p class="text-sm font-medium text-gray-900">${file.name}</p>
                                <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                            </div>
                        </div>
                    `;
                }
            });
            formattedMessage += '</div>';
        } else {
            console.log('updatePreview - No files found in localStorage'); // Debug log
        }
        
        console.log('updatePreview - Setting innerHTML to preview element'); // Debug log
        previewElement.innerHTML = formattedMessage;
    } else {
        console.error('updatePreview - previewElement not found'); // Debug log
    }
}

function validateForm() {
    const broadcastName = document.getElementById('broadcastName').value.trim();
    const message = document.getElementById('messageContent').value.trim();
    const selectedRecipients = document.querySelectorAll('.contact-group-checkbox:checked');
    
    if (!broadcastName) {
        showError('Nama broadcast harus diisi');
        return false;
    }
    
    if (!message) {
        showError('Pesan harus diisi');
        return false;
    }
    
    if (selectedRecipients.length === 0) {
        showError('Pilih minimal satu grup atau kontak');
        return false;
    }
    
    return true;
}

function getSelectedRecipients() {
    const checkboxes = document.querySelectorAll('.contact-group-checkbox:checked');
    const recipients = [];
    
    console.log('Found checked checkboxes:', checkboxes.length);
    
    checkboxes.forEach(checkbox => {
        const label = checkbox.nextElementSibling.textContent;
        const count = checkbox.dataset.count;
        const type = checkbox.dataset.type;
        const groupId = checkbox.dataset.groupId;
        
        console.log('Checkbox data:', {
            label: label,
            count: count,
            type: type,
            groupId: groupId,
            dataset: checkbox.dataset
        });
        
        recipients.push({ 
            name: label, 
            count: parseInt(count),
            type: type,
            group_id: groupId
        });
    });
    
    console.log('Final recipients array:', recipients);
    return recipients;
}

function filterContacts() {
    const searchTerm = document.getElementById('searchContacts').value.toLowerCase();
    const contactItems = document.querySelectorAll('#contactGroupsList .flex.items-center');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    
    // console.log('Search term:', searchTerm); // Debug log
    // console.log('Found contact items:', contactItems.length); // Debug log
    
    // Show/hide clear search button
    if (clearSearchBtn) {
        if (searchTerm.length > 0) {
            clearSearchBtn.classList.remove('hidden');
        } else {
            clearSearchBtn.classList.add('hidden');
        }
    }
    
    contactItems.forEach(item => {
        const label = item.querySelector('label');
        if (label) {
            const text = label.textContent.toLowerCase();
            // console.log('Checking item:', text); // Debug log
            
            if (text.includes(searchTerm)) {
                item.style.display = 'flex';
                item.style.opacity = '1';
            } else {
                item.style.display = 'none';
                item.style.opacity = '0';
            }
        }
    });
    
    // Show "no results" message if no items match
    const visibleItems = document.querySelectorAll('#contactGroupsList .flex.items-center[style*="display: flex"]');
    const noResultsMsg = document.getElementById('noResultsMsg');
    
    if (visibleItems.length === 0 && searchTerm.length > 0) {
        if (!noResultsMsg) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMsg';
            msg.className = 'text-center py-4 text-sm text-gray-500';
            msg.textContent = `Tidak ada hasil untuk "${searchTerm}"`;
            document.getElementById('contactGroupsList').appendChild(msg);
        }
    } else if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

function clearSearch() {
    const searchInput = document.getElementById('searchContacts');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    
    if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
        filterContacts();
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.classList.add('hidden');
    }
}

// Legacy function replacements - these are now handled by enhanced functions above
function insertEmoji(emoji) {
    const textarea = document.getElementById('messageContent');
    const start = textarea.selectionStart;
    textarea.value = textarea.value.substring(0, start) + emoji + textarea.value.substring(start);
    textarea.focus();
    textarea.setSelectionRange(start + emoji.length, start + emoji.length);
    updateCharCount();
}

// Additional helper functions for enhanced functionality
function showNotification(message, type = 'info', duration = 3000) {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 
                   type === 'error' ? 'bg-red-500' : 
                   type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
    
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 text-white ${bgColor} notification`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, duration);
}

// Enhanced validation function
function validateStep2() {
    const message = document.getElementById('messageContent').value.trim();
    
    if (!message) {
        showNotification('Pesan tidak boleh kosong!', 'error');
        return false;
    }
    
    if (message.length > 2000) {
        showNotification('Pesan terlalu panjang! Maksimal 2000 karakter.', 'error');
        return false;
    }
    
    return true;
}

// Enhanced next step function
function nextStep() {
    if (currentStep < totalSteps) {
        // Validate current step before proceeding
        if (currentStep === 1) {
            const broadcastName = document.getElementById('broadcastName').value.trim();
            const selectedRecipients = document.querySelectorAll('.contact-group-checkbox:checked');
            
            if (!broadcastName) {
                showNotification('Nama broadcast harus diisi!', 'error');
                return;
            }
            
            if (selectedRecipients.length === 0) {
                showNotification('Pilih minimal satu grup atau kontak!', 'error');
                return;
            }
        } else if (currentStep === 2) {
            if (!validateStep2()) {
                return;
            }
        }
        
        currentStep++;
        showStep(currentStep);
        
        // Update preview in step 3
        if (currentStep === 3) {
            // Ensure all current files are saved to localStorage
            saveCurrentFilesToStorage();
            updatePreview();
            updateSelectedCount();
            
            // Show file count in preview
            const savedFiles = JSON.parse(localStorage.getItem('campaign_attachments') || '[]');
            if (savedFiles.length > 0) {
                console.log(`${savedFiles.length} files loaded for preview`);
                showNotification(`${savedFiles.length} file berhasil dimuat untuk preview`, 'info', 2000);
            }
        }
        
        // Show success notification
        if (currentStep === 3) {
            const savedFiles = JSON.parse(localStorage.getItem('campaign_attachments') || '[]');
            if (savedFiles.length > 0) {
                showNotification(`Langkah ${currentStep} berhasil! ${savedFiles.length} file akan ditampilkan di preview.`, 'success', 3000);
            } else {
                showNotification(`Langkah ${currentStep} berhasil!`, 'success', 2000);
            }
        } else {
            showNotification(`Langkah ${currentStep} berhasil!`, 'success', 2000);
        }
    } else {
        // Final step - send campaign
        sendCampaign();
    }
}

// Enhanced previous step function
function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        showNotification(`Kembali ke langkah ${currentStep}`, 'info', 2000);
    }
}

// Enhanced campaign sending function
async function sendCampaign() {
    console.log('sendCampaign() called'); // Debug log
    
    try {
        // Validate all steps
    if (!validateForm()) {
            console.log('Form validation failed'); // Debug log
        return;
    }
        
        console.log('Form validation passed'); // Debug log
    
    // Collect all form data
    const formData = {
        name: document.getElementById('broadcastName').value,
        message: document.getElementById('messageContent').value,
        recipients: getSelectedRecipients(),
        settings: getCampaignSettings(),
        attachments: getAttachments()
    };
    
    console.log('Campaign formData collected:', {
        name: formData.name,
        message: formData.message,
        recipients_count: formData.recipients.length,
        attachments_count: formData.attachments.length,
        attachments: formData.attachments
    });
        
        console.log('Sending campaign with data:', formData); // Debug log
    
    // Show loading
    showLoading('Mengirim kampanye...');
    
        // Send to backend API
        console.log('Sending campaign data to API:', formData);
        await sendCampaignToAPI(formData);
        
    } catch (error) {
        console.error('Error in sendCampaign:', error);
        hideLoading();
        showNotification('Terjadi kesalahan saat mengirim kampanye: ' + error.message, 'error');
    }
}

// Function to send campaign data to backend
async function sendCampaignToAPI(formData) {
    console.log('sendCampaignToAPI() called with data:', formData); // Debug log
    
    // Get phone numbers from recipients
    const phoneNumbers = await extractPhoneNumbers(formData.recipients);
    console.log('Phone numbers for campaign:', phoneNumbers);
    
    // Get CSRF token from meta tag or create a hidden input
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        showNotification('Error: CSRF token tidak ditemukan', 'error');
        hideLoading();
        return;
    }
    
    const requestBody = {
        name: formData.name,
        message: formData.message,
        phone_numbers: phoneNumbers,
        session_id: getActiveSessionId(),
        settings: formData.settings,
        attachments: formData.attachments
    };
    
    console.log('Request body being sent to API:', requestBody);
    
    fetch('/api/campaigns', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestBody)
    })
    .then(response => {
        console.log('API Response status:', response.status);
        console.log('API Response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('API Response data:', data);
        hideLoading();
        if (data.success) {
            // Show success notification with details
            const recipientCount = formData.recipients.reduce((total, recipient) => total + recipient.count, 0);
            const attachmentCount = formData.attachments.length;
            let notificationMessage = `Kampanye "${formData.name}" berhasil dibuat dan akan dikirim ke ${recipientCount} kontak! 🎉`;
            
            if (attachmentCount > 0) {
                notificationMessage += `\nDengan ${attachmentCount} attachment yang akan dikirim bersama pesan.`;
            }
            
            showNotification(notificationMessage, 'success');
            
            // Clear saved data
            localStorage.removeItem('campaign_draft_message');
            localStorage.removeItem('campaign_attachments');
            
        setTimeout(() => {
                console.log('Redirecting to campaigns page...'); // Debug log
                window.location.href = '/campaigns';
        }, 2000);
        } else {
            showNotification(data.message || 'Gagal mengirim kampanye.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        hideLoading();
        showNotification('Terjadi kesalahan saat mengirim kampanye: ' + error.message, 'error');
    });
}

// Helper function to extract phone numbers from recipients
async function extractPhoneNumbers(recipients) {
    console.log('extractPhoneNumbers called with recipients:', recipients);
    
    try {
        const response = await fetch('/api/campaigns/phone-numbers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ recipients: recipients })
        });
        
        const data = await response.json();
        console.log('API response:', data);
        
        if (data.success) {
            console.log('Phone numbers from API:', data.phone_numbers);
            console.log('Debug info:', data.debug);
            
            // If no phone numbers found, use fallback
            if (data.phone_numbers.length === 0) {
                console.log('No phone numbers found, using fallback numbers');
                const fallbackNumbers = [];
                recipients.forEach(recipient => {
                    for (let i = 0; i < recipient.count; i++) {
                        fallbackNumbers.push(`62812345678${i.toString().padStart(2, '0')}`);
                    }
                });
                return fallbackNumbers;
            }
            
            return data.phone_numbers;
        } else {
            console.error('Failed to get phone numbers:', data.message);
            return [];
        }
    } catch (error) {
        console.error('Error getting phone numbers:', error);
        // Fallback to placeholder numbers
        const phoneNumbers = [];
        recipients.forEach(recipient => {
            for (let i = 0; i < recipient.count; i++) {
                phoneNumbers.push(`62812345678${i.toString().padStart(2, '0')}`);
            }
        });
        return phoneNumbers;
    }
}

// Helper function to get active session ID
function getActiveSessionId() {
    // This should return the active WhatsApp session ID
    // For now, return the first connected session or a placeholder
    const activeSessions = @json($sessions ?? []);
    const connectedSession = activeSessions.find(session => session.status === 'connected');
    return connectedSession ? connectedSession.id : 1;
}

// Debug function to check available contacts - REMOVED
// async function checkAvailableContacts() {
//     try {
//         const response = await fetch('/api/campaigns/check-contacts');
//         const data = await response.json();
//         
//         if (data.success) {
//             console.log('Available contacts data:', data.data);
//             
//             // Show notification with contact info
//             const contactInfo = `
//                 📊 Data Kontak Tersedia:
//                 • Contacts Table: ${data.data.contacts_table.individual_count} individual, ${data.data.contacts_table.group_count} groups
//                 • Phonebook Table: ${data.data.phonebook_table.individual_count} individual, ${data.data.phonebook_table.group_count} groups
//             `;
//             
//             showNotification(contactInfo, 'info', 8000);
//             
//             // Log sample data
//             if (data.data.contacts_table.individual_samples.length > 0) {
//                 console.log('Sample individual contacts:', data.data.contacts_table.individual_samples);
//             }
//             if (data.data.phonebook_table.individual_samples.length > 0) {
//                 console.log('Sample phonebook contacts:', data.data.phonebook_table.individual_samples);
//             }
//             
//             return data.data;
//         } else {
//             console.error('Failed to check contacts:', data.message);
//             return null;
//         }
//     } catch (error) {
//         console.error('Error checking contacts:', error);
//         return null;
//     }
// }

function updateProgress(percentage) {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        const progressText = loadingOverlay.querySelector('span');
        const progressFill = loadingOverlay.querySelector('.progress-fill');
        
        if (progressText) {
            progressText.textContent = `Mengirim kampanye... ${percentage}%`;
        }
        
        if (progressFill) {
            progressFill.style.width = `${percentage}%`;
            progressFill.style.transition = 'width 0.3s ease-in-out';
        }
        
        // Add visual feedback for different progress stages
        if (percentage >= 100) {
            progressFill.style.backgroundColor = '#10b981'; // Green for completion
        } else if (percentage >= 75) {
            progressFill.style.backgroundColor = '#3b82f6'; // Blue for high progress
        } else if (percentage >= 50) {
            progressFill.style.backgroundColor = '#f59e0b'; // Yellow for medium progress
        } else {
            progressFill.style.backgroundColor = '#ef4444'; // Red for low progress
        }
    }
}

function getAttachments() {
    console.log('getAttachments() called'); // Debug log
    
    const filePreview = document.getElementById('filePreview');
    const attachments = [];
    
    if (filePreview && !filePreview.classList.contains('hidden')) {
        const fileItems = filePreview.querySelectorAll('.file-preview-item');
        console.log('getAttachments - found fileItems in DOM:', fileItems.length); // Debug log
        
        fileItems.forEach((item, index) => {
            const fileName = item.querySelector('p').textContent;
            console.log(`getAttachments - file ${index + 1} from DOM:`, fileName); // Debug log
            attachments.push(fileName);
        });
    }
    
    // Get attachments from localStorage (this contains the actual file data)
    const savedFiles = JSON.parse(localStorage.getItem('campaign_attachments') || '[]');
    console.log('getAttachments - savedFiles from localStorage:', savedFiles.length); // Debug log
    
    return savedFiles;
}

// Enhanced loading function
function showLoading(message) {
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.className = 'loading-overlay';
    overlay.innerHTML = `
        <div class="bg-white rounded-xl p-8 flex flex-col items-center space-y-4 shadow-2xl">
            <div class="loading-spinner"></div>
            <span class="text-gray-700 font-medium">${message}</span>
            <div class="progress-bar w-64">
                <div class="progress-fill" style="width: 0%"></div>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

// Enhanced success and error functions
function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showWarning(message) {
    showNotification(message, 'warning');
}

function showInfo(message) {
    showNotification(message, 'info');
}

function setupApiNumbers() {
    // Function removed because "Show API Numbers" button was deleted
    // If you need this functionality back, uncomment the code below:
    /*
    const showApiNumbersBtn = document.getElementById('showApiNumbersBtn');
    if (showApiNumbersBtn) {
        showApiNumbersBtn.addEventListener('click', () => {
            showApiNumbersModal();
        });
    }
    */
}

function showApiNumbersModal() {
    const modal = document.createElement('div');
    modal.id = 'apiNumbersModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">API Numbers</h3>
                <button onclick="closeApiNumbersModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-medium text-gray-900">6285242766676</div>
                        <div class="text-sm text-gray-500">Status: Connected</div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">ON</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-medium text-gray-900">6281234567890</div>
                        <div class="text-sm text-gray-500">Status: Disconnected</div>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">OFF</span>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeApiNumbersModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    Tutup
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeApiNumbersModal();
        }
    });
}

function closeApiNumbersModal() {
    const modal = document.getElementById('apiNumbersModal');
    if (modal) {
        modal.remove();
    }
}

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Auto-save functionality
function autoSaveMessage() {
    const message = document.getElementById('messageContent').value;
    localStorage.setItem('campaign_draft_message', message);
    showAutoSaveNotification();
}

function showAutoSaveNotification() {
    const status = document.getElementById('autoSaveStatus');
    status.textContent = 'Saved';
    status.className = 'text-green-600';
    setTimeout(() => {
        status.textContent = 'ON';
    }, 2000);
}

// Content template functionality
function setupContentTemplates() {
    document.getElementById('addContentBtn').addEventListener('click', addNewContent);
    
    // Load saved content on page load
    loadSavedContent();
}

function loadSavedContent() {
    const contents = JSON.parse(localStorage.getItem('content_templates') || '{}');
    
    // Update existing content items with saved data
    Object.keys(contents).forEach(contentId => {
        const contentItem = document.querySelector(`[data-content-id="${contentId}"]`);
        if (contentItem && contents[contentId]) {
            // Update content name
            const nameElement = contentItem.querySelector('h4');
            if (nameElement) {
                nameElement.textContent = contents[contentId].name || nameElement.textContent;
            }
            
            // Update preview text
            const previewElement = contentItem.querySelector('p');
            if (previewElement && contents[contentId].text) {
                const previewText = contents[contentId].text.length > 50 ? 
                    contents[contentId].text.substring(0, 50) + '...' : 
                    contents[contentId].text;
                previewElement.textContent = previewText;
            }
        }
    });
}

function addNewContent() {
    const contentList = document.getElementById('contentList');
    const contentId = 'content-' + Date.now();
    const contentNumber = contentList.children.length + 1;
    
    const newContent = document.createElement('div');
    newContent.className = 'content-item bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md';
    newContent.setAttribute('data-content-id', contentId);
    newContent.innerHTML = `
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-medium text-gray-900">Content ${contentNumber}</h4>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                <button type="button" class="text-gray-400 hover:text-blue-500 transition-colors duration-200" onclick="editContent('${contentId}')" title="Edit Content">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </button>
                <button type="button" class="text-gray-400 hover:text-red-500 transition-colors duration-200" onclick="deleteContent('${contentId}')" title="Delete Content">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <p class="text-sm text-gray-600 line-clamp-2">Klik untuk mengedit konten...</p>
    `;
    
    contentList.appendChild(newContent);
    
    // Initialize empty content in localStorage
    const contents = JSON.parse(localStorage.getItem('content_templates') || '{}');
    contents[contentId] = {
        name: `Content ${contentNumber}`,
        text: '',
        createdAt: new Date().toISOString()
    };
    localStorage.setItem('content_templates', JSON.stringify(contents));
    
    showSuccess('Content template berhasil ditambahkan! Silakan klik tombol edit untuk mengisi konten.');
}

function deleteContent(contentId) {
    if (confirm('Apakah Anda yakin ingin menghapus content ini?')) {
        const contentItem = document.querySelector(`[data-content-id="${contentId}"]`);
        if (contentItem) {
            // Remove from localStorage
            const contents = JSON.parse(localStorage.getItem('content_templates') || '{}');
            delete contents[contentId];
            localStorage.setItem('content_templates', JSON.stringify(contents));
            
            // Remove from DOM
            contentItem.remove();
            showSuccess('Content berhasil dihapus!');
        }
    }
}

function editContent(contentId) {
    const contentItem = document.querySelector(`[data-content-id="${contentId}"]`);
    if (!contentItem) return;
    
    // Get current content from localStorage or use default
    const contents = JSON.parse(localStorage.getItem('content_templates') || '{}');
    let currentContent = '';
    let currentName = contentItem.querySelector('h4').textContent;
    
    if (contents[contentId] && contents[contentId].text) {
        currentContent = contents[contentId].text;
        currentName = contents[contentId].name || currentName;
    } else {
        // Fallback to default content
        const defaultContents = {
            'content-a': 'Halo {name}, selamat datang di layanan kami! 🎉\n\nKami senang dapat membantu Anda dengan kebutuhan bisnis Anda.\n\nTerima kasih telah memilih layanan kami! 🙏',
            'content-b': 'Terima kasih telah menggunakan layanan kami! 🙏\n\nKami berharap layanan kami memenuhi ekspektasi Anda.\n\nJangan ragu untuk menghubungi kami jika ada pertanyaan! 📞'
        };
        currentContent = defaultContents[contentId] || 'Klik untuk mengedit konten...';
    }
    
    // Create modal for editing
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Content Template</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="closeEditModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Content Name</label>
                <input type="text" id="contentNameInput" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="${currentName}">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Content Text</label>
                <textarea id="contentTextInput" rows="8" class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="Tulis konten template Anda di sini...">${currentContent}</textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200" onclick="closeEditModal()">Cancel</button>
                <button type="button" id="saveContentBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center" onclick="saveContentEdit('${contentId}')">
                    <span id="saveBtnText">Save Changes</span>
                    <svg id="saveBtnIcon" class="w-4 h-4 ml-2 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add modal-open class to body
    document.body.classList.add('modal-open');
    
    // Focus on the name input
    setTimeout(() => {
        document.getElementById('contentNameInput').focus();
    }, 100);
    
    // Add Enter key support for saving
    const nameInput = document.getElementById('contentNameInput');
    const textInput = document.getElementById('contentTextInput');
    
    if (nameInput) {
        nameInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                textInput.focus();
            }
        });
    }
    
    if (textInput) {
        textInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                saveContentEdit(contentId);
            }
        });
    }
    
    // Add Escape key support to close modal
    const handleEscape = function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
    
    // Add click outside modal to close
    const handleClickOutside = function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeEditModal();
            document.removeEventListener('click', handleClickOutside);
        }
    };
    document.addEventListener('click', handleClickOutside);
}

function closeEditModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
    }
}

function saveContentEdit(contentId) {
    const contentNameInput = document.getElementById('contentNameInput');
    const contentTextInput = document.getElementById('contentTextInput');
    const saveBtn = document.getElementById('saveContentBtn');
    const saveBtnText = document.getElementById('saveBtnText');
    const saveBtnIcon = document.getElementById('saveBtnIcon');
    
    if (!contentNameInput || !contentTextInput) {
        showError('Form elements not found');
        return;
    }
    
    const contentName = contentNameInput.value.trim();
    const contentText = contentTextInput.value.trim();
    
    if (!contentName) {
        showError('Nama konten harus diisi');
        contentNameInput.focus();
        return;
    }
    
    if (!contentText) {
        showError('Konten template harus diisi');
        contentTextInput.focus();
        return;
    }
    
    // Show loading state
    if (saveBtn && saveBtnText && saveBtnIcon) {
        saveBtn.disabled = true;
        saveBtnText.textContent = 'Saving...';
        saveBtnIcon.classList.remove('hidden');
        saveBtnIcon.classList.add('animate-spin');
    }
    
    const contentItem = document.querySelector(`[data-content-id="${contentId}"]`);
    if (contentItem) {
        try {
            // Update content name
            const nameElement = contentItem.querySelector('h4');
            if (nameElement) {
                nameElement.textContent = contentName;
            }
            
            // Update content preview text
            const previewElement = contentItem.querySelector('p');
            if (previewElement) {
                const previewText = contentText.length > 50 ? contentText.substring(0, 50) + '...' : contentText;
                previewElement.textContent = previewText;
            }
            
            // Store content in localStorage for persistence
            const contents = JSON.parse(localStorage.getItem('content_templates') || '{}');
            contents[contentId] = {
                name: contentName,
                text: contentText,
                updatedAt: new Date().toISOString()
            };
            localStorage.setItem('content_templates', JSON.stringify(contents));
            
            // Update textarea with new content if this content is currently active
            const activeContent = document.querySelector('.content-item.bg-gradient-to-r');
            if (activeContent && activeContent.getAttribute('data-content-id') === contentId) {
                document.getElementById('messageContent').value = contentText;
                updateCharCount();
            }
            
            // Close modal
            closeEditModal();
            
            showSuccess(`Template "${contentName}" berhasil disimpan!`);
            
        } catch (error) {
            console.error('Error saving content:', error);
            showError('Terjadi kesalahan saat menyimpan konten');
            
            // Reset loading state on error
            if (saveBtn && saveBtnText && saveBtnIcon) {
                saveBtn.disabled = false;
                saveBtnText.textContent = 'Save Changes';
                saveBtnIcon.classList.add('hidden');
                saveBtnIcon.classList.remove('animate-spin');
            }
        }
    } else {
        showError('Content template tidak ditemukan');
        
        // Reset loading state on error
        if (saveBtn && saveBtnText && saveBtnIcon) {
            saveBtn.disabled = false;
            saveBtnText.textContent = 'Save Changes';
            saveBtnIcon.classList.add('hidden');
            saveBtnIcon.classList.remove('animate-spin');
        }
    }
}

function setupContentSelection() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.content-item')) {
            const contentItem = e.target.closest('.content-item');
            const contentId = contentItem.getAttribute('data-content-id');
            
            // Remove active class from all content items
            document.querySelectorAll('.content-item').forEach(item => {
                item.classList.remove('bg-gradient-to-r', 'from-green-50', 'to-blue-50', 'border-green-200');
                item.classList.add('bg-gray-50', 'border-gray-200');
                const status = item.querySelector('span');
                if (status) {
                    status.textContent = 'Inactive';
                    status.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
                }
            });
            
            // Add active class to selected content item
            contentItem.classList.remove('bg-gray-50', 'border-gray-200');
            contentItem.classList.add('bg-gradient-to-r', 'from-green-50', 'to-blue-50', 'border-green-200');
            const status = contentItem.querySelector('span');
            if (status) {
                status.textContent = 'Active';
                status.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
            }
            
            // Load content into textarea
            loadContentToTextarea(contentId);
        }
    });
}

function loadContentToTextarea(contentId) {
    const textarea = document.getElementById('messageContent');
    
    // Try to get content from localStorage first
    const contents = JSON.parse(localStorage.getItem('content_templates') || '{}');
    
    if (contents[contentId] && contents[contentId].text) {
        textarea.value = contents[contentId].text;
    } else {
        // Fallback to default content
        const defaultContents = {
            'content-a': 'Halo {name}, selamat datang di layanan kami! 🎉\n\nKami senang dapat membantu Anda dengan kebutuhan bisnis Anda.\n\nTerima kasih telah memilih layanan kami! 🙏',
            'content-b': 'Terima kasih telah menggunakan layanan kami! 🙏\n\nKami berharap layanan kami memenuhi ekspektasi Anda.\n\nJangan ragu untuk menghubungi kami jika ada pertanyaan! 📞'
        };
        textarea.value = defaultContents[contentId] || 'Klik untuk mengedit konten...';
    }
    
    updateCharCount();
}



// Quick actions
function setupQuickActions() {
    document.getElementById('saveTemplateBtn').addEventListener('click', saveTemplate);
    document.getElementById('loadTemplateBtn').addEventListener('click', loadTemplate);
}

function saveTemplate() {
    const message = document.getElementById('messageContent').value;
    
    if (!message.trim()) {
        showError('Pesan tidak boleh kosong');
        return;
    }
    
    // Create modal for saving template
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Simpan Template</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="closeSaveTemplateModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Template</label>
                <input type="text" id="templateNameInput" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Masukkan nama template..." maxlength="50">
                <p class="text-xs text-gray-500 mt-1">Maksimal 50 karakter</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select id="templateCategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="general">Umum</option>
                    <option value="marketing">Marketing</option>
                    <option value="customer-service">Customer Service</option>
                    <option value="promotion">Promosi</option>
                    <option value="notification">Notifikasi</option>
                    <option value="custom">Kustom</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Preview Pesan</label>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 max-h-32 overflow-y-auto">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">${message.substring(0, 200)}${message.length > 200 ? '...' : ''}</p>
                </div>
                <p class="text-xs text-gray-500 mt-1">${message.length} karakter</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200" onclick="closeSaveTemplateModal()">Cancel</button>
                <button type="button" id="saveTemplateBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center" onclick="confirmSaveTemplate()">
                    <span id="saveTemplateBtnText">Simpan Template</span>
                    <svg id="saveTemplateBtnIcon" class="w-4 h-4 ml-2 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add modal-open class to body
    document.body.classList.add('modal-open');
    
    // Focus on the name input
    setTimeout(() => {
        document.getElementById('templateNameInput').focus();
    }, 100);
    
    // Add Enter key support
    const nameInput = document.getElementById('templateNameInput');
    if (nameInput) {
        nameInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmSaveTemplate();
            }
        });
    }
    
    // Add Escape key support to close modal
    const handleEscape = function(e) {
        if (e.key === 'Escape') {
            closeSaveTemplateModal();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
    
    // Add click outside modal to close
    const handleClickOutside = function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeSaveTemplateModal();
            document.removeEventListener('click', handleClickOutside);
        }
    };
    document.addEventListener('click', handleClickOutside);
}

function closeSaveTemplateModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
    }
}

function confirmSaveTemplate() {
    const templateName = document.getElementById('templateNameInput').value.trim();
    const templateCategory = document.getElementById('templateCategory').value;
    const message = document.getElementById('messageContent').value;
    const saveBtn = document.getElementById('saveTemplateBtn');
    const saveBtnText = document.getElementById('saveTemplateBtnText');
    const saveBtnIcon = document.getElementById('saveTemplateBtnIcon');
    
    if (!templateName) {
        showError('Nama template harus diisi');
        document.getElementById('templateNameInput').focus();
        return;
    }
    
    if (templateName.length > 50) {
        showError('Nama template maksimal 50 karakter');
        document.getElementById('templateNameInput').focus();
        return;
    }
    
    // Check if template name already exists
    const templates = JSON.parse(localStorage.getItem('message_templates') || '{}');
    if (templates[templateName]) {
        if (!confirm(`Template "${templateName}" sudah ada. Apakah Anda ingin menimpanya?`)) {
            return;
        }
    }
    
    // Show loading state
    if (saveBtn && saveBtnText && saveBtnIcon) {
        saveBtn.disabled = true;
        saveBtnText.textContent = 'Menyimpan...';
        saveBtnIcon.classList.remove('hidden');
        saveBtnIcon.classList.add('animate-spin');
    }
    
    try {
        // Save template with additional metadata
        templates[templateName] = {
            content: message,
            category: templateCategory,
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString()
        };
        localStorage.setItem('message_templates', JSON.stringify(templates));
        
        // Close modal
        closeSaveTemplateModal();
        
        showSuccess(`Template "${templateName}" berhasil disimpan!`);
        
    } catch (error) {
        console.error('Error saving template:', error);
        showError('Terjadi kesalahan saat menyimpan template');
        
        // Reset loading state on error
        if (saveBtn && saveBtnText && saveBtnIcon) {
            saveBtn.disabled = false;
            saveBtnText.textContent = 'Simpan Template';
            saveBtnIcon.classList.add('hidden');
            saveBtnIcon.classList.remove('animate-spin');
        }
    }
}

function loadTemplate() {
    const templates = JSON.parse(localStorage.getItem('message_templates') || '{}');
    const templateNames = Object.keys(templates);
    
    if (templateNames.length === 0) {
        showError('Tidak ada template yang tersimpan');
        return;
    }
    
    // Create modal for loading template
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    
    // Group templates by category
    const templatesByCategory = {};
    templateNames.forEach(name => {
        const template = templates[name];
        const category = template.category || 'general';
        if (!templatesByCategory[category]) {
            templatesByCategory[category] = [];
        }
        templatesByCategory[category].push({ name, ...template });
    });
    
    // Create category options
    const categoryOptions = Object.keys(templatesByCategory).map(category => {
        const categoryNames = {
            'general': 'Umum',
            'marketing': 'Marketing',
            'customer-service': 'Customer Service',
            'promotion': 'Promosi',
            'notification': 'Notifikasi',
            'custom': 'Kustom'
        };
        return `<option value="${category}">${categoryNames[category] || category} (${templatesByCategory[category].length})</option>`;
    }).join('');
    
    modal.innerHTML = `
        <div class="modal-content max-w-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Pilih Template</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="closeLoadTemplateModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kategori</label>
                <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="filterTemplates()">
                    <option value="all">Semua Kategori</option>
                    ${categoryOptions}
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Template Tersedia</label>
                <div id="templateList" class="max-h-64 overflow-y-auto space-y-2">
                    ${templateNames.map(name => {
                        const template = templates[name];
                        
                        // Handle both old format (string) and new format (object)
                        let category = 'general';
                        let content = '';
                        let date = '';
                        
                        if (typeof template === 'string') {
                            content = template;
                            category = 'general';
                        } else if (template) {
                            content = template.content || '';
                            category = template.category || 'general';
                            date = template.createdAt ? new Date(template.createdAt).toLocaleDateString('id-ID') : '';
                        }
                        
                        const categoryNames = {
                            'general': 'Umum',
                            'marketing': 'Marketing',
                            'customer-service': 'Customer Service',
                            'promotion': 'Promosi',
                            'notification': 'Notifikasi',
                            'custom': 'Kustom'
                        };
                        
                        const preview = content ? content.substring(0, 100) + (content.length > 100 ? '...' : '') : '';
                        
                        return `
                            <div class="template-item border border-gray-200 rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition-colors duration-200" data-category="${category}" onclick="selectTemplate('${name}')">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900">${name}</h4>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${categoryNames[category] || category}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">${preview}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>${content.length} karakter</span>
                                    <span>${date || 'Template lama'}</span>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200" onclick="closeLoadTemplateModal()">Cancel</button>
                <button type="button" id="deleteTemplateBtn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 hidden" onclick="deleteSelectedTemplate()">
                    Hapus Template
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add modal-open class to body
    document.body.classList.add('modal-open');
    
    // Add Escape key support to close modal
    const handleEscape = function(e) {
        if (e.key === 'Escape') {
            closeLoadTemplateModal();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
    
    // Add click outside modal to close
    const handleClickOutside = function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeLoadTemplateModal();
            document.removeEventListener('click', handleClickOutside);
        }
    };
    document.addEventListener('click', handleClickOutside);
}

function closeLoadTemplateModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
    }
}

function filterTemplates() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const templateItems = document.querySelectorAll('.template-item');
    
    templateItems.forEach(item => {
        const category = item.getAttribute('data-category');
        if (categoryFilter === 'all' || category === categoryFilter) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function selectTemplate(templateName) {
    const templates = JSON.parse(localStorage.getItem('message_templates') || '{}');
    const template = templates[templateName];
    
    let content = '';
    
    // Handle both old format (string) and new format (object)
    if (typeof template === 'string') {
        content = template;
    } else if (template && template.content) {
        content = template.content;
    } else {
        showError('Template tidak valid');
        return;
    }
    
    document.getElementById('messageContent').value = content;
    updateCharCount();
    closeLoadTemplateModal();
    showSuccess(`Template "${templateName}" berhasil dimuat!`);
}

function deleteSelectedTemplate() {
    // This function can be implemented later for template deletion
    showError('Fitur hapus template belum tersedia');
}

// Enhanced file upload functionality
function setupEnhancedFileUpload() {
    const fileUploadBtn = document.getElementById('fileUploadBtn');
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.multiple = true;
    fileInput.accept = 'image/*,.pdf,.doc,.docx,.txt';
    fileInput.style.display = 'none';
    document.body.appendChild(fileInput);
    
    fileUploadBtn.addEventListener('click', () => fileInput.click());
    
    // Drag and drop functionality
    const attachmentArea = document.getElementById('attachmentArea');
    
    attachmentArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        attachmentArea.classList.add('border-blue-300', 'bg-blue-50');
    });
    
    attachmentArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        attachmentArea.classList.remove('border-blue-300', 'bg-blue-50');
    });
    
    attachmentArea.addEventListener('drop', (e) => {
        e.preventDefault();
        attachmentArea.classList.remove('border-blue-300', 'bg-blue-50');
        const files = e.dataTransfer.files;
        handleFiles(files);
    });
    
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
    
    // Clear attachments
    document.getElementById('clearAttachmentBtn').addEventListener('click', clearAttachments);
}

function handleFiles(files) {
    console.log('handleFiles() called with files:', files.length); // Debug log
    
    const filePreview = document.getElementById('filePreview');
    const clearBtn = document.getElementById('clearAttachmentBtn');
    
    Array.from(files).forEach((file, index) => {
        console.log(`handleFiles - processing file ${index + 1}:`, file.name, file.type); // Debug log
        
        if (validateFile(file)) {
            console.log(`handleFiles - file ${index + 1} validated successfully`); // Debug log
            
            const fileItem = createFilePreview(file);
            filePreview.appendChild(fileItem);
            
            // Save file to localStorage for preview in step 3
            saveFileToStorage(file);
        } else {
            console.log(`handleFiles - file ${index + 1} validation failed`); // Debug log
        }
    });
    
    if (filePreview.children.length > 0) {
        filePreview.classList.remove('hidden');
        clearBtn.classList.remove('hidden');
        console.log('handleFiles - file preview area shown'); // Debug log
    }
}

function saveFileToStorage(file) {
    console.log('saveFileToStorage() called with file:', file.name, file.type, file.size); // Debug log
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const fileData = {
            name: file.name,
            type: file.type,
            size: file.size,
            data: e.target.result
        };
        
        console.log('saveFileToStorage - fileData created:', fileData.name, fileData.type); // Debug log
        
        // Get existing files from localStorage
        let savedFiles = JSON.parse(localStorage.getItem('campaign_attachments') || '[]');
        console.log('saveFileToStorage - existing files in localStorage:', savedFiles.length); // Debug log
        
        // Add new file
        savedFiles.push(fileData);
        
        // Save back to localStorage
        localStorage.setItem('campaign_attachments', JSON.stringify(savedFiles));
        console.log('saveFileToStorage - file saved to localStorage, total files:', savedFiles.length); // Debug log
        
        // Show notification
        showNotification(`File "${file.name}" berhasil disimpan untuk preview`, 'success', 2000);
    };
    reader.readAsDataURL(file);
}

function validateFile(file) {
    console.log('validateFile() called for:', file.name, file.type, file.size); // Debug log
    
    const maxImageSize = 1024 * 1024; // 1MB
    const maxDocSize = 5 * 1024 * 1024; // 5MB
    
    if (file.type.startsWith('image/') && file.size > maxImageSize) {
        console.log('validateFile - image file too large'); // Debug log
        showError(`File gambar ${file.name} terlalu besar. Maksimal 1MB.`);
        return false;
    }
    
    if (!file.type.startsWith('image/') && file.size > maxDocSize) {
        console.log('validateFile - document file too large'); // Debug log
        showError(`File ${file.name} terlalu besar. Maksimal 5MB.`);
        return false;
    }
    
    console.log('validateFile - file validation passed'); // Debug log
    return true;
}

function createFilePreview(file) {
    const fileItem = document.createElement('div');
    fileItem.className = 'file-preview-item flex items-center justify-between p-3 bg-gray-50 rounded-lg';
    
    const fileIcon = file.type.startsWith('image/') ? '🖼️' : '📄';
    
    fileItem.innerHTML = `
        <div class="flex items-center space-x-3">
            <span class="text-2xl">${fileIcon}</span>
            <div>
                <p class="text-sm font-medium text-gray-900">${file.name}</p>
                <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
            </div>
        </div>
        <button type="button" class="text-red-500 hover:text-red-700 transition-colors duration-200" onclick="removeFile(this)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    return fileItem;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeFile(button) {
    const fileItem = button.closest('.file-preview-item');
    const fileName = fileItem.querySelector('p').textContent;
    
    console.log('removeFile() called for:', fileName); // Debug log
    
    // Remove from localStorage
    removeFileFromStorage(fileName);
    
    fileItem.remove();
    const filePreview = document.getElementById('filePreview');
    if (filePreview.children.length === 0) {
        filePreview.classList.add('hidden');
        document.getElementById('clearAttachmentBtn').classList.add('hidden');
    }
}

function removeFileFromStorage(fileName) {
    console.log('removeFileFromStorage() called for:', fileName); // Debug log
    
    let savedFiles = JSON.parse(localStorage.getItem('campaign_attachments') || '[]');
    console.log('removeFileFromStorage - files before removal:', savedFiles.length); // Debug log
    
    savedFiles = savedFiles.filter(file => file.name !== fileName);
    console.log('removeFileFromStorage - files after removal:', savedFiles.length); // Debug log
    
    localStorage.setItem('campaign_attachments', JSON.stringify(savedFiles));
}

function loadSavedAttachments() {
    console.log('loadSavedAttachments() called'); // Debug log
    
    const savedFiles = JSON.parse(localStorage.getItem('campaign_attachments') || '[]');
    console.log('loadSavedAttachments - savedFiles from localStorage:', savedFiles.length); // Debug log
    
    const filePreview = document.getElementById('filePreview');
    const clearBtn = document.getElementById('clearAttachmentBtn');
    
    if (savedFiles.length > 0) {
        console.log('loadSavedAttachments - loading files to preview'); // Debug log
        savedFiles.forEach((fileData, index) => {
            console.log(`loadSavedAttachments - loading file ${index + 1}:`, fileData.name); // Debug log
            
            // Create a mock file object for display
            const mockFile = {
                name: fileData.name,
                type: fileData.type,
                size: fileData.size
            };
            
            const fileItem = createFilePreview(mockFile);
            filePreview.appendChild(fileItem);
        });
        
        filePreview.classList.remove('hidden');
        clearBtn.classList.remove('hidden');
        console.log('loadSavedAttachments - files loaded successfully'); // Debug log
    } else {
        console.log('loadSavedAttachments - no files to load'); // Debug log
    }
}

function loadSavedDraft() {
    const drafts = JSON.parse(localStorage.getItem('campaign_drafts') || '[]');
    if (drafts.length > 0) {
        const latestDraft = drafts[drafts.length - 1];
        
        // Load campaign name
        const broadcastNameInput = document.getElementById('broadcastName');
        if (broadcastNameInput && latestDraft.name) {
            broadcastNameInput.value = latestDraft.name;
        }
        
        // Load message content
        const messageContentInput = document.getElementById('messageContent');
        if (messageContentInput && latestDraft.message) {
            messageContentInput.value = latestDraft.message;
            updateCharCount();
            updatePreview();
        }
        
        // Load attachments if any
        if (latestDraft.attachments && latestDraft.attachments.length > 0) {
            localStorage.setItem('campaign_attachments', JSON.stringify(latestDraft.attachments));
            loadSavedAttachments();
        }
        
        // Load settings if any
        if (latestDraft.settings) {
            loadCampaignSettingsFromData(latestDraft.settings);
        }
    }
}

function loadCampaignSettingsFromData(settings) {
    // Load toggle settings
    if (settings.unsubscribe !== undefined) {
        const unsubscribeToggle = document.getElementById('unsubscribeToggle');
        if (unsubscribeToggle) {
            unsubscribeToggle.checked = settings.unsubscribe;
            updateToggleUI(unsubscribeToggle);
        }
    }
    
    if (settings.timing !== undefined) {
        const timingToggle = document.getElementById('timingToggle');
        if (timingToggle) {
            timingToggle.checked = settings.timing;
            updateToggleUI(timingToggle);
        }
    }
    
    // Load sending speed
    if (settings.sendingSpeed) {
        const speedRadios = document.querySelectorAll('input[name="sendingSpeed"]');
        speedRadios.forEach(radio => {
            if (radio.value === settings.sendingSpeed) {
                radio.checked = true;
            }
        });
        updateSendingSpeedUI(true);
    }
    
    // Load time to send
    if (settings.timeToSend) {
        const timeSelect = document.getElementById('timeToSendSelect');
        if (timeSelect) {
            timeSelect.value = settings.timeToSend;
            updateTimeToSendUI(true);
        }
    }
}

function setupAutoSaveAttachments() {
    console.log('setupAutoSaveAttachments() called'); // Debug log
    
    // Monitor file preview area for changes
    const filePreview = document.getElementById('filePreview');
    if (filePreview) {
        const observer = new MutationObserver(() => {
            console.log('setupAutoSaveAttachments - mutation observed'); // Debug log
            
            // Auto-save current attachments to localStorage
            const fileItems = filePreview.querySelectorAll('.file-preview-item');
            console.log('setupAutoSaveAttachments - found fileItems:', fileItems.length); // Debug log
            
            const currentFiles = [];
            
            fileItems.forEach((item, index) => {
                const fileName = item.querySelector('p').textContent;
                console.log(`setupAutoSaveAttachments - processing file ${index + 1}:`, fileName); // Debug log
                
                const savedFiles = JSON.parse(localStorage.getItem('campaign_attachments') || '[]');
                const existingFile = savedFiles.find(file => file.name === fileName);
                if (existingFile) {
                    console.log(`setupAutoSaveAttachments - found existing file:`, existingFile.name); // Debug log
                    currentFiles.push(existingFile);
                } else {
                    console.log(`setupAutoSaveAttachments - file not found in localStorage:`, fileName); // Debug log
                }
            });
            
            localStorage.setItem('campaign_attachments', JSON.stringify(currentFiles));
            
            // Update preview if we're on step 3
            if (currentStep === 3) {
                updatePreview();
            }
        });
        
        observer.observe(filePreview, { childList: true, subtree: true });
    }
}

function saveCurrentFilesToStorage() {
    console.log('saveCurrentFilesToStorage() called'); // Debug log
    
    const filePreview = document.getElementById('filePreview');
    if (filePreview && !filePreview.classList.contains('hidden')) {
        const fileItems = filePreview.querySelectorAll('.file-preview-item');
        console.log('saveCurrentFilesToStorage - found fileItems:', fileItems.length); // Debug log
        
        const currentFiles = [];
        
        fileItems.forEach((item, index) => {
            const fileName = item.querySelector('p').textContent;
            console.log(`saveCurrentFilesToStorage - processing file ${index + 1}:`, fileName); // Debug log
            
            const savedFiles = JSON.parse(localStorage.getItem('campaign_attachments') || '[]');
            const existingFile = savedFiles.find(file => file.name === fileName);
            if (existingFile) {
                console.log(`saveCurrentFilesToStorage - found existing file:`, existingFile.name); // Debug log
                currentFiles.push(existingFile);
            } else {
                console.log(`saveCurrentFilesToStorage - file not found in localStorage:`, fileName); // Debug log
            }
        });
        
        console.log('saveCurrentFilesToStorage - saving currentFiles to localStorage:', currentFiles.length); // Debug log
        localStorage.setItem('campaign_attachments', JSON.stringify(currentFiles));
    } else {
        console.log('saveCurrentFilesToStorage - filePreview not found or hidden'); // Debug log
    }
}

function clearAttachments() {
    console.log('clearAttachments() called'); // Debug log
    
    document.getElementById('filePreview').innerHTML = '';
    document.getElementById('filePreview').classList.add('hidden');
    document.getElementById('clearAttachmentBtn').classList.add('hidden');
    
    // Clear from localStorage
    localStorage.removeItem('campaign_attachments');
    console.log('clearAttachments - localStorage cleared'); // Debug log
}

function clearAllCampaignData() {
    // Show confirmation dialog
    if (!confirm('Apakah Anda yakin ingin membersihkan semua data campaign? Tindakan ini tidak dapat dibatalkan.')) {
        return;
    }
    
    // Clear all campaign-related data from localStorage
    localStorage.removeItem('campaign_draft_message');
    localStorage.removeItem('campaign_attachments');
    localStorage.removeItem('campaign_drafts');
    localStorage.removeItem('campaign_settings');
    
    // Clear form fields
    const broadcastNameInput = document.getElementById('broadcastName');
    if (broadcastNameInput) {
        broadcastNameInput.value = '';
    }
    
    const messageContentInput = document.getElementById('messageContent');
    if (messageContentInput) {
        messageContentInput.value = '';
        updateCharCount();
        updatePreview();
    }
    
    // Clear attachments display
    clearAttachments();
    
    // Reset to step 1
    currentStep = 1;
    showStep(1);
    updateStepIndicators();
    
    showNotification('Semua data campaign telah dibersihkan', 'info');
}

// Enhanced text formatting functionality
function setupEnhancedTextFormatting() {
    const textarea = document.getElementById('messageContent');
    
    // Bold button
    document.getElementById('boldBtn').addEventListener('click', () => {
        insertText('*', '*');
    });
    
    // Italic button
    document.getElementById('italicBtn').addEventListener('click', () => {
        insertText('_', '_');
    });
    
    // Strike button
    document.getElementById('strikeBtn').addEventListener('click', () => {
        insertText('~', '~');
    });
    
    // Link button
    document.getElementById('linkBtn').addEventListener('click', () => {
        const url = prompt('Masukkan URL:');
        if (url) {
            insertTextAtCursor(url);
        }
    });
    
    // Variable select
    document.getElementById('variableSelect').addEventListener('change', function() {
        if (this.value) {
            insertTextAtCursor(this.value);
            this.value = '';
        }
    });
    
    // Enhanced emoji picker
    document.getElementById('emojiBtn').addEventListener('click', () => {
        showEnhancedEmojiPicker();
    });
    
    function insertText(before, after) {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        const newText = before + selectedText + after;
        
        textarea.value = textarea.value.substring(0, start) + newText + textarea.value.substring(end);
        textarea.focus();
        textarea.setSelectionRange(start + before.length, end + before.length);
        updateCharCount();
    }
    
    function insertTextAtCursor(text) {
        const start = textarea.selectionStart;
        textarea.value = textarea.value.substring(0, start) + text + textarea.value.substring(start);
        textarea.focus();
        textarea.setSelectionRange(start + text.length, start + text.length);
        updateCharCount();
    }
}

function showEnhancedEmojiPicker() {
    const emojis = {
        'Smileys': ['😊', '😄', '😃', '😁', '😆', '😅', '😂', '🤣', '😉', '😋', '😎', '😍', '🥰', '😘', '😗', '😙', '😚'],
        'Gestures': ['👍', '👎', '👌', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '👋', '🤚', '🖐️'],
        'Hearts': ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘'],
        'Objects': ['📱', '📞', '📧', '💻', '🖥️', '⌨️', '🖱️', '🖨️', '📷', '📹', '🎥', '📺', '📻', '🎙️', '🎚️', '🎛️'],
        'Symbols': ['✨', '🌟', '💫', '⭐', '🔥', '💯', '💪', '🙏', '👏', '🎉', '🎊', '🎈', '🎂', '🎁', '🎄', '🎃', '🎗️']
    };
        
        const emojiPicker = document.createElement('div');
    emojiPicker.className = 'absolute bg-white border border-gray-300 rounded-xl p-4 shadow-xl z-50 max-w-sm';
        emojiPicker.style.top = '100%';
        emojiPicker.style.left = '0';
    
    let emojiHtml = '<div class="space-y-3">';
    Object.entries(emojis).forEach(([category, emojiList]) => {
        emojiHtml += `
            <div>
                <h4 class="text-xs font-semibold text-gray-700 mb-2">${category}</h4>
                <div class="grid grid-cols-8 gap-1">
                    ${emojiList.map(emoji => `<button class="emoji-btn p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 text-lg" onclick="insertEmoji('${emoji}')">${emoji}</button>`).join('')}
                </div>
            </div>
        `;
    });
    emojiHtml += '</div>';
    
    emojiPicker.innerHTML = emojiHtml;
        
        const emojiBtn = document.getElementById('emojiBtn');
        emojiBtn.parentNode.style.position = 'relative';
        emojiBtn.parentNode.appendChild(emojiPicker);
        
        // Close emoji picker when clicking outside
        document.addEventListener('click', function closeEmojiPicker(e) {
            if (!emojiPicker.contains(e.target) && e.target !== emojiBtn) {
                emojiPicker.remove();
                document.removeEventListener('click', closeEmojiPicker);
            }
        });
    }



function showModal(title, content) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            ${content}
            <div class="mt-6 flex justify-end">
                <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    Tutup
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Load saved content
function loadSavedContent() {
    const savedMessage = localStorage.getItem('campaign_draft_message');
    if (savedMessage) {
        document.getElementById('messageContent').value = savedMessage;
        updateCharCount();
    }
}

function getCampaignSettings() {
    return {
        unsubscribe: document.getElementById('unsubscribeToggle') ? document.getElementById('unsubscribeToggle').checked : true,
        timing: document.getElementById('timingToggle') ? document.getElementById('timingToggle').checked : true,
        sendingSpeed: document.querySelector('input[name="sendingSpeed"]:checked') ? document.querySelector('input[name="sendingSpeed"]:checked').value : 'auto',
        timeToSend: document.getElementById('timeToSendSelect') ? document.getElementById('timeToSendSelect').value : 'immediately'
    };
}

function saveCampaignSettings() {
    const settings = getCampaignSettings();
    localStorage.setItem('campaign_settings', JSON.stringify(settings));
}

function loadCampaignSettings() {
    const savedSettings = localStorage.getItem('campaign_settings');
    if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        
        // Apply settings to UI
        if (settings.unsubscribe !== undefined) {
            const unsubscribeToggle = document.getElementById('unsubscribeToggle');
            if (unsubscribeToggle) {
                unsubscribeToggle.checked = settings.unsubscribe;
                updateToggleUI(unsubscribeToggle);
            }
        }
        
        if (settings.timing !== undefined) {
            const timingToggle = document.getElementById('timingToggle');
            if (timingToggle) {
                timingToggle.checked = settings.timing;
                updateToggleUI(timingToggle);
            }
        }
        
        if (settings.sendingSpeed) {
            const radioButton = document.querySelector(`input[name="sendingSpeed"][value="${settings.sendingSpeed}"]`);
            if (radioButton) {
                radioButton.checked = true;
                updateSendingSpeedUI(false);
            }
        }
        
        if (settings.timeToSend) {
            const select = document.getElementById('timeToSendSelect');
            if (select) {
                select.value = settings.timeToSend;
                updateTimeToSendUI(false);
            }
        }
    }
}

function updateToggleUI(toggle) {
    const dot = toggle.parentElement.querySelector('.dot');
    const bg = toggle.parentElement.querySelector('.w-10.h-4');
    if (toggle.checked) {
        dot.style.transform = 'translateX(1.5rem)';
        bg.classList.remove('bg-gray-200', 'bg-red-500', 'bg-blue-500');
        bg.classList.add('bg-green-500');
    } else {
        dot.style.transform = 'translateX(0)';
        bg.classList.remove('bg-green-500', 'bg-blue-500', 'bg-gray-200');
        bg.classList.add('bg-red-500');
    }
}

function updateSendingSpeedUI(showModal = false) {
    const autoRadio = document.querySelector('input[name="sendingSpeed"][value="auto"]');
    const customRadio = document.querySelector('input[name="sendingSpeed"][value="custom"]');
    
    if (autoRadio && autoRadio.checked) {
        // Show auto speed info only if showModal is true
        if (showModal) {
            showNotification('Auto speed: Messages will be sent at optimal intervals', 'info');
        }
    } else if (customRadio && customRadio.checked) {
        // Show custom speed options only if showModal is true
        if (showModal) {
            showCustomSpeedModal();
        }
    }
}

function updateTimeToSendUI(showModal = false) {
    const select = document.getElementById('timeToSendSelect');
    const selectedValue = select.value;
    
    if (!showModal) return; // Don't show modals during initialization
    
    switch(selectedValue) {
        case 'immediately':
            showNotification('Kampanye akan dikirim segera', 'info');
            break;
        case 'schedule':
            showScheduleModal();
            break;
        case 'custom':
            showCustomTimeModal();
            break;
    }
}

function showCustomSpeedModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Custom Sending Speed</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="this.closest('.modal-overlay').remove()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Messages per minute</label>
                <input type="number" id="customSpeed" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="1" max="60" value="10">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delay between messages (seconds)</label>
                <input type="number" id="customDelay" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" min="1" max="300" value="6">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200" onclick="this.closest('.modal-overlay').remove()">Cancel</button>
                <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200" onclick="saveCustomSpeed()">Save</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.classList.add('modal-open');
}

function saveCustomSpeed() {
    const speed = document.getElementById('customSpeed').value;
    const delay = document.getElementById('customDelay').value;
    
    const settings = getCampaignSettings();
    settings.customSpeed = parseInt(speed);
    settings.customDelay = parseInt(delay);
    
    localStorage.setItem('campaign_settings', JSON.stringify(settings));
    
    document.querySelector('.modal-overlay').remove();
    document.body.classList.remove('modal-open');
    
    showSuccess(`Custom speed set: ${speed} messages/minute with ${delay}s delay`);
}

function showScheduleModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Jadwalkan Kampanye</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="this.closest('.modal-overlay').remove()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" id="scheduleDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu</label>
                <input type="time" id="scheduleTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200" onclick="this.closest('.modal-overlay').remove()">Batal</button>
                <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200" onclick="saveSchedule()">Jadwalkan</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.classList.add('modal-open');
    
    // Set default date and time
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('scheduleDate').value = tomorrow.toISOString().split('T')[0];
    document.getElementById('scheduleTime').value = '09:00';
}

function saveSchedule() {
    const date = document.getElementById('scheduleDate').value;
    const time = document.getElementById('scheduleTime').value;
    
    const settings = getCampaignSettings();
    settings.scheduledDate = date;
    settings.scheduledTime = time;
    
    localStorage.setItem('campaign_settings', JSON.stringify(settings));
    
    document.querySelector('.modal-overlay').remove();
    document.body.classList.remove('modal-open');
    
    showSuccess(`Kampanye dijadwalkan untuk ${date} pada ${time}`);
}

function showCustomTimeModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Waktu Kirim Kustom</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="this.closest('.modal-overlay').remove()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal & Waktu Kirim</label>
                <input type="datetime-local" id="customDateTime" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Zona Waktu</label>
                <select id="timezone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                    <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                    <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200" onclick="this.closest('.modal-overlay').remove()">Batal</button>
                <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200" onclick="saveCustomTime()">Atur Waktu</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    document.body.classList.add('modal-open');
    
    // Set default datetime (1 hour from now)
    const defaultTime = new Date();
    defaultTime.setHours(defaultTime.getHours() + 1);
    document.getElementById('customDateTime').value = defaultTime.toISOString().slice(0, 16);
}

function saveCustomTime() {
    const dateTime = document.getElementById('customDateTime').value;
    const timezone = document.getElementById('timezone').value;
    
    const settings = getCampaignSettings();
    settings.customDateTime = dateTime;
    settings.timezone = timezone;
    
    localStorage.setItem('campaign_settings', JSON.stringify(settings));
    
    document.querySelector('.modal-overlay').remove();
    document.body.classList.remove('modal-open');
    
    const formattedDate = new Date(dateTime).toLocaleString('id-ID');
    showSuccess(`Waktu kustom diatur: ${formattedDate}`);
}

function showSettingHelp(settingType) {
    const helpContent = {
        'allow unsubscribe': `
            <div class="space-y-4">
                <p class="text-sm text-gray-600">
                    Allow recipients to unsubscribe from future campaigns by replying with "STOP".
                </p>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm"><strong>Benefits:</strong></p>
                    <ul class="text-sm mt-2 space-y-1">
                        <li>• Compliance with anti-spam regulations</li>
                        <li>• Better engagement rates</li>
                        <li>• Reduced bounce rates</li>
                    </ul>
                </div>
            </div>
        `,
        'perfect timing': `
            <div class="space-y-4">
                <p class="text-sm text-gray-600">
                    Automatically send messages at the optimal time for maximum engagement.
                </p>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm"><strong>Optimal Times:</strong></p>
                    <ul class="text-sm mt-2 space-y-1">
                        <li>• Weekdays: 9:00 AM - 11:00 AM</li>
                        <li>• Lunch: 12:00 PM - 2:00 PM</li>
                        <li>• Evening: 7:00 PM - 9:00 PM</li>
                    </ul>
                </div>
            </div>
        `
    };
    
    const content = helpContent[settingType] || '<p class="text-sm text-gray-600">Help information not available.</p>';
    showModal(`${settingType.charAt(0).toUpperCase() + settingType.slice(1)} Help`, content);
}

function sendPreview() {
    const message = document.getElementById('messageContent').value;
    if (!message.trim()) {
        showError('Pesan tidak boleh kosong');
        return;
    }
    
    showSuccess('Preview berhasil dikirim! Cek WhatsApp Anda.');
}

function saveAsDraft() {
    const campaignData = {
        name: document.getElementById('broadcastName').value,
        message: document.getElementById('messageContent').value,
        settings: getCampaignSettings(),
        attachments: JSON.parse(localStorage.getItem('campaign_attachments') || '[]'),
        createdAt: new Date().toISOString()
    };
    
    const drafts = JSON.parse(localStorage.getItem('campaign_drafts') || '[]');
    drafts.push(campaignData);
    localStorage.setItem('campaign_drafts', JSON.stringify(drafts));
    
    showSuccess('Campaign berhasil disimpan sebagai draft!');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
    
    const colors = {
        info: 'bg-blue-500 text-white',
        success: 'bg-green-500 text-white',
        warning: 'bg-yellow-500 text-white',
        error: 'bg-red-500 text-white'
    };
    
    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span class="text-sm font-medium">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Function to send personalized messages to each contact
async function sendPersonalizedCampaign(campaignData) {
    const { message, recipients } = campaignData;
    
    // Get all selected recipients
    const selectedRecipients = getSelectedRecipients();
    
    // For each recipient group, get the contacts and personalize messages
    for (const recipient of selectedRecipients) {
        if (recipient.type === 'individual') {
            // Get individual contacts
            const contacts = await getIndividualContacts();
            await sendToContacts(contacts, message);
        } else if (recipient.type === 'group') {
            // Get group contacts
            const contacts = await getGroupContacts(recipient.group_id);
            await sendToContacts(contacts, message);
        }
    }
}

// Function to get individual contacts from database
async function getIndividualContacts() {
    try {
        const response = await fetch('/api/contacts/individual', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            return data.contacts || [];
        }
    } catch (error) {
        console.error('Error fetching individual contacts:', error);
    }
    
    return [];
}

// Function to get group contacts from database
async function getGroupContacts(groupId) {
    try {
        const response = await fetch(`/api/contacts/group/${groupId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            return data.contacts || [];
        }
    } catch (error) {
        console.error('Error fetching group contacts:', error);
    }
    
    return [];
}

// Function to send personalized messages to contacts
async function sendToContacts(contacts, messageTemplate) {
    for (const contact of contacts) {
        // Personalize message for each contact
        const personalizedMessage = personalizeMessage(messageTemplate, {
            name: contact.name || contact.full_name || 'User',
            phone: contact.phone || contact.phone_number || '',
            email: contact.email || '',
            group: contact.group_name || 'Grup',
            company: contact.company || 'Perusahaan',
            date: new Date().toLocaleDateString('id-ID')
        });
        
        // Send the personalized message
        await sendWhatsAppMessage(contact.phone, personalizedMessage);
        
        // Add delay to avoid rate limiting
        await new Promise(resolve => setTimeout(resolve, 1000));
    }
}

// Function to send WhatsApp message
async function sendWhatsAppMessage(phoneNumber, message) {
    const selectedSessions = getSelectedSenderNumbers();
    
    if (selectedSessions.length === 0) {
        console.error('No sender sessions selected');
        return;
    }
    
    // Send message using the first selected session (or you can implement round-robin)
    const sessionId = selectedSessions[0];
    
    try {
        const response = await fetch(`/api/sessions/${sessionId}/test-send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                phone_number: phoneNumber,
                message: message
            })
        });
        
        if (response.ok) {
            console.log(`Message sent to ${phoneNumber} using session ${sessionId}`);
        } else {
            console.error(`Failed to send message to ${phoneNumber}`);
        }
    } catch (error) {
        console.error(`Error sending message to ${phoneNumber}:`, error);
    }
}

// Function to load and display active WhatsApp numbers
async function loadActiveWhatsAppNumbers() {
    const senderNumbersList = document.getElementById('senderNumbersList');
    
    try {
        // Fetch active WhatsApp sessions
        const response = await fetch('/api/sessions/active', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            const sessions = data.sessions || [];
            
            if (sessions.length > 0) {
                // Display active sessions
                senderNumbersList.innerHTML = sessions.map(session => `
                    <div class="flex items-center bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4" data-session-id="${session.session_id}">
                        <input type="checkbox" id="session_${session.id}" class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 mr-4" checked>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <label for="session_${session.id}" class="text-sm font-semibold text-gray-900 cursor-pointer">${session.phone_number || session.session_id || 'Nomor WhatsApp'}</label>
                            </div>
                            <div class="flex items-center space-x-3 text-xs text-gray-600">
                                <span>Status: ${session.status}</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Terhubung</span>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                // No active sessions
                senderNumbersList.innerHTML = `
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 mb-1">Tidak Ada Session Aktif</h4>
                        <p class="text-xs text-gray-600 mb-3">Anda perlu membuat session WhatsApp terlebih dahulu</p>
                        <a href="/sessions" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Buat Session
                        </a>
                    </div>
                `;
            }
        } else {
            throw new Error('Failed to fetch sessions');
        }
    } catch (error) {
        console.error('Error loading WhatsApp numbers:', error);
        
        // Show error state
        senderNumbersList.innerHTML = `
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-6 text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">Gagal Memuat Data</h4>
                <p class="text-xs text-gray-600 mb-3">Terjadi kesalahan saat memuat nomor WhatsApp</p>
                <button onclick="loadActiveWhatsAppNumbers()" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Coba Lagi
                </button>
            </div>
        `;
    }
}

// Function to get selected sender numbers
function getSelectedSenderNumbers() {
    const selectedNumbers = [];
    const checkboxes = document.querySelectorAll('#senderNumbersList input[type="checkbox"]:checked');
    
    checkboxes.forEach(checkbox => {
        const sessionId = checkbox.id.replace('session_', '');
        // Find the session data to get the session_id (UUID)
        const sessionElement = checkbox.closest('.flex.items-center');
        const sessionData = sessionElement?.dataset?.sessionId;
        if (sessionData) {
            selectedNumbers.push(sessionData);
        } else {
            selectedNumbers.push(sessionId);
        }
    });
    
    return selectedNumbers;
}
</script>

<style>
/* Enhanced toggle switch styles */
.dot {
    transition: transform 0.3s ease-in-out;
}

input[type="checkbox"]:checked + label .dot {
    transform: translateX(1.5rem);
}

/* Step content transitions */
.step-content {
    transition: all 0.3s ease-in-out;
}

.step-content.hidden {
    display: none;
}

/* Animation for loading spinner */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Enhanced hover effects for buttons */
.emoji-btn:hover {
    background-color: #f3f4f6;
    transform: scale(1.1);
    transition: all 0.2s ease-in-out;
}

/* Custom scrollbar for contact list */
#contactGroupsList::-webkit-scrollbar {
    width: 6px;
}

#contactGroupsList::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#contactGroupsList::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#contactGroupsList::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Focus styles for better accessibility */
input:focus, textarea:focus, select:focus {
    outline: none;
    ring: 2px;
    ring-color: #3b82f6;
}

/* Notification animations */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification {
    animation: slideIn 0.3s ease-out;
}

/* Content item animations */
.content-item {
    transition: all 0.3s ease-in-out;
}

.content-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Enhanced button styles */
.btn-primary {
    @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200;
}

.btn-secondary {
    @apply bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200;
}

.btn-success {
    @apply bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200;
}

/* File upload area styles */
#attachmentArea {
    transition: all 0.3s ease-in-out;
}

#attachmentArea.drag-over {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

/* Enhanced textarea styles */
#messageContent {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
}

/* Formatting button styles */
.formatting-btn {
    @apply flex items-center px-3 py-2 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 rounded-lg border border-gray-200 transition-all duration-200;
}

.formatting-btn.active {
    @apply bg-blue-50 text-blue-700 border-blue-200;
}

/* Tips section enhancements */
.tips-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Auto-save indicator */
.auto-save-indicator {
    @apply flex items-center space-x-1 text-xs;
}

.auto-save-indicator.saving {
    @apply text-yellow-600;
}

.auto-save-indicator.saved {
    @apply text-green-600;
}

/* Enhanced modal styles */
.modal-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 9999 !important;
    backdrop-filter: blur(4px);
}

.modal-content {
    background-color: white !important;
    border-radius: 12px !important;
    padding: 24px !important;
    max-width: 28rem !important;
    width: 100% !important;
    margin: 0 16px !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    position: relative !important;
    z-index: 10000 !important;
}

.modal-content.max-w-2xl {
    max-width: 42rem !important;
}

/* Ensure modal is always on top */
.modal-overlay * {
    position: relative !important;
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden !important;
}

/* Additional modal positioning fixes */
.modal-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 9999 !important;
    backdrop-filter: blur(4px);
}

/* Ensure modal content is properly positioned */
.modal-content {
    background-color: white !important;
    border-radius: 12px !important;
    padding: 24px !important;
    max-width: 28rem !important;
    width: calc(100% - 32px) !important;
    max-height: 90vh !important;
    overflow-y: auto !important;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    position: relative !important;
    z-index: 10000 !important;
    margin: 0 !important;
}

/* Template item styles */
.template-item {
    @apply transition-all duration-200;
}

.template-item:hover {
    @apply bg-blue-50 border-blue-200;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Category badge styles */
.category-badge {
    @apply inline-flex items-center px-2 py-1 rounded-full text-xs font-medium;
}

.category-general {
    @apply bg-gray-100 text-gray-800;
}

.category-marketing {
    @apply bg-purple-100 text-purple-800;
}

.category-customer-service {
    @apply bg-green-100 text-green-800;
}

.category-promotion {
    @apply bg-orange-100 text-orange-800;
}

.category-notification {
    @apply bg-blue-100 text-blue-800;
}

.category-custom {
    @apply bg-pink-100 text-pink-800;
}

/* File preview styles */
.file-preview-item {
    @apply flex items-center justify-between p-3 bg-gray-50 rounded-lg;
    transition: all 0.2s ease-in-out;
}

.file-preview-item:hover {
    @apply bg-gray-100;
}

/* Enhanced emoji picker */
.emoji-picker {
    @apply absolute bg-white border border-gray-300 rounded-xl p-4 shadow-xl z-50 max-w-sm;
    backdrop-filter: blur(10px);
}

.emoji-category {
    @apply space-y-3;
}

.emoji-grid {
    @apply grid grid-cols-8 gap-1;
}

.emoji-button {
    @apply p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 text-lg;
}

.emoji-button:hover {
    transform: scale(1.1);
}

/* Progress indicators */
.progress-bar {
    @apply w-full bg-gray-200 rounded-full h-2;
}

.progress-fill {
    @apply bg-blue-600 h-2 rounded-full transition-all duration-300;
}

/* Enhanced form styles */
.form-group {
    @apply space-y-2;
}

.form-label {
    @apply block text-sm font-medium text-gray-700;
}

.form-input {
    @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200;
}

.form-textarea {
    @apply w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-gray-700 placeholder-gray-400 transition-all duration-200;
}

/* Status badges */
.status-badge {
    @apply inline-flex items-center px-2 py-1 rounded-full text-xs font-medium;
}

.status-active {
    @apply bg-green-100 text-green-800;
}

.status-inactive {
    @apply bg-gray-100 text-gray-800;
}

.status-new {
    @apply bg-red-100 text-red-800;
}

/* Loading states */
.loading-overlay {
    @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
    backdrop-filter: blur(4px);
}

.loading-spinner {
    @apply animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600;
}

/* Responsive enhancements */
@media (max-width: 768px) {
    .emoji-grid {
        @apply grid-cols-6;
    }
    
    .formatting-btn {
        @apply px-2 py-1 text-sm;
    }
    
    .content-item {
        @apply p-3;
    }
}

/* Dark mode support (optional) */
@media (prefers-color-scheme: dark) {
    .dark-mode {
        @apply bg-gray-900 text-white;
    }
    
    .dark-mode .bg-white {
        @apply bg-gray-800;
    }
    
    .dark-mode .text-gray-900 {
        @apply text-white;
    }
    
    .dark-mode .border-gray-200 {
        @apply border-gray-700;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .formatting-btn {
        @apply border-2;
    }
    
    .content-item {
        @apply border-2;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endsection 