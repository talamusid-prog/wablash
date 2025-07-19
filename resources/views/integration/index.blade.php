@extends('layouts.app')

@section('title', 'API Integration')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">API Integration</h1>
        <p class="text-gray-600">Integrasikan WA Blast dengan aplikasi web lain melalui REST API</p>
    </div>

    <!-- Quick Start Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-blue-900 mb-4">🚀 Quick Start</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-medium text-blue-800 mb-2">1. Setup API Key</h3>
                <div class="bg-gray-800 text-green-400 p-3 rounded text-sm font-mono">
                    curl -H "X-API-Key: your-api-key" \<br>
                    &nbsp;&nbsp;https://your-domain.com/api/v1/integration/system-status
                </div>
            </div>
            <div>
                <h3 class="font-medium text-blue-800 mb-2">2. Test Connection</h3>
                <div class="bg-gray-800 text-green-400 p-3 rounded text-sm font-mono">
                    curl -X GET "{{ url('/api/v1/integration/system-status') }}" \<br>
                    &nbsp;&nbsp;-H "X-API-Key: your-api-key"
                </div>
            </div>
        </div>
    </div>

    <!-- API Status -->
    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">API Status</p>
                    <p class="text-2xl font-bold text-gray-900" id="api-status">Checking...</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Sessions</p>
                    <p class="text-2xl font-bold text-gray-900" id="active-sessions">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Campaigns</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-campaigns">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Contacts</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-contacts">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- API Documentation Cards -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">API Documentation</h3>
                </div>
                <p class="text-gray-600 mb-4">Dokumentasi lengkap untuk semua endpoint API</p>
                <a href="{{ route('integration.documentation') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    Lihat Dokumentasi
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">SDK & Examples</h3>
                </div>
                <p class="text-gray-600 mb-4">SDK dan contoh kode untuk berbagai bahasa</p>
                <a href="{{ route('integration.sdk') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    Lihat SDK
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">API Testing</h3>
                </div>
                <p class="text-gray-600 mb-4">Test API langsung dari browser</p>
                <a href="{{ route('integration.testing') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    Test API
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Webhook Setup</h3>
                </div>
                <p class="text-gray-600 mb-4">Konfigurasi webhook untuk real-time events</p>
                <a href="{{ route('integration.webhook') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    Setup Webhook
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">API Keys</h3>
                </div>
                <p class="text-gray-600 mb-4">Kelola API keys untuk aplikasi Anda</p>
                <a href="{{ route('integration.keys.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    Kelola Keys
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Support</h3>
                </div>
                <p class="text-gray-600 mb-4">Bantuan dan dukungan teknis</p>
                <a href="{{ route('integration.support') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    Dapatkan Bantuan
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent API Calls -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent API Calls</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="api-calls-table">
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900" colspan="4">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load API status
    loadApiStatus();
    
    // Load recent API calls
    loadRecentApiCalls();
});

function loadApiStatus() {
    fetch('/api/v1/integration/system-status', {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('api-status').textContent = 'Online';
            document.getElementById('active-sessions').textContent = data.data.active_sessions || 0;
            document.getElementById('total-campaigns').textContent = data.data.total_campaigns || 0;
            document.getElementById('total-contacts').textContent = data.data.total_contacts || 0;
        } else {
            document.getElementById('api-status').textContent = 'Offline';
            console.error('API returned error:', data.message);
        }
    })
    .catch(error => {
        document.getElementById('api-status').textContent = 'Error';
        console.error('Error loading API status:', error);
        
        // Show more detailed error information
        if (error.message.includes('404')) {
            console.error('API endpoint not found. Please check if the route is properly configured.');
        } else if (error.message.includes('401')) {
            console.error('Unauthorized. Please check API key configuration.');
        } else if (error.message.includes('500')) {
            console.error('Internal server error. Please check server logs.');
        }
    });
}

function loadRecentApiCalls() {
    // This would typically load from your API logs
    const mockData = [
        { endpoint: '/api/v1/whatsapp/sessions', method: 'GET', status: 200, time: '2 minutes ago' },
        { endpoint: '/api/v1/integration/send-template', method: 'POST', status: 200, time: '5 minutes ago' },
        { endpoint: '/api/v1/blast/campaigns', method: 'GET', status: 200, time: '10 minutes ago' }
    ];
    
    const tableBody = document.getElementById('api-calls-table');
    tableBody.innerHTML = '';
    
    mockData.forEach(call => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 text-sm text-gray-900">${call.endpoint}</td>
            <td class="px-6 py-4 text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${call.method === 'GET' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                    ${call.method}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    ${call.status}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">${call.time}</td>
        `;
        tableBody.appendChild(row);
    });
}
</script>
@endsection 