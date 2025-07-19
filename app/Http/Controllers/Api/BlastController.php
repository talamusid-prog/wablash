<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlastCampaignResource;
use App\Models\BlastCampaign;
use App\Models\BlastMessage;
use App\Models\WhatsAppSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BlastController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $campaigns = BlastCampaign::with('session')->get();
        return response()->json([
            'success' => true,
            'campaigns' => $campaigns
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Debug: Log the received data
        \Log::info('store campaign called with data:', $request->all());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string',
            'phone_numbers' => 'required|array',
            'phone_numbers.*' => 'string',
            'session_id' => 'required|integer',
            'attachments' => 'nullable|array',
            'attachments.*.name' => 'required_with:attachments|string',
            'attachments.*.type' => 'required_with:attachments|string',
            'attachments.*.data' => 'required_with:attachments|string',
        ]);

        $session = WhatsAppSession::find($request->session_id);
        
        \Log::info('Looking for session:', [
            'session_id_requested' => $request->session_id,
            'session_found' => $session ? 'yes' : 'no',
            'session_status' => $session ? $session->status : 'not found'
        ]);
        
        if (!$session || $session->status !== 'connected') {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp session is not connected'
            ], 400);
        }

        // Get the first available user or create a default one
        $user = \App\Models\User::first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Default User',
                'email' => 'default@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Process attachments if any
        $attachmentPath = null;
        if ($request->has('attachments') && !empty($request->attachments)) {
            $attachment = $request->attachments[0]; // Take first attachment for now
            $attachmentPath = $this->saveAttachment($attachment);
            
            \Log::info('Attachment processed:', [
                'original_name' => $attachment['name'],
                'type' => $attachment['type'],
                'saved_path' => $attachmentPath
            ]);
        }

        $campaign = BlastCampaign::create([
            'name' => $request->name,
            'message_template' => $request->message,
            'target_numbers' => $request->phone_numbers,
            'status' => 'draft',
            'total_count' => count($request->phone_numbers),
            'session_id' => $session->session_id,
            'created_by' => $user->id,
        ]);

        \Log::info('Campaign created:', [
            'campaign_id' => $campaign->id,
            'name' => $campaign->name,
            'total_count' => $campaign->total_count,
            'has_attachment' => !is_null($attachmentPath)
        ]);

        // Create blast messages for each target number
        foreach ($request->phone_numbers as $phoneNumber) {
            // Personalize message for each recipient
            $personalizedMessage = $this->personalizeMessage($request->message, $phoneNumber);
            
            BlastMessage::create([
                'campaign_id' => $campaign->id,
                'phone_number' => $phoneNumber,
                'message_content' => $personalizedMessage,
                'status' => 'pending',
                'attachment_path' => $attachmentPath,
            ]);
        }

        \Log::info('Blast messages created:', [
            'count' => count($request->phone_numbers),
            'with_attachment' => !is_null($attachmentPath)
        ]);

        // Start sending messages immediately
        try {
            $campaign->update(['status' => 'running']);
            $this->sendCampaignMessages($campaign);
            
            \Log::info('Campaign started immediately:', [
                'campaign_id' => $campaign->id,
                'status' => 'running'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to start campaign immediately:', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Blast campaign created and started successfully',
            'data' => new BlastCampaignResource($campaign)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BlastCampaign $blastCampaign): JsonResponse
    {
        $blastCampaign->load(['session', 'messages']);
        
        return response()->json([
            'success' => true,
            'data' => new BlastCampaignResource($blastCampaign)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlastCampaign $blastCampaign): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'message_template' => 'sometimes|string',
            'status' => 'sometimes|in:draft,scheduled,running,completed,failed',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $blastCampaign->update($request->only([
            'name', 'message_template', 'status', 'scheduled_at'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Blast campaign updated successfully',
            'data' => new BlastCampaignResource($blastCampaign)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlastCampaign $blastCampaign): JsonResponse
    {
        $blastCampaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blast campaign deleted successfully'
        ]);
    }

    /**
     * Start blast campaign
     */
    public function start(BlastCampaign $blastCampaign): JsonResponse
    {
        if ($blastCampaign->status !== 'draft' && $blastCampaign->status !== 'scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'Campaign cannot be started'
            ], 400);
        }

        $blastCampaign->update(['status' => 'running']);

        // Here you would trigger the actual sending process
        // For now, we'll just update the status

        return response()->json([
            'success' => true,
            'message' => 'Blast campaign started successfully',
            'data' => new BlastCampaignResource($blastCampaign)
        ]);
    }

    /**
     * Get campaign statistics
     */
    public function statistics(BlastCampaign $blastCampaign): JsonResponse
    {
        $stats = [
            'total' => $blastCampaign->total_count,
            'sent' => $blastCampaign->sent_count,
            'failed' => $blastCampaign->failed_count,
            'pending' => $blastCampaign->total_count - $blastCampaign->sent_count - $blastCampaign->failed_count,
            'success_rate' => $blastCampaign->total_count > 0 ? 
                round(($blastCampaign->sent_count / $blastCampaign->total_count) * 100, 2) : 0
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get campaign messages
     */
    public function messages(BlastCampaign $blastCampaign): JsonResponse
    {
        $messages = $blastCampaign->messages()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Pause blast campaign
     */
    public function pause(BlastCampaign $blastCampaign): JsonResponse
    {
        if ($blastCampaign->status !== 'running') {
            return response()->json([
                'success' => false,
                'message' => 'Campaign is not running'
            ], 400);
        }

        $blastCampaign->update(['status' => 'paused']);

        return response()->json([
            'success' => true,
            'message' => 'Blast campaign paused successfully',
            'data' => new BlastCampaignResource($blastCampaign)
        ]);
    }

    /**
     * Get phone numbers from recipients
     */
    public function getPhoneNumbers(Request $request): JsonResponse
    {
        $request->validate([
            'recipients' => 'required|array',
            'recipients.*.type' => 'required|in:individual,group',
            'recipients.*.group_id' => 'required_if:recipients.*.type,group',
        ]);

        // Debug: Log the received data
        \Log::info('getPhoneNumbers called with data:', [
            'recipients' => $request->recipients,
            'request_all' => $request->all()
        ]);

        $phoneNumbers = [];
        
        foreach ($request->recipients as $recipient) {
            \Log::info('Processing recipient:', $recipient);
            
            if ($recipient['type'] === 'individual') {
                // Get individual contacts (not in groups)
                $contacts = \App\Models\Contact::where('type', 'individual')
                    ->whereNull('group_id')
                    ->whereNotNull('phone_number')
                    ->where('phone_number', '!=', '')
                    ->where(function($query) {
                        $query->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) >= 10')
                              ->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) <= 15')
                              ->where(function($q) {
                                  $q->whereRaw('REPLACE(phone_number, " ", "") LIKE "62%"')
                                    ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "0%"')
                                    ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "8%"');
                              });
                    })
                    ->pluck('phone_number')
                    ->toArray();
                
                \Log::info('Individual contacts found:', ['count' => count($contacts), 'numbers' => $contacts]);
                $phoneNumbers = array_merge($phoneNumbers, $contacts);
                
                // Also get from phonebook
                $phonebookContacts = \App\Models\Phonebook::whereNull('group')
                    ->orWhere('group', '')
                    ->where('is_active', true)
                    ->whereNotNull('phone_number')
                    ->where('phone_number', '!=', '')
                    ->where(function($query) {
                        $query->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) >= 10')
                              ->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) <= 15')
                              ->where(function($q) {
                                  $q->whereRaw('REPLACE(phone_number, " ", "") LIKE "62%"')
                                    ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "0%"')
                                    ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "8%"');
                              });
                    })
                    ->pluck('phone_number')
                    ->toArray();
                
                \Log::info('Phonebook contacts found:', ['count' => count($phonebookContacts), 'numbers' => $phonebookContacts]);
                $phoneNumbers = array_merge($phoneNumbers, $phonebookContacts);
                
            } elseif ($recipient['type'] === 'group') {
                // Get group participants
                $group = \App\Models\Contact::where('type', 'group')
                    ->where('contact_id', $recipient['group_id'])
                    ->first();
                
                \Log::info('Looking for group:', ['group_id' => $recipient['group_id'], 'group_found' => $group ? 'yes' : 'no']);
                
                if ($group) {
                    $participants = \App\Models\Contact::where('type', 'individual')
                        ->where('group_id', $group->contact_id)
                        ->whereNotNull('phone_number')
                        ->where('phone_number', '!=', '')
                        ->where(function($query) {
                            $query->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) >= 10')
                                  ->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) <= 15')
                                  ->where(function($q) {
                                      $q->whereRaw('REPLACE(phone_number, " ", "") LIKE "62%"')
                                        ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "0%"')
                                        ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "8%"');
                                  });
                        })
                        ->pluck('phone_number')
                        ->toArray();
                    
                    \Log::info('Group participants found:', ['count' => count($participants), 'numbers' => $participants]);
                    $phoneNumbers = array_merge($phoneNumbers, $participants);
                } else {
                    // Check if it's a phonebook group
                    if (strpos($recipient['group_id'], 'phonebook_') === 0) {
                        // Extract the original group name from the hash
                        $hash = str_replace('phonebook_', '', $recipient['group_id']);
                        
                        // Get all phonebook groups to find the one with matching hash
                        $allGroups = \App\Models\Phonebook::select('group')
                            ->whereNotNull('group')
                            ->where('group', '!=', '')
                            ->where('is_active', true)
                            ->distinct()
                            ->pluck('group')
                            ->toArray();
                        
                        $groupName = null;
                        foreach ($allGroups as $group) {
                            if (md5($group) === $hash) {
                                $groupName = $group;
                                break;
                            }
                        }
                        
                        \Log::info('Looking for phonebook group:', [
                            'hash' => $hash,
                            'group_name_found' => $groupName,
                            'all_groups' => $allGroups
                        ]);
                        
                        if ($groupName) {
                            $phonebookParticipants = \App\Models\Phonebook::where('group', $groupName)
                                ->where('is_active', true)
                                ->whereNotNull('phone_number')
                                ->where('phone_number', '!=', '')
                                ->where(function($query) {
                                    $query->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) >= 10')
                                          ->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) <= 15')
                                          ->where(function($q) {
                                              $q->whereRaw('REPLACE(phone_number, " ", "") LIKE "62%"')
                                                ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "0%"')
                                                ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "8%"');
                                          });
                                })
                                ->pluck('phone_number')
                                ->toArray();
                            
                            \Log::info('Phonebook group participants found:', ['count' => count($phonebookParticipants), 'numbers' => $phonebookParticipants]);
                            $phoneNumbers = array_merge($phoneNumbers, $phonebookParticipants);
                        }
                    }
                }
            }
        }
        
        \Log::info('Total phone numbers before formatting:', ['count' => count($phoneNumbers), 'numbers' => $phoneNumbers]);
        
        // Remove duplicates and format numbers
        $phoneNumbers = array_unique($phoneNumbers);
        $formattedNumbers = array_map(function($number) {
            // Format to 62xxx format
            $cleaned = preg_replace('/[^0-9]/', '', $number);
            if (strlen($cleaned) >= 10) {
                if (substr($cleaned, 0, 1) === '0') {
                    return '62' . substr($cleaned, 1);
                } elseif (substr($cleaned, 0, 2) === '62') {
                    return $cleaned;
                } elseif (substr($cleaned, 0, 1) === '8') {
                    return '62' . $cleaned;
                }
            }
            return $cleaned;
        }, $phoneNumbers);
        
        // Filter valid numbers
        $formattedNumbers = array_filter($formattedNumbers, function($number) {
            return strlen($number) >= 10 && strlen($number) <= 15;
        });
        
        \Log::info('Final formatted phone numbers:', ['count' => count($formattedNumbers), 'numbers' => array_values($formattedNumbers)]);
        
        return response()->json([
            'success' => true,
            'phone_numbers' => array_values($formattedNumbers),
            'count' => count($formattedNumbers),
            'debug' => [
                'recipients_received' => $request->recipients,
                'total_before_formatting' => count($phoneNumbers),
                'total_after_formatting' => count($formattedNumbers)
            ]
        ]);
    }

    /**
     * Update campaign status manually
     */
    public function updateCampaignStatus(BlastCampaign $campaign): JsonResponse
    {
        $sentCount = $campaign->messages()->where('status', 'sent')->count();
        $failedCount = $campaign->messages()->where('status', 'failed')->count();
        $pendingCount = $campaign->messages()->where('status', 'pending')->count();
        $totalCount = $campaign->total_count;

        \Log::info('Manual campaign status update:', [
            'campaign_id' => $campaign->id,
            'sent' => $sentCount,
            'failed' => $failedCount,
            'pending' => $pendingCount,
            'total' => $totalCount
        ]);

        // Update counts
        $campaign->update([
            'sent_count' => $sentCount,
            'failed_count' => $failedCount
        ]);

        // Update status if all messages processed
        if ($pendingCount === 0) {
            $status = $failedCount === 0 ? 'completed' : 'failed';
            $campaign->update(['status' => $status]);
            
            \Log::info('Campaign status updated to:', [
                'campaign_id' => $campaign->id,
                'status' => $status
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Campaign status updated',
            'data' => new BlastCampaignResource($campaign)
        ]);
    }

    /**
     * Check available contacts for debugging
     */
    public function checkContacts(): JsonResponse
    {
        // Check Contact model
        $contactIndividualCount = \App\Models\Contact::where('type', 'individual')
            ->whereNull('group_id')
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->count();
            
        $contactGroupCount = \App\Models\Contact::where('type', 'group')->count();
        
        $contactIndividualWithPhone = \App\Models\Contact::where('type', 'individual')
            ->whereNull('group_id')
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->limit(5)
            ->get(['name', 'phone_number']);
            
        // Check Phonebook model
        $phonebookIndividualCount = \App\Models\Phonebook::whereNull('group')
            ->orWhere('group', '')
            ->where('is_active', true)
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->count();
            
        $phonebookGroupCount = \App\Models\Phonebook::select('group')
            ->whereNotNull('group')
            ->where('group', '!=', '')
            ->where('is_active', true)
            ->distinct()
            ->count();
            
        $phonebookIndividualWithPhone = \App\Models\Phonebook::whereNull('group')
            ->orWhere('group', '')
            ->where('is_active', true)
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->limit(5)
            ->get(['name', 'phone_number']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'contacts_table' => [
                    'individual_count' => $contactIndividualCount,
                    'group_count' => $contactGroupCount,
                    'individual_samples' => $contactIndividualWithPhone
                ],
                'phonebook_table' => [
                    'individual_count' => $phonebookIndividualCount,
                    'group_count' => $phonebookGroupCount,
                    'individual_samples' => $phonebookIndividualWithPhone
                ]
            ]
        ]);
    }

    /**
     * Send campaign messages to WhatsApp
     */
    private function sendCampaignMessages(BlastCampaign $campaign): void
    {
        $messages = $campaign->messages()->where('status', 'pending')->get();
        
        if ($messages->isEmpty()) {
            \Log::info('No pending messages to send for campaign:', ['campaign_id' => $campaign->id]);
            return;
        }

        \Log::info('Starting to send campaign messages:', [
            'campaign_id' => $campaign->id,
            'pending_count' => $messages->count()
        ]);

        foreach ($messages as $message) {
            try {
                // Send message via WhatsApp service
                $whatsappService = new \App\Services\WhatsAppService();
                
                // Check if message has attachment
                if ($message->attachment_path) {
                    // Get full path to attachment
                    $attachmentFullPath = storage_path('app/public/' . $message->attachment_path);
                    
                    \Log::info('Sending message with attachment:', [
                        'campaign_id' => $campaign->id,
                        'message_id' => $message->id,
                        'phone_number' => $message->phone_number,
                        'attachment_path' => $message->attachment_path,
                        'full_path' => $attachmentFullPath
                    ]);
                    
                    // Send message with attachment
                    $result = $whatsappService->sendMessageWithAttachment(
                        $campaign->session_id,
                        $message->phone_number,
                        $message->message_content,
                        $attachmentFullPath,
                        $message->attachment_path
                    );
                } else {
                    // Send text message only
                    $result = $whatsappService->sendMessage(
                        $campaign->session_id,
                        $message->phone_number,
                        $message->message_content
                    );
                }

                if ($result['success']) {
                    $message->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'whatsapp_message_id' => $result['message_id'] ?? null
                    ]);
                    
                    \Log::info('Message sent successfully:', [
                        'campaign_id' => $campaign->id,
                        'message_id' => $message->id,
                        'phone_number' => $message->phone_number,
                        'whatsapp_message_id' => $result['message_id'] ?? null,
                        'has_attachment' => !is_null($message->attachment_path)
                    ]);
                } else {
                    $message->update([
                        'status' => 'failed',
                        'error_message' => $result['error'] ?? 'Unknown error'
                    ]);
                    
                    \Log::error('Message failed to send:', [
                        'campaign_id' => $campaign->id,
                        'message_id' => $message->id,
                        'phone_number' => $message->phone_number,
                        'error' => $result['error'] ?? 'Unknown error',
                        'has_attachment' => !is_null($message->attachment_path)
                    ]);
                }

                // Add delay between messages to avoid rate limiting
                usleep(1000000); // 1 second delay

            } catch (\Exception $e) {
                $message->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
                
                \Log::error('Exception while sending message:', [
                    'campaign_id' => $campaign->id,
                    'message_id' => $message->id,
                    'phone_number' => $message->phone_number,
                    'error' => $e->getMessage(),
                    'has_attachment' => !is_null($message->attachment_path)
                ]);
            }
        }

        // Update campaign status based on message results
        $sentCount = $campaign->messages()->where('status', 'sent')->count();
        $failedCount = $campaign->messages()->where('status', 'failed')->count();
        $pendingCount = $campaign->messages()->where('status', 'pending')->count();
        $totalCount = $campaign->total_count;

        \Log::info('Campaign status check:', [
            'campaign_id' => $campaign->id,
            'sent' => $sentCount,
            'failed' => $failedCount,
            'pending' => $pendingCount,
            'total' => $totalCount,
            'sent_plus_failed' => $sentCount + $failedCount
        ]);

        // Always update sent_count and failed_count
        $campaign->update([
            'sent_count' => $sentCount,
            'failed_count' => $failedCount
        ]);

        // Check if all messages have been processed (no pending messages left)
        if ($pendingCount === 0) {
            $status = $failedCount === 0 ? 'completed' : 'failed';
            $campaign->update(['status' => $status]);
            
            \Log::info('Campaign completed:', [
                'campaign_id' => $campaign->id,
                'status' => $status,
                'sent' => $sentCount,
                'failed' => $failedCount,
                'total' => $totalCount
            ]);
        } else {
            \Log::info('Campaign still has pending messages:', [
                'campaign_id' => $campaign->id,
                'pending_count' => $pendingCount
            ]);
        }
    }

    /**
     * Save attachment from base64 to storage
     */
    private function saveAttachment(array $attachment): ?string
    {
        try {
            // Extract base64 data
            $base64Data = $attachment['data'];
            
            // Remove data URL prefix if present
            if (strpos($base64Data, 'data:') === 0) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            }
            
            // Decode base64
            $fileData = base64_decode($base64Data);
            
            if ($fileData === false) {
                \Log::error('Failed to decode base64 attachment data');
                return null;
            }
            
            // Generate unique filename
            $extension = $this->getExtensionFromMimeType($attachment['type']);
            $filename = uniqid('blast_attachment_') . '_' . time() . '.' . $extension;
            
            // Save to storage
            $path = 'blast-attachments/' . $filename;
            \Storage::disk('public')->put($path, $fileData);
            
            \Log::info('Attachment saved successfully:', [
                'original_name' => $attachment['name'],
                'saved_path' => $path,
                'file_size' => strlen($fileData)
            ]);
            
            return $path;
            
        } catch (\Exception $e) {
            \Log::error('Error saving attachment:', [
                'error' => $e->getMessage(),
                'attachment_name' => $attachment['name'] ?? 'unknown'
            ]);
            return null;
        }
    }

    /**
     * Personalize message by replacing placeholders with actual data
     */
    private function personalizeMessage(string $message, string $phoneNumber): string
    {
        \Log::info('Starting personalization for phone number:', ['phone_number' => $phoneNumber]);
        
        // Generate different phone number formats for searching
        $normalizedNumber = $this->normalizePhoneNumber($phoneNumber);
        $withCountryCode = $this->addCountryCode($phoneNumber);
        
        \Log::info('Phone number formats for search:', [
            'original' => $phoneNumber,
            'normalized' => $normalizedNumber,
            'with_country_code' => $withCountryCode
        ]);
        
        // Try to find contact information from database with different phone number formats
        $contact = \App\Models\Contact::where('phone_number', $phoneNumber)
            ->orWhere('phone_number', $normalizedNumber)
            ->orWhere('phone_number', $withCountryCode)
            ->first();
            
        $phonebookContact = \App\Models\Phonebook::where('phone_number', $phoneNumber)
            ->orWhere('phone_number', $normalizedNumber)
            ->orWhere('phone_number', $withCountryCode)
            ->first();
        
        \Log::info('Database query results:', [
            'phone_number' => $phoneNumber,
            'contact_found' => $contact ? 'yes' : 'no',
            'phonebook_contact_found' => $phonebookContact ? 'yes' : 'no',
            'contact_data' => $contact ? [
                'name' => $contact->name,
                'email' => $contact->email,
                'group_name' => $contact->group_name ?? 'null',
                'company' => $contact->company ?? 'null'
            ] : 'null',
            'phonebook_data' => $phonebookContact ? [
                'name' => $phonebookContact->name,
                'email' => $phonebookContact->email,
                'group' => $phonebookContact->group,
                'company' => $phonebookContact->company ?? 'null'
            ] : 'null'
        ]);
        
        // Use contact data if available, otherwise use defaults
        $contactData = [
            'name' => $contact->name ?? $phonebookContact->name ?? 'Pelanggan',
            'phone' => $phoneNumber,
            'email' => $contact->email ?? $phonebookContact->email ?? 'pelanggan@example.com',
            'group' => $contact->group_name ?? $phonebookContact->group ?? 'Umum',
            'company' => $contact->company ?? $phonebookContact->company ?? 'Perusahaan',
            'date' => now()->format('d/m/Y'),
        ];
        
        \Log::info('Contact data prepared:', [
            'phone_number' => $phoneNumber,
            'final_contact_data' => $contactData
        ]);
        
        // Replace placeholders with actual data
        $personalizedMessage = $message;
        $personalizedMessage = str_replace('{name}', $contactData['name'], $personalizedMessage);
        $personalizedMessage = str_replace('{phone}', $contactData['phone'], $personalizedMessage);
        $personalizedMessage = str_replace('{email}', $contactData['email'], $personalizedMessage);
        $personalizedMessage = str_replace('{group}', $contactData['group'], $personalizedMessage);
        $personalizedMessage = str_replace('{company}', $contactData['company'], $personalizedMessage);
        $personalizedMessage = str_replace('{date}', $contactData['date'], $personalizedMessage);
        
        \Log::info('Message personalized:', [
            'phone_number' => $phoneNumber,
            'original_message' => $message,
            'personalized_message' => $personalizedMessage,
            'contact_data' => $contactData
        ]);
        
        return $personalizedMessage;
    }

    /**
     * Normalize phone number by removing spaces and special characters
     */
    private function normalizePhoneNumber(string $phoneNumber): string
    {
        return preg_replace('/[^0-9]/', '', $phoneNumber);
    }

    /**
     * Add country code if not present
     */
    private function addCountryCode(string $phoneNumber): string
    {
        $cleanNumber = $this->normalizePhoneNumber($phoneNumber);
        
        // If number starts with 0, replace with 62
        if (strpos($cleanNumber, '0') === 0) {
            return '62' . substr($cleanNumber, 1);
        }
        
        // If number doesn't start with 62, add it
        if (strpos($cleanNumber, '62') !== 0) {
            return '62' . $cleanNumber;
        }
        
        return $cleanNumber;
    }

    /**
     * Get file extension from MIME type
     */
    private function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'text/plain' => 'txt',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        ];
        
        return $mimeMap[$mimeType] ?? 'bin';
    }
}
