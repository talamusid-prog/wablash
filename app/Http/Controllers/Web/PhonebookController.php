<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Phonebook;
use App\Models\WhatsAppSession;
use App\Models\Contact;
use App\Services\WhatsAppService;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exports\PhonebookExport;
use Maatwebsite\Excel\Facades\Excel;

class PhonebookController extends Controller
{
    /**
     * Display group participants
     */
    public function groupParticipants($groupId)
    {
        $group = Contact::where('type', 'group')
            ->where('id', $groupId)
            ->firstOrFail();

        // Get participants by group_id (which is the contact_id of the group)
        $participants = Contact::where('type', 'individual')
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
            ->orderBy('name')
            ->get();

        $group->participants = $participants;

        return view('phonebook.group-participants', compact('group'));
    }

    /**
     * Display individual contacts
     */
    public function individualContacts(Request $request)
    {
        $query = Contact::where('type', 'individual')
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
            });

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $contacts = $query->orderBy('name')->paginate(10);

        // Get total count for stats (without pagination)
        $totalCount = Contact::where('type', 'individual')
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

        $group = (object) [
            'id' => 'individual',
            'name' => 'Kontak Individual',
            'type' => 'individual_group',
            'participants' => $contacts,
            'contact_id' => 'INDIVIDUAL_GROUP',
            'group_participants_count' => $totalCount
        ];

        return view('phonebook.individual-contacts', compact('group'));
    }

    /**
     * Delete a group and its participants
     */
    public function deleteGroup($groupId)
    {
        try {
            // Find the group
            $group = Contact::where('type', 'group')
                ->where('id', $groupId)
                ->firstOrFail();

            // Get participants count for confirmation
            $participantsCount = Contact::where('type', 'individual')
                ->where('group_id', $groupId)
                ->count();

            // Delete all participants of this group
            Contact::where('type', 'individual')
                ->where('group_id', $groupId)
                ->delete();

            // Delete the group itself
            $group->delete();

            return redirect()->route('phonebook.index')
                ->with('success', "Grup '" . html_entity_decode($group->name) . "' dan {$participantsCount} peserta berhasil dihapus");

        } catch (\Exception $e) {
            return redirect()->route('phonebook.index')
                ->with('error', 'Gagal menghapus grup: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cache key for this request
        $cacheKey = 'phonebook_index_' . md5($request->fullUrl());
        
        // Add cache key to tracking
        ContactService::addCacheKey($cacheKey);
        
        // Try to get from cache first (only for data, not response)
        if (cache()->has($cacheKey)) {
            $cachedData = cache()->get($cacheKey);
            return view('phonebook.index', $cachedData);
        }
        
        // Get WhatsApp groups with eager loading and optimized queries
        $whatsappGroups = Contact::where('type', 'group')
            ->orderBy('name')
            ->get(['id', 'name', 'contact_id', 'type']);
        
        // Get all participants count in one query for WhatsApp groups
        $participantsCounts = Contact::where('type', 'individual')
            ->whereNotNull('group_id')
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
            ->selectRaw('group_id, COUNT(*) as count')
            ->groupBy('group_id')
            ->pluck('count', 'group_id');
        
        // Attach participant counts to groups
        foreach ($whatsappGroups as $group) {
            $group->participants_with_phone_count = $participantsCounts->get($group->contact_id, 0);
            $group->participants = collect([]); // Don't load full participants list here
        }

        // Get manual groups with optimized query
        $manualGroupsData = Phonebook::whereNotNull('group')
            ->where('group', '!=', '')
            ->where('phone_number', 'like', 'GROUP_%')
            ->distinct()
            ->pluck('group');
        
        // Get manual groups participant counts in one query
        $manualGroupsCounts = Phonebook::whereNotNull('group')
            ->where('group', '!=', '')
            ->where('phone_number', 'not like', 'GROUP_%')
            ->selectRaw('`group`, COUNT(*) as count')
            ->groupBy('group')
            ->pluck('count', 'group');
        
        $manualGroups = $manualGroupsData->map(function($groupName) use ($manualGroupsCounts) {
            return (object) [
                'id' => 'manual_' . md5($groupName),
                'name' => $groupName,
                'type' => 'manual_group',
                'contact_id' => 'MANUAL_' . md5($groupName),
                'participants' => collect([]),
                'participants_with_phone_count' => $manualGroupsCounts->get($groupName, 0),
                'group_participants_count' => $manualGroupsCounts->get($groupName, 0),
                'is_manual_group' => true
            ];
        });

        // Combine WhatsApp groups and manual groups
        $allGroups = $whatsappGroups->concat($manualGroups);
        
        // Apply pagination to groups (16 per page)
        $perPage = 16;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        // Get paginated groups
        $groups = $allGroups->slice($offset, $perPage);
        
        // Create paginator manually
        $groupsPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $groups,
            $allGroups->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        // Get individual contacts count only (not full data)
        $individualContactsCount = Contact::where('type', 'individual')
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
            ->count();

        // Get contact counts using service
        $contactCounts = ContactService::getContactCounts();
        $allContactsCount = $contactCounts['all'];
        $activeContactsCount = $contactCounts['active'];

        // Create virtual group for individual contacts (without loading full data)
        $individualGroup = (object) [
            'id' => 'individual',
            'name' => 'Kontak Individual',
            'type' => 'individual_group',
            'participants' => collect([]), // Don't load full participants list
            'contact_id' => 'INDIVIDUAL_GROUP',
            'group_participants_count' => $individualContactsCount
        ];

        // Prepare view data
        $viewData = [
            'groupsPaginator' => $groupsPaginator,
            'individualContacts' => collect([]), // Don't load full data
            'allContactsCount' => $allContactsCount,
            'activeContactsCount' => $activeContactsCount,
            'individualContactsCount' => $individualContactsCount,
            'individualGroup' => $individualGroup
        ];

        // Cache the data for 5 minutes (not the response)
        cache()->put($cacheKey, $viewData, now()->addMinutes(5));
        
        return view('phonebook.index', $viewData);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get existing groups for dropdown selection
        // Include both regular contacts with groups and manual group entries
        $groups = Phonebook::whereNotNull('group')
            ->where('group', '!=', '')
            ->distinct()
            ->pluck('group')
            ->sort()
            ->values();
            
        return view('phonebook.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('=== DEBUG: Store contact called ===');
        \Log::info('Request method:', ['method' => $request->method()]);
        \Log::info('Request URL:', ['url' => $request->url()]);
        \Log::info('Request headers:', $request->headers->all());
        \Log::info('Request all data:', $request->all());
        \Log::info('CSRF token:', ['token' => $request->input('_token')]);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'group' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Nama wajib diisi',
            'phone_number.required' => 'Nomor telepon wajib diisi',
            'email.email' => 'Format email tidak valid',
            'name.max' => 'Nama maksimal 255 karakter',
            'phone_number.max' => 'Nomor telepon maksimal 20 karakter'
        ]);

        \Log::info('=== DEBUG: Validation rules applied ===');

        if ($validator->fails()) {
            \Log::error('=== DEBUG: Validation failed ===');
            \Log::error('Validation errors:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        \Log::info('=== DEBUG: Validation passed ===');

        try {
            // Prepare data for creation
            $data = $request->only(['name', 'phone_number', 'email', 'notes', 'group']);
            $data['is_active'] = $request->has('is_active') ? true : false;
            
            \Log::info('=== DEBUG: Data prepared for creation ===');
            \Log::info('Data to be saved:', $data);
            
            $phonebook = Phonebook::create($data);
            
            \Log::info('=== DEBUG: Contact created successfully ===');
            \Log::info('Created contact ID:', ['id' => $phonebook->id]);
            \Log::info('Created contact data:', $phonebook->toArray());

            \Log::info('=== DEBUG: Redirecting with success message ===');
            return redirect()->route('phonebook.index')
                ->with('success', 'Kontak berhasil ditambahkan');
                
        } catch (\Exception $e) {
            \Log::error('=== DEBUG: Failed to create contact ===');
            \Log::error('Exception details:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyimpan kontak: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Phonebook $phonebook)
    {
        return view('phonebook.show', compact('phonebook'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Phonebook $phonebook)
    {
        $groups = Phonebook::distinct()->pluck('group')->filter()->sort()->values();
        return view('phonebook.edit', compact('phonebook', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Phonebook $phonebook)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'group' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $phonebook->update($request->all());

        return redirect()->route('phonebook.index')
            ->with('success', 'Kontak berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Phonebook $phonebook)
    {
        $phonebook->delete();

        return redirect()->route('phonebook.index')
            ->with('success', 'Kontak berhasil dihapus');
    }

    /**
     * Import contacts from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data);
        
        $imported = 0;
        $errors = [];
        
        foreach ($data as $row) {
            $rowData = array_combine($header, $row);
            
            // Validate required fields
            if (empty($rowData['name']) || empty($rowData['phone_number'])) {
                $errors[] = "Baris " . ($imported + 1) . ": Nama dan nomor telepon wajib diisi";
                continue;
            }
            
            try {
                Phonebook::create([
                    'name' => $rowData['name'],
                    'phone_number' => $rowData['phone_number'],
                    'email' => $rowData['email'] ?? null,
                    'notes' => $rowData['notes'] ?? null,
                    'group' => $rowData['group'] ?? null,
                    'is_active' => isset($rowData['is_active']) ? $rowData['is_active'] === 'true' : true
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($imported + 1) . ": " . $e->getMessage();
            }
        }
        
        $message = "Berhasil mengimpor {$imported} kontak";
        if (!empty($errors)) {
            $message .= ". " . count($errors) . " baris gagal diimpor.";
        }
        
        return redirect()->route('phonebook.index')
            ->with('success', $message)
            ->with('errors', $errors);
    }

    /**
     * Export contacts to Excel
     */
    public function export(Request $request)
    {
        $fileName = 'phonebook_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new PhonebookExport, $fileName);
    }

    /**
     * Download template Excel untuk import
     */
    public function template()
    {
        try {
            $fileName = 'template_import_phonebook.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PhonebookTemplateExport, $fileName);
        } catch (\Exception $e) {
            \Log::error('Error downloading template: ' . $e->getMessage());
            abort(500, 'Error downloading template: ' . $e->getMessage());
        }
    }

    /**
     * Show contact grabber page
     */
    public function grabber()
    {
        // Get all connected WhatsApp sessions
        $sessions = WhatsAppSession::where('status', 'connected')
            ->orderBy('name')
            ->get();

        // Get contact statistics
        $contactStats = [];
        foreach ($sessions as $session) {
            $contactStats[$session->session_id] = [
                'individual' => Contact::where('session_id', $session->session_id)
                    ->where('type', 'individual')
                    ->count(),
                'groups' => Contact::where('session_id', $session->session_id)
                    ->where('type', 'group')
                    ->count(),
                'total' => Contact::where('session_id', $session->session_id)->count()
            ];
        }

        return view('phonebook.grabber', compact('sessions', 'contactStats'));
    }

    /**
     * Grab contacts from WhatsApp session
     */
    public function grabContacts(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'type' => 'required|in:individual,groups,all'
        ]);

        try {
            $whatsappService = app(WhatsAppService::class);
            $contactService = app(ContactService::class);

            $session = WhatsAppSession::where('session_id', $request->session_id)
                ->where('status', 'connected')
                ->firstOrFail();

            $result = null;
            $message = '';

            switch ($request->type) {
                case 'individual':
                    $result = $whatsappService->grabIndividualContacts($session->session_id);
                    if ($result['success']) {
                        $saveResult = $contactService->saveIndividualContacts($session->session_id, $result['data']['data']['contacts'] ?? []);
                        $message = "Berhasil mengambil {$saveResult['saved_count']} kontak individual baru dan update {$saveResult['updated_count']} kontak";
                    }
                    break;

                case 'groups':
                    $result = $whatsappService->grabGroupContacts($session->session_id);
                    if ($result['success']) {
                        $saveResult = $contactService->saveGroupContacts($session->session_id, $result['data']['data']['groups'] ?? []);
                        $message = "Berhasil mengambil {$saveResult['saved_count']} grup baru dan update {$saveResult['updated_count']} grup";
                    }
                    break;

                case 'all':
                    $result = $whatsappService->grabAllContacts($session->session_id);
                    if ($result['success']) {
                        $saveResult = $contactService->saveAllContacts($session->session_id, $result['data']['data'] ?? []);
                        $message = "Berhasil mengambil {$saveResult['total_saved']} kontak baru dan update {$saveResult['total_updated']} kontak";
                    }
                    break;
            }

            if (!$result || !$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil kontak: ' . ($result['error'] ?? 'Unknown error')
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $saveResult ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get grabbed contacts from database
     */
    public function getGrabbedContacts(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'type' => 'nullable|in:individual,group'
        ]);

        try {
            $contactService = app(ContactService::class);
            $result = $contactService->getContactsBySession($request->session_id, $request->type);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil kontak: ' . $result['error']
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $result['data'],
                'count' => $result['count']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Import grabbed contacts to phonebook
     */
    public function importGrabbedContacts(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'string'
        ]);

        try {
            $contacts = Contact::where('session_id', $request->session_id)
                ->whereIn('contact_id', $request->contact_ids)
                ->get();

            $imported = 0;
            $errors = [];
            $skipped = 0;

            foreach ($contacts as $contact) {
                try {
                    // Skip grup karena tidak memiliki nomor telepon
                    if ($contact->type === 'group') {
                        $skipped++;
                        continue;
                    }

                    // Skip kontak individual yang tidak memiliki nomor telepon
                    if (empty($contact->phone_number)) {
                        $skipped++;
                        continue;
                    }

                    // Get group name for better display
                    $groupName = null;
                    if ($contact->group_id) {
                        $group = Contact::where('session_id', $contact->session_id)
                            ->where('contact_id', $contact->group_id)
                            ->where('type', 'group')
                            ->first();
                        $groupName = $group ? $group->name : $contact->group_id;
                    }
                    
                    // Check if contact already exists in phonebook
                    $existingContact = Phonebook::where('phone_number', $contact->phone_number)->first();
                    
                    if ($existingContact) {
                        // Update existing contact
                        $existingContact->update([
                            'name' => $contact->name,
                            'group' => $groupName ? "WhatsApp Group: {$groupName}" : null,
                            'notes' => $groupName ? "Imported from WhatsApp group: {$groupName}" : "Imported from WhatsApp contact"
                        ]);
                    } else {
                        // Create new contact
                        Phonebook::create([
                            'name' => $contact->name,
                            'phone_number' => $contact->phone_number,
                            'group' => $groupName ? "WhatsApp Group: {$groupName}" : null,
                            'notes' => $groupName ? "Imported from WhatsApp group: {$groupName}" : "Imported from WhatsApp contact",
                            'is_active' => true
                        ]);
                    }
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error importing {$contact->name}: " . $e->getMessage();
                }
            }

            $message = "Berhasil mengimpor {$imported} kontak ke phonebook";
            if ($skipped > 0) {
                $message .= ". {$skipped} kontak dilewati (grup atau tanpa nomor telepon)";
            }
            if (!empty($errors)) {
                $message .= ". " . count($errors) . " kontak gagal diimpor.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show form untuk membuat grup baru
     */
    public function createGroup()
    {
        return view('phonebook.create-group');
    }

    /**
     * Store grup baru
     */
    public function storeGroup(Request $request)
    {
        \Log::info('storeGroup called', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:phonebook,group',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i'
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed', $validator->errors()->toArray());
            
            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Buat grup baru dengan menambahkan kontak dummy untuk menyimpan informasi grup
            $phonebook = Phonebook::create([
                'name' => 'Grup: ' . $request->name,
                'phone_number' => 'GROUP_' . uniqid(),
                'group' => $request->name,
                'notes' => $request->description ? "Deskripsi grup: {$request->description}" : null,
                'is_active' => true
            ]);

            \Log::info('Group created successfully', ['id' => $phonebook->id, 'name' => $request->name]);

            // Clear cache after creating group
            ContactService::clearPhonebookCache();

            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => "Grup '" . html_entity_decode($request->name) . "' berhasil dibuat"
                ]);
            }

            return redirect()->route('phonebook.index')
                ->with('success', "Grup '" . html_entity_decode($request->name) . "' berhasil dibuat");

        } catch (\Exception $e) {
            \Log::error('Failed to create group', ['error' => $e->getMessage()]);
            
            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat grup: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal membuat grup: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Get all groups untuk API
     */
    public function getGroups()
    {
        $groups = Phonebook::distinct()
            ->whereNotNull('group')
            ->where('group', '!=', '')
            ->pluck('group')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    /**
     * Update grup
     */
    public function updateGroup(Request $request, $groupName)
    {
        $validator = Validator::make($request->all(), [
            'new_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update semua kontak yang memiliki grup ini
            $updated = Phonebook::where('group', $groupName)
                ->update([
                    'group' => $request->new_name,
                    'notes' => $request->description ? "Deskripsi grup: {$request->description}" : null
                ]);

            return redirect()->route('phonebook.index')
                ->with('success', "Grup '" . html_entity_decode($groupName) . "' berhasil diubah menjadi '" . html_entity_decode($request->new_name) . "'");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengubah grup: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete grup dan semua kontaknya
     */
    public function deletePhonebookGroup($groupName)
    {
        \Log::info('deletePhonebookGroup called', ['groupName' => $groupName]);
        
        try {
            // Hitung jumlah kontak dalam grup
            $contactCount = Phonebook::where('group', $groupName)->count();
            \Log::info('Contact count in group', ['groupName' => $groupName, 'count' => $contactCount]);

            // Hapus semua kontak dalam grup
            $deletedCount = Phonebook::where('group', $groupName)->delete();
            \Log::info('Deleted contacts', ['groupName' => $groupName, 'deletedCount' => $deletedCount]);

            // Clear cache after deleting group
            ContactService::clearPhonebookCache();

            return redirect()->route('phonebook.index')
                ->with('success', "Grup '" . html_entity_decode($groupName) . "' dan {$contactCount} kontak berhasil dihapus");

        } catch (\Exception $e) {
            \Log::error('Failed to delete phonebook group', [
                'groupName' => $groupName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('phonebook.index')
                ->with('error', 'Gagal menghapus grup: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan peserta grup manual (Phonebook)
     */
    public function manualGroupParticipants($groupName)
    {
        $contacts = \App\Models\Phonebook::where('group', $groupName)
            ->where('phone_number', 'not like', 'GROUP_%')
            ->orderBy('name')
            ->get();

        return view('phonebook.manual-group-participants', [
            'groupName' => $groupName,
            'contacts' => $contacts,
        ]);
    }

    /**
     * Hapus kontak dari grup manual
     */
    public function deleteManualGroupContact($groupName, $contactId)
    {
        try {
            $contact = \App\Models\Phonebook::where('id', $contactId)
                ->where('group', $groupName)
                ->where('phone_number', 'not like', 'GROUP_%')
                ->firstOrFail();

            $contactName = $contact->name;
            $contact->delete();

            return redirect()->route('phonebook.manual-group-participants', $groupName)
                ->with('success', "Kontak '" . html_entity_decode($contactName) . "' berhasil dihapus dari grup '" . html_entity_decode($groupName) . "'");

        } catch (\Exception $e) {
            return redirect()->route('phonebook.manual-group-participants', $groupName)
                ->with('error', 'Gagal menghapus kontak: ' . $e->getMessage());
        }
    }
}
