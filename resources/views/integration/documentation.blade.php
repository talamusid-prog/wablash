@extends('layouts.app')

@section('title', 'API Documentation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">API Documentation</h1>
        <p class="text-gray-600">Dokumentasi lengkap untuk semua endpoint API WA Blast</p>
    </div>

    <!-- Navigation -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600" onclick="showSection('overview')">
                    Overview
                </button>
                <button class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" onclick="showSection('authentication')">
                    Authentication
                </button>
                <button class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" onclick="showSection('endpoints')">
                    Endpoints
                </button>
                <button class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" onclick="showSection('examples')">
                    Examples
                </button>
                <button class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" onclick="showSection('errors')">
                    Error Codes
                </button>
            </nav>
        </div>
    </div>

    <!-- Overview Section -->
    <div id="overview-section" class="section-content">
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Overview</h2>
            
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Base URL</h3>
                    <div class="bg-gray-100 p-3 rounded font-mono text-sm">
                        {{ url('/api/v1') }}
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Response Format</h3>
                    <div class="bg-gray-100 p-3 rounded font-mono text-sm">
                        JSON
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Quick Start</h3>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded mr-2">1</span>
                        <span class="text-blue-800">Get your API key from the dashboard</span>
                    </div>
                    <div class="flex items-center">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded mr-2">2</span>
                        <span class="text-blue-800">Include API key in your requests</span>
                    </div>
                    <div class="flex items-center">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded mr-2">3</span>
                        <span class="text-blue-800">Start integrating with our endpoints</span>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-900 mb-2">WhatsApp Sessions</h4>
                    <p class="text-green-800 text-sm">Manage WhatsApp sessions, send messages, and handle QR codes</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2">Blast Campaigns</h4>
                    <p class="text-blue-800 text-sm">Create and manage bulk messaging campaigns</p>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h4 class="font-semibold text-purple-900 mb-2">Contact Management</h4>
                    <p class="text-purple-800 text-sm">Import, export, and manage your contact lists</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Authentication Section -->
    <div id="authentication-section" class="section-content hidden">
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Authentication</h2>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">API Key (Recommended)</h3>
                    <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                        curl -H "X-API-Key: your-api-key" \<br>
                        &nbsp;&nbsp;{{ url('/api/v1/integration/system-status') }}
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Bearer Token</h3>
                    <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                        curl -H "Authorization: Bearer your-token" \<br>
                        &nbsp;&nbsp;{{ url('/api/v1/integration/system-status') }}
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Basic Auth</h3>
                    <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                        curl -u "username:password" \<br>
                        &nbsp;&nbsp;{{ url('/api/v1/integration/system-status') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Endpoints Section -->
    <div id="endpoints-section" class="section-content hidden">
        <div class="space-y-8">
            <!-- WhatsApp Sessions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">WhatsApp Sessions</h2>
                
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">GET</span>
                            <code class="text-sm font-mono text-gray-600">/api/v1/whatsapp/sessions</code>
                        </div>
                        <p class="text-gray-700 mb-2">Get all WhatsApp sessions</p>
                        <button onclick="testEndpoint('GET', '/api/v1/whatsapp/sessions')" class="text-blue-600 hover:text-blue-800 text-sm">Test this endpoint</button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">POST</span>
                            <code class="text-sm font-mono text-gray-600">/api/v1/whatsapp/sessions</code>
                        </div>
                        <p class="text-gray-700 mb-2">Create a new WhatsApp session</p>
                        <div class="bg-gray-100 p-3 rounded text-sm font-mono mb-2">
                            {<br>
                            &nbsp;&nbsp;"name": "My Session",<br>
                            &nbsp;&nbsp;"phone_number": "6281234567890"<br>
                            }
                        </div>
                        <button onclick="testEndpoint('POST', '/api/v1/whatsapp/sessions')" class="text-blue-600 hover:text-blue-800 text-sm">Test this endpoint</button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">GET</span>
                            <code class="text-sm font-mono text-gray-600">/api/v1/whatsapp/sessions/{id}/qr</code>
                        </div>
                        <p class="text-gray-700 mb-2">Get QR code for session</p>
                        <button onclick="testEndpoint('GET', '/api/v1/whatsapp/sessions/1/qr')" class="text-blue-600 hover:text-blue-800 text-sm">Test this endpoint</button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">POST</span>
                            <code class="text-sm font-mono text-gray-600">/api/v1/whatsapp/sessions/{id}/send</code>
                        </div>
                        <p class="text-gray-700 mb-2">Send message via WhatsApp</p>
                        <div class="bg-gray-100 p-3 rounded text-sm font-mono mb-2">
                            {<br>
                            &nbsp;&nbsp;"to_number": "6281234567890",<br>
                            &nbsp;&nbsp;"message": "Hello from API!",<br>
                            &nbsp;&nbsp;"message_type": "text"<br>
                            }
                        </div>
                        <button onclick="testEndpoint('POST', '/api/v1/whatsapp/sessions/1/send')" class="text-blue-600 hover:text-blue-800 text-sm">Test this endpoint</button>
                    </div>
                </div>
            </div>

            <!-- Integration Features -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Integration Features</h2>
                
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">GET</span>
                            <code class="text-sm font-mono text-gray-600">/api/v1/integration/system-status</code>
                        </div>
                        <p class="text-gray-700 mb-2">Get system status and statistics</p>
                        <button onclick="testEndpoint('GET', '/api/v1/integration/system-status')" class="text-blue-600 hover:text-blue-800 text-sm">Test this endpoint</button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">POST</span>
                            <code class="text-sm font-mono text-gray-600">/api/v1/integration/send-template</code>
                        </div>
                        <p class="text-gray-700 mb-2">Send template message with variables</p>
                        <div class="bg-gray-100 p-3 rounded text-sm font-mono mb-2">
                            {<br>
                            &nbsp;&nbsp;"session_id": 1,<br>
                            &nbsp;&nbsp;"to_number": "6281234567890",<br>
                            &nbsp;&nbsp;"template": "Halo {name}, ada promo menarik!",<br>
                            &nbsp;&nbsp;"variables": {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;"name": "John Doe"<br>
                            &nbsp;&nbsp;}<br>
                            }
                        </div>
                        <button onclick="testEndpoint('POST', '/api/v1/integration/send-template')" class="text-blue-600 hover:text-blue-800 text-sm">Test this endpoint</button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">POST</span>
                            <code class="text-sm font-mono text-gray-600">/api/v1/integration/bulk-send</code>
                        </div>
                        <p class="text-gray-700 mb-2">Send messages to multiple numbers</p>
                        <div class="bg-gray-100 p-3 rounded text-sm font-mono mb-2">
                            {<br>
                            &nbsp;&nbsp;"session_id": 1,<br>
                            &nbsp;&nbsp;"messages": [<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"to_number": "6281234567890",<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"message": "Hello!"<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                            &nbsp;&nbsp;]<br>
                            }
                        </div>
                        <button onclick="testEndpoint('POST', '/api/v1/integration/bulk-send')" class="text-blue-600 hover:text-blue-800 text-sm">Test this endpoint</button>
                    </div>
                </div>
            </div>

            <!-- Blast Campaigns -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Blast Campaigns</h2>
                
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">GET</span>
                            <code class="text-sm font-mono text-gray-600">/api/v1/blast/campaigns</code>
                        </div>
                        <p class="text-gray-700 mb-2">Get all blast campaigns</p>
                        <button onclick="testEndpoint('GET', '/api/v1/blast/campaigns')" class="text-blue-600 hover:text-blue-800 text-sm">Test this endpoint</button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">POST</span>
                            <code class="text-sm font-mono text-gray-600">/api/v1/blast/campaigns</code>
                        </div>
                        <p class="text-gray-700 mb-2">Create a new blast campaign</p>
                        <div class="bg-gray-100 p-3 rounded text-sm font-mono mb-2">
                            {<br>
                            &nbsp;&nbsp;"name": "Promo Campaign",<br>
                            &nbsp;&nbsp;"message": "Halo {name}, ada promo menarik!",<br>
                            &nbsp;&nbsp;"phone_numbers": ["6281234567890"],<br>
                            &nbsp;&nbsp;"session_id": 1<br>
                            }
                        </div>
                        <button onclick="testEndpoint('POST', '/api/v1/blast/campaigns')" class="text-blue-600 hover:text-blue-800 text-sm">Test this endpoint</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Examples Section -->
    <div id="examples-section" class="section-content hidden">
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Code Examples</h2>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">JavaScript/Node.js</h3>
                    <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                        const axios = require('axios');<br><br>
                        const api = axios.create({<br>
                        &nbsp;&nbsp;baseURL: '{{ url('/api/v1') }}',<br>
                        &nbsp;&nbsp;headers: {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;'X-API-Key': 'your-api-key',<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;'Content-Type': 'application/json'<br>
                        &nbsp;&nbsp;}<br>
                        });<br><br>
                        // Send message<br>
                        const sendMessage = async () => {<br>
                        &nbsp;&nbsp;try {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;const response = await api.post('/whatsapp/sessions/1/send', {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to_number: '6281234567890',<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;message: 'Hello from API!'<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;});<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;console.log(response.data);<br>
                        &nbsp;&nbsp;} catch (error) {<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;console.error('Error:', error.response.data);<br>
                        &nbsp;&nbsp;}<br>
                        };
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">PHP</h3>
                    <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                        $url = '{{ url('/api/v1/whatsapp/sessions/1/send') }}';<br>
                        $data = [<br>
                        &nbsp;&nbsp;'to_number' => '6281234567890',<br>
                        &nbsp;&nbsp;'message' => 'Hello from PHP!'<br>
                        ];<br><br>
                        $ch = curl_init();<br>
                        curl_setopt($ch, CURLOPT_URL, $url);<br>
                        curl_setopt($ch, CURLOPT_POST, true);<br>
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));<br>
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [<br>
                        &nbsp;&nbsp;'X-API-Key: your-api-key',<br>
                        &nbsp;&nbsp;'Content-Type: application/json'<br>
                        ]);<br>
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br><br>
                        $response = curl_exec($ch);<br>
                        curl_close($ch);<br><br>
                        $result = json_decode($response, true);
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Python</h3>
                    <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                        import requests<br><br>
                        url = '{{ url('/api/v1/whatsapp/sessions/1/send') }}'<br>
                        headers = {<br>
                        &nbsp;&nbsp;'X-API-Key': 'your-api-key',<br>
                        &nbsp;&nbsp;'Content-Type': 'application/json'<br>
                        }<br>
                        data = {<br>
                        &nbsp;&nbsp;'to_number': '6281234567890',<br>
                        &nbsp;&nbsp;'message': 'Hello from Python!'<br>
                        }<br><br>
                        response = requests.post(url, headers=headers, json=data)<br>
                        result = response.json()<br>
                        print(result)
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Codes Section -->
    <div id="errors-section" class="section-content hidden">
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Error Codes</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HTTP Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">AUTH001</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Unauthorized access</td>
                            <td class="px-6 py-4 text-sm text-gray-900">401</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">WHATSAPP001</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Invalid session</td>
                            <td class="px-6 py-4 text-sm text-gray-900">404</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">WHATSAPP002</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Session not connected</td>
                            <td class="px-6 py-4 text-sm text-gray-900">400</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">MESSAGE001</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Message send failed</td>
                            <td class="px-6 py-4 text-sm text-gray-900">500</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">CAMPAIGN001</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Campaign not found</td>
                            <td class="px-6 py-4 text-sm text-gray-900">404</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">RATE001</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Rate limit exceeded</td>
                            <td class="px-6 py-4 text-sm text-gray-900">429</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm font-mono text-gray-900">VALID001</td>
                            <td class="px-6 py-4 text-sm text-gray-900">Validation error</td>
                            <td class="px-6 py-4 text-sm text-gray-900">422</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- API Testing Modal -->
    <div id="api-test-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Test API Endpoint</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Method</label>
                        <select id="test-method" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Endpoint</label>
                        <input type="text" id="test-endpoint" class="w-full border border-gray-300 rounded-md px-3 py-2" value="/api/v1/integration/system-status">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Request Body (JSON)</label>
                        <textarea id="test-body" class="w-full border border-gray-300 rounded-md px-3 py-2 h-32" placeholder="{}"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button onclick="closeTestModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                        <button onclick="executeTest()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Test</button>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Response:</h4>
                    <pre id="test-response" class="bg-gray-100 p-3 rounded text-sm overflow-auto max-h-64"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.section-content').forEach(section => {
        section.classList.add('hidden');
    });
    
    // Show selected section
    document.getElementById(sectionName + '-section').classList.remove('hidden');
    
    // Update navigation
    document.querySelectorAll('nav button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    event.target.classList.remove('border-transparent', 'text-gray-500');
    event.target.classList.add('border-blue-500', 'text-blue-600');
}

function testEndpoint(method, endpoint) {
    document.getElementById('test-method').value = method;
    document.getElementById('test-endpoint').value = endpoint;
    document.getElementById('test-body').value = '{}';
    document.getElementById('api-test-modal').classList.remove('hidden');
}

function closeTestModal() {
    document.getElementById('api-test-modal').classList.add('hidden');
}

function executeTest() {
    const method = document.getElementById('test-method').value;
    const endpoint = document.getElementById('test-endpoint').value;
    const body = document.getElementById('test-body').value;
    
    const options = {
        method: method,
        headers: {
            'X-API-Key': '{{ config("app.api_key") }}',
            'Content-Type': 'application/json'
        }
    };
    
    if (method !== 'GET' && body) {
        options.body = body;
    }
    
    fetch(endpoint, options)
        .then(response => response.json())
        .then(data => {
            document.getElementById('test-response').textContent = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            document.getElementById('test-response').textContent = 'Error: ' + error.message;
        });
}
</script>
@endsection 