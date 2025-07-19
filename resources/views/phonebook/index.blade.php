@extends('layouts.app')

@section('title', 'Phonebook')

@push('head')
    {{-- Preload important resources --}}
    <link rel="preload" href="{{ asset('js/sweetalert.js') }}" as="script">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
@endpush

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.group-card {
    transition: all 0.3s ease;
}

.group-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.group-participants {
    transition: all 0.3s ease;
}

.group-participants.show {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive improvements */
@media (max-width: 640px) {
    .mobile-stack {
        flex-direction: column;
        align-items: stretch;
    }
    
    .mobile-full-width {
        width: 100%;
    }
    
    .mobile-text-center {
        text-align: center;
    }
    
    .mobile-mb-4 {
        margin-bottom: 1rem;
    }
    
    .mobile-p-4 {
        padding: 1rem;
    }
    
    .mobile-text-sm {
        font-size: 0.875rem;
    }
    
    .mobile-text-lg {
        font-size: 1.125rem;
    }
    
    .mobile-text-xl {
        font-size: 1.25rem;
    }
    
    .mobile-text-2xl {
        font-size: 1.5rem;
    }
    
    .mobile-text-3xl {
        font-size: 1.875rem;
    }
}

@media (max-width: 768px) {
    .tablet-stack {
        flex-direction: column;
        align-items: stretch;
    }
    
    .tablet-full-width {
        width: 100%;
    }
    
    .tablet-mb-4 {
        margin-bottom: 1rem;
    }
    
    .tablet-text-center {
        text-align: center;
    }
}

/* Modal responsive improvements */
@media (max-width: 640px) {
    .modal-mobile {
        width: 95%;
        margin: 1rem auto;
        top: 10px;
    }
    
    .modal-mobile .p-5 {
        padding: 1rem;
    }
}

/* Touch device improvements */
@media (hover: none) and (pointer: coarse) {
    .group-card:hover {
        transform: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .group-card:active {
        transform: scale(0.98);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    button, a {
        min-height: 44px;
        min-width: 44px;
    }
    
    input, select, textarea {
        font-size: 16px !important;
    }
}

/* Landscape mobile improvements */
@media (max-width: 768px) and (orientation: landscape) {
    .p-4 {
        padding: 0.75rem;
    }
    
    .mb-6 {
        margin-bottom: 1rem;
    }
    
    .text-2xl {
        font-size: 1.5rem;
    }
    
    .grid-cols-1 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* High DPI display improvements */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .group-card {
        border-width: 0.5px;
    }
    
    input, select, textarea {
        border-width: 0.5px;
    }
}

/* Additional mobile improvements */
@media (max-width: 480px) {
    .p-4 {
        padding: 0.75rem;
    }
    
    .mb-6 {
        margin-bottom: 1rem;
    }
    
    .text-2xl {
        font-size: 1.25rem;
    }
    
    .text-3xl {
        font-size: 1.5rem;
    }
    
    .grid-cols-1 {
        grid-template-columns: 1fr;
    }
    
    .space-y-3 > * + * {
        margin-top: 0.5rem;
    }
    
    .space-y-4 > * + * {
        margin-top: 0.75rem;
    }
    
}

/* Ensure proper touch targets on all devices */
button, a, input[type="button"], input[type="submit"], input[type="reset"] {
    touch-action: manipulation;
}

/* Improve focus states for accessibility */
button:focus, a:focus, input:focus, select:focus, textarea:focus {
    outline: 2px solid #8b5cf6;
    outline-offset: 2px;
}

/* Smooth scrolling for better UX */
html {
    scroll-behavior: smooth;
}

/* Search field improvements */
#searchContacts {
    font-size: 0.875rem;
    line-height: 1.25rem;
    padding-left: 2.25rem !important;
}

#searchContacts::placeholder {
    color: #9ca3af;
    font-size: 0.875rem;
}

/* Ensure proper spacing for search icon */
.relative.flex-1.max-w-md input[type="text"] {
    padding-left: 2.25rem !important;
}

/* Search icon positioning */
.relative.flex-1.max-w-md svg {
    pointer-events: none;
    z-index: 10;
}

/* Ensure icon is properly positioned inside input */
.relative.flex-1.max-w-md {
    position: relative;
}

.relative.flex-1.max-w-md svg {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    z-index: 10;
    color: #9ca3af;
}

/* Ensure icon is inside the input field */
.relative.flex-1.max-w-md {
    position: relative;
    overflow: visible;
}

.relative.flex-1.max-w-md input {
    position: relative;
    z-index: 1;
}

/* Fix icon positioning to be inside the input border */
.relative.flex-1.max-w-md svg {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    z-index: 5;
    color: #9ca3af;
    width: 1rem;
    height: 1rem;
}

/* Ensure input has proper padding for icon */
.relative.flex-1.max-w-md input[type="text"] {
    padding-left: 2.5rem !important;
}

/* Final fix for search icon positioning */
.relative.flex-1.max-w-md {
    position: relative;
}

.relative.flex-1.max-w-md svg {
    position: absolute !important;
    left: 0.75rem !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    pointer-events: none !important;
    z-index: 10 !important;
    color: #9ca3af !important;
    width: 1rem !important;
    height: 1rem !important;
}

/* Ensure icon is visible and properly positioned */
.relative.flex-1.max-w-md input {
    position: relative;
    z-index: 1;
}

/* Search section improvements */
.bg-white.rounded-lg.shadow-sm.border.border-gray-200 .flex {
    align-items: center;
}

/* Action buttons improvements */
.flex.items-center.space-x-2 {
    flex-shrink: 0;
}

.flex.items-center.space-x-2 button,
.flex.items-center.space-x-2 a {
    white-space: nowrap;
    font-size: 0.875rem;
    position: relative;
}

/* Tooltip for action buttons */
.flex.items-center.space-x-2 button[title]:hover::after,
.flex.items-center.space-x-2 a[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
    pointer-events: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Alternative tooltip method for template button */
.flex.items-center.space-x-2 a[title]:hover::before {
    content: attr(title);
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
    pointer-events: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Ensure tooltips are visible */
.flex.items-center.space-x-2 button[title]:hover::after,
.flex.items-center.space-x-2 a[title]:hover::after,
.flex.items-center.space-x-2 a[title]:hover::before {
    opacity: 1 !important;
    visibility: visible !important;
}

/* Specific fix for template button tooltip */
.flex.items-center.space-x-2 a[href*="template"][title]:hover::after,
.flex.items-center.space-x-2 a[href*="template"][title]:hover::before {
    content: attr(title) !important;
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Force tooltip display for template button */
.flex.items-center.space-x-2 a[title*="template"]:hover::after {
    content: attr(title) !important;
    position: absolute !important;
    bottom: -35px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    background: rgba(0, 0, 0, 0.9) !important;
    color: white !important;
    padding: 0.375rem 0.75rem !important;
    border-radius: 0.25rem !important;
    font-size: 0.75rem !important;
    white-space: nowrap !important;
    z-index: 1000 !important;
    pointer-events: none !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    opacity: 1 !important;
    visibility: visible !important;
}

/* Override any conflicting styles */
.flex.items-center.space-x-2 a[title]:hover::after {
    display: block !important;
}

/* Ensure template button has proper positioning */
.flex.items-center.space-x-2 a[href*="template"] {
    position: relative !important;
}

/* Mobile tooltip adjustments */
@media (max-width: 640px) {
    .flex.items-center.space-x-2 a[title]:hover::after,
    .flex.items-center.space-x-2 a[title]:hover::before {
        display: none !important;
    }
}

/* Responsive search field */
@media (max-width: 640px) {
    .relative.flex-1.max-w-md {
        max-width: 100%;
    }
    
    #searchContacts {
        font-size: 1rem;
        padding: 0.75rem 0.75rem 0.75rem 2.5rem;
    }
    
    .relative.flex-1.max-w-md svg {
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
    }
    
    /* Mobile button improvements */
.flex.items-center.space-x-2 {
    flex-wrap: wrap;
    gap: 0.5rem;
}

.flex.items-center.space-x-2 button,
.flex.items-center.space-x-2 a {
    flex: 1;
    min-width: 100px;
    padding: 0.5rem 0.75rem;
}

/* Template button specific styling */
.flex.items-center.space-x-2 a[href*="template"] {
    min-width: 120px;
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
}


</style>

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Phonebook</h1>
                <p class="text-gray-600 mt-2">Kelola kontak untuk kampanye WhatsApp</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Create Group Button -->
                <button onclick="openCreateGroupModal()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors duration-200" title="Buat grup baru untuk mengelompokkan kontak">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Buat Grup
                </button>
                
                <!-- Add Contact Button -->
                <a href="{{ route('phonebook.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-purple-700 bg-purple-100 hover:bg-purple-200 transition-colors duration-200" title="Tambah kontak individual baru secara manual">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Kontak
                </a>
                
                <!-- Contact Grabber Button -->
                <a href="{{ route('phonebook.grabber') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors duration-200" title="Ambil kontak dari grup WhatsApp secara otomatis">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Grabber Kontak
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 bg-purple-100 rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Kontak</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $allContactsCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Aktif</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $activeContactsCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Grup</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $groupsPaginator->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-3 sm:ml-4">
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Kontak Individual</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $individualContactsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <!-- Search Field -->
            <div class="relative flex-1 max-w-md">
                <input type="text" id="searchContacts" placeholder="Cari kontak..." class="w-full px-3 py-2 pl-9 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 text-sm">
                <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <button onclick="exportContacts()" class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center space-x-2 text-sm min-h-[36px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export</span>
                </button>
                <button onclick="showImportModal()" class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center space-x-2 text-sm min-h-[36px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <span>Import</span>
                </button>
                <a href="{{ route('phonebook.template') }}" class="px-3 py-2 border border-dashed border-gray-400 text-gray-500 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center space-x-2 text-sm min-h-[36px]" title="Download template Excel untuk import kontak">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"></path>
                    </svg>
                    <span>Download Template</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Groups Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Grup WhatsApp</h2>
            <div class="text-sm text-gray-500">
                Menampilkan {{ $groupsPaginator->firstItem() ?? 0 }} - {{ $groupsPaginator->lastItem() ?? 0 }} dari {{ $groupsPaginator->total() }} grup
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            <!-- Individual Contacts Group -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden group-card" data-group-name="{{ $individualGroup->name }}">
                <div class="p-3 sm:p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start space-x-2 sm:space-x-3 flex-1 min-w-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('phonebook.individual-contacts') }}" class="text-left w-full block">
                                    <h3 class="text-xs sm:text-base font-semibold text-gray-900 group-name hover:text-purple-600 transition-colors duration-200 line-clamp-2">{{ $individualGroup->name }}</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $individualGroup->participants->count() }} kontak</p>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('phonebook.individual-contacts') }}" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 ml-2 sm:ml-3 p-2 flex-shrink-0" title="Lihat kontak individual">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Groups -->
            @forelse($groupsPaginator as $group)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden group-card" data-group-name="{{ $group->name }}">
                <div class="p-3 sm:p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start space-x-2 sm:space-x-3 flex-1 min-w-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                @if(isset($group->type) && $group->type === 'manual_group')
                                    <a href="{{ route('phonebook.manual-group-participants', $group->name) }}" class="text-left w-full block">
                                        <h3 class="text-xs sm:text-base font-semibold text-gray-900 group-name hover:text-blue-600 transition-colors duration-200 line-clamp-2">{{ $group->name }}</h3>
                                        <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $group->participants_with_phone_count ?? $group->participants->count() }} peserta</p>
                                    </a>
                                @else
                                    <a href="{{ route('phonebook.group-participants', $group->id) }}" class="text-left w-full block">
                                        <h3 class="text-xs sm:text-base font-semibold text-gray-900 group-name hover:text-blue-600 transition-colors duration-200 line-clamp-2">{{ $group->name }}</h3>
                                        <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $group->participants->count() }} peserta</p>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-1 sm:space-x-2 flex-shrink-0 ml-2">
                            @if(isset($group->type) && $group->type === 'manual_group')
                                <a href="{{ route('phonebook.manual-group-participants', $group->name) }}" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2" title="Lihat peserta grup">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('phonebook.group-participants', $group->id) }}" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1" title="Lihat peserta grup">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            @endif
                            <button onclick="deleteGroup('{{ $group->id }}', '{{ $group->name }}', '{{ $group->type ?? 'whatsapp' }}')" class="text-red-400 hover:text-red-600 transition-colors duration-200 p-1" title="Hapus grup">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
                    <div class="w-16 h-16 sm:w-24 sm:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">Belum ada grup</h3>
                    <p class="text-sm sm:text-base text-gray-500">Grup akan muncul setelah Anda menggunakan fitur grabber kontak</p>
                </div>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($groupsPaginator->hasPages())
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center text-sm text-gray-700">
                <span>Menampilkan {{ $groupsPaginator->firstItem() ?? 0 }} - {{ $groupsPaginator->lastItem() ?? 0 }} dari {{ $groupsPaginator->total() }} grup</span>
            </div>
            
            <div class="flex items-center space-x-1 sm:space-x-2">
                {{-- Previous Page Link --}}
                @if ($groupsPaginator->onFirstPage())
                    <span class="px-2 sm:px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </span>
                @else
                    <a href="{{ $groupsPaginator->previousPageUrl() }}" class="px-2 sm:px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $start = max(1, $groupsPaginator->currentPage() - 2);
                    $end = min($groupsPaginator->lastPage(), $groupsPaginator->currentPage() + 2);
                @endphp
                
                {{-- First page --}}
                @if($start > 1)
                    <a href="{{ $groupsPaginator->url(1) }}" class="px-2 sm:px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">1</a>
                    @if($start > 2)
                        <span class="px-1 sm:px-2 py-2 text-sm text-gray-500">...</span>
                    @endif
                @endif
                
                {{-- Page numbers around current page --}}
                @for($page = $start; $page <= $end; $page++)
                    @if ($page == $groupsPaginator->currentPage())
                        <span class="px-2 sm:px-3 py-2 text-sm text-white bg-purple-600 rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $groupsPaginator->url($page) }}" class="px-2 sm:px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">{{ $page }}</a>
                    @endif
                @endfor
                
                {{-- Last page --}}
                @if($end < $groupsPaginator->lastPage())
                    @if($end < $groupsPaginator->lastPage() - 1)
                        <span class="px-1 sm:px-2 py-2 text-sm text-gray-500">...</span>
                    @endif
                    <a href="{{ $groupsPaginator->url($groupsPaginator->lastPage()) }}" class="px-2 sm:px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">{{ $groupsPaginator->lastPage() }}</a>
                @endif

                {{-- Next Page Link --}}
                @if ($groupsPaginator->hasMorePages())
                    <a href="{{ $groupsPaginator->nextPageUrl() }}" class="px-2 sm:px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @else
                    <span class="px-2 sm:px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Create Group -->
<div id="createGroupModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-5 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 sm:w-96 lg:w-[500px] shadow-lg rounded-md bg-white modal-mobile">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Buat Grup Baru</h3>
                <button onclick="closeCreateGroupModal()" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="createGroupForm" class="space-y-4" method="POST" action="{{ route('phonebook.store-group') }}">
                @csrf
                <div>
                    <label for="modalGroupName" class="block text-sm font-medium text-gray-700 mb-2">Nama Grup *</label>
                    <input type="text" id="modalGroupName" name="name" required 
                           class="w-full px-3 sm:px-4 py-3 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 text-sm sm:text-base"
                           placeholder="Masukkan nama grup">
                </div>
                
                <div>
                    <label for="modalGroupDescription" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="modalGroupDescription" name="description" rows="3" 
                              class="w-full px-3 sm:px-4 py-3 sm:py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 text-sm sm:text-base"
                              placeholder="Tambahkan deskripsi tentang grup ini"></textarea>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-4">
                    <button type="button" onclick="closeCreateGroupModal()" 
                            class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 text-sm sm:text-base min-h-[44px]">
                        Batal
                    </button>
                    <button type="submit" id="submitGroupBtn"
                            class="px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 text-sm sm:text-base min-h-[44px]">
                        Buat Grup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for functionality -->
<script defer>


// Search functionality only
document.getElementById('searchContacts').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    
    // Search in groups
    document.querySelectorAll('.group-card').forEach(card => {
        const groupName = card.querySelector('.group-name').textContent.toLowerCase();
        const hasMatch = groupName.includes(searchTerm);
        card.style.display = hasMatch ? 'block' : 'none';
    });
    
    // Search in individual contacts
    document.querySelectorAll('.individual-contact').forEach(contact => {
        const contactName = contact.querySelector('h4').textContent.toLowerCase();
        const contactPhone = contact.querySelector('.text-sm').textContent.toLowerCase();
        const hasMatch = contactName.includes(searchTerm) || contactPhone.includes(searchTerm);
        contact.style.display = hasMatch ? 'flex' : 'none';
    });
});

function editContact(contactId) {
    // Implement edit functionality
    console.log('Edit contact:', contactId);
}

function deleteContact(contactId) {
    // Implement delete functionality
    console.log('Delete contact:', contactId);
}

function exportContacts() {
    // Implement export functionality
    console.log('Export contacts');
}

function showImportModal() {
    // Implement import modal
    console.log('Show import modal');
}

function deleteGroup(groupId, groupName, groupType) {
    // Decode HTML entities in group name
    const decodedGroupName = groupName.replace(/&#039;/g, "'").replace(/&quot;/g, '"').replace(/&amp;/g, '&');
    
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus grup "${decodedGroupName}" dan semua pesertanya?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve, reject) => {
                // Create form for DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                
                // Determine the correct route based on group type
                if (groupType === 'manual_group') {
                    form.action = `/phonebook/manual-group/${encodeURIComponent(groupName)}/delete`;
                } else {
                    form.action = `/phonebook/group/${groupId}/delete`;
                }
                
                // Add CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
                
                // Add method override
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                // Submit form
                document.body.appendChild(form);
                form.submit();
                
                // Resolve after a short delay to show loading
                setTimeout(() => resolve(), 1000);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // The form has been submitted, page will reload with flash message
        }
    });
}

// Modal functions
function openCreateGroupModal() {
    console.log('Opening modal...');
    const modal = document.getElementById('createGroupModal');
    const nameInput = document.getElementById('modalGroupName');
    
    if (modal && nameInput) {
        modal.classList.remove('hidden');
        nameInput.focus();
        console.log('Modal opened successfully');
    } else {
        console.error('Modal or name input not found');
    }
}

function closeCreateGroupModal() {
    console.log('Closing modal...');
    const modal = document.getElementById('createGroupModal');
    const form = document.getElementById('createGroupForm');
    
    if (modal && form) {
        modal.classList.add('hidden');
        form.reset();
        console.log('Modal closed successfully');
    } else {
        console.error('Modal or form not found');
    }
}

// Setup create group form
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up form...');
    const form = document.getElementById('createGroupForm');
    if (form) {
        console.log('Form found, adding event listener...');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted, calling createGroup...');
            createGroup();
        });
        
        // Also add click event to submit button as backup
        const submitBtn = document.getElementById('submitGroupBtn');
        if (submitBtn) {
            console.log('Submit button found, adding click listener...');
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Submit button clicked, calling createGroup...');
                createGroup();
            });
        }
    } else {
        console.error('Form not found!');
    }
});

// Create group function
function createGroup() {
    console.log('=== createGroup function called ===');
    
    const form = document.getElementById('createGroupForm');
    const nameInput = document.getElementById('modalGroupName');
    const descriptionInput = document.getElementById('modalGroupDescription');
    const submitBtn = document.querySelector('#createGroupForm button[type="submit"]');
    
    if (!form || !nameInput || !descriptionInput || !submitBtn) {
        console.error('Required elements not found:', {
            form: !!form,
            nameInput: !!nameInput,
            descriptionInput: !!descriptionInput,
            submitBtn: !!submitBtn
        });
        showAlert('error', 'Terjadi kesalahan: elemen form tidak ditemukan');
        return;
    }
    
    const name = nameInput.value.trim();
    const description = descriptionInput.value.trim();
    
    if (!name) {
        showAlert('error', 'Nama grup harus diisi');
        nameInput.focus();
        return;
    }
    
    console.log('Form data:', { name, description });
    
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Membuat...';
    submitBtn.disabled = true;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('CSRF Token:', csrfToken);
    
    // Create form data instead of JSON
    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    formData.append('_token', csrfToken);
    
    fetch('{{ route("phonebook.store-group") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text().then(text => {
            console.log('Response text:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                return { success: false, message: 'Invalid response format' };
            }
        });
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Show success message
            showAlert('success', data.message || 'Grup berhasil dibuat!');
            
            // Close modal
            closeCreateGroupModal();
            
            // Reload page to show new group
            console.log('Reloading page in 1 second...');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert('error', data.message || 'Gagal membuat grup');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showAlert('error', 'Terjadi kesalahan saat membuat grup: ' + error.message);
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Simple alert function
function showAlert(type, message) {
    console.log('Showing alert:', type, message);
    
    // Remove existing alerts first
    const existingAlerts = document.querySelectorAll('.alert-notification');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert-notification fixed top-4 right-4 p-4 rounded-lg shadow-lg z-[9999] ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 1rem;
        right: 1rem;
        left: 1rem;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        font-weight: 500;
        min-width: auto;
        max-width: 400px;
        margin: 0 auto;
        ${type === 'success' ? 'background-color: #10b981; color: white;' : 'background-color: #ef4444; color: white;'}
    `;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}
</script>
@endsection 