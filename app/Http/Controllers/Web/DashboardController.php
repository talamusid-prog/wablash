<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSession;
use App\Models\BlastCampaign;
use App\Models\BlastMessage;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Import Contact model
        $contactModel = 'App\Models\Contact';
        
        // Calculate success rate based on actual data from both WhatsAppMessage and BlastMessage
        $whatsappSentMessages = WhatsAppMessage::whereIn('status', ['sent', 'delivered', 'read'])->count();
        $whatsappAttemptedMessages = WhatsAppMessage::whereIn('status', ['sent', 'delivered', 'read', 'failed', 'error'])->count();
        
        $blastSentMessages = BlastMessage::whereIn('status', ['sent', 'delivered', 'read'])->count();
        $blastAttemptedMessages = BlastMessage::whereIn('status', ['sent', 'delivered', 'read', 'failed', 'error', 'pending'])->count();
        
        // Combine both sources
        $totalSentMessages = $whatsappSentMessages + $blastSentMessages;
        $totalAttemptedMessages = $whatsappAttemptedMessages + $blastAttemptedMessages;
        
        $successRate = 0;
        if ($totalAttemptedMessages > 0) {
            $successRate = round(($totalSentMessages / $totalAttemptedMessages) * 100, 1);
        }
        
        $stats = [
            'total_sessions' => WhatsAppSession::count(),
            'active_sessions' => WhatsAppSession::where('is_active', true)->count(),
            'connected_sessions' => WhatsAppSession::where('status', 'connected')->count(),
            'total_campaigns' => BlastCampaign::count(),
            'running_campaigns' => BlastCampaign::where('status', 'running')->count(),
            'total_messages' => WhatsAppMessage::count() + BlastMessage::count(),
            'sent_messages' => $totalSentMessages,
            'total_contacts' => class_exists($contactModel) ? $contactModel::where('type', 'individual')->count() : 0,
            'total_groups' => class_exists($contactModel) ? $contactModel::where('type', 'group')->count() : 0,
            'success_rate' => $successRate . '%',
            'success_rate_percentage' => $successRate,
            'total_attempted_messages' => $totalAttemptedMessages,
        ];

        $recent_sessions = WhatsAppSession::latest()->take(5)->get();
        $recent_campaigns = BlastCampaign::with('session')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recent_sessions', 'recent_campaigns'));
    }

    /**
     * Display WhatsApp sessions page.
     */
    public function sessions()
    {
        $sessions = WhatsAppSession::latest()->paginate(10);
        return view('sessions.index', compact('sessions'));
    }

    /**
     * Display create session page.
     */
    public function createSession()
    {
        return view('sessions.create');
    }

    /**
     * Display blast campaigns page.
     */
    public function campaigns()
    {
        $campaigns = BlastCampaign::with('session')->latest()->paginate(10);
        return view('campaigns.index', compact('campaigns'));
    }

    /**
     * Display create campaign page.
     */
    public function createCampaign()
    {
        // Import Contact model
        $contactModel = 'App\Models\Contact';
        $phonebookModel = 'App\Models\Phonebook';
        
        $groups = [];
        
        // Get groups from contacts table
        if (class_exists($contactModel)) {
            $contactGroups = $contactModel::where('type', 'group')
                ->orderBy('name')
                ->get()
                ->map(function($group) use ($contactModel) {
                    // Count participants with valid phone numbers
                    $participantCount = $contactModel::where('type', 'individual')
                        ->where('group_id', $group->contact_id)
                        ->whereNotNull('phone_number')
                        ->where('phone_number', '!=', '')
                        ->where(function($query) {
                            // Filter nomor telepon yang valid (format Indonesia)
                            $query->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) >= 10')
                                  ->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) <= 15')
                                  ->where(function($q) {
                                      $q->whereRaw('REPLACE(phone_number, " ", "") LIKE "62%"')
                                        ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "0%"')
                                        ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "8%"');
                                  });
                        })
                        ->count();
                    
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'contact_id' => $group->contact_id,
                        'participant_count' => $participantCount,
                        'source' => 'contacts'
                    ];
                })
                ->filter(function($group) {
                    return $group['participant_count'] > 0; // Only show groups with participants
                })
                ->toArray();
            
            $groups = array_merge($groups, $contactGroups);
        }
        
        // Get groups from phonebook table
        if (class_exists($phonebookModel)) {
            $phonebookGroups = $phonebookModel::select('group')
                ->whereNotNull('group')
                ->where('group', '!=', '')
                ->where('is_active', true)
                ->distinct()
                ->pluck('group')
                ->map(function($groupName) use ($phonebookModel) {
                    // Count contacts in this group with valid phone numbers
                    $participantCount = $phonebookModel::where('group', $groupName)
                        ->where('is_active', true)
                        ->whereNotNull('phone_number')
                        ->where('phone_number', '!=', '')
                        ->where(function($query) {
                            // Filter nomor telepon yang valid (format Indonesia)
                            $query->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) >= 10')
                                  ->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) <= 15')
                                  ->where(function($q) {
                                      $q->whereRaw('REPLACE(phone_number, " ", "") LIKE "62%"')
                                        ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "0%"')
                                        ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "8%"');
                                  });
                        })
                        ->count();
                    
                    return [
                        'id' => 'phonebook_' . md5($groupName),
                        'name' => $groupName,
                        'contact_id' => 'phonebook_' . md5($groupName),
                        'participant_count' => $participantCount,
                        'source' => 'phonebook'
                    ];
                })
                ->filter(function($group) {
                    return $group['participant_count'] > 0; // Only show groups with participants
                })
                ->toArray();
            
            $groups = array_merge($groups, $phonebookGroups);
        }
        
        // Sort all groups by name
        usort($groups, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        
        // Get individual contacts count (not in groups)
        $individualContactsCount = 0;
        if (class_exists($contactModel)) {
            $individualContactsCount = $contactModel::where('type', 'individual')
                ->whereNull('group_id')
                ->whereNotNull('phone_number')
                ->where('phone_number', '!=', '')
                ->where(function($query) {
                    // Filter nomor telepon yang valid (format Indonesia)
                    $query->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) >= 10')
                          ->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) <= 15')
                          ->where(function($q) {
                              $q->whereRaw('REPLACE(phone_number, " ", "") LIKE "62%"')
                                ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "0%"')
                                ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "8%"');
                          });
                })
                ->count();
        }
        
        // Add individual contacts from phonebook (not in any group)
        if (class_exists($phonebookModel)) {
            $phonebookIndividualCount = $phonebookModel::whereNull('group')
                ->orWhere('group', '')
                ->where('is_active', true)
                ->whereNotNull('phone_number')
                ->where('phone_number', '!=', '')
                ->where(function($query) {
                    // Filter nomor telepon yang valid (format Indonesia)
                    $query->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) >= 10')
                          ->whereRaw('LENGTH(REPLACE(phone_number, " ", "")) <= 15')
                          ->where(function($q) {
                              $q->whereRaw('REPLACE(phone_number, " ", "") LIKE "62%"')
                                ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "0%"')
                                ->orWhereRaw('REPLACE(phone_number, " ", "") LIKE "8%"');
                          });
                })
                ->count();
            
            $individualContactsCount += $phonebookIndividualCount;
        }
        
        // Get WhatsApp sessions for campaign creation
        $sessions = WhatsAppSession::where('status', 'connected')->get();
        
        return view('campaigns.create', compact('groups', 'individualContactsCount', 'sessions'));
    }

    /**
     * Display messages page.
     */
    public function messages()
    {
        // Get messages from both WhatsAppMessage and BlastMessage tables
        $whatsappMessages = WhatsAppMessage::with('session')->get()->map(function($message) {
            return [
                'id' => $message->id,
                'phone_number' => $message->phone_number ?? $message->to_number,
                'message' => $message->message ?? $message->content,
                'status' => $message->status,
                'sent_at' => $message->sent_at ?? $message->timestamp,
                'session' => $message->session,
                'campaign' => null,
                'source_table' => 'whatsapp',
                'original_id' => $message->id,
                'attachment_path' => $message->media_url,
                'error_message' => $message->error_message
            ];
        });
        
        $blastMessages = BlastMessage::with('campaign.session')->get()->map(function($message) {
            return [
                'id' => $message->id,
                'phone_number' => $message->phone_number,
                'message' => $message->message_content,
                'status' => $message->status,
                'sent_at' => $message->sent_at,
                'session' => $message->campaign->session ?? null,
                'campaign' => $message->campaign,
                'source_table' => 'blast',
                'original_id' => $message->id,
                'attachment_path' => $message->attachment_path,
                'error_message' => $message->error_message
            ];
        });
        
        // Combine and sort by sent_at
        $allMessages = $whatsappMessages->concat($blastMessages)
            ->sortByDesc('sent_at')
            ->values();
        
        // Manual pagination
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $messages = $allMessages->slice($offset, $perPage);
        
        // Create paginator manually
        $messages = new \Illuminate\Pagination\LengthAwarePaginator(
            $messages,
            $allMessages->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );
        
        return view('messages.index', compact('messages'));
    }

    /**
     * Display test send message page.
     */
    public function testSend()
    {
        $sessions = WhatsAppSession::where('status', 'connected')->get();
        
        // Log debug information
        \Log::info('Test send page accessed', [
            'total_sessions' => WhatsAppSession::count(),
            'connected_sessions' => $sessions->count(),
            'sessions_data' => $sessions->map(function($session) {
                return [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'name' => $session->name,
                    'phone_number' => $session->phone_number,
                    'status' => $session->status,
                    'created_at' => $session->created_at
                ];
            })
        ]);
        
        return view('test-send.index', compact('sessions'));
    }
}
