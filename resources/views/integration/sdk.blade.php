@extends('layouts.app')

@section('title', 'SDK & Examples')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">SDK & Examples</h1>
        <p class="text-gray-600">SDK dan contoh kode untuk integrasi dengan berbagai bahasa pemrograman</p>
    </div>

    <!-- Language Tabs -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600" onclick="showLanguage('javascript')">
                    JavaScript/Node.js
                </button>
                <button class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" onclick="showLanguage('php')">
                    PHP
                </button>
                <button class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" onclick="showLanguage('python')">
                    Python
                </button>
                <button class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700" onclick="showLanguage('curl')">
                    cURL
                </button>
            </nav>
        </div>
    </div>

    <!-- JavaScript/Node.js Section -->
    <div id="javascript-section" class="language-content">
        <div class="space-y-8">
            <!-- Installation -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Installation</h2>
                <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                    npm install axios<br>
                    # or<br>
                    yarn add axios
                </div>
            </div>

            <!-- Basic Setup -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Basic Setup</h2>
                <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                    const axios = require('axios');<br><br>
                    const api = axios.create({<br>
                    &nbsp;&nbsp;baseURL: '{{ url('/api/v1') }}',<br>
                    &nbsp;&nbsp;headers: {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'X-API-Key': 'your-api-key',<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;'Content-Type': 'application/json'<br>
                    &nbsp;&nbsp;}<br>
                    });
                </div>
            </div>

            <!-- Examples -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Examples</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Send Message</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
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
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Send Template Message</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            const sendTemplate = async () => {<br>
                            &nbsp;&nbsp;try {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;const response = await api.post('/integration/send-template', {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;session_id: 1,<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to_number: '6281234567890',<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;template: 'Halo {name}, ada promo menarik!',<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;variables: {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;name: 'John Doe'<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;});<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;console.log(response.data);<br>
                            &nbsp;&nbsp;} catch (error) {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;console.error('Error:', error.response.data);<br>
                            &nbsp;&nbsp;}<br>
                            };
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Bulk Send</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            const bulkSend = async () => {<br>
                            &nbsp;&nbsp;try {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;const response = await api.post('/integration/bulk-send', {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;session_id: 1,<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;messages: [<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to_number: '6281234567890',<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;message: 'Hello 1!'<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to_number: '6281234567891',<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;message: 'Hello 2!'<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;]<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;});<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;console.log(response.data);<br>
                            &nbsp;&nbsp;} catch (error) {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;console.error('Error:', error.response.data);<br>
                            &nbsp;&nbsp;}<br>
                            };
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PHP Section -->
    <div id="php-section" class="language-content hidden">
        <div class="space-y-8">
            <!-- Basic Setup -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Basic Setup</h2>
                <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                    class WABlastAPI {<br>
                    &nbsp;&nbsp;private $baseUrl;<br>
                    &nbsp;&nbsp;private $apiKey;<br><br>
                    &nbsp;&nbsp;public function __construct($baseUrl, $apiKey) {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;$this->baseUrl = $baseUrl;<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;$this->apiKey = $apiKey;<br>
                    &nbsp;&nbsp;}<br><br>
                    &nbsp;&nbsp;private function request($method, $endpoint, $data = null) {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;$url = $this->baseUrl . $endpoint;<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;$headers = [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'X-API-Key: ' . $this->apiKey,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'Content-Type: application/json'<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;];<br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;$ch = curl_init();<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;curl_setopt($ch, CURLOPT_URL, $url);<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);<br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;$response = curl_exec($ch);<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;curl_close($ch);<br><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;return json_decode($response, true);<br>
                    &nbsp;&nbsp;}<br>
                    }
                </div>
            </div>

            <!-- Examples -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Examples</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Send Message</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            $api = new WABlastAPI('{{ url('/api/v1') }}', 'your-api-key');<br><br>
                            $result = $api->request('POST', '/whatsapp/sessions/1/send', [<br>
                            &nbsp;&nbsp;'to_number' => '6281234567890',<br>
                            &nbsp;&nbsp;'message' => 'Hello from PHP!'<br>
                            ]);<br><br>
                            print_r($result);
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Get Sessions</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            $sessions = $api->request('GET', '/whatsapp/sessions');<br>
                            print_r($sessions);
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Python Section -->
    <div id="python-section" class="language-content hidden">
        <div class="space-y-8">
            <!-- Installation -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Installation</h2>
                <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                    pip install requests
                </div>
            </div>

            <!-- Basic Setup -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Basic Setup</h2>
                <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                    import requests<br><br>
                    class WABlastAPI:<br>
                    &nbsp;&nbsp;def __init__(self, base_url, api_key):<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;self.base_url = base_url<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;self.api_key = api_key<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;self.headers = {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'X-API-Key': api_key,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'Content-Type': 'application/json'<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br><br>
                    &nbsp;&nbsp;def request(self, method, endpoint, data=None):<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;url = self.base_url + endpoint<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;response = requests.request(method, url, headers=self.headers, json=data)<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;return response.json()
                </div>
            </div>

            <!-- Examples -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Examples</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Send Message</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            api = WABlastAPI('{{ url('/api/v1') }}', 'your-api-key')<br><br>
                            result = api.request('POST', '/whatsapp/sessions/1/send', {<br>
                            &nbsp;&nbsp;'to_number': '6281234567890',<br>
                            &nbsp;&nbsp;'message': 'Hello from Python!'<br>
                            })<br><br>
                            print(result)
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Get System Status</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            status = api.request('GET', '/integration/system-status')<br>
                            print(status)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- cURL Section -->
    <div id="curl-section" class="language-content hidden">
        <div class="space-y-8">
            <!-- Examples -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Examples</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Get System Status</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            curl -X GET "{{ url('/api/v1/integration/system-status') }}" \<br>
                            &nbsp;&nbsp;-H "X-API-Key: your-api-key"
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Send Message</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            curl -X POST "{{ url('/api/v1/whatsapp/sessions/1/send') }}" \<br>
                            &nbsp;&nbsp;-H "X-API-Key: your-api-key" \<br>
                            &nbsp;&nbsp;-H "Content-Type: application/json" \<br>
                            &nbsp;&nbsp;-d '{<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;"to_number": "6281234567890",<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;"message": "Hello from cURL!"<br>
                            &nbsp;&nbsp;}'
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Send Template Message</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            curl -X POST "{{ url('/api/v1/integration/send-template') }}" \<br>
                            &nbsp;&nbsp;-H "X-API-Key: your-api-key" \<br>
                            &nbsp;&nbsp;-H "Content-Type: application/json" \<br>
                            &nbsp;&nbsp;-d '{<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;"session_id": 1,<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;"to_number": "6281234567890",<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;"template": "Halo {name}, ada promo menarik!",<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;"variables": {<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"name": "John Doe"<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                            &nbsp;&nbsp;}'
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Bulk Send</h3>
                        <div class="bg-gray-800 text-green-400 p-4 rounded font-mono text-sm">
                            curl -X POST "{{ url('/api/v1/integration/bulk-send') }}" \<br>
                            &nbsp;&nbsp;-H "X-API-Key: your-api-key" \<br>
                            &nbsp;&nbsp;-H "Content-Type: application/json" \<br>
                            &nbsp;&nbsp;-d '{<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;"session_id": 1,<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;"messages": [<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"to_number": "6281234567890",<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"message": "Hello 1!"<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"to_number": "6281234567891",<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"message": "Hello 2!"<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;]<br>
                            &nbsp;&nbsp;}'
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Download SDK -->
    <div class="bg-white rounded-lg shadow p-6 mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Download SDK</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">JavaScript SDK</h3>
                <p class="text-gray-600 mb-4">Complete JavaScript SDK with examples</p>
                <a href="{{ route('integration.download-sdk', 'javascript') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download
                </a>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">PHP SDK</h3>
                <p class="text-gray-600 mb-4">Complete PHP SDK with examples</p>
                <a href="{{ route('integration.download-sdk', 'php') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showLanguage(language) {
    // Hide all language sections
    document.querySelectorAll('.language-content').forEach(section => {
        section.classList.add('hidden');
    });
    
    // Show selected language section
    document.getElementById(language + '-section').classList.remove('hidden');
    
    // Update navigation
    document.querySelectorAll('nav button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    event.target.classList.remove('border-transparent', 'text-gray-500');
    event.target.classList.add('border-blue-500', 'text-blue-600');
}
</script>
@endsection 