<!-- Sidebar -->
<aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 lg:border-r lg:border-gray-200 lg:bg-white lg:pt-5 lg:pb-4">
    <div class="flex items-center flex-shrink-0 px-6">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">WA Blast</h1>
                <p class="text-xs text-gray-500">WhatsApp Blast System</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="mt-8 flex-1 px-6 space-y-2">
        <div class="space-y-1">
            <a href="{{ route('dashboard') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 border-l-4 border-blue-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                Dashboard
                @if(request()->routeIs('dashboard'))
                <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                @endif
            </a>
            
            <a href="{{ route('sessions.index') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('sessions.*') ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 border-l-4 border-blue-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 transition-colors duration-200 {{ request()->routeIs('sessions.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Sessions
                @if(request()->routeIs('sessions.*'))
                <div class="ml-auto w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                @endif
            </a>
            
            <a href="{{ route('campaigns') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('campaigns') ? 'bg-gradient-to-r from-green-50 to-green-100 text-green-700 border-l-4 border-green-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 transition-colors duration-200 {{ request()->routeIs('campaigns') ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM11 16.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM4.5 11a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM16.5 11a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2z"></path>
                </svg>
                Campaigns
                @if(request()->routeIs('campaigns'))
                <div class="ml-auto w-2 h-2 bg-green-600 rounded-full animate-pulse"></div>
                @endif
            </a>
            
            <a href="{{ route('messages') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('messages') ? 'bg-gradient-to-r from-purple-50 to-purple-100 text-purple-700 border-l-4 border-purple-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 transition-colors duration-200 {{ request()->routeIs('messages') ? 'text-purple-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Messages
                @if(request()->routeIs('messages'))
                <div class="ml-auto w-2 h-2 bg-purple-600 rounded-full animate-pulse"></div>
                @endif
            </a>
            
            <a href="{{ route('phonebook.index') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('phonebook.*') ? 'bg-gradient-to-r from-indigo-50 to-indigo-100 text-indigo-700 border-l-4 border-indigo-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 transition-colors duration-200 {{ request()->routeIs('phonebook.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Phonebook
                @if(request()->routeIs('phonebook.*'))
                <div class="ml-auto w-2 h-2 bg-indigo-600 rounded-full animate-pulse"></div>
                @endif
            </a>
            
            <a href="{{ route('test-send') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('test-send') ? 'bg-gradient-to-r from-orange-50 to-orange-100 text-orange-700 border-l-4 border-orange-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 transition-colors duration-200 {{ request()->routeIs('test-send') ? 'text-orange-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Test Send
                @if(request()->routeIs('test-send'))
                <div class="ml-auto w-2 h-2 bg-orange-600 rounded-full animate-pulse"></div>
                @endif
            </a>
            

            
            <a href="{{ route('integration.index') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('integration.*') ? 'bg-gradient-to-r from-teal-50 to-teal-100 text-teal-700 border-l-4 border-teal-600 shadow-sm' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 transition-colors duration-200 {{ request()->routeIs('integration.*') ? 'text-teal-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
                API Integration
                @if(request()->routeIs('integration.*'))
                <div class="ml-auto w-2 h-2 bg-teal-600 rounded-full animate-pulse"></div>
                @endif
            </a>
        </div>
        

    </nav>
    
    <!-- Footer -->
    <div class="flex-shrink-0 px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'Admin User' }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email ?? 'admin@wablast.com' }}</p>
                </div>
            </div>
            <a href="#" onclick="logoutUser()" class="text-gray-400 hover:text-red-500 transition-colors duration-200" title="Logout">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </a>
        </div>
    </div>
</aside>

<!-- Mobile sidebar backdrop -->
<div class="lg:hidden fixed inset-0 bg-gray-600 bg-opacity-75 z-40 hidden" id="mobileSidebarBackdrop"></div>

<!-- Mobile sidebar -->
<div class="lg:hidden fixed inset-y-0 left-0 z-50 w-64 bg-white transform -translate-x-full transition-transform duration-300 ease-in-out" id="mobileSidebar">
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <h1 class="text-lg font-bold text-gray-900">WA Blast</h1>
        </div>
        <button onclick="closeMobileSidebar()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    
    <!-- Mobile navigation (same as desktop but simplified) -->
    <nav class="mt-4 px-6 space-y-2">
        <a href="{{ route('dashboard') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
            </svg>
            Dashboard
        </a>
        
        <a href="{{ route('sessions.index') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('sessions.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('sessions.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            Sessions
        </a>
        
        <a href="{{ route('campaigns') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('campaigns') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('campaigns') ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM11 16.5a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM4.5 11a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2zM16.5 11a.5.5 0 01.5-.5h2a.5.5 0 01.5.5v2a.5.5 0 01-.5.5h-2a.5.5 0 01-.5-.5v-2z"></path>
            </svg>
            Campaigns
        </a>
        
        <a href="{{ route('messages') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('messages') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('messages') ? 'text-purple-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            Messages
        </a>
        
        <a href="{{ route('phonebook.index') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('phonebook.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('phonebook.*') ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Phonebook
        </a>
        
        <a href="{{ route('test-send') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('test-send') ? 'bg-orange-50 text-orange-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('test-send') ? 'text-orange-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
            Test Send
        </a>
        

        
        <a href="{{ route('integration.index') }}" class="nav-link group flex items-center px-3 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('integration.*') ? 'bg-teal-50 text-teal-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('integration.*') ? 'text-teal-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
            </svg>
            API Integration
        </a>
        
        <!-- Mobile Logout Button -->
        <div class="pt-4 mt-4 border-t border-gray-200">
            <a href="#" onclick="logoutUser()" class="w-full flex items-center px-3 py-3 text-sm font-medium rounded-xl text-red-600 hover:bg-red-50 transition-all duration-200">
                <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </a>
        </div>
    </nav>
</div>

<script>
function openMobileSidebar() {
    document.getElementById('mobileSidebarBackdrop').classList.remove('hidden');
    document.getElementById('mobileSidebar').classList.remove('-translate-x-full');
}

function closeMobileSidebar() {
    document.getElementById('mobileSidebarBackdrop').classList.add('hidden');
    document.getElementById('mobileSidebar').classList.add('-translate-x-full');
}

// Close mobile sidebar when clicking backdrop
document.getElementById('mobileSidebarBackdrop').addEventListener('click', closeMobileSidebar);

// Close mobile sidebar when pressing Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMobileSidebar();
    }
});

// Logout function
function logoutUser() {
    if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
        // Create a form dynamically
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add to body and submit
        document.body.appendChild(form);
        form.submit();
    }
}
</script> 