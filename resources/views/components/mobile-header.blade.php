<!-- Mobile Header -->
<header class="lg:hidden bg-white border-b border-gray-200 px-4 py-3">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <button onclick="openMobileSidebar()" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors duration-200 mr-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">WA Blast</h1>
                    <p class="text-xs text-gray-500">WhatsApp Blast System</p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <!-- System Status Indicator -->
            <div class="hidden sm:flex items-center px-3 py-1 bg-green-50 rounded-full">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>
                <span class="text-xs text-green-600 font-medium">Online</span>
            </div>
            
            <!-- Notifications -->
            <button class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors duration-200 relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
            </button>
            
            <!-- User Menu -->
            <div class="relative">
                <button onclick="toggleUserMenu()" class="flex items-center p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors duration-200">
                    <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mr-2">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <!-- User Dropdown Menu -->
                <div id="userMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-2 hidden z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">Admin User</p>
                        <p class="text-xs text-gray-500">admin@wa-blast.test</p>
                    </div>
                    <div class="py-1">
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="#" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats Bar (Mobile) -->
    <div class="mt-3 flex items-center justify-between px-2">
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                <span class="text-xs text-gray-600">{{ \App\Models\WhatsAppSession::where('status', 'connected')->count() }} Sessions</span>
            </div>
            <div class="flex items-center">
                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                <span class="text-xs text-gray-600">{{ \App\Models\BlastCampaign::where('status', 'running')->count() }} Campaigns</span>
            </div>
            <div class="flex items-center">
                <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                <span class="text-xs text-gray-600">{{ \App\Models\WhatsAppMessage::where('status', 'sent')->count() }} Messages</span>
            </div>
        </div>
        
        <!-- Refresh Button -->
        <button onclick="location.reload()" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </button>
    </div>
</header>

<script>
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.classList.toggle('hidden');
}

// Close user menu when clicking outside
document.addEventListener('click', function(e) {
    const userMenu = document.getElementById('userMenu');
    const userButton = e.target.closest('button[onclick="toggleUserMenu()"]');
    
    if (!userButton && !userMenu.contains(e.target)) {
        userMenu.classList.add('hidden');
    }
});

// Close user menu when pressing Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('userMenu').classList.add('hidden');
    }
});

// Add loading animation to refresh button
document.querySelector('button[onclick="location.reload()"]').addEventListener('click', function() {
    this.querySelector('svg').classList.add('animate-spin');
    setTimeout(() => {
        this.querySelector('svg').classList.remove('animate-spin');
    }, 1000);
});
</script> 