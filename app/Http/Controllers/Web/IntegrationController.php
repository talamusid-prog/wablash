<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WebhookConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IntegrationController extends Controller
{
    /**
     * Show integration dashboard
     */
    public function index()
    {
        return view('integration.index');
    }

    /**
     * Show API documentation
     */
    public function documentation()
    {
        return view('integration.documentation');
    }

    /**
     * Show SDK and examples
     */
    public function sdk()
    {
        return view('integration.sdk');
    }

    /**
     * Show API testing page
     */
    public function testing()
    {
        return view('integration.testing');
    }

    /**
     * Show webhook configuration
     */
    public function webhook()
    {
        try {
            // Debug logging
            \Log::info('🐛 Webhook page loaded');
            
            $webhookConfig = WebhookConfig::getCurrentConfig();
            
            \Log::info('🐛 Webhook config retrieved:', [
                'exists' => $webhookConfig ? 'YES' : 'NO',
                'url' => $webhookConfig->url ?? 'NULL',
                'secret' => $webhookConfig->secret ?? 'NULL',
                'enabled' => $webhookConfig->enabled ?? 'NULL',
                'events' => $webhookConfig->events ?? 'NULL',
                'is_new_instance' => !$webhookConfig->exists,
                'updated_at' => $webhookConfig->updated_at ?? 'NULL'
            ]);
            
            // Check database count
            $dbCount = WebhookConfig::count();
            \Log::info('🐛 Database webhook_configs count: ' . $dbCount);
            
            return view('integration.webhook', compact('webhookConfig'));
        } catch (\Exception $e) {
            \Log::error('🐛 Error loading webhook page: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Show API keys management
     */
    public function keys()
    {
        return view('integration.keys');
    }

    /**
     * Show support page
     */
    public function support()
    {
        return view('integration.support');
    }

    /**
     * Download SDK
     */
    public function downloadSdk($language)
    {
        $sdkFiles = [
            'javascript' => [
                'filename' => 'wa-blast-sdk.js',
                'content' => file_get_contents(public_path('examples/wa-blast-sdk.js')),
                'mime' => 'application/javascript'
            ],
            'php' => [
                'filename' => 'WABlastIntegration.php',
                'content' => file_get_contents(public_path('examples/integration-examples.php')),
                'mime' => 'application/x-httpd-php'
            ]
        ];

        if (!isset($sdkFiles[$language])) {
            abort(404);
        }

        $file = $sdkFiles[$language];

        return response($file['content'])
            ->header('Content-Type', $file['mime'])
            ->header('Content-Disposition', 'attachment; filename="' . $file['filename'] . '"');
    }

    /**
     * Get API statistics for dashboard
     */
    public function getApiStats()
    {
        try {
            // Get statistics from API
            $stats = [
                'total_sessions' => \App\Models\WhatsAppSession::count(),
                'active_sessions' => \App\Models\WhatsAppSession::where('status', 'connected')->count(),
                'total_campaigns' => \App\Models\BlastCampaign::count(),
                'running_campaigns' => \App\Models\BlastCampaign::where('status', 'running')->count(),
                'total_contacts' => \App\Models\Phonebook::count(),
                'total_messages_sent' => \App\Models\WhatsAppMessage::where('status', 'sent')->count(),
                'engine_status' => 'running',
                'last_activity' => \App\Models\WhatsAppMessage::latest()->first()?->created_at
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting API stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent API calls
     */
    public function getRecentApiCalls()
    {
        // This would typically come from API logs
        $recentCalls = [
            [
                'endpoint' => '/api/v1/whatsapp/sessions',
                'method' => 'GET',
                'status' => 200,
                'time' => '2 minutes ago'
            ],
            [
                'endpoint' => '/api/v1/integration/send-template',
                'method' => 'POST',
                'status' => 200,
                'time' => '5 minutes ago'
            ],
            [
                'endpoint' => '/api/v1/blast/campaigns',
                'method' => 'GET',
                'status' => 200,
                'time' => '10 minutes ago'
            ],
            [
                'endpoint' => '/api/v1/whatsapp/sessions/1/send',
                'method' => 'POST',
                'status' => 200,
                'time' => '15 minutes ago'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $recentCalls
        ]);
    }

    /**
     * Test API endpoint
     */
    public function testApiEndpoint(Request $request)
    {
        $request->validate([
            'method' => 'required|in:GET,POST,PUT,DELETE',
            'endpoint' => 'required|string',
            'body' => 'nullable|string'
        ]);

        try {
            $method = $request->method;
            $endpoint = $request->endpoint;
            $body = $request->body ? json_decode($request->body, true) : null;

            // Make request to API
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'X-API-Key' => config('app.api_key', 'test-key')
            ])->timeout(30);

            if ($method === 'GET') {
                $apiResponse = $response->get(url($endpoint));
            } else {
                $apiResponse = $response->$method(url($endpoint), $body);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $apiResponse->status(),
                    'body' => $apiResponse->json(),
                    'headers' => $apiResponse->headers()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing API: ' . $e->getMessage()
            ], 500);
        }
    }
} 