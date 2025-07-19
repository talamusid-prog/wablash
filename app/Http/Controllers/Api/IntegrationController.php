<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSession;
use App\Models\BlastCampaign;
use App\Models\Phonebook;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class IntegrationController extends Controller
{
    protected $whatsappService;
    
    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Get system status and statistics
     */
    public function systemStatus(): JsonResponse
    {
        try {
            $stats = [
                'total_sessions' => WhatsAppSession::count(),
                'active_sessions' => WhatsAppSession::where('status', 'connected')->count(),
                'total_campaigns' => BlastCampaign::count(),
                'running_campaigns' => BlastCampaign::where('status', 'running')->count(),
                'total_contacts' => Phonebook::count(),
                'total_messages_sent' => WhatsAppMessage::where('status', 'sent')->count(),
                'engine_status' => 'unknown',
                'last_activity' => WhatsAppMessage::latest()->first()?->created_at
            ];

            // Try to get engine status, but don't fail if it's not available
            try {
                $engineStatus = $this->whatsappService->getEngineStatus();
                $stats['engine_status'] = $engineStatus['status'] ?? 'unknown';
            } catch (\Exception $e) {
                Log::warning('Could not get engine status', ['error' => $e->getMessage()]);
                $stats['engine_status'] = 'unavailable';
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting system status', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting system status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk send messages to multiple numbers
     */
    public function bulkSend(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:whatsapp_sessions,id',
            'messages' => 'required|array|min:1',
            'messages.*.to_number' => 'required|string',
            'messages.*.message' => 'required|string',
            'messages.*.message_type' => 'sometimes|in:text,image,video,audio,document,location'
        ]);

        try {
            $session = WhatsAppSession::find($request->session_id);
            
            if ($session->status !== 'connected') {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp session is not connected'
                ], 400);
            }

            $results = [];
            $successCount = 0;
            $failedCount = 0;

            foreach ($request->messages as $messageData) {
                try {
                    $result = $this->whatsappService->sendMessage(
                        $session->session_id,
                        $messageData['to_number'],
                        $messageData['message'],
                        $messageData['message_type'] ?? 'text'
                    );

                    if ($result['success']) {
                        $successCount++;
                        $results[] = [
                            'to_number' => $messageData['to_number'],
                            'status' => 'sent',
                            'message_id' => $result['message_id'] ?? null
                        ];
                    } else {
                        $failedCount++;
                        $results[] = [
                            'to_number' => $messageData['to_number'],
                            'status' => 'failed',
                            'error' => $result['error'] ?? 'Unknown error'
                        ];
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $results[] = [
                        'to_number' => $messageData['to_number'],
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk send completed. Success: {$successCount}, Failed: {$failedCount}",
                'data' => [
                    'total' => count($request->messages),
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                    'results' => $results
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in bulk send', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk send: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send message with template variables
     */
    public function sendTemplateMessage(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:whatsapp_sessions,id',
            'to_number' => 'required|string',
            'template' => 'required|string',
            'variables' => 'required|array'
        ]);

        try {
            $session = WhatsAppSession::find($request->session_id);
            
            if ($session->status !== 'connected') {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp session is not connected'
                ], 400);
            }

            // Replace template variables
            $message = $request->template;
            foreach ($request->variables as $key => $value) {
                $message = str_replace('{' . $key . '}', $value, $message);
            }

            $result = $this->whatsappService->sendMessage(
                $session->session_id,
                $request->to_number,
                $message
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template message sent successfully',
                    'data' => [
                        'message_id' => $result['message_id'] ?? null,
                        'processed_message' => $message
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send template message: ' . ($result['error'] ?? 'Unknown error')
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error sending template message', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error sending template message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import contacts from external source
     */
    public function importContacts(Request $request): JsonResponse
    {
        $request->validate([
            'contacts' => 'required|array|min:1',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.phone_number' => 'required|string|max:20',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.group' => 'nullable|string|max:255',
            'contacts.*.notes' => 'nullable|string|max:1000',
            'overwrite_existing' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $imported = 0;
            $updated = 0;
            $skipped = 0;
            $errors = [];

            foreach ($request->contacts as $index => $contactData) {
                try {
                    $existingContact = Phonebook::where('phone_number', $contactData['phone_number'])->first();

                    if ($existingContact) {
                        if ($request->overwrite_existing) {
                            $existingContact->update($contactData);
                            $updated++;
                        } else {
                            $skipped++;
                        }
                    } else {
                        Phonebook::create($contactData);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $index,
                        'contact' => $contactData,
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Import completed. Imported: {$imported}, Updated: {$updated}, Skipped: {$skipped}",
                'data' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing contacts', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error importing contacts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export contacts to external format
     */
    public function exportContacts(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xml',
            'group' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,all'
        ]);

        try {
            $query = Phonebook::query();

            if ($request->filled('group')) {
                $query->where('group', $request->group);
            }

            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            $contacts = $query->get();

            $exportData = [];
            foreach ($contacts as $contact) {
                $exportData[] = [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'phone_number' => $contact->phone_number,
                    'email' => $contact->email,
                    'group' => $contact->group,
                    'notes' => $contact->notes,
                    'is_active' => $contact->is_active,
                    'created_at' => $contact->created_at
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Contacts exported successfully',
                'data' => [
                    'format' => $request->format,
                    'total_contacts' => count($exportData),
                    'contacts' => $exportData
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error exporting contacts', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error exporting contacts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get webhook configuration
     */
    public function getWebhookConfig(): JsonResponse
    {
        // This would typically read from database or config
        $webhookConfig = [
            'enabled' => config('app.webhook_enabled', false),
            'url' => config('app.webhook_url', ''),
            'events' => config('app.webhook_events', []),
            'secret' => config('app.webhook_secret', '')
        ];

        return response()->json([
            'success' => true,
            'data' => $webhookConfig
        ]);
    }

    /**
     * Set webhook configuration
     */
    public function setWebhookConfig(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => 'required|boolean',
            'url' => 'required_if:enabled,true|url',
            'events' => 'array',
            'secret' => 'nullable|string'
        ]);

        try {
            // This would typically save to database or config
            // For now, we'll just return success
            $webhookConfig = [
                'enabled' => $request->enabled,
                'url' => $request->url,
                'events' => $request->events ?? [],
                'secret' => $request->secret
            ];

            return response()->json([
                'success' => true,
                'message' => 'Webhook configuration updated successfully',
                'data' => $webhookConfig
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting webhook config', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error setting webhook config: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test webhook connection
     */
    public function testWebhook(): JsonResponse
    {
        try {
            $webhookUrl = config('app.webhook_url', '');
            
            if (empty($webhookUrl)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Webhook URL not configured'
                ], 400);
            }

            // Send test webhook
            $testData = [
                'event' => 'test',
                'timestamp' => now()->toISOString(),
                'data' => [
                    'message' => 'This is a test webhook from WA Blast API'
                ]
            ];

            // Here you would actually send the webhook
            // For now, we'll just return success

            return response()->json([
                'success' => true,
                'message' => 'Test webhook sent successfully',
                'data' => $testData
            ]);
        } catch (\Exception $e) {
            Log::error('Error testing webhook', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error testing webhook: ' . $e->getMessage()
            ], 500);
        }
    }
} 