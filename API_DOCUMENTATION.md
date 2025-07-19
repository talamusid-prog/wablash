# WA Blast API Documentation

## Overview
API ini menyediakan endpoint untuk mengintegrasikan aplikasi WA Blast dengan aplikasi web lain. Semua endpoint mengembalikan response dalam format JSON.

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
Saat ini API menggunakan Laravel Sanctum untuk autentikasi. Untuk menggunakan API, Anda perlu:

1. Mendapatkan token autentikasi
2. Menyertakan token dalam header `Authorization: Bearer {token}`

## Response Format
Semua response mengikuti format standar:
```json
{
    "success": true/false,
    "message": "Pesan response",
    "data": {...},
    "errors": {...} // jika ada error
}
```

## Endpoints

### 1. WhatsApp Sessions

#### GET /whatsapp/sessions
Mendapatkan daftar semua session WhatsApp.

**Response:**
```json
{
    "success": true,
    "sessions": [
        {
            "id": 1,
            "name": "Session 1",
            "session_id": "uuid-session",
            "phone_number": "6281234567890",
            "status": "connected",
            "is_active": true,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    ]
}
```

#### POST /whatsapp/sessions
Membuat session WhatsApp baru.

**Request Body:**
```json
{
    "name": "Session Baru",
    "phone_number": "6281234567890"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Session created, waiting for QR code...",
    "session": {
        "id": 1,
        "name": "Session Baru",
        "session_id": "uuid-session",
        "phone_number": "6281234567890",
        "status": "connecting",
        "is_active": true
    },
    "status": "connecting"
}
```

#### GET /whatsapp/sessions/{id}
Mendapatkan detail session WhatsApp.

#### PUT /whatsapp/sessions/{id}
Update session WhatsApp.

**Request Body:**
```json
{
    "status": "connected",
    "phone_number": "6281234567890",
    "is_active": true
}
```

#### DELETE /whatsapp/sessions/{id}
Hapus session WhatsApp.

#### GET /whatsapp/sessions/{id}/qr
Mendapatkan QR code untuk session.

**Response:**
```json
{
    "success": true,
    "data": {
        "session_id": "uuid-session",
        "qr_code": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...",
        "status": "connecting"
    }
}
```

#### POST /whatsapp/sessions/{id}/send
Kirim pesan melalui WhatsApp.

**Request Body:**
```json
{
    "to_number": "6281234567890",
    "message": "Halo, ini pesan test",
    "message_type": "text"
}
```

### 2. Blast Campaigns

#### GET /blast/campaigns
Mendapatkan daftar semua campaign blast.

**Response:**
```json
{
    "success": true,
    "campaigns": [
        {
            "id": 1,
            "name": "Campaign Promo",
            "message_template": "Halo {name}, ada promo menarik untuk Anda!",
            "target_numbers": ["6281234567890", "6281234567891"],
            "status": "draft",
            "total_count": 2,
            "sent_count": 0,
            "failed_count": 0,
            "session_id": "uuid-session",
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    ]
}
```

#### POST /blast/campaigns
Membuat campaign blast baru.

**Request Body:**
```json
{
    "name": "Campaign Promo",
    "message": "Halo {name}, ada promo menarik untuk Anda!",
    "phone_numbers": ["6281234567890", "6281234567891"],
    "session_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "message": "Blast campaign created successfully",
    "data": {
        "id": 1,
        "name": "Campaign Promo",
        "message_template": "Halo {name}, ada promo menarik untuk Anda!",
        "target_numbers": ["6281234567890", "6281234567891"],
        "status": "draft",
        "total_count": 2,
        "sent_count": 0,
        "failed_count": 0
    }
}
```

#### GET /blast/campaigns/{id}
Mendapatkan detail campaign blast.

#### PUT /blast/campaigns/{id}
Update campaign blast.

**Request Body:**
```json
{
    "name": "Campaign Promo Updated",
    "message_template": "Halo {name}, ada promo menarik untuk Anda!",
    "status": "scheduled",
    "scheduled_at": "2024-01-01T10:00:00.000000Z"
}
```

#### DELETE /blast/campaigns/{id}
Hapus campaign blast.

#### POST /blast/campaigns/{id}/start
Mulai campaign blast.

**Response:**
```json
{
    "success": true,
    "message": "Blast campaign started successfully",
    "data": {
        "id": 1,
        "name": "Campaign Promo",
        "status": "running"
    }
}
```

#### GET /blast/campaigns/{id}/statistics
Mendapatkan statistik campaign.

**Response:**
```json
{
    "success": true,
    "data": {
        "total": 100,
        "sent": 75,
        "failed": 5,
        "pending": 20,
        "success_rate": 75.0
    }
}
```

#### GET /blast/campaigns/{id}/messages
Mendapatkan daftar pesan campaign.

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "campaign_id": 1,
                "phone_number": "6281234567890",
                "message_content": "Halo {name}, ada promo menarik untuk Anda!",
                "status": "sent",
                "sent_at": "2024-01-01T10:00:00.000000Z"
            }
        ],
        "total": 100
    }
}
```

### 3. Phonebook

#### GET /phonebook
Mendapatkan daftar kontak phonebook.

**Query Parameters:**
- `search`: Pencarian berdasarkan nama atau nomor
- `group`: Filter berdasarkan grup
- `status`: Filter berdasarkan status (active/inactive)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "phone_number": "6281234567890",
                "email": "john@example.com",
                "group": "VIP",
                "is_active": true,
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "total": 50
    }
}
```

#### POST /phonebook
Tambah kontak baru.

**Request Body:**
```json
{
    "name": "John Doe",
    "phone_number": "6281234567890",
    "email": "john@example.com",
    "notes": "Customer VIP",
    "group": "VIP",
    "is_active": true
}
```

#### GET /phonebook/{id}
Mendapatkan detail kontak.

#### PUT /phonebook/{id}
Update kontak.

#### DELETE /phonebook/{id}
Hapus kontak.

#### GET /phonebook-groups
Mendapatkan daftar semua grup.

**Response:**
```json
{
    "success": true,
    "data": ["VIP", "Regular", "Premium"]
}
```

#### GET /phonebook-search
Pencarian kontak untuk autocomplete.

**Query Parameters:**
- `q`: Query pencarian

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "phone_number": "6281234567890",
            "group": "VIP"
        }
    ]
}
```

### 4. Messages

#### GET /messages
Mendapatkan daftar pesan WhatsApp.

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "session_id": "uuid-session",
                "message_id": "uuid-message",
                "from_number": "6281234567890",
                "to_number": "6281234567891",
                "message_content": "Halo, ini pesan test",
                "message_type": "text",
                "status": "sent",
                "sent_at": "2024-01-01T10:00:00.000000Z"
            }
        ],
        "total": 100
    }
}
```

#### GET /messages/{id}
Mendapatkan detail pesan.

#### DELETE /messages/{id}
Hapus pesan.

#### POST /messages/{id}/retry
Coba kirim ulang pesan yang gagal.

### 5. System Status

#### GET /engine-status
Mendapatkan status WhatsApp engine.

**Response:**
```json
{
    "success": true,
    "data": {
        "engine_status": "running",
        "active_sessions": 2,
        "total_messages_sent": 150,
        "last_activity": "2024-01-01T10:00:00.000000Z"
    }
}
```

## Error Handling

### Error Response Format
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Error detail"]
    }
}
```

### Common HTTP Status Codes
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `404`: Not Found
- `422`: Validation Error
- `500`: Internal Server Error

## Rate Limiting
API memiliki rate limiting untuk mencegah abuse:
- 60 requests per minute per IP
- 1000 requests per hour per IP

## Webhook Support
Untuk integrasi real-time, Anda dapat mendaftarkan webhook URL yang akan dipanggil ketika:
- Pesan diterima
- Status campaign berubah
- Session status berubah

**Webhook Registration:**
```json
{
    "url": "https://your-domain.com/webhook",
    "events": ["message_received", "campaign_status_changed", "session_status_changed"]
}
```

## SDK Examples

### JavaScript/Node.js
```javascript
const axios = require('axios');

const api = axios.create({
    baseURL: 'http://your-domain.com/api/v1',
    headers: {
        'Authorization': 'Bearer YOUR_TOKEN',
        'Content-Type': 'application/json'
    }
});

// Send message
const sendMessage = async (sessionId, toNumber, message) => {
    try {
        const response = await api.post(`/whatsapp/sessions/${sessionId}/send`, {
            to_number: toNumber,
            message: message
        });
        return response.data;
    } catch (error) {
        console.error('Error sending message:', error.response.data);
    }
};

// Create blast campaign
const createCampaign = async (name, message, phoneNumbers, sessionId) => {
    try {
        const response = await api.post('/blast/campaigns', {
            name: name,
            message: message,
            phone_numbers: phoneNumbers,
            session_id: sessionId
        });
        return response.data;
    } catch (error) {
        console.error('Error creating campaign:', error.response.data);
    }
};
```

### PHP
```php
<?php

class WABlastAPI {
    private $baseUrl;
    private $token;
    
    public function __construct($baseUrl, $token) {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
    }
    
    private function request($method, $endpoint, $data = null) {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function sendMessage($sessionId, $toNumber, $message) {
        return $this->request('POST', "/whatsapp/sessions/{$sessionId}/send", [
            'to_number' => $toNumber,
            'message' => $message
        ]);
    }
    
    public function createCampaign($name, $message, $phoneNumbers, $sessionId) {
        return $this->request('POST', '/blast/campaigns', [
            'name' => $name,
            'message' => $message,
            'phone_numbers' => $phoneNumbers,
            'session_id' => $sessionId
        ]);
    }
}

// Usage
$api = new WABlastAPI('http://your-domain.com/api/v1', 'YOUR_TOKEN');

// Send message
$result = $api->sendMessage('session-id', '6281234567890', 'Halo, ini pesan test');

// Create campaign
$result = $api->createCampaign('Promo Campaign', 'Halo {name}, ada promo menarik!', 
    ['6281234567890', '6281234567891'], 1);
?>
```

## Support
Untuk bantuan teknis atau pertanyaan tentang API, silakan hubungi tim support kami. 