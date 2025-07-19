<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\WhatsAppSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ContactService
{
    /**
     * Clear phonebook cache
     */
    public static function clearPhonebookCache(): void
    {
        // Clear specific phonebook cache keys
        $keys = Cache::get('phonebook_cache_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('phonebook_cache_keys');
        
        // Clear contact counts cache
        Cache::forget('contact_counts');
        
        // Clear any phonebook index cache
        Cache::forget('phonebook_index_*');
    }

    /**
     * Add cache key to tracking
     */
    public static function addCacheKey(string $key): void
    {
        $keys = Cache::get('phonebook_cache_keys', []);
        if (!in_array($key, $keys)) {
            $keys[] = $key;
            Cache::put('phonebook_cache_keys', $keys, now()->addDays(30));
        }
    }

    /**
     * Get optimized contact counts
     */
    public static function getContactCounts(): array
    {
        return Cache::remember('contact_counts', 300, function () {
            $allContactsCount = Contact::where('type', 'individual')
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

            $activeContactsCount = Contact::where('type', 'individual')
                ->where('status', 'active')
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

            return [
                'all' => $allContactsCount,
                'active' => $activeContactsCount,
                'individual' => $individualContactsCount
            ];
        });
    }

    /**
     * Simpan kontak grup ke database
     */
    public function saveGroupContacts(string $sessionId, array $groups): array
    {
        try {
            DB::beginTransaction();

            $savedCount = 0;
            $updatedCount = 0;
            $errors = [];
            $participantSaved = 0;
            $participantUpdated = 0;

            foreach ($groups as $group) {
                try {
                    $contactData = [
                        'session_id' => $sessionId,
                        'contact_id' => isset($group['id']) ? $group['id'] : (isset($group['jid']) ? $group['jid'] : uniqid()),
                        'name' => isset($group['name']) ? $group['name'] : (isset($group['subject']) ? $group['subject'] : 'Unknown Group'),
                        'type' => 'group',
                        'group_name' => isset($group['name']) ? $group['name'] : (isset($group['subject']) ? $group['subject'] : 'Unknown Group'),
                        'group_description' => isset($group['desc']) ? $group['desc'] : (isset($group['description']) ? $group['description'] : null),
                        'group_participants_count' => isset($group['participants']) ? count($group['participants']) : (isset($group['participants_count']) ? $group['participants_count'] : (isset($group['size']) ? $group['size'] : 0)),
                        'is_admin' => isset($group['is_admin']) ? $group['is_admin'] : false,
                        'profile_picture' => isset($group['profile_picture']) ? $group['profile_picture'] : (isset($group['picture']) ? $group['picture'] : null),
                        'status' => 'active',
                        'grabbed_at' => now()
                    ];

                    // Cek apakah kontak grup sudah ada
                    $existingContact = Contact::where('session_id', $sessionId)
                        ->where('contact_id', $contactData['contact_id'])
                        ->where('type', 'group')
                        ->first();

                    if ($existingContact) {
                        $existingContact->update($contactData);
                        $updatedCount++;
                        Log::info('Group updated', [
                            'session_id' => $sessionId,
                            'contact_id' => $contactData['contact_id'],
                            'name' => $contactData['name']
                        ]);
                    } else {
                        try {
                            Contact::create($contactData);
                            $savedCount++;
                            Log::info('Group saved successfully', [
                                'session_id' => $sessionId,
                                'contact_id' => $contactData['contact_id'],
                                'name' => $contactData['name']
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to save group', [
                                'session_id' => $sessionId,
                                'contact_id' => $contactData['contact_id'],
                                'name' => $contactData['name'],
                                'error' => $e->getMessage(),
                                'data' => $contactData
                            ]);
                            $errors[] = "Error saving group: " . $e->getMessage();
                        }
                    }

                    // Simpan peserta grup sebagai kontak individual
                    if (isset($group['participants']) && is_array($group['participants'])) {
                        foreach ($group['participants'] as $participant) {
                            try {
                                $participantData = [
                                    'session_id' => $sessionId,
                                    'contact_id' => isset($participant['id']) ? $participant['id'] : uniqid(),
                                    'name' => isset($participant['name']) ? $participant['name'] : (isset($participant['pushname']) ? $participant['pushname'] : (isset($participant['number']) ? $participant['number'] : 'Unknown')),
                                    'phone_number' => isset($participant['number']) ? $participant['number'] : (isset($participant['phone']) ? $participant['phone'] : null),
                                    'type' => 'individual',
                                    'group_id' => $contactData['contact_id'],
                                    'is_admin' => isset($participant['isAdmin']) ? $participant['isAdmin'] : false,
                                    'profile_picture' => isset($participant['profile_picture']) ? $participant['profile_picture'] : (isset($participant['picture']) ? $participant['picture'] : null),
                                    'status' => 'active',
                                    'grabbed_at' => now()
                                ];

                                // Cek apakah kontak individual sudah ada
                                $existingParticipant = Contact::where('session_id', $sessionId)
                                    ->where('contact_id', $participantData['contact_id'])
                                    ->where('type', 'individual')
                                    ->first();

                                if ($existingParticipant) {
                                    $existingParticipant->update($participantData);
                                    $participantUpdated++;
                                    Log::info('Participant updated', [
                                        'session_id' => $sessionId,
                                        'contact_id' => $participantData['contact_id'],
                                        'phone_number' => $participantData['phone_number'],
                                        'name' => $participantData['name']
                                    ]);
                                } else {
                                    // Hanya simpan jika ada nomor
                                    if (!empty($participantData['phone_number'])) {
                                        try {
                                            Contact::create($participantData);
                                            $participantSaved++;
                                            Log::info('Participant saved successfully', [
                                                'session_id' => $sessionId,
                                                'contact_id' => $participantData['contact_id'],
                                                'phone_number' => $participantData['phone_number'],
                                                'name' => $participantData['name']
                                            ]);
                                        } catch (\Exception $e) {
                                            Log::error('Failed to save participant', [
                                                'session_id' => $sessionId,
                                                'contact_id' => $participantData['contact_id'],
                                                'phone_number' => $participantData['phone_number'],
                                                'name' => $participantData['name'],
                                                'error' => $e->getMessage(),
                                                'data' => $participantData
                                            ]);
                                            $errors[] = "Error saving participant: " . $e->getMessage();
                                        }
                                    } else {
                                        Log::warning('Participant skipped (no phone_number)', [
                                            'session_id' => $sessionId,
                                            'contact_id' => $participantData['contact_id'],
                                            'name' => $participantData['name']
                                        ]);
                                    }
                                }
                            } catch (\Exception $e) {
                                $errors[] = "Error saving participant: " . $e->getMessage();
                                Log::error('Error saving group participant', [
                                    'session_id' => $sessionId,
                                    'participant' => $participant,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }

                } catch (\Exception $e) {
                    $groupName = isset($group['name']) ? $group['name'] : 'Unknown';
                    $errors[] = "Error saving group {$groupName}: " . $e->getMessage();
                    Log::error('Error saving group contact', [
                        'session_id' => $sessionId,
                        'group' => $group,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            Log::info('Group contacts saved successfully', [
                'session_id' => $sessionId,
                'saved_count' => $savedCount,
                'updated_count' => $updatedCount,
                'participant_saved' => $participantSaved,
                'participant_updated' => $participantUpdated,
                'total_groups' => count($groups),
                'errors_count' => count($errors)
            ]);

            return [
                'success' => true,
                'saved_count' => $savedCount,
                'updated_count' => $updatedCount,
                'participant_saved' => $participantSaved,
                'participant_updated' => $participantUpdated,
                'total_groups' => count($groups),
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving group contacts', [
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
     * Simpan kontak individual ke database
     */
    public function saveIndividualContacts(string $sessionId, array $contacts): array
    {
        try {
            DB::beginTransaction();

            $savedCount = 0;
            $updatedCount = 0;
            $errors = [];

            foreach ($contacts as $contact) {
                try {
                    $contactData = [
                        'session_id' => $sessionId,
                        'contact_id' => isset($contact['id']) ? $contact['id'] : (isset($contact['jid']) ? $contact['jid'] : uniqid()),
                        'name' => isset($contact['name']) ? $contact['name'] : (isset($contact['pushname']) ? $contact['pushname'] : (isset($contact['number']) ? $contact['number'] : 'Unknown Contact')),
                        'phone_number' => isset($contact['number']) ? $contact['number'] : (isset($contact['phone']) ? $contact['phone'] : null),
                        'type' => 'individual',
                        'group_id' => isset($contact['group_id']) ? $contact['group_id'] : null,
                        'is_admin' => isset($contact['is_admin']) ? $contact['is_admin'] : false,
                        'profile_picture' => isset($contact['profile_picture']) ? $contact['profile_picture'] : (isset($contact['picture']) ? $contact['picture'] : null),
                        'status' => 'active',
                        'grabbed_at' => now()
                    ];

                    // Hanya simpan jika ada nomor
                    if (empty($contactData['phone_number'])) {
                        continue;
                    }

                    // Cek apakah kontak sudah ada
                    $existingContact = Contact::where('session_id', $sessionId)
                        ->where('contact_id', $contactData['contact_id'])
                        ->where('type', 'individual')
                        ->first();

                    if ($existingContact) {
                        $existingContact->update($contactData);
                        $updatedCount++;
                        Log::info('Individual contact updated', [
                            'session_id' => $sessionId,
                            'contact_id' => $contactData['contact_id'],
                            'phone_number' => $contactData['phone_number'],
                            'name' => $contactData['name']
                        ]);
                    } else {
                        try {
                            Contact::create($contactData);
                            $savedCount++;
                            Log::info('Individual contact saved successfully', [
                                'session_id' => $sessionId,
                                'contact_id' => $contactData['contact_id'],
                                'phone_number' => $contactData['phone_number'],
                                'name' => $contactData['name']
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to save individual contact', [
                                'session_id' => $sessionId,
                                'contact_id' => $contactData['contact_id'],
                                'phone_number' => $contactData['phone_number'],
                                'name' => $contactData['name'],
                                'error' => $e->getMessage(),
                                'data' => $contactData
                            ]);
                            $errors[] = "Error saving individual contact: " . $e->getMessage();
                        }
                    }

                } catch (\Exception $e) {
                    $contactName = isset($contact['name']) ? $contact['name'] : 'Unknown';
                    $errors[] = "Error saving contact {$contactName}: " . $e->getMessage();
                    Log::error('Error saving individual contact', [
                        'session_id' => $sessionId,
                        'contact' => $contact,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            Log::info('Individual contacts saved successfully', [
                'session_id' => $sessionId,
                'saved_count' => $savedCount,
                'updated_count' => $updatedCount,
                'total_contacts' => count($contacts),
                'errors_count' => count($errors)
            ]);

            return [
                'success' => true,
                'saved_count' => $savedCount,
                'updated_count' => $updatedCount,
                'total_contacts' => count($contacts),
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving individual contacts', [
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
     * Simpan semua kontak (grup dan individual) ke database
     */
    public function saveAllContacts(string $sessionId, array $allContacts): array
    {
        try {
            $groups = $allContacts['groups'] ?? [];
            $contacts = $allContacts['contacts'] ?? [];

            // Simpan grup terlebih dahulu
            $groupResult = $this->saveGroupContacts($sessionId, $groups);
            if (!$groupResult['success']) {
                return $groupResult;
            }

            // Simpan kontak individual
            $contactResult = $this->saveIndividualContacts($sessionId, $contacts);
            if (!$contactResult['success']) {
                return $contactResult;
            }

            return [
                'success' => true,
                'groups' => $groupResult,
                'contacts' => $contactResult,
                'total_saved' => $groupResult['saved_count'] + $contactResult['saved_count'],
                'total_updated' => $groupResult['updated_count'] + $contactResult['updated_count']
            ];

        } catch (\Exception $e) {
            Log::error('Error saving all contacts', [
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
     * Ambil kontak dari database berdasarkan session
     */
    public function getContactsBySession(string $sessionId, string $type = null): array
    {
        try {
            $query = Contact::where('session_id', $sessionId);

            if ($type) {
                $query->where('type', $type);
            }

            $contacts = $query->orderBy('name')->get();

            return [
                'success' => true,
                'data' => $contacts,
                'count' => $contacts->count()
            ];

        } catch (\Exception $e) {
            Log::error('Error getting contacts by session', [
                'session_id' => $sessionId,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Hapus kontak berdasarkan session
     */
    public function deleteContactsBySession(string $sessionId): array
    {
        try {
            $deletedCount = Contact::where('session_id', $sessionId)->delete();

            Log::info('Contacts deleted by session', [
                'session_id' => $sessionId,
                'deleted_count' => $deletedCount
            ]);

            return [
                'success' => true,
                'deleted_count' => $deletedCount
            ];

        } catch (\Exception $e) {
            Log::error('Error deleting contacts by session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
} 