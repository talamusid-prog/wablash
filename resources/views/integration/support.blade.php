@extends('layouts.app')

@section('title', 'Support & Help')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Support & Help</h1>
        <p class="text-gray-600">Bantuan dan dukungan teknis untuk integrasi API WA Blast</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Quick Help -->
        <div class="lg:col-span-2 space-y-6">
            <!-- FAQ Section -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Frequently Asked Questions</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg">
                            <button class="w-full px-4 py-3 text-left focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="toggleFAQ(this)">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">How do I get started with the API?</span>
                                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                            <div class="px-4 pb-4 hidden">
                                <p class="text-gray-600">To get started with the API, you need to:</p>
                                <ol class="list-decimal list-inside mt-2 text-gray-600">
                                    <li>Generate an API key from the API Keys section</li>
                                    <li>Include the API key in your requests using the X-API-Key header</li>
                                    <li>Start with the system status endpoint to test your connection</li>
                                    <li>Refer to the documentation for specific endpoint details</li>
                                </ol>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg">
                            <button class="w-full px-4 py-3 text-left focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="toggleFAQ(this)">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">What authentication methods are supported?</span>
                                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                            <div class="px-4 pb-4 hidden">
                                <p class="text-gray-600">We support multiple authentication methods:</p>
                                <ul class="list-disc list-inside mt-2 text-gray-600">
                                    <li><strong>API Key:</strong> Include X-API-Key header (recommended)</li>
                                    <li><strong>Bearer Token:</strong> Include Authorization: Bearer header</li>
                                    <li><strong>Basic Auth:</strong> Username and password authentication</li>
                                </ul>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg">
                            <button class="w-full px-4 py-3 text-left focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="toggleFAQ(this)">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">How do I handle rate limits?</span>
                                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                            <div class="px-4 pb-4 hidden">
                                <p class="text-gray-600">Rate limits are enforced to ensure fair usage:</p>
                                <ul class="list-disc list-inside mt-2 text-gray-600">
                                    <li>Check the X-RateLimit-* headers in responses</li>
                                    <li>Implement exponential backoff for retries</li>
                                    <li>Consider upgrading your plan for higher limits</li>
                                    <li>Use bulk endpoints when possible to reduce requests</li>
                                </ul>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg">
                            <button class="w-full px-4 py-3 text-left focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="toggleFAQ(this)">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">What should I do if I get a 401 error?</span>
                                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                            <div class="px-4 pb-4 hidden">
                                <p class="text-gray-600">A 401 error indicates authentication failure:</p>
                                <ul class="list-disc list-inside mt-2 text-gray-600">
                                    <li>Verify your API key is correct and included in the request</li>
                                    <li>Check that your API key hasn't expired or been revoked</li>
                                    <li>Ensure you're using the correct authentication method</li>
                                    <li>Contact support if the issue persists</li>
                                </ul>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg">
                            <button class="w-full px-4 py-3 text-left focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="toggleFAQ(this)">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">How do I set up webhooks?</span>
                                    <svg class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                            <div class="px-4 pb-4 hidden">
                                <p class="text-gray-600">To set up webhooks:</p>
                                <ol class="list-decimal list-inside mt-2 text-gray-600">
                                    <li>Go to the Webhook Configuration page</li>
                                    <li>Enter your webhook URL and secret key</li>
                                    <li>Select which events you want to receive</li>
                                    <li>Test the webhook to ensure it's working</li>
                                    <li>Monitor webhook delivery status</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Codes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Common Error Codes</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solution</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-900">401</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Unauthorized</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Check your API key and authentication method</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-900">403</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Forbidden</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Your API key doesn't have permission for this action</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-900">404</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Not Found</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Check the endpoint URL and resource ID</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-900">422</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Validation Error</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Check your request body and required fields</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-900">429</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Rate Limit Exceeded</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Wait before making more requests or upgrade your plan</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-900">500</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Internal Server Error</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Contact support if this persists</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact & Resources -->
        <div class="space-y-6">
            <!-- Contact Support -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Contact Support</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Email Support</p>
                                <p class="text-sm text-gray-600">support@wa-blast.com</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Live Chat</p>
                                <p class="text-sm text-gray-600">Available 24/7</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Documentation</p>
                                <p class="text-sm text-gray-600">Complete API reference</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="mailto:support@wa-blast.com" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 block text-center">
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Links</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('integration.documentation') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            API Documentation
                        </a>
                        <a href="{{ route('integration.sdk') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                            </svg>
                            SDK & Examples
                        </a>
                        <a href="{{ route('integration.testing') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            API Testing
                        </a>
                        <a href="{{ route('integration.webhook') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Webhook Setup
                        </a>
                        <a href="{{ route('integration.keys') }}" class="flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            API Keys
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">System Status</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">API Service</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Operational
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">WhatsApp Engine</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Operational
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Database</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Operational
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Webhooks</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Operational
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View detailed status →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Community & Resources -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Community & Resources</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 mb-1">Tutorials</h3>
                    <p class="text-sm text-gray-600">Step-by-step guides</p>
                </div>

                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 mb-1">Community</h3>
                    <p class="text-sm text-gray-600">Developer forum</p>
                </div>

                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 mb-1">Examples</h3>
                    <p class="text-sm text-gray-600">Code samples</p>
                </div>

                <div class="text-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 mb-1">Ideas</h3>
                    <p class="text-sm text-gray-600">Feature requests</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('svg');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}
</script>
@endsection 