@extends('layouts.app')

@section('title', 'API Testing')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">API Testing</h1>
        <p class="text-gray-600">Test API endpoints langsung dari browser</p>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- API Testing Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Test API Endpoint</h2>
            </div>
            <div class="p-6">
                <form id="api-test-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Method</label>
                            <select id="test-method" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Endpoint</label>
                            <input type="text" id="test-endpoint" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" value="/api/v1/integration/system-status" placeholder="/api/v1/whatsapp/sessions">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                            <input type="text" id="test-api-key" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" value="{{ config('app.api_key', 'test-key') }}" placeholder="Your API Key">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Request Body (JSON)</label>
                            <textarea id="test-body" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 h-32" placeholder="{}"></textarea>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Test Endpoint
                            </button>
                            <button type="button" onclick="clearForm()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Clear
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Response -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Response</h2>
            </div>
            <div class="p-6">
                <div id="response-loading" class="hidden">
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="ml-3 text-gray-600">Testing endpoint...</span>
                    </div>
                </div>
                
                <div id="response-content" class="hidden">
                    <div class="mb-4">
                        <div class="flex items-center space-x-4 mb-2">
                            <span class="text-sm font-medium text-gray-700">Status:</span>
                            <span id="response-status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"></span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-700">Time:</span>
                            <span id="response-time" class="text-sm text-gray-600"></span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Response Body</label>
                        <pre id="response-body" class="bg-gray-100 p-4 rounded text-sm overflow-auto max-h-96"></pre>
                    </div>
                </div>
                
                <div id="response-error" class="hidden">
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Error</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p id="error-message"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Test Examples -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Quick Test Examples</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">System Status</h3>
                    <p class="text-sm text-gray-600 mb-3">Get system status and statistics</p>
                    <button onclick="loadExample('GET', '/api/v1/integration/system-status', '{}')" class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                        Test
                    </button>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">Get Sessions</h3>
                    <p class="text-sm text-gray-600 mb-3">Get all WhatsApp sessions</p>
                    <button onclick="loadExample('GET', '/api/v1/whatsapp/sessions', '{}')" class="w-full bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                        Test
                    </button>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">Send Message</h3>
                    <p class="text-sm text-gray-600 mb-3">Send a test message</p>
                    <button onclick="loadExample('POST', '/api/v1/whatsapp/sessions/1/send', '{\"to_number\": \"6281234567890\", \"message\": \"Hello from API test!\"}')" class="w-full bg-purple-600 text-white px-3 py-2 rounded text-sm hover:bg-purple-700">
                        Test
                    </button>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">Template Message</h3>
                    <p class="text-sm text-gray-600 mb-3">Send template message with variables</p>
                    <button onclick="loadExample('POST', '/api/v1/integration/send-template', '{\"session_id\": 1, \"to_number\": \"6281234567890\", \"template\": \"Halo {name}, ada promo menarik!\", \"variables\": {\"name\": \"John Doe\"}}')" class="w-full bg-orange-600 text-white px-3 py-2 rounded text-sm hover:bg-orange-700">
                        Test
                    </button>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">Get Campaigns</h3>
                    <p class="text-sm text-gray-600 mb-3">Get all blast campaigns</p>
                    <button onclick="loadExample('GET', '/api/v1/blast/campaigns', '{}')" class="w-full bg-indigo-600 text-white px-3 py-2 rounded text-sm hover:bg-indigo-700">
                        Test
                    </button>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">Get Contacts</h3>
                    <p class="text-sm text-gray-600 mb-3">Get all contacts</p>
                    <button onclick="loadExample('GET', '/api/v1/phonebook', '{}')" class="w-full bg-teal-600 text-white px-3 py-2 rounded text-sm hover:bg-teal-700">
                        Test
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- API History -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Tests</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="test-history">
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900" colspan="5">No tests yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let testHistory = [];

document.getElementById('api-test-form').addEventListener('submit', function(e) {
    e.preventDefault();
    testEndpoint();
});

function testEndpoint() {
    const method = document.getElementById('test-method').value;
    const endpoint = document.getElementById('test-endpoint').value;
    const apiKey = document.getElementById('test-api-key').value;
    const body = document.getElementById('test-body').value;

    // Show loading
    document.getElementById('response-loading').classList.remove('hidden');
    document.getElementById('response-content').classList.add('hidden');
    document.getElementById('response-error').classList.add('hidden');

    const startTime = Date.now();

    fetch(endpoint, {
        method: method,
        headers: {
            'X-API-Key': apiKey,
            'Content-Type': 'application/json'
        },
        body: method !== 'GET' && body ? body : null
    })
    .then(response => {
        const endTime = Date.now();
        const responseTime = endTime - startTime;

        return response.json().then(data => ({
            status: response.status,
            data: data,
            time: responseTime
        }));
    })
    .then(result => {
        // Hide loading
        document.getElementById('response-loading').classList.add('hidden');

        // Show response
        document.getElementById('response-content').classList.remove('hidden');
        
        // Update response details
        const statusElement = document.getElementById('response-status');
        statusElement.textContent = result.status;
        statusElement.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
            result.status >= 200 && result.status < 300 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
        }`;

        document.getElementById('response-time').textContent = `${result.time}ms`;
        document.getElementById('response-body').textContent = JSON.stringify(result.data, null, 2);

        // Add to history
        addToHistory(method, endpoint, result.status, result.time);
    })
    .catch(error => {
        // Hide loading
        document.getElementById('response-loading').classList.add('hidden');
        
        // Show error
        document.getElementById('response-error').classList.remove('hidden');
        document.getElementById('error-message').textContent = error.message;
    });
}

function loadExample(method, endpoint, body) {
    document.getElementById('test-method').value = method;
    document.getElementById('test-endpoint').value = endpoint;
    document.getElementById('test-body').value = body;
}

function clearForm() {
    document.getElementById('test-method').value = 'GET';
    document.getElementById('test-endpoint').value = '/api/v1/integration/system-status';
    document.getElementById('test-body').value = '{}';
}

function addToHistory(method, endpoint, status, time) {
    const test = {
        method: method,
        endpoint: endpoint,
        status: status,
        time: time,
        timestamp: new Date().toLocaleTimeString()
    };

    testHistory.unshift(test);
    if (testHistory.length > 10) {
        testHistory.pop();
    }

    updateHistoryTable();
}

function updateHistoryTable() {
    const tbody = document.getElementById('test-history');
    
    if (testHistory.length === 0) {
        tbody.innerHTML = '<tr><td class="px-6 py-4 text-sm text-gray-900" colspan="5">No tests yet</td></tr>';
        return;
    }

    tbody.innerHTML = testHistory.map(test => `
        <tr>
            <td class="px-6 py-4 text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                    test.method === 'GET' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
                }">
                    ${test.method}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 font-mono">${test.endpoint}</td>
            <td class="px-6 py-4 text-sm text-gray-900">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                    test.status >= 200 && test.status < 300 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                }">
                    ${test.status}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">${test.time}ms</td>
            <td class="px-6 py-4 text-sm text-gray-500">
                <button onclick="loadExample('${test.method}', '${test.endpoint}', '{}')" class="text-blue-600 hover:text-blue-800">
                    Retry
                </button>
            </td>
        </tr>
    `).join('');
}
</script>
@endsection 