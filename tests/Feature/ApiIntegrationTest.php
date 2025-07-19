<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\WhatsAppSession;
use App\Models\BlastCampaign;
use App\Models\Phonebook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ApiIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $apiKey = 'test-api-key-123';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set API key for testing
        config(['api.valid_api_keys' => [$this->apiKey]]);
    }

    /**
     * Test system status endpoint
     */
    public function test_system_status_endpoint()
    {
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get('/api/v1/integration/system-status');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total_sessions',
                        'active_sessions',
                        'total_campaigns',
                        'running_campaigns',
                        'total_contacts',
                        'total_messages_sent',
                        'engine_status',
                        'last_activity'
                    ]
                ]);
    }

    /**
     * Test WhatsApp sessions endpoints
     */
    public function test_whatsapp_sessions_endpoints()
    {
        // Test get sessions
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get('/api/v1/whatsapp/sessions');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'sessions'
                ]);

        // Test create session
        $sessionData = [
            'name' => 'Test Session',
            'phone_number' => '6281234567890'
        ];

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post('/api/v1/whatsapp/sessions', $sessionData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'session'
                ]);

        $sessionId = $response->json('session.id');

        // Test get session details
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get("/api/v1/whatsapp/sessions/{$sessionId}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data'
                ]);

        // Test send message
        $messageData = [
            'to_number' => '6281234567890',
            'message' => 'Test message from API'
        ];

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post("/api/v1/whatsapp/sessions/{$sessionId}/send", $messageData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message'
                ]);
    }

    /**
     * Test template message endpoint
     */
    public function test_template_message_endpoint()
    {
        $session = WhatsAppSession::factory()->create([
            'status' => 'connected'
        ]);

        $templateData = [
            'session_id' => $session->id,
            'to_number' => '6281234567890',
            'template' => 'Halo {name}, ada promo menarik untuk Anda: {promo_message}',
            'variables' => [
                'name' => 'John Doe',
                'promo_message' => 'Diskon 50% untuk semua produk!'
            ]
        ];

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post('/api/v1/integration/send-template', $templateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'message_id',
                        'processed_message'
                    ]
                ]);
    }

    /**
     * Test bulk send endpoint
     */
    public function test_bulk_send_endpoint()
    {
        $session = WhatsAppSession::factory()->create([
            'status' => 'connected'
        ]);

        $bulkData = [
            'session_id' => $session->id,
            'messages' => [
                [
                    'to_number' => '6281234567890',
                    'message' => 'Halo, ini pesan pertama'
                ],
                [
                    'to_number' => '6281234567891',
                    'message' => 'Halo, ini pesan kedua'
                ]
            ]
        ];

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post('/api/v1/integration/bulk-send', $bulkData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'total',
                        'success_count',
                        'failed_count',
                        'results'
                    ]
                ]);
    }

    /**
     * Test blast campaigns endpoints
     */
    public function test_blast_campaigns_endpoints()
    {
        $session = WhatsAppSession::factory()->create([
            'status' => 'connected'
        ]);

        // Test create campaign
        $campaignData = [
            'name' => 'Test Campaign',
            'message' => 'Halo {name}, ada promo menarik untuk Anda!',
            'phone_numbers' => ['6281234567890', '6281234567891'],
            'session_id' => $session->id
        ];

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post('/api/v1/blast/campaigns', $campaignData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);

        $campaignId = $response->json('data.id');

        // Test start campaign
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post("/api/v1/blast/campaigns/{$campaignId}/start");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);

        // Test get campaign statistics
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get("/api/v1/blast/campaigns/{$campaignId}/statistics");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'total',
                        'sent',
                        'failed',
                        'pending',
                        'success_rate'
                    ]
                ]);
    }

    /**
     * Test contact management endpoints
     */
    public function test_contact_management_endpoints()
    {
        // Test import contacts
        $importData = [
            'contacts' => [
                [
                    'name' => 'John Doe',
                    'phone_number' => '6281234567890',
                    'email' => 'john@example.com',
                    'group' => 'VIP'
                ],
                [
                    'name' => 'Jane Smith',
                    'phone_number' => '6281234567891',
                    'email' => 'jane@example.com',
                    'group' => 'Regular'
                ]
            ],
            'overwrite_existing' => false
        ];

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post('/api/v1/integration/import-contacts', $importData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'imported',
                        'updated',
                        'skipped',
                        'errors'
                    ]
                ]);

        // Test export contacts
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get('/api/v1/integration/export-contacts?format=json&group=VIP&status=active');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'format',
                        'total_contacts',
                        'contacts'
                    ]
                ]);

        // Test get contacts
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get('/api/v1/phonebook?search=John&group=VIP&status=active');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'current_page',
                        'data',
                        'total'
                    ]
                ]);

        // Test add contact
        $contactData = [
            'name' => 'New Contact',
            'phone_number' => '6281234567892',
            'email' => 'new@example.com',
            'group' => 'Test',
            'notes' => 'Test contact'
        ];

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post('/api/v1/phonebook', $contactData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);

        $contactId = $response->json('data.id');

        // Test update contact
        $updateData = [
            'name' => 'Updated Contact',
            'phone_number' => '6281234567892',
            'email' => 'updated@example.com',
            'group' => 'Updated',
            'notes' => 'Updated contact'
        ];

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->put("/api/v1/phonebook/{$contactId}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);

        // Test delete contact
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->delete("/api/v1/phonebook/{$contactId}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message'
                ]);
    }

    /**
     * Test webhook configuration endpoints
     */
    public function test_webhook_configuration_endpoints()
    {
        // Test get webhook config
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get('/api/v1/integration/webhook-config');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'enabled',
                        'url',
                        'events',
                        'secret'
                    ]
                ]);

        // Test set webhook config
        $webhookData = [
            'enabled' => true,
            'url' => 'https://test.com/webhook',
            'events' => ['message_received', 'campaign_completed'],
            'secret' => 'test-secret'
        ];

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post('/api/v1/integration/webhook-config', $webhookData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);

        // Test webhook
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post('/api/v1/integration/test-webhook');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ]);
    }

    /**
     * Test authentication methods
     */
    public function test_authentication_methods()
    {
        // Test API key authentication
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get('/api/v1/integration/system-status');

        $response->assertStatus(200);

        // Test unauthorized access
        $response = $this->get('/api/v1/integration/system-status');
        $response->assertStatus(401);

        // Test invalid API key
        $response = $this->withHeaders([
            'X-API-Key' => 'invalid-key'
        ])->get('/api/v1/integration/system-status');

        $response->assertStatus(401);
    }

    /**
     * Test rate limiting
     */
    public function test_rate_limiting()
    {
        // Make multiple requests to test rate limiting
        for ($i = 0; $i < 65; $i++) {
            $response = $this->withHeaders([
                'X-API-Key' => $this->apiKey
            ])->get('/api/v1/integration/system-status');

            if ($i < 60) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    /**
     * Test error handling
     */
    public function test_error_handling()
    {
        // Test invalid session
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get('/api/v1/whatsapp/sessions/999');

        $response->assertStatus(404);

        // Test invalid campaign
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->get('/api/v1/blast/campaigns/999');

        $response->assertStatus(404);

        // Test validation error
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey
        ])->post('/api/v1/whatsapp/sessions', []);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors'
                ]);
    }
} 