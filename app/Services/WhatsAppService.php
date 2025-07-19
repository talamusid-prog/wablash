<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class WhatsAppService
{
    private $engineUrl;
    private $apiKey;
    private $sessions = [];
    
    public function __construct()
    {
        $this->engineUrl = env('WHATSAPP_ENGINE_URL', 'http://localhost:3000');
        $this->apiKey = env('WHATSAPP_ENGINE_API_KEY', 'wa_blast_api_key_2024');
    }
    
    /**
     * Get engine status
     */
    public function getEngineStatus(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-API-Key' => $this->getApiKey()
                ])
                ->get("{$this->getEngineUrl()}/status");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => 'running',
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'status' => 'stopped',
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting engine status', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get engine URL
     */
    public function getEngineUrl(): string
    {
        return config('services.whatsapp.engine_url', 'http://localhost:3000');
    }

    /**
     * Get API key for engine
     */
    public function getApiKey(): string
    {
        return config('services.whatsapp.api_key', 'your_api_key');
    }

    /**
     * Create session in engine
     */
    public function createSession(string $sessionId, string $name, string $phoneNumber): array
    {
        try {
            Log::info('Creating session in engine', [
                'session_id' => $sessionId,
                'name' => $name,
                'phone_number' => $phoneNumber
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $this->getApiKey(),
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->getEngineUrl()}/sessions/create", [
                    'sessionId' => $sessionId,
                    'name' => $name,
                    'phoneNumber' => $phoneNumber
                ]);

            Log::info('Engine response for create session', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error creating session in engine', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get QR code from engine
     */
    public function getQrCode(string $sessionId): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-API-Key' => $this->getApiKey()
                ])
                ->get("{$this->getEngineUrl()}/sessions/{$sessionId}/qr");

            Log::info('Engine response for QR code', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting QR code from engine', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get session status from engine
     */
    public function getSessionStatus(string $sessionId): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-API-Key' => $this->getApiKey()
                ])
                ->get("{$this->getEngineUrl()}/sessions/{$sessionId}/status");

            Log::info('Engine response for session status', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting session status from engine', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Reconnect session in engine
     */
    public function reconnectSession(string $sessionId): array
    {
        try {
            Log::info('Reconnecting session in engine', [
                'session_id' => $sessionId
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $this->getApiKey(),
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->getEngineUrl()}/sessions/{$sessionId}/reconnect");

            Log::info('Engine response for reconnect session', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error reconnecting session in engine', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send message via engine
     */
    public function sendMessage(string $sessionId, string $toNumber, string $message, string $type = 'text'): array
    {
        try {
            // Validate inputs
            if (empty($sessionId) || empty($toNumber)) {
                Log::error('Missing required parameters for sendMessage', [
                    'session_id' => $sessionId,
                    'to_number' => $toNumber,
                    'message' => $message,
                    'type' => $type
                ]);
                return [
                    'success' => false,
                    'error' => 'Session ID and phone number are required'
                ];
            }
            
            // For text type, message is required
            if ($type === 'text' && empty($message)) {
                Log::error('Message is required for text type', [
                    'session_id' => $sessionId,
                    'to_number' => $toNumber,
                    'type' => $type
                ]);
                return [
                    'success' => false,
                    'error' => 'Message is required for text type'
                ];
            }
            
            // Clean phone number
            $cleanedNumber = $this->cleanPhoneNumber($toNumber);
            Log::info('Phone number cleaned', [
                'original' => $toNumber,
                'cleaned' => $cleanedNumber
            ]);

            Log::info('Sending message', [
                'session_id' => $sessionId,
                'to' => $toNumber,
                'cleaned_number' => $cleanedNumber,
                'message' => $message,
                'type' => $type
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $this->getApiKey(),
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->getEngineUrl()}/sessions/{$sessionId}/send", [
                    'to' => $cleanedNumber,
                    'message' => $message,
                    'type' => $type
                ]);

            Log::info('Engine response for send message', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['data']['message_id'] ?? null,
                    'data' => $data
                ];
            }

            Log::error('Engine error for send message', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\InvalidArgumentException $e) {
            Log::error('Invalid argument in sendMessage', [
                'session_id' => $sessionId,
                'to_number' => $toNumber,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Invalid phone number format: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Error sending message via engine', [
                'session_id' => $sessionId,
                'to_number' => $toNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to send message: ' . $e->getMessage()
            ];
        }
    }
    
    public function deleteSession($sessionId)
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'X-API-Key' => $this->getApiKey()
            ])->delete("{$this->getEngineUrl()}/sessions/{$sessionId}");
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Session deleted successfully'
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Failed to delete session'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error deleting session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function cleanPhoneNumber($phoneNumber)
    {
        // Validate input
        if (empty($phoneNumber)) {
            Log::error('Empty phone number provided', [
                'phone_number' => $phoneNumber,
                'type' => gettype($phoneNumber)
            ]);
            throw new \InvalidArgumentException('Phone number must not be empty');
        }
        
        // Convert to string if not already
        if (!is_string($phoneNumber)) {
            $phoneNumber = (string) $phoneNumber;
        }
        
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Validate cleaned number
        if (empty($cleaned) || strlen($cleaned) < 10) {
            Log::error('Phone number too short after cleaning', [
                'original' => $phoneNumber,
                'cleaned' => $cleaned,
                'length' => strlen($cleaned)
            ]);
            throw new \InvalidArgumentException('Phone number must be at least 10 digits');
        }
        
        // If number starts with 0, replace with 62
        if (strlen($cleaned) >= 10 && $cleaned[0] === '0') {
            $cleaned = '62' . substr($cleaned, 1);
        }
        
        // If number doesn't start with country code, add 62
        if (strlen($cleaned) === 10) {
            $cleaned = '62' . $cleaned;
        }
        
        // Ensure number starts with 62
        if (strlen($cleaned) >= 10 && substr($cleaned, 0, 2) !== '62') {
            $cleaned = '62' . $cleaned;
        }
        
        Log::info('Phone number cleaned', [
            'original' => $phoneNumber,
            'cleaned' => $cleaned
        ]);
        
        return $cleaned;
    }
    
    public function getSession($sessionId)
    {
        return $this->sessions[$sessionId] ?? null;
    }
    
    public function updateSessionStatus($sessionId, $status)
    {
        if (isset($this->sessions[$sessionId])) {
            $this->sessions[$sessionId]['status'] = $status;
            return true;
        }
        return false;
    }

    /**
     * Grab kontak grup dari WhatsApp session
     */
    public function grabGroupContacts(string $sessionId): array
    {
        try {
            Log::info('Grabbing group contacts from WhatsApp session', [
                'session_id' => $sessionId
            ]);

            $response = Http::timeout(60)
                ->withHeaders([
                    'X-API-Key' => $this->getApiKey(),
                    'Content-Type' => 'application/json'
                ])
                ->get("{$this->getEngineUrl()}/sessions/{$sessionId}/groups");

            Log::info('Engine response for grab group contacts', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error grabbing group contacts from engine', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Grab kontak individual dari WhatsApp session
     */
    public function grabIndividualContacts(string $sessionId): array
    {
        try {
            Log::info('Grabbing individual contacts from WhatsApp session', [
                'session_id' => $sessionId
            ]);

            $response = Http::timeout(60)
                ->withHeaders([
                    'X-API-Key' => $this->getApiKey(),
                    'Content-Type' => 'application/json'
                ])
                ->get("{$this->getEngineUrl()}/sessions/{$sessionId}/contacts");

            Log::info('Engine response for grab individual contacts', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error grabbing individual contacts from engine', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Grab semua kontak (grup dan individual) dari WhatsApp session
     */
    public function grabAllContacts(string $sessionId): array
    {
        try {
            Log::info('Grabbing all contacts from WhatsApp session', [
                'session_id' => $sessionId
            ]);

            $response = Http::timeout(120)
                ->withHeaders([
                    'X-API-Key' => $this->getApiKey(),
                    'Content-Type' => 'application/json'
                ])
                ->get("{$this->getEngineUrl()}/sessions/{$sessionId}/all-contacts");

            Log::info('Engine response for grab all contacts', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error grabbing all contacts from engine', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send message with attachment via engine
     */
    public function sendMessageWithAttachment(string $sessionId, string $toNumber, string $message, string $filePath, string $attachmentPath): array
    {
        try {
            // Clean phone number
            $cleanedNumber = $this->cleanPhoneNumber($toNumber);
            Log::info('Phone number cleaned for attachment message', [
                'original' => $toNumber,
                'cleaned' => $cleanedNumber
            ]);

            Log::info('Sending message with attachment', [
                'session_id' => $sessionId,
                'to' => $toNumber,
                'cleaned_number' => $cleanedNumber,
                'message' => $message,
                'file_path' => $filePath,
                'attachment_path' => $attachmentPath
            ]);

            // Check if file exists
            if (!file_exists($filePath)) {
                Log::error('Attachment file not found', [
                    'session_id' => $sessionId,
                    'file_path' => $filePath
                ]);
                return [
                    'success' => false,
                    'error' => 'Attachment file not found'
                ];
            }

            // Get file info
            $fileInfo = pathinfo($filePath);
            $fileName = $fileInfo['basename'];
            $fileSize = filesize($filePath);
            $mimeType = mime_content_type($filePath);

            Log::info('File info for attachment', [
                'session_id' => $sessionId,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType
            ]);

            // Prepare multipart form data using cURL for better file handling
            $ch = curl_init();
            
            $postData = [
                'to' => $cleanedNumber,
                'message' => $message,
                'type' => $this->getMessageTypeFromMimeType($mimeType)
            ];
            
            // Add file using CURLFile for proper binary file handling
            $postData['file'] = new \CURLFile($filePath, $mimeType, $fileName);
            
            Log::info('CURLFile created for attachment', [
                'session_id' => $sessionId,
                'file_path' => $filePath,
                'mime_type' => $mimeType,
                'file_name' => $fileName,
                'file_exists' => file_exists($filePath),
                'file_size' => filesize($filePath)
            ]);
            
            curl_setopt_array($ch, [
                CURLOPT_URL => "{$this->getEngineUrl()}/sessions/{$sessionId}/send-media",
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => [
                    'X-API-Key: ' . $this->getApiKey(),
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
            
            $responseBody = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            Log::info('cURL response for attachment upload', [
                'session_id' => $sessionId,
                'http_code' => $httpCode,
                'response_body' => $responseBody,
                'curl_error' => $error
            ]);
            
            if ($error) {
                throw new \Exception('cURL error: ' . $error);
            }
            
            // Create a response object that mimics Laravel's HTTP response
            $response = new class($httpCode, $responseBody) {
                private $statusCode;
                private $responseBody;
                
                public function __construct($status, $body) {
                    $this->statusCode = $status;
                    $this->responseBody = $body;
                }
                
                public function status() {
                    return $this->statusCode;
                }
                
                public function body() {
                    return $this->responseBody;
                }
                
                public function successful() {
                    return $this->statusCode >= 200 && $this->statusCode < 300;
                }
                
                public function json() {
                    return json_decode($this->responseBody, true);
                }
            };

            Log::info('Engine response for send message with attachment', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['data']['message_id'] ?? null,
                    'data' => $data
                ];
            }

            Log::error('Engine error for send message with attachment', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error sending message with attachment via engine', [
                'session_id' => $sessionId,
                'to_number' => $toNumber,
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get message type from MIME type
     */
    private function getMessageTypeFromMimeType(string $mimeType): string
    {
        if (strpos($mimeType, 'image/') === 0) {
            return 'image';
        } elseif (strpos($mimeType, 'video/') === 0) {
            return 'video';
        } elseif (strpos($mimeType, 'audio/') === 0) {
            return 'audio';
        } elseif ($mimeType === 'application/pdf') {
            return 'document';
        } elseif (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ])) {
            return 'document';
        } else {
            return 'document';
        }
    }
    
} 