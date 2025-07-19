@extends('layouts.app')

@section('title', 'Grabber Kontak WhatsApp')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Grabber Kontak WhatsApp</h1>
                <p class="text-gray-600 mt-2">Ambil kontak dari WhatsApp yang terhubung dan impor ke phonebook</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('phonebook.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Phonebook</span>
                </a>
            </div>
        </div>
    </div>

    @if($sessions->isEmpty())
    <!-- No Connected Sessions -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-yellow-800">Tidak ada session WhatsApp yang terhubung</h3>
                <p class="text-yellow-700 mt-1">Anda perlu menghubungkan session WhatsApp terlebih dahulu untuk menggunakan fitur grabber kontak.</p>
                <div class="mt-4">
                    <a href="{{ route('sessions.index') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Buat Session WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Connected Sessions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        @foreach($sessions as $session)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $session->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $session->phone_number }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Terhubung
                </span>
            </div>

            <!-- Contact Statistics -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $contactStats[$session->session_id]['individual'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Kontak Individual</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $contactStats[$session->session_id]['groups'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Grup</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $contactStats[$session->session_id]['total'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500">Total</div>
                </div>
            </div>

            <!-- Grab Actions -->
            <div class="space-y-3">
                <button onclick="grabContacts('{{ $session->session_id }}', 'individual')" 
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Grab Kontak Individual</span>
                </button>

                <button onclick="grabContacts('{{ $session->session_id }}', 'groups')" 
                        class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Grab Kontak Grup</span>
                </button>

                <button onclick="grabContacts('{{ $session->session_id }}', 'all')" 
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Grab Semua Kontak</span>
                </button>

                <button onclick="viewGrabbedContacts('{{ $session->session_id }}')" 
                        class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>Lihat Kontak yang Di-grab</span>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Grabbed Contacts Modal -->
    <div id="grabbedContactsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Kontak yang Di-grab</h3>
                    <button onclick="closeGrabbedContactsModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Filter Tabs -->
                <div class="border-b border-gray-200 mb-4">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="filterContacts('all')" id="tab-all" class="border-b-2 border-purple-500 py-2 px-1 text-sm font-medium text-purple-600">
                            Semua
                        </button>
                        <button onclick="filterContacts('individual')" id="tab-individual" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Individual
                        </button>
                        <button onclick="filterContacts('group')" id="tab-group" class="border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                            Grup
                        </button>
                    </nav>
                </div>

                <!-- Contacts List -->
                <div id="contactsList" class="max-h-96 overflow-y-auto">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
                        <p class="mt-2 text-gray-500">Memuat kontak...</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="selectAll" class="text-sm text-gray-700">Pilih Semua</label>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span id="selectedCount" class="text-sm text-gray-500">0 kontak dipilih</span>
                        <button onclick="importSelectedContacts()" id="importBtn" disabled
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            Impor ke Phonebook
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
// Global variables
let currentSessionId = null;
let currentContacts = [];
let currentFilter = 'all';

// Check if SweetAlert is available
if (typeof Swal === 'undefined') {
    console.error('SweetAlert is not loaded');
    // Fallback alert
    window.Swal = {
        fire: function(options) {
            alert(options.text || options.title || 'Alert');
            return Promise.resolve({ isConfirmed: true });
        }
    };
}

// Semua function utama (grabContacts, viewGrabbedContacts, dst) tetap di atas
// Grab contacts from WhatsApp
function grabContacts(sessionId, type) {
    try {
        const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = `
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
        <span>Mengambil kontak...</span>
    `;
    button.disabled = true;

    fetch('{{ route("phonebook.grab-contacts") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            session_id: sessionId,
            type: type
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload page to update statistics
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengambil kontak',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        // Restore button state
        if (button) {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
    } catch (error) {
        console.error('Error in grabContacts:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengambil kontak',
            confirmButtonText: 'OK'
        });
    }
}
window.grabContacts = grabContacts;

// View grabbed contacts
function viewGrabbedContacts(sessionId) {
    try {
        currentSessionId = sessionId;
        currentFilter = 'all';
        
        // Show modal
        const modal = document.getElementById('grabbedContactsModal');
        if (modal) {
            modal.classList.remove('hidden');
        } else {
            console.error('Modal element not found');
            return;
        }
        
        // Load contacts
        loadGrabbedContacts(sessionId);
    } catch (error) {
        console.error('Error in viewGrabbedContacts:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat membuka modal kontak',
            confirmButtonText: 'OK'
        });
    }
}
window.viewGrabbedContacts = viewGrabbedContacts;

// Load grabbed contacts
function loadGrabbedContacts(sessionId, type = null) {
    try {
        const url = new URL('{{ route("phonebook.get-grabbed-contacts") }}');
        url.searchParams.append('session_id', sessionId);
        if (type) {
            url.searchParams.append('type', type);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }
        
        fetch(url, {
            method: 'GET',
            headers: headers
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
                    if (data.success) {
            currentContacts = data.data;
            renderContactsList();
            } else {
                console.error('API returned error:', data.message);
                const contactsList = document.getElementById('contactsList');
                if (contactsList) {
                    contactsList.innerHTML = `
                        <div class="text-center py-8">
                            <p class="text-red-500">${data.message}</p>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            const contactsList = document.getElementById('contactsList');
            if (contactsList) {
                contactsList.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-red-500">Terjadi kesalahan saat memuat kontak: ${error.message}</p>
                    </div>
                `;
            }
        });
    } catch (error) {
        console.error('Error in loadGrabbedContacts:', error);
    }
}
window.loadGrabbedContacts = loadGrabbedContacts;

// Render contacts list
function renderContactsList() {
    try {
        const container = document.getElementById('contactsList');
        if (!container) {
            console.error('Contacts list container not found');
            return;
        }
        
        if (currentFilter === 'group') {
            renderGroupsView(container);
        } else {
            renderContactsView(container);
        }
    } catch (error) {
        console.error('Error in renderContactsList:', error);
    }
}
window.renderContactsList = renderContactsList;

// Render groups view (for group filter)
function renderGroupsView(container) {
    try {
        // Get all groups
        const groups = currentContacts.filter(contact => contact.type === 'group');
    
    if (groups.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500">Tidak ada grup yang ditemukan</p>
            </div>
        `;
        return;
    }

    const groupsHtml = groups.map(group => {
        // Get ALL participants (with and without phone numbers)
        const allParticipants = currentContacts.filter(contact => 
            contact.type === 'individual' && 
            contact.group_id === group.contact_id
        );
        
        // Count participants with phone numbers
        const participantsWithPhone = allParticipants.filter(contact => 
            contact.phone_number && 
            contact.phone_number.trim() !== ''
        );
        
        const participantCount = participantsWithPhone.length;
        const totalParticipants = allParticipants.length;
        const canImport = participantCount > 0;
        
        return `
        <div class="border border-gray-200 rounded-lg mb-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-t-lg">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" value="${group.contact_id}" onchange="toggleGroupSelection('${group.contact_id}')" 
                           class="group-checkbox h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                           ${!canImport ? 'disabled' : ''}>
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-medium text-sm">${group.name.charAt(0).toUpperCase()}</span>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 flex items-center space-x-2">
                            <span>${group.name}</span>
                            <button onclick="editGroupName('${group.contact_id}', '${group.name}')" 
                                    class="text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="text-sm text-gray-500">
                            ${totalParticipants} total peserta • ${participantCount} dengan nomor telepon
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${canImport ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        ${canImport ? 'Dapat Diimport' : 'Tidak Ada Nomor'}
                    </span>
                    <button onclick="toggleGroupParticipants('${group.contact_id}')" 
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5 transform transition-transform" id="icon-${group.contact_id}" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="hidden" id="participants-${group.contact_id}">
                <div class="p-4 border-t border-gray-200">
                    <div class="text-sm font-medium text-gray-700 mb-3">Daftar Peserta:</div>
                    ${renderGroupParticipants(allParticipants)}
                </div>
            </div>
        </div>
    `}).join('');

    container.innerHTML = groupsHtml;
    updateSelectedCount();
    } catch (error) {
        console.error('Error in renderGroupsView:', error);
        if (container) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-500">Terjadi kesalahan saat menampilkan grup</p>
                </div>
            `;
        }
    }
}

// Render group participants
function renderGroupParticipants(participants) {
    if (participants.length === 0) {
        return `
        <div class="text-center py-4">
            <p class="text-gray-500 text-sm">Tidak ada peserta dalam grup ini</p>
        </div>
        `;
    }

    return participants.map(participant => {
        const hasPhoneNumber = participant.phone_number && participant.phone_number.trim() !== '';
        
        return `
        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0 ${!hasPhoneNumber ? 'opacity-60' : ''}">
            <div class="flex items-center space-x-3">
                <input type="checkbox" value="${participant.contact_id}" onchange="updateSelectedCount()" 
                       class="participant-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                       ${!hasPhoneNumber ? 'disabled' : ''}>
                <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-medium text-xs">${participant.name.charAt(0).toUpperCase()}</span>
                </div>
                <div>
                    <div class="font-medium text-gray-900 text-sm">${participant.name}</div>
                    <div class="text-xs text-gray-500">
                        ${hasPhoneNumber ? participant.phone_number : 'Tidak ada nomor telepon'}
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${hasPhoneNumber ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'}">
                    ${hasPhoneNumber ? 'Peserta' : 'Tanpa Nomor'}
                </span>
            </div>
        </div>
        `;
    }).join('');
}

// Render contacts view (for individual and all filters)
function renderContactsView(container) {
    try {
        // Filter contacts based on current filter
        let filteredContacts = currentContacts;
    if (currentFilter === 'individual') {
        filteredContacts = currentContacts.filter(contact => 
            contact.type === 'individual' && !contact.group_id
        );
    }

    if (filteredContacts.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500">Tidak ada kontak yang ditemukan</p>
            </div>
        `;
        return;
    }

    const contactsHtml = filteredContacts.map(contact => {
        const isGroup = contact.type === 'group';
        const isGroupParticipant = contact.type === 'individual' && contact.group_id;
        const hasPhoneNumber = contact.phone_number && contact.phone_number.trim() !== '';
        const canImport = !isGroup && hasPhoneNumber;
        
        // Get group name for participants
        let groupInfo = '';
        if (isGroupParticipant) {
            const group = currentContacts.find(g => g.type === 'group' && g.contact_id === contact.group_id);
            groupInfo = group ? ' • Grup: ' + group.name : ' • Grup ID: ' + contact.group_id;
        }
        
        return `
        <div class="flex items-center justify-between p-3 border-b border-gray-200 hover:bg-gray-50 ${!canImport ? 'opacity-60' : ''}">
            <div class="flex items-center space-x-3">
                <input type="checkbox" value="${contact.contact_id}" onchange="updateSelectedCount()" 
                       class="contact-checkbox h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                       ${!canImport ? 'disabled' : ''}>
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-medium text-sm">${contact.name.charAt(0).toUpperCase()}</span>
                </div>
                <div>
                    <div class="font-medium text-gray-900">${contact.name}</div>
                    <div class="text-sm text-gray-500">
                        ${hasPhoneNumber ? contact.phone_number : 'Tidak ada nomor telepon'}
                        ${groupInfo}
                        ${!canImport ? ' • Tidak dapat diimpor' : ''}
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${isGroup ? 'bg-purple-100 text-purple-800' : isGroupParticipant ? 'bg-blue-100 text-blue-800' : hasPhoneNumber ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${isGroup ? 'Grup' : isGroupParticipant ? 'Peserta Grup' : hasPhoneNumber ? 'Individual' : 'Tanpa Nomor'}
                </span>
            </div>
        </div>
        `;
    }).join('');

    container.innerHTML = contactsHtml;
    updateSelectedCount();
    } catch (error) {
        console.error('Error in renderContactsView:', error);
        if (container) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-500">Terjadi kesalahan saat menampilkan kontak</p>
                </div>
            `;
        }
    }
}

// Filter contacts
function filterContacts(type) {
    try {
        currentFilter = type;
    
    // Update tab styles
    document.querySelectorAll('[id^="tab-"]').forEach(tab => {
        tab.classList.remove('border-purple-500', 'text-purple-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById(`tab-${type}`).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(`tab-${type}`).classList.add('border-purple-500', 'text-purple-600');
    
    // Load contacts with filter
    // For group filter, we need all contacts (groups + participants)
    // For other filters, we can use the type filter
    if (type === 'group') {
        loadGrabbedContacts(currentSessionId, null); // Load all contacts
    } else {
        loadGrabbedContacts(currentSessionId, type === 'all' ? null : type);
    }
    } catch (error) {
        console.error('Error in filterContacts:', error);
    }
}
window.filterContacts = filterContacts;

// Toggle select all
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    
    if (currentFilter === 'group') {
        // Toggle all group checkboxes and participant checkboxes
        const groupCheckboxes = document.querySelectorAll('.group-checkbox:not(:disabled)');
        const participantCheckboxes = document.querySelectorAll('.participant-checkbox:not(:disabled)');
        
        groupCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        
        participantCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    } else {
        // Toggle regular contact checkboxes
        const checkboxes = document.querySelectorAll('.contact-checkbox:not(:disabled)');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }
    
    updateSelectedCount();
}
window.toggleSelectAll = toggleSelectAll;

// Update selected count
function updateSelectedCount() {
    try {
        let count = 0;
        
        if (currentFilter === 'group') {
            // Count selected group checkboxes and participant checkboxes
            const groupCheckboxes = document.querySelectorAll('.group-checkbox:checked');
            const participantCheckboxes = document.querySelectorAll('.participant-checkbox:checked');
            count = groupCheckboxes.length + participantCheckboxes.length;
        } else {
            // Count regular contact checkboxes
            const checkboxes = document.querySelectorAll('.contact-checkbox:checked');
            count = checkboxes.length;
        }
        
        // Count importable contacts based on current filter
        let importableContacts = currentContacts.filter(contact => {
            const isGroup = contact.type === 'group';
            const hasPhoneNumber = contact.phone_number && contact.phone_number.trim() !== '';
            return !isGroup && hasPhoneNumber;
        });
        
        // If group filter is active, only count group participants
        if (currentFilter === 'group') {
            importableContacts = importableContacts.filter(contact => contact.group_id);
        } else if (currentFilter === 'individual') {
            importableContacts = importableContacts.filter(contact => !contact.group_id);
        }
        
        const selectedCountElement = document.getElementById('selectedCount');
        const importBtnElement = document.getElementById('importBtn');
        
        if (selectedCountElement) {
            selectedCountElement.textContent = `${count} kontak dipilih (${importableContacts.length} dapat diimpor)`;
        }
        
        if (importBtnElement) {
            importBtnElement.disabled = count === 0;
        }
    } catch (error) {
        console.error('Error in updateSelectedCount:', error);
    }
}
window.updateSelectedCount = updateSelectedCount;

// Toggle group participants visibility
function toggleGroupParticipants(groupId) {
    try {
        const participantsDiv = document.getElementById(`participants-${groupId}`);
        const icon = document.getElementById(`icon-${groupId}`);
        
        if (participantsDiv && icon) {
            if (participantsDiv.classList.contains('hidden')) {
                participantsDiv.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                participantsDiv.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    } catch (error) {
        console.error('Error in toggleGroupParticipants:', error);
    }
}
window.toggleGroupParticipants = toggleGroupParticipants;

// Toggle group selection (select/deselect all participants)
function toggleGroupSelection(groupId) {
    try {
        const groupCheckbox = document.querySelector(`input[value="${groupId}"].group-checkbox`);
        const participantCheckboxes = document.querySelectorAll(`#participants-${groupId} .participant-checkbox:not(:disabled)`);
        
        if (groupCheckbox) {
            participantCheckboxes.forEach(checkbox => {
                checkbox.checked = groupCheckbox.checked;
            });
        }
        
        updateSelectedCount();
    } catch (error) {
        console.error('Error in toggleGroupSelection:', error);
    }
}
window.toggleGroupSelection = toggleGroupSelection;

// Edit group name
function editGroupName(groupId, currentName) {
    try {
        Swal.fire({
        title: 'Edit Nama Grup',
        input: 'text',
        inputValue: currentName,
        inputPlaceholder: 'Masukkan nama grup baru',
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Nama grup tidak boleh kosong!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Update group name in current contacts
            const group = currentContacts.find(g => g.type === 'group' && g.contact_id === groupId);
            if (group) {
                group.name = result.value;
                // Re-render the view
                renderContactsList();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Nama grup berhasil diperbarui',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        }
    });
    } catch (error) {
        console.error('Error in editGroupName:', error);
    }
}
window.editGroupName = editGroupName;

// Get selected contact IDs for import
function getSelectedContactIds() {
    try {
        const selectedIds = [];
        
        if (currentFilter === 'group') {
            // Get selected group participants
            const groupCheckboxes = document.querySelectorAll('.group-checkbox:checked');
            const participantCheckboxes = document.querySelectorAll('.participant-checkbox:checked');
            
            // Add selected group participants
            participantCheckboxes.forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });
            
            // Add all participants from selected groups
            groupCheckboxes.forEach(checkbox => {
                const groupId = checkbox.value;
                const participants = currentContacts.filter(contact => 
                    contact.type === 'individual' && 
                    contact.group_id === groupId &&
                    contact.phone_number && 
                    contact.phone_number.trim() !== ''
                );
                participants.forEach(participant => {
                    if (!selectedIds.includes(participant.contact_id)) {
                        selectedIds.push(participant.contact_id);
                    }
                });
            });
        } else {
            // Get selected individual contacts
            const checkboxes = document.querySelectorAll('.contact-checkbox:checked');
            checkboxes.forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });
        }
        
        return selectedIds;
    } catch (error) {
        console.error('Error in getSelectedContactIds:', error);
        return [];
    }
}
window.getSelectedContactIds = getSelectedContactIds;

// Import selected contacts
function importSelectedContacts() {
    try {
        const contactIds = getSelectedContactIds();
    
    if (contactIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Pilih kontak yang akan diimpor',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Show loading
    const importBtn = document.getElementById('importBtn');
    const originalText = importBtn.textContent;
    importBtn.textContent = 'Mengimpor...';
    importBtn.disabled = true;

    fetch('{{ route("phonebook.import-grabbed-contacts") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            session_id: currentSessionId,
            contact_ids: contactIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonText: 'OK'
            }).then(() => {
                closeGrabbedContactsModal();
                // Redirect to phonebook
                window.location.href = '{{ route("phonebook.index") }}';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengimpor kontak',
            confirmButtonText: 'OK'
        });
    })
    .finally(() => {
        importBtn.textContent = originalText;
        importBtn.disabled = false;
    });
    } catch (error) {
        console.error('Error in importSelectedContacts:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengimpor kontak',
            confirmButtonText: 'OK'
        });
    }
}
window.importSelectedContacts = importSelectedContacts;

// Close modal
function closeGrabbedContactsModal() {
    try {
        const modal = document.getElementById('grabbedContactsModal');
        if (modal) {
            modal.classList.add('hidden');
        }
        currentSessionId = null;
        currentContacts = [];
        currentFilter = 'all';
    } catch (error) {
        console.error('Error in closeGrabbedContactsModal:', error);
    }
}
window.closeGrabbedContactsModal = closeGrabbedContactsModal;


</script>
@endsection 