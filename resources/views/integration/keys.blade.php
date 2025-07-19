@extends('layouts.app')

@section('title', 'API Keys Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">API Keys Management</h1>
        <p class="text-gray-600">Kelola API keys untuk aplikasi Anda</p>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Create New API Key -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Create New API Key</h2>
            </div>
            <div class="p-6">
                <form id="api-key-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                            <input type="text" id="app-name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="My Application" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="app-description" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 h-20" placeholder="Description of your application"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" id="perm-read" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-700">Read (GET requests)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="perm-write" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    <span class="ml-2 text-sm text-gray-700">Write (POST, PUT, DELETE requests)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" id="perm-admin" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Admin (Full access)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rate Limit</label>
                            <select id="rate-limit" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="1000">1,000 requests/hour</option>
                                <option value="5000">5,000 requests/hour</option>
                                <option value="10000">10,000 requests/hour</option>
                                <option value="unlimited">Unlimited</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiration</label>
                            <select id="expiration" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="never">Never</option>
                                <option value="30">30 days</option>
                                <option value="90">90 days</option>
                                <option value="365">1 year</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Generate API Key
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current API Keys -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Current API Keys</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4" id="api-keys-list">
                    @if($apiKeys->count() > 0)
                        @foreach($apiKeys as $apiKey)
                        <div class="border border-gray-200 rounded-lg p-4" data-api-key-id="{{ $apiKey->id }}">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-medium text-gray-900">{{ $apiKey->name }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $apiKey->getStatusBadgeClass() }}">
                                    {{ $apiKey->getStatusText() }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">{{ $apiKey->description ?: 'No description' }}</p>
                            <div class="flex items-center space-x-2 mb-3">
                                <input type="text" value="{{ $apiKey->key }}" class="flex-1 bg-gray-100 border border-gray-300 rounded px-3 py-2 text-sm font-mono" readonly>
                                <button onclick="copyToClipboard(this.previousElementSibling)" class="px-3 py-2 bg-gray-100 border border-gray-300 rounded hover:bg-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>Created: {{ $apiKey->created_at->format('M d, Y') }}</span>
                                <span>Last used: {{ $apiKey->last_used_at ? $apiKey->last_used_at->diffForHumans() : 'Never' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500 mt-1">
                                <span>Requests: {{ number_format($apiKey->usage_count) }} / {{ $apiKey->rate_limit == 0 ? 'Unlimited' : number_format($apiKey->rate_limit) }}</span>
                                <button onclick="revokeKey({{ $apiKey->id }})" class="text-red-600 hover:text-red-800">Revoke</button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No API keys</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new API key.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- API Key Usage -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">API Key Usage</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($totalRequests) }}</div>
                    <div class="text-sm text-gray-600">Total Requests</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $activeKeys }}</div>
                    <div class="text-sm text-gray-600">Active Keys</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $expiredKeys }}</div>
                    <div class="text-sm text-gray-600">Expired Keys</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $totalKeys }}</div>
                    <div class="text-sm text-gray-600">Total Keys</div>
                </div>
            </div>

            <!-- Usage Chart -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Request History (Last 7 Days)</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-end space-x-2 h-32">
                        @php
                            $maxRequests = max(array_column($usageData, 'requests'));
                            $maxRequests = $maxRequests > 0 ? $maxRequests : 1;
                        @endphp
                        @foreach($usageData as $data)
                        <div class="flex-1 bg-blue-200 rounded-t" style="height: {{ ($data['requests'] / $maxRequests) * 100 }}%"></div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        @foreach($usageData as $data)
                        <span>{{ $data['day'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Tips -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Security Best Practices</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Keep API Keys Secure</h3>
                            <p class="text-sm text-gray-600">Never expose your API keys in client-side code or public repositories.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Use Environment Variables</h3>
                            <p class="text-sm text-gray-600">Store API keys in environment variables or secure configuration files.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Rotate Keys Regularly</h3>
                            <p class="text-sm text-gray-600">Generate new API keys periodically and revoke old ones.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Monitor Usage</h3>
                            <p class="text-sm text-gray-600">Regularly check API usage patterns for any suspicious activity.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Set Appropriate Permissions</h3>
                            <p class="text-sm text-gray-600">Only grant the permissions that your application actually needs.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Use HTTPS</h3>
                            <p class="text-sm text-gray-600">Always use HTTPS when making API requests to ensure data security.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('api-key-form').addEventListener('submit', function(e) {
    e.preventDefault();
    generateApiKey();
});

function generateApiKey() {
    const appName = document.getElementById('app-name').value;
    const description = document.getElementById('app-description').value;
    const permissions = [];
    
    if (document.getElementById('perm-read').checked) permissions.push('read');
    if (document.getElementById('perm-write').checked) permissions.push('write');
    if (document.getElementById('perm-admin').checked) permissions.push('admin');
    
    const rateLimit = document.getElementById('rate-limit').value;
    const expiration = document.getElementById('expiration').value;
    
    if (!appName) {
        showWarning('Please enter an application name.');
        return;
    }

    // Calculate expiration date
    let expiresAt = null;
    if (expiration !== 'never') {
        expiresAt = new Date();
        expiresAt.setDate(expiresAt.getDate() + parseInt(expiration));
    }

    // Send request to create API key
    fetch('{{ route("integration.keys.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            name: appName,
            description: description,
            permissions: permissions,
            rate_limit: rateLimit === 'unlimited' ? 0 : parseInt(rateLimit),
            expires_at: expiresAt ? expiresAt.toISOString().split('T')[0] : null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showGeneratedApiKey(data.data);
            document.getElementById('api-key-form').reset();
            // Reload the page to show the new API key
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Error creating API key. Please try again.');
    });
}

function showGeneratedApiKey(apiKey) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">API Key Generated</h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">Your new API key has been generated. Please copy it now as it won't be shown again.</p>
                <div class="flex items-center space-x-2 mb-4">
                    <input type="text" value="${apiKey.key}" class="flex-1 bg-gray-100 border border-gray-300 rounded px-3 py-2 text-sm font-mono" readonly>
                    <button onclick="copyToClipboard(this.previousElementSibling)" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Copy
                    </button>
                </div>
                <div class="flex space-x-3">
                    <button onclick="this.closest('.fixed').remove()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Done
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function copyToClipboard(element) {
    element.select();
    document.execCommand('copy');
    
    // Show feedback
    const originalText = element.nextElementSibling.textContent;
    element.nextElementSibling.textContent = 'Copied!';
    setTimeout(() => {
        element.nextElementSibling.textContent = originalText;
    }, 2000);
}

function revokeKey(apiKeyId) {
    showConfirm('Are you sure you want to revoke this API key? This action cannot be undone.', 'Konfirmasi Revoke', 'Ya, Revoke', 'Batal').then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route('integration.keys.index') }}/${apiKeyId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('API key has been revoked successfully.');
                    // Remove the API key from the DOM
                    document.querySelector(`[data-api-key-id="${apiKeyId}"]`).remove();
                    // Reload the page to update statistics
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error revoking API key. Please try again.');
            });
        }
    });
}
</script>
@endsection 