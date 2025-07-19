<?php

/**
 * Contoh Integrasi WA Blast API dengan Aplikasi Lain
 * 
 * File ini berisi contoh penggunaan API WA Blast untuk integrasi
 * dengan aplikasi web atau sistem lain.
 */

class WABlastIntegration
{
    private $baseUrl;
    private $apiKey;
    private $timeout;
    
    public function __construct($baseUrl, $apiKey, $timeout = 30)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
    }
    
    /**
     * Kirim request ke API
     */
    private function request($method, $endpoint, $data = null)
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        
        $headers = [
            'X-API-Key: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            throw new Exception('API Error: ' . ($result['message'] ?? 'Unknown error'));
        }
        
        return $result;
    }
    
    /**
     * Cek status sistem
     */
    public function getSystemStatus()
    {
        return $this->request('GET', '/integration/system-status');
    }
    
    /**
     * Dapatkan daftar session WhatsApp
     */
    public function getWhatsAppSessions()
    {
        return $this->request('GET', '/whatsapp/sessions');
    }
    
    /**
     * Buat session WhatsApp baru
     */
    public function createWhatsAppSession($name, $phoneNumber)
    {
        return $this->request('POST', '/whatsapp/sessions', [
            'name' => $name,
            'phone_number' => $phoneNumber
        ]);
    }
    
    /**
     * Dapatkan QR code untuk session
     */
    public function getQRCode($sessionId)
    {
        return $this->request('GET', "/whatsapp/sessions/{$sessionId}/qr");
    }
    
    /**
     * Kirim pesan WhatsApp
     */
    public function sendMessage($sessionId, $toNumber, $message)
    {
        return $this->request('POST', "/whatsapp/sessions/{$sessionId}/send", [
            'to_number' => $toNumber,
            'message' => $message,
            'message_type' => 'text'
        ]);
    }
    
    /**
     * Kirim pesan template
     */
    public function sendTemplateMessage($sessionId, $toNumber, $template, $variables)
    {
        return $this->request('POST', '/integration/send-template', [
            'session_id' => $sessionId,
            'to_number' => $toNumber,
            'template' => $template,
            'variables' => $variables
        ]);
    }
    
    /**
     * Kirim pesan bulk
     */
    public function bulkSend($sessionId, $messages)
    {
        return $this->request('POST', '/integration/bulk-send', [
            'session_id' => $sessionId,
            'messages' => $messages
        ]);
    }
    
    /**
     * Buat campaign blast
     */
    public function createBlastCampaign($name, $message, $phoneNumbers, $sessionId)
    {
        return $this->request('POST', '/blast/campaigns', [
            'name' => $name,
            'message' => $message,
            'phone_numbers' => $phoneNumbers,
            'session_id' => $sessionId
        ]);
    }
    
    /**
     * Mulai campaign blast
     */
    public function startBlastCampaign($campaignId)
    {
        return $this->request('POST', "/blast/campaigns/{$campaignId}/start");
    }
    
    /**
     * Dapatkan statistik campaign
     */
    public function getCampaignStatistics($campaignId)
    {
        return $this->request('GET', "/blast/campaigns/{$campaignId}/statistics");
    }
    
    /**
     * Import kontak
     */
    public function importContacts($contacts, $overwriteExisting = false)
    {
        return $this->request('POST', '/integration/import-contacts', [
            'contacts' => $contacts,
            'overwrite_existing' => $overwriteExisting
        ]);
    }
    
    /**
     * Export kontak
     */
    public function exportContacts($format = 'json', $group = null, $status = null)
    {
        $params = http_build_query([
            'format' => $format,
            'group' => $group,
            'status' => $status
        ]);
        
        return $this->request('GET', "/integration/export-contacts?{$params}");
    }
    
    /**
     * Dapatkan daftar kontak
     */
    public function getContacts($search = null, $group = null, $status = null)
    {
        $params = http_build_query(array_filter([
            'search' => $search,
            'group' => $group,
            'status' => $status
        ]));
        
        return $this->request('GET', "/phonebook?{$params}");
    }
    
    /**
     * Tambah kontak baru
     */
    public function addContact($name, $phoneNumber, $email = null, $group = null, $notes = null)
    {
        return $this->request('POST', '/phonebook', [
            'name' => $name,
            'phone_number' => $phoneNumber,
            'email' => $email,
            'group' => $group,
            'notes' => $notes,
            'is_active' => true
        ]);
    }
    
    /**
     * Dapatkan daftar grup
     */
    public function getGroups()
    {
        return $this->request('GET', '/phonebook-groups');
    }
    
    /**
     * Cari kontak
     */
    public function searchContacts($query)
    {
        return $this->request('GET', "/phonebook-search?q={$query}");
    }
}

// Contoh penggunaan
try {
    // Inisialisasi integrasi
    $waBlast = new WABlastIntegration(
        'https://your-wa-blast-domain.com',
        'your-api-key-here'
    );
    
    // 1. Cek status sistem
    echo "=== Status Sistem ===\n";
    $status = $waBlast->getSystemStatus();
    print_r($status);
    
    // 2. Dapatkan session WhatsApp
    echo "\n=== Session WhatsApp ===\n";
    $sessions = $waBlast->getWhatsAppSessions();
    print_r($sessions);
    
    // 3. Kirim pesan template
    echo "\n=== Kirim Pesan Template ===\n";
    $templateResult = $waBlast->sendTemplateMessage(
        1, // session_id
        '6281234567890', // to_number
        'Halo {name}, ada promo menarik untuk Anda: {promo_message}',
        [
            'name' => 'John Doe',
            'promo_message' => 'Diskon 50% untuk semua produk!'
        ]
    );
    print_r($templateResult);
    
    // 4. Kirim pesan bulk
    echo "\n=== Kirim Pesan Bulk ===\n";
    $bulkResult = $waBlast->bulkSend(1, [
        [
            'to_number' => '6281234567890',
            'message' => 'Halo, ini pesan pertama'
        ],
        [
            'to_number' => '6281234567891',
            'message' => 'Halo, ini pesan kedua'
        ]
    ]);
    print_r($bulkResult);
    
    // 5. Buat campaign blast
    echo "\n=== Buat Campaign Blast ===\n";
    $campaignResult = $waBlast->createBlastCampaign(
        'Campaign Promo',
        'Halo {name}, ada promo menarik untuk Anda!',
        ['6281234567890', '6281234567891'],
        1
    );
    print_r($campaignResult);
    
    // 6. Import kontak
    echo "\n=== Import Kontak ===\n";
    $importResult = $waBlast->importContacts([
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
    ]);
    print_r($importResult);
    
    // 7. Export kontak
    echo "\n=== Export Kontak ===\n";
    $exportResult = $waBlast->exportContacts('json', 'VIP', 'active');
    print_r($exportResult);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

/**
 * Contoh Integrasi dengan Framework Laravel
 */
class LaravelWABlastIntegration
{
    private $baseUrl;
    private $apiKey;
    
    public function __construct($baseUrl, $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }
    
    public function sendNotification($userId, $message)
    {
        // Ambil data user dari database
        $user = User::find($userId);
        
        if (!$user) {
            throw new Exception('User not found');
        }
        
        // Kirim pesan WhatsApp
        $waBlast = new WABlastIntegration($this->baseUrl, $this->apiKey);
        
        return $waBlast->sendTemplateMessage(
            1, // session_id
            $user->phone_number,
            'Halo {name}, {message}',
            [
                'name' => $user->name,
                'message' => $message
            ]
        );
    }
    
    public function sendBulkNotification($userIds, $message)
    {
        $users = User::whereIn('id', $userIds)->get();
        $messages = [];
        
        foreach ($users as $user) {
            $messages[] = [
                'to_number' => $user->phone_number,
                'message' => "Halo {$user->name}, {$message}"
            ];
        }
        
        $waBlast = new WABlastIntegration($this->baseUrl, $this->apiKey);
        return $waBlast->bulkSend(1, $messages);
    }
}

/**
 * Contoh Integrasi dengan E-commerce
 */
class EcommerceWABlastIntegration
{
    private $waBlast;
    
    public function __construct($baseUrl, $apiKey)
    {
        $this->waBlast = new WABlastIntegration($baseUrl, $apiKey);
    }
    
    public function sendOrderConfirmation($order)
    {
        return $this->waBlast->sendTemplateMessage(
            1,
            $order->customer->phone_number,
            'Terima kasih {name}, pesanan Anda dengan ID {order_id} telah dikonfirmasi. Total: {total}',
            [
                'name' => $order->customer->name,
                'order_id' => $order->order_number,
                'total' => number_format($order->total, 0, ',', '.')
            ]
        );
    }
    
    public function sendOrderStatusUpdate($order, $status)
    {
        return $this->waBlast->sendTemplateMessage(
            1,
            $order->customer->phone_number,
            'Halo {name}, status pesanan {order_id} telah diperbarui menjadi: {status}',
            [
                'name' => $order->customer->name,
                'order_id' => $order->order_number,
                'status' => $status
            ]
        );
    }
    
    public function sendPromoCampaign($customers, $promoMessage)
    {
        $messages = [];
        
        foreach ($customers as $customer) {
            $messages[] = [
                'to_number' => $customer->phone_number,
                'message' => "Halo {$customer->name}, {$promoMessage}"
            ];
        }
        
        return $this->waBlast->bulkSend(1, $messages);
    }
} 