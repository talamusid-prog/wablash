@extends('layouts.app')

@section('title', 'Webhook Configuration')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Webhook Configuration</h1>
        <p class="text-gray-600">Konfigurasi webhook untuk menerima real-time events dari WA Blast</p>
        
        <!-- DEBUG INFO -->
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h3 class="text-sm font-medium text-yellow-800 mb-2">🐛 Debug Information:</h3>
            <div class="text-xs text-yellow-700 space-y-1">
                <p><strong>Webhook Config Exists:</strong> {{ $webhookConfig ? 'YES' : 'NO' }}</p>
                <p><strong>URL:</strong> "{{ $webhookConfig->url ?? 'NULL' }}"</p>
                <p><strong>Secret:</strong> "{{ $webhookConfig->secret ?? 'NULL' }}"</p>
                <p><strong>Enabled:</strong> {{ $webhookConfig->enabled ?? 'NULL' ? 'TRUE' : 'FALSE' }}</p>
                <p><strong>Has Events:</strong> {{ $webhookConfig->events ? 'YES' : 'NO' }}</p>
                @if($webhookConfig->events)
                    <p><strong>Events:</strong> {{ json_encode($webhookConfig->events) }}</p>
                @endif
                <p><strong>Database Records Count:</strong> <span id="db-count">Loading...</span></p>
                <p><strong>Last Updated:</strong> {{ $webhookConfig->updated_at ?? 'Never' }}</p>
                <button onclick="loadDatabaseInfo()" class="mt-2 px-3 py-1 bg-yellow-200 text-yellow-800 rounded text-xs hover:bg-yellow-300">
                    🔄 Refresh Debug Info
                </button>
                <button onclick="location.reload()" class="mt-2 ml-2 px-3 py-1 bg-blue-200 text-blue-800 rounded text-xs hover:bg-blue-300">
                    🔄 Refresh Page
                </button>
                <button onclick="debugAPIEndpoint()" class="mt-2 ml-2 px-3 py-1 bg-red-200 text-red-800 rounded text-xs hover:bg-red-300">
                    🔧 Debug API
                </button>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Webhook Configuration -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Webhook Settings</h2>
            </div>
            <div class="p-6">
                <form id="webhook-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
                            <input type="url" id="webhook-url" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://your-domain.com/webhook" value="{{ $webhookConfig->url ?? 'https://your-domain.com/webhook' }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                            <div class="flex">
                                <input type="text" id="webhook-secret" class="flex-1 border border-gray-300 rounded-l-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Your webhook secret" value="{{ $webhookConfig->secret ?? 'wa-blast-webhook-secret' }}">
                                <button type="button" onclick="generateSecret()" class="px-3 py-2 bg-gray-100 border border-gray-300 border-l-0 rounded-r-md hover:bg-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Events</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-message-sent" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($webhookConfig->events['message_sent'] ?? true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Message Sent</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-message-delivered" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($webhookConfig->events['message_delivered'] ?? true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Message Delivered</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-message-failed" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($webhookConfig->events['message_failed'] ?? true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Message Failed</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-session-connected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($webhookConfig->events['session_connected'] ?? true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Session Connected</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-session-disconnected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($webhookConfig->events['session_disconnected'] ?? true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Session Disconnected</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-campaign-started" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($webhookConfig->events['campaign_started'] ?? true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Campaign Started</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-campaign-completed" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($webhookConfig->events['campaign_completed'] ?? true) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Campaign Completed</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <div class="flex items-center">
                                <input type="checkbox" id="webhook-enabled" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($webhookConfig->enabled ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Enable webhook</span>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Save Configuration
                            </button>
                            <button type="button" onclick="saveWebhookConfig()" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                💾 Save (Direct)
                            </button>
                            <button type="button" onclick="testWebhook()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Test Webhook
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Webhook Events & Documentation -->
        <div class="space-y-6">
            <!-- Event Documentation -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Event Documentation</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Message Sent Event</h3>
                            <div class="bg-gray-100 p-3 rounded text-sm font-mono">
                                {<br>
                                &nbsp;&nbsp;"event": "message.sent",<br>
                                &nbsp;&nbsp;"timestamp": "2024-01-15T10:30:00Z",<br>
                                &nbsp;&nbsp;"data": {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"message_id": "msg_123",<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"session_id": 1,<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"to_number": "6281234567890",<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"message": "Hello!",<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"status": "sent"<br>
                                &nbsp;&nbsp;}<br>
                                }
                            </div>
                        </div>

                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Session Connected Event</h3>
                            <div class="bg-gray-100 p-3 rounded text-sm font-mono">
                                {<br>
                                &nbsp;&nbsp;"event": "session.connected",<br>
                                &nbsp;&nbsp;"timestamp": "2024-01-15T10:30:00Z",<br>
                                &nbsp;&nbsp;"data": {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"session_id": 1,<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"phone_number": "6281234567890",<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"status": "connected"<br>
                                &nbsp;&nbsp;}<br>
                                }
                            </div>
                        </div>

                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">Campaign Started Event</h3>
                            <div class="bg-gray-100 p-3 rounded text-sm font-mono">
                                {<br>
                                &nbsp;&nbsp;"event": "campaign.started",<br>
                                &nbsp;&nbsp;"timestamp": "2024-01-15T10:30:00Z",<br>
                                &nbsp;&nbsp;"data": {<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"campaign_id": 1,<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"name": "Promo Campaign",<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"total_recipients": 100,<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;"status": "running"<br>
                                &nbsp;&nbsp;}<br>
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Webhook Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Webhook Status</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Last Delivery</span>
                            <span class="text-sm text-gray-600">2 minutes ago</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Success Rate</span>
                            <span class="text-sm text-gray-600">98.5%</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Total Events</span>
                            <span class="text-sm text-gray-600">1,234</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Webhook Events -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Webhook Events</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    message.sent
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Success
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">2 minutes ago</td>
                            <td class="px-6 py-4 text-sm text-gray-500">200 OK</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    session.connected
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Success
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">5 minutes ago</td>
                            <td class="px-6 py-4 text-sm text-gray-500">200 OK</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    campaign.started
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Success
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">10 minutes ago</td>
                            <td class="px-6 py-4 text-sm text-gray-500">200 OK</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Page load debugging
document.addEventListener('DOMContentLoaded', function() {
    console.log('🐛 Page loaded - debugging webhook config');
    console.log('Current URL:', window.location.href);
    
    // Load database count for debugging
    loadDatabaseInfo();
});

// Debug: Check if form exists
const webhookForm = document.getElementById('webhook-form');
console.log('🔍 Webhook form found:', !!webhookForm);

if (webhookForm) {
    webhookForm.addEventListener('submit', function(e) {
        console.log('💾 Form submit event triggered!');
        e.preventDefault();
        saveWebhookConfig();
    });
    console.log('💾 Submit event listener added');
} else {
    console.error('❌ Webhook form not found!');
}

function loadDatabaseInfo() {
    fetch('/api/v1/integration/webhook-config', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('🐛 Database API response:', data);
        const countElement = document.getElementById('db-count');
        if (data.success) {
            countElement.textContent = 'API Success - Config loaded';
            countElement.className = 'text-green-600 font-bold';
        } else {
            countElement.textContent = 'API Failed - No config found';
            countElement.className = 'text-red-600 font-bold';
        }
    })
    .catch(error => {
        console.error('🐛 Database API error:', error);
        document.getElementById('db-count').textContent = 'API Error: ' + error.message;
        document.getElementById('db-count').className = 'text-red-600 font-bold';
    });
}

function generateSecret() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 32; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('webhook-secret').value = result;
}

function saveWebhookConfig() {
    console.log('💾 Starting saveWebhookConfig...');
    
    const config = {
        url: document.getElementById('webhook-url').value,
        secret: document.getElementById('webhook-secret').value,
        events: {
            message_sent: document.getElementById('event-message-sent').checked,
            message_delivered: document.getElementById('event-message-delivered').checked,
            message_failed: document.getElementById('event-message-failed').checked,
            session_connected: document.getElementById('event-session-connected').checked,
            session_disconnected: document.getElementById('event-session-disconnected').checked,
            campaign_started: document.getElementById('event-campaign-started').checked,
            campaign_completed: document.getElementById('event-campaign-completed').checked
        },
        enabled: document.getElementById('webhook-enabled').checked
    };
    
    console.log('💾 Config to save:', config);

    // Show loading state
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;

    console.log('💾 About to send fetch request...');
    
    // Send to API
    fetch('/api/v1/integration/webhook-config', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(config)
    })
    .then(response => {
        console.log('💾 Response received:', response.status, response.statusText);
        return response.json();
    })
    .then(data => {
        console.log('💾 Response data:', data);
        if (data.success) {
            showSuccess(data.message || 'Webhook configuration saved successfully!');
            // Reload the page to show the new state
            setTimeout(() => location.reload(), 1500);
        } else {
            showError(data.message || 'Failed to save webhook configuration');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error saving webhook configuration: ' + error.message);
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function testWebhook() {
    const url = document.getElementById('webhook-url').value;
    const secret = document.getElementById('webhook-secret').value;

    if (!url || !secret) {
        showWarning('Please fill in webhook URL and secret first.');
        return;
    }

    // Show loading state
    const testBtn = document.querySelector('button[onclick="testWebhook()"]');
    const originalText = testBtn.textContent;
    testBtn.textContent = 'Testing...';
    testBtn.disabled = true;

    // Send test request to API
    fetch('/api/v1/integration/test-webhook', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message || 'Webhook test sent successfully!');
        } else {
            showError(data.message || 'Failed to test webhook');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error testing webhook: ' + error.message);
    })
    .finally(() => {
        testBtn.textContent = originalText;
        testBtn.disabled = false;
    });
}

function debugAPIEndpoint() {
    console.log('🔧 Testing debug API endpoint...');
    
    const testData = {
        url: document.getElementById('webhook-url').value,
        secret: document.getElementById('webhook-secret').value,
        test_from: 'frontend_debug',
        timestamp: new Date().toISOString()
    };
    
    console.log('🔧 Sending test data:', testData);
    
    fetch('/api/v1/integration/debug-webhook', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        body: JSON.stringify(testData)
    })
    .then(response => {
        console.log('🔧 Debug API response status:', response.status);
        console.log('🔧 Debug API response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('🔧 Debug API response data:', data);
        if (data.success) {
            showSuccess('Debug API test successful! Check console for details.');
        } else {
            showError('Debug API test failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('🔧 Debug API error:', error);
        showError('Debug API error: ' + error.message);
    });
}

// Helper functions for showing notifications
function showSuccess(message) {
    console.log('✅ Success:', message);
    alert('✅ ' + message);
}

function showError(message) {
    console.error('❌ Error:', message);
    alert('❌ ' + message);
}

function showWarning(message) {
    console.warn('⚠️ Warning:', message);
    alert('⚠️ ' + message);
}
</script>
@endsection
