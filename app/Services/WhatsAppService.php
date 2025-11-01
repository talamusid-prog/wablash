<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsAppSession;

class WhatsAppService
{
    protected $engineUrl;
    protected $apiKey;
    protected $sessions = [];

    public function __construct()
    {
        $this->engineUrl = env('WHATSAPP_ENGINE_URL', 'http://localhost:3000');
        $this->apiKey = env('WHATSAPP_ENGINE_API_KEY', 'your_api_key');
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
     * Get fallback engine URLs to try if primary fails (connection refused/timeouts)
     */
    private function getEngineUrlCandidates(): array
    {
        $primary = rtrim($this->getEngineUrl(), '/');
        // Prioritaskan 127.0.0.1 agar tidak terjebak resolusi IPv6 localhost -> ::1
        $candidates = ['http://127.0.0.1:3000', 'http://localhost:3000'];
        if (!in_array($primary, $candidates, true)) {
            array_unshift($candidates, $primary);
        }
        // De-duplicate dengan menjaga urutan prioritas pertama muncul pertama dipakai
        $seen = [];
        $ordered = [];
        foreach ($candidates as $u) {
            if (!isset($seen[$u])) { $seen[$u] = true; $ordered[] = $u; }
        }
        return $ordered;
    }

    /**
     * Perform HTTP request with fallback engine URLs when connection fails.
     */
    private function requestWithFallback(string $method, string $path, array $body = null, int $timeoutSeconds = 10)
    {
        $lastResponse = null;
        $lastError = null;
        foreach ($this->getEngineUrlCandidates() as $base) {
            try {
                $url = $base . $path;
                $req = Http::timeout($timeoutSeconds)
                    ->withHeaders([
                        'X-API-Key' => $this->getApiKey(),
                        'Content-Type' => 'application/json'
                    ]);
                $response = match (strtoupper($method)) {
                    'GET' => $req->get($url),
                    'POST' => $req->post($url, $body ?? []),
                    'DELETE' => $req->delete($url),
                    default => $req->get($url)
                };
                $lastResponse = $response;
                if ($response->successful()) {
                    return $response;
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning('Engine request failed, trying fallback', [
                    'method' => $method,
                    'path' => $path,
                    'base' => $base,
                    'error' => $lastError
                ]);
                continue;
            }
        }
        // If we reach here, either we have a non-successful response or an exception
        if ($lastResponse) {
            return $lastResponse;
        }
        throw new \RuntimeException($lastError ?: 'Engine request failed');
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
            $response = $this->requestWithFallback('GET', "/sessions/{$sessionId}/qr", null, 10);

            // Avoid logging full base64 QR body to keep logs readable
            Log::info('Engine response for QR code', [
                'session_id' => $sessionId,
                'status' => $response->status(),
                'body_length' => strlen($response->body())
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
            $response = $this->requestWithFallback('GET', "/sessions/{$sessionId}/status", null, 10);

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
    public function reconnectSession(string $sessionId, bool $fresh = false): array
    {
        try {
            Log::info('Reconnecting session in engine', [
                'session_id' => $sessionId,
                'fresh' => $fresh
            ]);

            $query = $fresh ? '?fresh=1' : '';
            $response = $this->requestWithFallback('POST', "/sessions/{$sessionId}/reconnect{$query}", [], 30);

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
     * Send message via engine with automatic reconnection and engine health check
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
                    'error' => 'Session ID dan nomor telepon diperlukan'
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
                    'error' => 'Pesan diperlukan untuk tipe teks'
                ];
            }
            
            // Clean phone number
            $cleanedNumber = $this->cleanPhoneNumber($toNumber);
            Log::info('Nomor telepon telah dibersihkan', [
                'original' => $toNumber,
                'cleaned' => $cleanedNumber
            ]);

            Log::info('Mengirim pesan', [
                'session_id' => $sessionId,
                'to' => $toNumber,
                'cleaned_number' => $cleanedNumber,
                'message' => $message,
                'type' => $type
            ]);

            // Check engine status first
            $engineStatus = $this->getEngineStatus();
            if (!$engineStatus['success']) {
                Log::error('Engine tidak berjalan', [
                    'session_id' => $sessionId,
                    'engine_status' => $engineStatus
                ]);
                return [
                    'success' => false,
                    'error' => 'WhatsApp engine tidak berjalan: ' . $engineStatus['error']
                ];
            }

            // Implement a more robust retry mechanism
            $maxRetries = 5; // Increase retry attempts
            $retryDelay = 8; // Increase delay between retries
            
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                Log::info("Percobaan mengirim pesan {$attempt} dari {$maxRetries}", [
                    'session_id' => $sessionId
                ]);
                
                // Check session status before sending
                $statusResult = $this->getSessionStatus($sessionId);
                
                // Log the raw status result for debugging
                Log::info('Hasil status sesi mentah', [
                    'session_id' => $sessionId,
                    'attempt' => $attempt,
                    'result' => $statusResult
                ]);
                
                // If session not found in engine or connection error, try to recreate it
                if (!$statusResult['success'] && 
                    (strpos($statusResult['error'], 'Session not found') !== false || 
                     strpos($statusResult['error'], 'cURL error') !== false ||
                     strpos($statusResult['error'], 'Session closed') !== false)) {
                    
                    Log::warning('Sesi tidak ditemukan di engine atau kesalahan koneksi, mencoba membuat ulang', [
                        'session_id' => $sessionId,
                        'attempt' => $attempt,
                        'error' => $statusResult['error']
                    ]);
                    
                    // Get session data from database
                    $sessionModel = WhatsAppSession::where('session_id', $sessionId)->first();
                    if ($sessionModel) {
                        Log::info('Data sesi dari database', [
                            'session_id' => $sessionId,
                            'name' => $sessionModel->name,
                            'phone_number' => $sessionModel->phone_number,
                            'status' => $sessionModel->status
                        ]);
                        
                        // Remove auth data to force fresh login
                        $this->removeAuthData($sessionId);
                        
                        // Recreate session in engine with fresh flag
                        Log::info('Membuat ulang sesi di engine dengan flag fresh', [
                            'session_id' => $sessionId,
                            'attempt' => $attempt
                        ]);
                        
                        $reconnectResult = $this->reconnectSession($sessionId, true);
                        Log::info('Hasil pembuatan ulang sesi', [
                            'session_id' => $sessionId,
                            'attempt' => $attempt,
                            'result' => $reconnectResult
                        ]);
                        
                        if ($reconnectResult['success']) {
                            Log::info('Sesi berhasil dibuat ulang, menunggu inisialisasi', [
                                'session_id' => $sessionId,
                                'attempt' => $attempt
                            ]);
                            // Wait longer for initialization
                            sleep(15);
                            
                            // Check status again
                            $statusResult = $this->getSessionStatus($sessionId);
                            Log::info('Status sesi setelah pembuatan ulang', [
                                'session_id' => $sessionId,
                                'attempt' => $attempt,
                                'result' => $statusResult
                            ]);
                        } else {
                            Log::error('Gagal membuat ulang sesi', [
                                'session_id' => $sessionId,
                                'attempt' => $attempt,
                                'error' => $reconnectResult['error']
                            ]);
                            // Continue to next attempt
                            if ($attempt < $maxRetries) {
                                Log::info('Menunggu sebelum percobaan berikutnya', [
                                    'session_id' => $sessionId,
                                    'attempt' => $attempt,
                                    'delay' => $retryDelay
                                ]);
                                sleep($retryDelay);
                            }
                            continue;
                        }
                    } else {
                        Log::error('Sesi tidak ditemukan di database', [
                            'session_id' => $sessionId,
                            'attempt' => $attempt
                        ]);
                        return [
                            'success' => false,
                            'error' => 'Sesi tidak ditemukan di database'
                        ];
                    }
                }
                
                // Extract status from response
                $sessionStatus = null;
                if ($statusResult['success']) {
                    $statusData = $statusResult['data'];
                    
                    // Handle different response structures
                    if (isset($statusData['data']['status'])) {
                        $sessionStatus = $statusData['data']['status'];
                    } elseif (isset($statusData['status'])) {
                        $sessionStatus = $statusData['status'];
                    }
                }
                
                Log::info('Status sesi sebelum mengirim', [
                    'session_id' => $sessionId,
                    'attempt' => $attempt,
                    'status' => $sessionStatus
                ]);
                
                // If session is not connected, try to reconnect
                if ($sessionStatus !== 'connected') {
                    Log::warning('Sesi tidak terhubung, mencoba menghubungkan kembali', [
                        'session_id' => $sessionId,
                        'attempt' => $attempt,
                        'current_status' => $sessionStatus
                    ]);
                    
                    // Try to reconnect the session with fresh flag
                    $reconnectResult = $this->reconnectSession($sessionId, true);
                    Log::info('Hasil percobaan menghubungkan kembali', [
                        'session_id' => $sessionId,
                        'attempt' => $attempt,
                        'result' => $reconnectResult
                    ]);
                    
                    if ($reconnectResult['success']) {
                        // Wait longer for reconnection
                        sleep(15);
                        
                        // Check status again
                        $statusResult = $this->getSessionStatus($sessionId);
                        Log::info('Status sesi setelah percobaan menghubungkan kembali', [
                            'session_id' => $sessionId,
                            'attempt' => $attempt,
                            'result' => $statusResult
                        ]);
                        
                        if ($statusResult['success']) {
                            $newStatusData = $statusResult['data'];
                            $newSessionStatus = null;
                            
                            // Handle different response structures
                            if (isset($newStatusData['data']['status'])) {
                                $newSessionStatus = $newStatusData['data']['status'];
                            } elseif (isset($newStatusData['status'])) {
                                $newSessionStatus = $newStatusData['status'];
                            }
                            
                            if ($newSessionStatus === 'connected') {
                                Log::info('Sesi berhasil terhubung kembali', [
                                    'session_id' => $sessionId,
                                    'attempt' => $attempt,
                                    'new_status' => $newSessionStatus
                                ]);
                                $sessionStatus = $newSessionStatus;
                            } else {
                                Log::warning('Sesi terhubung kembali tetapi tidak terhubung', [
                                    'session_id' => $sessionId,
                                    'attempt' => $attempt,
                                    'new_status' => $newSessionStatus
                                ]);
                            }
                        }
                    } else {
                        Log::error('Gagal menghubungkan kembali sesi', [
                            'session_id' => $sessionId,
                            'attempt' => $attempt,
                            'error' => $reconnectResult['error']
                        ]);
                    }
                }
                
                // If session is now connected, try to send the message
                if ($sessionStatus === 'connected') {
                    Log::info('Sesi terhubung, mencoba mengirim pesan', [
                        'session_id' => $sessionId,
                        'attempt' => $attempt
                    ]);
                    
                    $response = $this->requestWithFallback('POST', "/sessions/{$sessionId}/send", [
                        'to' => $cleanedNumber,
                        'message' => $message,
                        'type' => $type
                    ], 45); // Increase timeout

                    Log::info('Respons engine untuk mengirim pesan', [
                        'session_id' => $sessionId,
                        'attempt' => $attempt,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        Log::info('Pesan berhasil dikirim', [
                            'session_id' => $sessionId,
                            'attempt' => $attempt,
                            'message_id' => $data['data']['message_id'] ?? null
                        ]);
                        return [
                            'success' => true,
                            'message_id' => $data['data']['message_id'] ?? null,
                            'data' => $data
                        ];
                    }

                    // Log error
                    Log::error('Kesalahan engine saat mengirim pesan', [
                        'session_id' => $sessionId,
                        'attempt' => $attempt,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);

                    // If it's a session issue, we'll retry after reconnection
                    $responseBody = $response->body();
                    if (strpos($responseBody, 'Session not found') !== false || 
                        strpos($responseBody, 'Session is not connected') !== false || 
                        strpos($responseBody, 'Session disconnected during send attempt') !== false ||
                        strpos($responseBody, 'Session state check failed') !== false ||
                        strpos($responseBody, 'Session closed') !== false) {
                        
                        Log::warning('Masalah sesi terdeteksi saat percobaan mengirim', [
                            'session_id' => $sessionId,
                            'attempt' => $attempt,
                            'error' => $responseBody
                        ]);
                        
                        // Mark session as disconnected in database
                        $sessionModel = WhatsAppSession::where('session_id', $sessionId)->first();
                        if ($sessionModel) {
                            $sessionModel->update(['status' => 'disconnected']);
                            Log::info('Status sesi di database diperbarui menjadi disconnected', [
                                'session_id' => $sessionId
                            ]);
                        }
                        
                        // If this is not the last attempt, force a fresh reconnect
                        if ($attempt < $maxRetries) {
                            Log::info('Memaksa pembuatan ulang sesi dengan flag fresh', [
                                'session_id' => $sessionId,
                                'attempt' => $attempt
                            ]);
                            $this->reconnectSession($sessionId, true);
                            sleep(15); // Wait longer before retry
                        }
                    }
                } else {
                    Log::error('Sesi tidak terhubung setelah semua percobaan menghubungkan kembali', [
                        'session_id' => $sessionId,
                        'attempt' => $attempt,
                        'status' => $sessionStatus
                    ]);
                    
                    // If this is not the last attempt, force a fresh reconnect
                    if ($attempt < $maxRetries) {
                        Log::info('Memaksa pembuatan ulang sesi dengan flag fresh karena sesi tidak terhubung', [
                            'session_id' => $sessionId,
                            'attempt' => $attempt
                        ]);
                        $this->reconnectSession($sessionId, true);
                        sleep(15); // Wait longer before retry
                    }
                }
                
                // If this is not the last attempt, wait before retrying
                if ($attempt < $maxRetries) {
                    Log::info('Menunggu sebelum percobaan berikutnya', [
                        'session_id' => $sessionId,
                        'attempt' => $attempt,
                        'delay' => $retryDelay
                    ]);
                    sleep($retryDelay);
                }
            }

            // If we get here, all attempts failed
            return [
                'success' => false,
                'error' => 'Gagal mengirim pesan setelah ' . $maxRetries . ' percobaan. Sesi mungkin terputus atau WhatsApp Web mengalami masalah. Silakan scan ulang QR code.'
            ];

        } catch (\InvalidArgumentException $e) {
            Log::error('Argumen tidak valid dalam sendMessage', [
                'session_id' => $sessionId,
                'to_number' => $toNumber,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Format nomor telepon tidak valid: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Kesalahan saat mengirim pesan melalui engine', [
                'session_id' => $sessionId,
                'to_number' => $toNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Send message with simplified approach
     */
    public function sendMessageSimple(string $sessionId, string $toNumber, string $message): array
    {
        try {
            // Validate inputs
            if (empty($sessionId) || empty($toNumber)) {
                return [
                    'success' => false,
                    'error' => 'Session ID dan nomor telepon diperlukan'
                ];
            }
            
            if (empty($message)) {
                return [
                    'success' => false,
                    'error' => 'Pesan diperlukan'
                ];
            }
            
            // Clean phone number
            $cleanedNumber = $this->cleanPhoneNumber($toNumber);

            // Check engine status
            $engineStatus = $this->getEngineStatus();
            if (!$engineStatus['success']) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp engine tidak berjalan: ' . $engineStatus['error']
                ];
            }

            // Check session status
            $statusResult = $this->getSessionStatus($sessionId);
            if (!$statusResult['success']) {
                return [
                    'success' => false,
                    'error' => 'Gagal mendapatkan status session: ' . $statusResult['error']
                ];
            }

            // Extract session status
            $statusData = $statusResult['data'];
            $sessionStatus = null;
            
            if (isset($statusData['data']['status'])) {
                $sessionStatus = $statusData['data']['status'];
            } elseif (isset($statusData['status'])) {
                $sessionStatus = $statusData['status'];
            }

            // If not connected, try to reconnect
            if ($sessionStatus !== 'connected') {
                Log::info('Session not connected, attempting to reconnect', [
                    'session_id' => $sessionId,
                    'current_status' => $sessionStatus
                ]);
                
                $reconnectResult = $this->reconnectSession($sessionId, false);
                if ($reconnectResult['success']) {
                    sleep(5);
                    $statusResult = $this->getSessionStatus($sessionId);
                    if ($statusResult['success']) {
                        $newStatusData = $statusResult['data'];
                        if (isset($newStatusData['data']['status'])) {
                            $sessionStatus = $newStatusData['data']['status'];
                        } elseif (isset($newStatusData['status'])) {
                            $sessionStatus = $newStatusData['status'];
                        }
                    }
                }
            }

            // If still not connected, return error
            if ($sessionStatus !== 'connected') {
                return [
                    'success' => false,
                    'error' => 'Session tidak terhubung. Status saat ini: ' . $sessionStatus
                ];
            }

            // Send message with simplified approach
            $response = $this->requestWithFallback('POST', "/sessions/{$sessionId}/send-simple", [
                'to' => $cleanedNumber,
                'message' => $message
            ], 30);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['data']['message_id'] ?? null,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Error in sendMessageSimple', [
                'session_id' => $sessionId,
                'to_number' => $toNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Gagal mengirim pesan: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Remove auth data for session to force fresh login
     */
    private function removeAuthData(string $sessionId): void
    {
        try {
            $authDir = storage_path("app/.wwebjs_auth/{$sessionId}");
            if (file_exists($authDir)) {
                $this->deleteDirectory($authDir);
                Log::info('Removed auth data for session', [
                    'session_id' => $sessionId,
                    'auth_dir' => $authDir
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to remove auth data', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Delete directory recursively
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!file_exists($dir)) {
            return true;
        }
        
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        
        return rmdir($dir);
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
        if (!isset($this->sessions[$sessionId])) {
            $this->sessions[$sessionId] = [];
        }
        $this->sessions[$sessionId]['status'] = $status;
        return true;
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

            // Check engine status first
            $engineStatus = $this->getEngineStatus();
            if (!$engineStatus['success']) {
                Log::error('Engine is not running', [
                    'session_id' => $sessionId,
                    'engine_status' => $engineStatus
                ]);
                return [
                    'success' => false,
                    'error' => 'WhatsApp engine is not running: ' . $engineStatus['error']
                ];
            }

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

            // Check engine status first
            $engineStatus = $this->getEngineStatus();
            if (!$engineStatus['success']) {
                Log::error('Engine is not running', [
                    'session_id' => $sessionId,
                    'engine_status' => $engineStatus
                ]);
                return [
                    'success' => false,
                    'error' => 'WhatsApp engine is not running: ' . $engineStatus['error']
                ];
            }

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

            // Check engine status first
            $engineStatus = $this->getEngineStatus();
            if (!$engineStatus['success']) {
                Log::error('Engine is not running', [
                    'session_id' => $sessionId,
                    'engine_status' => $engineStatus
                ]);
                return [
                    'success' => false,
                    'error' => 'WhatsApp engine is not running: ' . $engineStatus['error']
                ];
            }

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
     * Periksa dan perbaiki session WhatsApp
     */
    public function checkAndFixSession(string $sessionId): array
    {
        try {
            Log::info('Memeriksa dan memperbaiki session', [
                'session_id' => $sessionId
            ]);

            // Cek status engine
            $engineStatus = $this->getEngineStatus();
            if (!$engineStatus['success']) {
                return [
                    'success' => false,
                    'error' => 'Engine tidak berjalan: ' . $engineStatus['error']
                ];
            }

            // Cek status session
            $statusResult = $this->getSessionStatus($sessionId);
            
            // Jika session tidak ditemukan, coba buat ulang
            if (!$statusResult['success'] && strpos($statusResult['error'], 'Session not found') !== false) {
                Log::info('Session tidak ditemukan, mencoba membuat ulang', [
                    'session_id' => $sessionId
                ]);
                
                // Dapatkan data session dari database
                $sessionModel = WhatsAppSession::where('session_id', $sessionId)->first();
                if (!$sessionModel) {
                    return [
                        'success' => false,
                        'error' => 'Session tidak ditemukan di database'
                    ];
                }
                
                // Buat ulang session
                $createResult = $this->createSession($sessionId, $sessionModel->name, $sessionModel->phone_number);
                if (!$createResult['success']) {
                    return [
                        'success' => false,
                        'error' => 'Gagal membuat ulang session: ' . $createResult['error']
                    ];
                }
                
                // Tunggu sebentar
                sleep(3);
                
                // Cek status lagi
                $statusResult = $this->getSessionStatus($sessionId);
            }
            
            // Jika session tidak terhubung, coba reconnect
            if ($statusResult['success']) {
                $statusData = $statusResult['data'];
                $sessionStatus = null;
                
                if (isset($statusData['data']['status'])) {
                    $sessionStatus = $statusData['data']['status'];
                } elseif (isset($statusData['status'])) {
                    $sessionStatus = $statusData['status'];
                }
                
                if ($sessionStatus !== 'connected') {
                    Log::info('Session tidak terhubung, mencoba reconnect', [
                        'session_id' => $sessionId,
                        'current_status' => $sessionStatus
                    ]);
                    
                    // Coba reconnect dengan flag fresh
                    $reconnectResult = $this->reconnectSession($sessionId, true);
                    if ($reconnectResult['success']) {
                        sleep(10); // Tunggu lebih lama untuk reconnect fresh
                        return [
                            'success' => true,
                            'message' => 'Session dalam proses reconnect, silakan coba lagi dalam beberapa menit',
                            'status' => 'reconnecting'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'error' => 'Gagal reconnect session: ' . $reconnectResult['error']
                        ];
                    }
                } else {
                    return [
                        'success' => true,
                        'message' => 'Session sudah terhubung',
                        'status' => 'connected'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Gagal memeriksa status session: ' . $statusResult['error']
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error in checkAndFixSession', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Error saat memeriksa dan memperbaiki session: ' . $e->getMessage()
            ];
        }
    }
}