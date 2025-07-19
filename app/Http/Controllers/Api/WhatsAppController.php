<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WhatsAppSessionResource;
use App\Models\WhatsAppSession;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WhatsAppController extends Controller
{
    protected $whatsappService;
    protected $contactService;
    
    public function __construct(WhatsAppService $whatsappService, ContactService $contactService)
    {
        $this->whatsappService = $whatsappService;
        $this->contactService = $contactService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $sessions = WhatsAppSession::all();
        return response()->json([
            'success' => true,
            'sessions' => $sessions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        \Log::info('Creating WhatsApp session', $request->all());
        
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|regex:/^[0-9+\-\s()]+$/',
            ]);

            $sessionId = (string) Str::uuid();
            
            // Create session using WhatsApp service
            $result = $this->whatsappService->createSession($sessionId, $request->name, $request->phone_number);
            
            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            $session = WhatsAppSession::create([
                'name' => $request->name,
                'session_id' => $sessionId,
                'phone_number' => $request->phone_number,
                'status' => 'connecting',
                'is_active' => true,
                'qr_code' => null // QR code will be fetched separately
            ]);

            \Log::info('Session created', ['session_id' => $session->id]);

            return response()->json([
                'success' => true,
                'message' => 'Session created, waiting for QR code...',
                'session' => new WhatsAppSessionResource($session),
                'status' => 'connecting'
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WhatsAppSession $whatsAppSession): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new WhatsAppSessionResource($whatsAppSession)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WhatsAppSession $whatsAppSession): JsonResponse
    {
                    $request->validate([
                'status' => 'sometimes|in:connecting,qr_ready,connected,disconnected,error',
                'qr_code' => 'sometimes|string',
                'phone_number' => 'sometimes|string',
                'is_active' => 'sometimes|boolean',
            ]);

        $whatsAppSession->update($request->only([
            'status', 'qr_code', 'phone_number', 'is_active'
        ]));

        if ($request->has('status') && $request->status === 'connected') {
            $whatsAppSession->update(['last_activity' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp session updated successfully',
            'data' => new WhatsAppSessionResource($whatsAppSession)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($session_id): JsonResponse
    {
        $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
        if (!$whatsAppSession) {
            return response()->json([
                'success' => false,
                'error' => 'Session tidak ditemukan',
            ], 404);
        }

        // Delete from WhatsApp service
        $this->whatsappService->deleteSession($whatsAppSession->session_id);
        $whatsAppSession->delete();

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp session deleted successfully'
        ]);
    }

    /**
     * Get active WhatsApp sessions
     */
    public function getActiveSessions(): JsonResponse
    {
        try {
            $activeSessions = WhatsAppSession::where('is_active', true)
                ->where('status', 'connected')
                ->get(['id', 'name', 'phone_number', 'status', 'session_id']);
            
            return response()->json([
                'success' => true,
                'sessions' => $activeSessions
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting active sessions', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting active sessions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get QR code for session
     */
    public function getQrCode($session_id): JsonResponse
    {
        try {
            // Cari session berdasarkan session_id (UUID)
            $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
            if (!$whatsAppSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan',
                ], 404);
            }

            $result = $this->whatsappService->getQrCode($whatsAppSession->session_id);
            
            if (!$result['success']) {
                throw new \Exception($result['error']);
            }
            
            $responseData = $result['data'];
            $engineData = $responseData['data'] ?? $responseData;
            
            // Update session status jika valid
            if (isset($engineData['status'])) {
                $status = (string) $engineData['status'];
                // Hanya update jika status valid
                if (in_array($status, ['connecting', 'connected', 'disconnected', 'error', 'qr_ready'])) {
                    $whatsAppSession->update(['status' => $status]);
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'session_id' => $whatsAppSession->session_id,
                    'qr_code' => $engineData['qrCode'] ?? null,
                    'qrCode' => $engineData['qrCode'] ?? null, // Keep both for compatibility
                    'status' => $engineData['status'] ?? $whatsAppSession->status
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting QR code', [
                'session_id' => $session_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Gagal mendapatkan QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send message via WhatsApp
     */
    public function sendMessage(Request $request, WhatsAppSession $whatsAppSession): JsonResponse
    {
        $request->validate([
            'to_number' => 'required|string',
            'message' => 'required|string',
            'message_type' => 'sometimes|in:text,image,video,audio,document,location'
        ]);

        // Here you would integrate with your WhatsApp engine
        // For now, we'll just create a message record
        $message = $whatsAppSession->messages()->create([
            'message_id' => Str::uuid(),
            'from_number' => $whatsAppSession->phone_number,
            'to_number' => $request->to_number,
            'message_type' => $request->get('message_type', 'text'),
            'content' => $request->message,
            'status' => 'pending',
            'direction' => 'out',
            'timestamp' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message
        ]);
    }

    /**
     * Connect session
     */
    public function connect($session_id): JsonResponse
    {
        try {
            // Cari session berdasarkan session_id (UUID)
            $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
            if (!$whatsAppSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan',
                ], 404);
            }

            // Check current status
            $statusResult = $this->whatsappService->getSessionStatus($whatsAppSession->session_id);
            
            if (!$statusResult['success']) {
                throw new \Exception($statusResult['error']);
            }
            
            $engineData = $statusResult['data']['data'] ?? $statusResult['data'];
            $currentStatus = $engineData['status'] ?? 'unknown';
            
            // Update session status
            $whatsAppSession->update(['status' => $currentStatus]);
            
            if ($currentStatus === 'connected') {
                return response()->json([
                    'success' => true,
                    'message' => 'Session sudah terhubung',
                    'data' => [
                        'session_id' => $whatsAppSession->session_id,
                        'status' => $currentStatus
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Status session: ' . $currentStatus,
                'data' => [
                    'session_id' => $whatsAppSession->session_id,
                    'status' => $currentStatus
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error connecting session', [
                'session_id' => $session_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungkan session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reconnect session
     */
    public function reconnect($session_id): JsonResponse
    {
        try {
            // Cari session berdasarkan session_id (UUID)
            $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
            if (!$whatsAppSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan',
                ], 404);
            }

            Log::info('Reconnecting WhatsApp session', [
                'session_id' => $whatsAppSession->session_id,
                'session_name' => $whatsAppSession->name
            ]);

            // Reconnect session via WhatsApp service
            $result = $this->whatsappService->reconnectSession($whatsAppSession->session_id);
            
            if (!$result['success']) {
                throw new \Exception($result['error']);
            }
            
            $responseData = $result['data'];
            $engineData = $responseData['data'] ?? $responseData;
            
            // Update session status
            if (isset($engineData['status'])) {
                $whatsAppSession->update(['status' => $engineData['status']]);
            }
            
            Log::info('Session reconnection initiated', [
                'session_id' => $whatsAppSession->session_id,
                'status' => $engineData['status'] ?? 'unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reconnect session berhasil diinisiasi',
                'data' => [
                    'session_id' => $whatsAppSession->session_id,
                    'status' => $engineData['status'] ?? 'connecting',
                    'message' => $engineData['message'] ?? 'Reconnection initiated'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error reconnecting session', [
                'session_id' => $session_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal reconnect session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all messages
     */
    public function messages(): JsonResponse
    {
        $messages = WhatsAppMessage::with('session')->latest()->paginate(20);
        
        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Show specific message
     */
    public function showMessage($id, Request $request): JsonResponse
    {
        $source = $request->get('source', 'whatsapp');
        
        if ($source === 'blast') {
            $message = \App\Models\BlastMessage::with('campaign.session')->find($id);
        } else {
            $message = WhatsAppMessage::with('session')->find($id);
        }
        
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $message
        ]);
    }

    /**
     * Delete message
     */
    public function deleteMessage($id, Request $request): JsonResponse
    {
        $source = $request->get('source', 'whatsapp');
        
        if ($source === 'blast') {
            $message = \App\Models\BlastMessage::find($id);
        } else {
            $message = WhatsAppMessage::find($id);
        }
        
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
        
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }

    /**
     * Retry failed message
     */
    public function retryMessage($id, Request $request): JsonResponse
    {
        $source = $request->get('source', 'whatsapp');
        
        if ($source === 'blast') {
            $message = \App\Models\BlastMessage::find($id);
        } else {
            $message = WhatsAppMessage::find($id);
        }
        
        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found'
            ], 404);
        }
        
        $message->update([
            'status' => 'pending',
            'error_message' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message queued for retry'
        ]);
    }

    /**
     * Get session status from engine and update database
     */
    public function getStatus($session_id): JsonResponse
    {
        try {
            // Cari session berdasarkan session_id (UUID), bukan ID numerik
            $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
            if (!$whatsAppSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan di database',
                ], 404);
            }

            $result = $this->whatsappService->getSessionStatus($whatsAppSession->session_id);
            
            // Jika session tidak ditemukan di engine, update status menjadi disconnected
            if (!$result['success']) {
                $errorMessage = $result['error'];
                
                // Check if it's a "Session not found" error
                if (strpos($errorMessage, 'Session not found') !== false) {
                    Log::warning('Session not found in WhatsApp Engine, marking as disconnected', [
                        'session_id' => $whatsAppSession->session_id,
                        'database_status' => $whatsAppSession->status
                    ]);
                    
                    // Update status to disconnected
                    $whatsAppSession->update(['status' => 'disconnected']);
                    
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'session_id' => $whatsAppSession->session_id,
                            'status' => 'disconnected',
                            'phone_number' => $whatsAppSession->phone_number,
                            'message' => 'Session tidak ditemukan di WhatsApp Engine'
                        ]
                    ]);
                }
                
                throw new \Exception($errorMessage);
            }
            
            $responseData = $result['data'];
            
            // Update session status di database jika berbeda
            if (isset($responseData['status']) && $responseData['status'] !== $whatsAppSession->status) {
                $status = (string) $responseData['status'];
                // Hanya update jika status valid
                if (in_array($status, ['connecting', 'connected', 'disconnected', 'error', 'qr_ready', 'auth_failed'])) {
                    $whatsAppSession->update(['status' => $status]);
                    Log::info('Session status updated', [
                        'session_id' => $whatsAppSession->session_id,
                        'old_status' => $whatsAppSession->getOriginal('status'),
                        'new_status' => $status
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'session_id' => $whatsAppSession->session_id,
                    'status' => $responseData['status'] ?? $whatsAppSession->status,
                    'phone_number' => $responseData['phoneNumber'] ?? $whatsAppSession->phone_number
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Session tidak ditemukan di database',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error getting session status', [
                'session_id' => $session_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Gagal mendapatkan status session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test send message
     */
    public function testSendMessage(Request $request, $session_id): JsonResponse
    {
        Log::info('=== TEST SEND MESSAGE CALLED ===');
        Log::info('Request data:', $request->all());
        
        // Cari session berdasarkan session_id (UUID)
        $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
        if (!$whatsAppSession) {
            return response()->json([
                'success' => false,
                'error' => 'Session tidak ditemukan',
            ], 404);
        }
        
        Log::info('Session data:', [
            'id' => $whatsAppSession->id,
            'session_id' => $whatsAppSession->session_id,
            'name' => $whatsAppSession->name,
            'status' => $whatsAppSession->status,
            'phone_number' => $whatsAppSession->phone_number
        ]);

        $request->validate([
            'to_number' => 'required|string|min:10',
            'message' => 'sometimes|string|max:1000',
            'message_type' => 'sometimes|in:text,image,video,audio,document,location'
        ], [
            'to_number.required' => 'Nomor telepon wajib diisi',
            'to_number.min' => 'Nomor telepon minimal 10 digit',
            'message.max' => 'Pesan maksimal 1000 karakter'
        ]);

        try {
            // Check if session is connected
            if ($whatsAppSession->status !== 'connected') {
                Log::warning('Session not connected for test send', [
                    'session_id' => $whatsAppSession->session_id,
                    'status' => $whatsAppSession->status
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak terhubung. Status: ' . $whatsAppSession->status
                ], 400);
            }

            // Get message content - for text type, use message field, for media types, use caption or empty string
            $messageContent = '';
            $messageType = $request->get('message_type', 'text');
            
            if ($messageType === 'text') {
                $messageContent = $request->message ?? '';
                if (empty($messageContent)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pesan text wajib diisi'
                    ], 400);
                }
            } else {
                // For media types, message is optional (caption)
                $messageContent = $request->message ?? '';
                
                // TODO: Add file validation for media types when file upload is implemented
                // For now, treat all non-text types as text
                $messageType = 'text';
            }

            Log::info('Sending test message via WhatsApp service', [
                'session_id' => $whatsAppSession->session_id,
                'to_number' => $request->to_number,
                'message' => $messageContent,
                'message_type' => $messageType
            ]);

            // Send message via WhatsApp service
            $result = $this->whatsappService->sendMessage(
                $whatsAppSession->session_id,
                $request->to_number,
                $messageContent,
                $messageType
            );

            Log::info('WhatsApp service result', [
                'session_id' => $whatsAppSession->session_id,
                'result' => $result
            ]);

            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            // Create message record
            $message = $whatsAppSession->messages()->create([
                'message_id' => Str::uuid(),
                'from_number' => $whatsAppSession->phone_number,
                'to_number' => $request->to_number,
                'message_type' => $messageType,
                'content' => $messageContent,
                'status' => 'sent',
                'direction' => 'out',
                'timestamp' => now(),
                'whatsapp_message_id' => $result['message_id'] ?? null,
            ]);

            Log::info('Test message sent successfully', [
                'session_id' => $whatsAppSession->session_id,
                'message_id' => $message->id,
                'whatsapp_message_id' => $result['message_id'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan test berhasil dikirim!',
                'data' => [
                    'message_id' => $message->id,
                    'whatsapp_message_id' => $result['message_id'] ?? null,
                    'to_number' => $request->to_number,
                    'message_type' => $messageType,
                    'content' => $messageContent,
                    'status' => 'sent',
                    'sent_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending test message', [
                'session_id' => $whatsAppSession->session_id,
                'to_number' => $request->to_number,
                'message_type' => $messageType ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check engine status
     */
    public function engineStatus(): JsonResponse
    {
        try {
            $engineUrl = $this->whatsappService->getEngineUrl();
            $response = Http::timeout(10)->get("{$engineUrl}/health");
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => $data['message'] ?? 'Engine is running',
                    'timestamp' => $data['timestamp'] ?? now()->toISOString()
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Engine not responding'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Engine status check failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Engine connection failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Grab kontak grup dari WhatsApp session
     */
    public function grabGroupContacts($session_id): JsonResponse
    {
        try {
            // Cari session berdasarkan session_id (UUID)
            $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
            if (!$whatsAppSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan',
                ], 404);
            }

            // Check if session is connected
            if ($whatsAppSession->status !== 'connected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak terhubung. Status: ' . $whatsAppSession->status
                ], 400);
            }

            Log::info('Grabbing group contacts', [
                'session_id' => $whatsAppSession->session_id,
                'session_name' => $whatsAppSession->name
            ]);

            // Grab group contacts via WhatsApp service
            $result = $this->whatsappService->grabGroupContacts($whatsAppSession->session_id);

            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            $contacts = $result['data'];
            
            // Simpan kontak grup ke database
            $saveResult = $this->contactService->saveGroupContacts($whatsAppSession->session_id, $contacts['data']['groups'] ?? []);
            
            Log::info('Group contacts grabbed and saved successfully', [
                'session_id' => $whatsAppSession->session_id,
                'total_groups' => count($contacts['data']['groups'] ?? []),
                'saved_count' => $saveResult['saved_count'] ?? 0,
                'updated_count' => $saveResult['updated_count'] ?? 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kontak grup berhasil diambil dan disimpan',
                'data' => [
                    'session_id' => $whatsAppSession->session_id,
                    'session_name' => $whatsAppSession->name,
                    'total_groups' => count($contacts['data']['groups'] ?? []),
                    'groups' => $contacts['data']['groups'] ?? [],
                    'saved_count' => $saveResult['saved_count'] ?? 0,
                    'updated_count' => $saveResult['updated_count'] ?? 0,
                    'grabbed_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error grabbing group contacts', [
                'session_id' => $session_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kontak grup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grab kontak individual dari WhatsApp session
     */
    public function grabIndividualContacts($session_id): JsonResponse
    {
        try {
            // Cari session berdasarkan session_id (UUID)
            $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
            if (!$whatsAppSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan',
                ], 404);
            }

            // Check if session is connected
            if ($whatsAppSession->status !== 'connected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak terhubung. Status: ' . $whatsAppSession->status
                ], 400);
            }

            Log::info('Grabbing individual contacts', [
                'session_id' => $whatsAppSession->session_id,
                'session_name' => $whatsAppSession->name
            ]);

            // Grab individual contacts via WhatsApp service
            $result = $this->whatsappService->grabIndividualContacts($whatsAppSession->session_id);

            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            $contacts = $result['data'];
            
            // Simpan kontak individual ke database
            $saveResult = $this->contactService->saveIndividualContacts($whatsAppSession->session_id, $contacts['data']['contacts'] ?? []);
            
            Log::info('Individual contacts grabbed and saved successfully', [
                'session_id' => $whatsAppSession->session_id,
                'total_contacts' => count($contacts['data']['contacts'] ?? []),
                'saved_count' => $saveResult['saved_count'] ?? 0,
                'updated_count' => $saveResult['updated_count'] ?? 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kontak individual berhasil diambil dan disimpan',
                'data' => [
                    'session_id' => $whatsAppSession->session_id,
                    'session_name' => $whatsAppSession->name,
                    'total_contacts' => count($contacts['data']['contacts'] ?? []),
                    'contacts' => $contacts['data']['contacts'] ?? [],
                    'saved_count' => $saveResult['saved_count'] ?? 0,
                    'updated_count' => $saveResult['updated_count'] ?? 0,
                    'grabbed_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error grabbing individual contacts', [
                'session_id' => $session_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kontak individual: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grab semua kontak (grup dan individual) dari WhatsApp session
     */
    public function grabAllContacts($session_id): JsonResponse
    {
        try {
            // Cari session berdasarkan session_id (UUID)
            $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
            if (!$whatsAppSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan',
                ], 404);
            }

            // Check if session is connected
            if ($whatsAppSession->status !== 'connected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak terhubung. Status: ' . $whatsAppSession->status
                ], 400);
            }

            Log::info('Grabbing all contacts', [
                'session_id' => $whatsAppSession->session_id,
                'session_name' => $whatsAppSession->name
            ]);

            // Grab all contacts via WhatsApp service
            $result = $this->whatsappService->grabAllContacts($whatsAppSession->session_id);

            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            $contacts = $result['data'];
            
            // Simpan semua kontak ke database
            $saveResult = $this->contactService->saveAllContacts($whatsAppSession->session_id, $contacts['data'] ?? []);
            
            Log::info('All contacts grabbed and saved successfully', [
                'session_id' => $whatsAppSession->session_id,
                'total_groups' => count($contacts['data']['groups'] ?? []),
                'total_contacts' => count($contacts['data']['contacts'] ?? []),
                'total_saved' => $saveResult['total_saved'] ?? 0,
                'total_updated' => $saveResult['total_updated'] ?? 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Semua kontak berhasil diambil dan disimpan',
                'data' => [
                    'session_id' => $whatsAppSession->session_id,
                    'session_name' => $whatsAppSession->name,
                    'total_groups' => count($contacts['data']['groups'] ?? []),
                    'total_contacts' => count($contacts['data']['contacts'] ?? []),
                    'groups' => $contacts['data']['groups'] ?? [],
                    'contacts' => $contacts['data']['contacts'] ?? [],
                    'saved_count' => $saveResult['total_saved'] ?? 0,
                    'updated_count' => $saveResult['total_updated'] ?? 0,
                    'grabbed_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error grabbing all contacts', [
                'session_id' => $session_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil semua kontak: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil kontak yang sudah disimpan dari database
     */
    public function getSavedContacts($session_id, Request $request): JsonResponse
    {
        try {
            // Cari session berdasarkan session_id (UUID)
            $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
            if (!$whatsAppSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan',
                ], 404);
            }

            $type = $request->get('type'); // 'individual', 'group', atau null untuk semua

            // Ambil kontak dari database
            $result = $this->contactService->getContactsBySession($whatsAppSession->session_id, $type);

            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil diambil dari database',
                'data' => [
                    'session_id' => $whatsAppSession->session_id,
                    'session_name' => $whatsAppSession->name,
                    'type' => $type ?: 'all',
                    'total_contacts' => $result['count'],
                    'contacts' => $result['data']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting saved contacts', [
                'session_id' => $session_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kontak dari database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus kontak yang disimpan berdasarkan session
     */
    public function deleteSavedContacts($session_id): JsonResponse
    {
        try {
            // Cari session berdasarkan session_id (UUID)
            $whatsAppSession = \App\Models\WhatsAppSession::where('session_id', $session_id)->first();
            if (!$whatsAppSession) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session tidak ditemukan',
                ], 404);
            }

            // Hapus kontak dari database
            $result = $this->contactService->deleteContactsBySession($whatsAppSession->session_id);

            if (!$result['success']) {
                throw new \Exception($result['error']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil dihapus dari database',
                'data' => [
                    'session_id' => $whatsAppSession->session_id,
                    'session_name' => $whatsAppSession->name,
                    'deleted_count' => $result['deleted_count']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting saved contacts', [
                'session_id' => $session_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kontak dari database: ' . $e->getMessage()
            ], 500);
        }
    }
}
