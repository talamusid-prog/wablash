@extends('layouts.app')

@section('title', 'Webhook Configuration')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Webhook Configuration</h1>
        <p class="text-gray-600">Konfigurasi webhook untuk menerima real-time events dari WA Blast</p>
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
                            <input type="url" id="webhook-url" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://your-domain.com/webhook" value="https://your-domain.com/webhook">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Secret Key</label>
                            <div class="flex">
                                <input type="text" id="webhook-secret" class="flex-1 border border-gray-300 rounded-l-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Your webhook secret" value="wa-blast-webhook-secret">
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
                                    <input type="checkbox" id="event-message-sent" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-700">Message Sent</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-message-delivered" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-700">Message Delivered</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-message-failed" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-700">Message Failed</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-session-connected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-700">Session Connected</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-session-disconnected" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-700">Session Disconnected</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-campaign-started" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-700">Campaign Started</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="event-campaign-completed" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-700">Campaign Completed</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <div class="flex items-center">
                                <input type="checkbox" id="webhook-enabled" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                <span class="ml-2 text-sm text-gray-700">Enable webhook</span>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Save Configuration
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
document.getElementById('webhook-form').addEventListener('submit', function(e) {
    e.preventDefault();
    saveWebhookConfig();
});

function generateSecret() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 32; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('webhook-secret').value = result;
}

function saveWebhookConfig() {
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

    // Simulate saving configuration
    console.log('Saving webhook config:', config);
    
    // Show success message
    showSuccess('Webhook configuration saved successfully!');
}

function testWebhook() {
    const url = document.getElementById('webhook-url').value;
    const secret = document.getElementById('webhook-secret').value;

    if (!url || !secret) {
        showWarning('Please fill in webhook URL and secret first.');
        return;
    }

    // Simulate webhook test
    const testEvent = {
        event: 'webhook.test',
        timestamp: new Date().toISOString(),
        data: {
            message: 'This is a test webhook event from WA Blast',
            test: true
        }
    };

    console.log('Testing webhook:', testEvent);
    
    // Show success message
    showSuccess('Webhook test sent successfully! Check your webhook endpoint for the test event.');
}
</script>
@endsection 