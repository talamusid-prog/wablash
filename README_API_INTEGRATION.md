# WA Blast API Integration Guide

## Overview
WA Blast menyediakan REST API yang lengkap untuk integrasi dengan aplikasi web lain. API ini memungkinkan Anda untuk mengirim pesan WhatsApp, mengelola campaign blast, dan mengelola kontak phonebook melalui HTTP requests.

## Quick Start

### 1. Setup Authentication
```bash
# Set API key di environment
API_KEY_1=your-secret-api-key-here
API_KEY_2=another-api-key-for-different-apps
```

### 2. Test Connection
```bash
curl -X GET "https://your-domain.com/api/v1/integration/system-status" \
  -H "X-API-Key: your-secret-api-key-here"
```

### 3. Send First Message
```bash
curl -X POST "https://your-domain.com/api/v1/whatsapp/sessions/1/send" \
  -H "X-API-Key: your-secret-api-key-here" \
  -H "Content-Type: application/json" \
  -d '{
    "to_number": "6281234567890",
    "message": "Halo, ini pesan test dari API!"
  }'
```

## Authentication Methods

### 1. API Key (Recommended)
```bash
curl -H "X-API-Key: your-api-key" https://your-domain.com/api/v1/whatsapp/sessions
```

### 2. Bearer Token
```bash
curl -H "Authorization: Bearer your-token" https://your-domain.com/api/v1/whatsapp/sessions
```

### 3. Basic Auth
```bash
curl -u "username:password" https://your-domain.com/api/v1/whatsapp/sessions
```

## Core Features

### 1. WhatsApp Session Management

#### Create Session
```bash
curl -X POST "https://your-domain.com/api/v1/whatsapp/sessions" \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "My WhatsApp Session",
    "phone_number": "6281234567890"
  }'
```

#### Get QR Code
```bash
curl -X GET "https://your-domain.com/api/v1/whatsapp/sessions/1/qr" \
  -H "X-API-Key: your-api-key"
```

#### Send Message
```bash
curl -X POST "https://your-domain.com/api/v1/whatsapp/sessions/1/send" \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "to_number": "6281234567890",
    "message": "Halo, ini pesan dari API!",
    "message_type": "text"
  }'
```

### 2. Template Messages

#### Send Template Message
```bash
curl -X POST "https://your-domain.com/api/v1/integration/send-template" \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": 1,
    "to_number": "6281234567890",
    "template": "Halo {name}, ada promo menarik untuk Anda: {promo_message}",
    "variables": {
      "name": "John Doe",
      "promo_message": "Diskon 50% untuk semua produk!"
    }
  }'
```

### 3. Bulk Messages

#### Send Bulk Messages
```bash
curl -X POST "https://your-domain.com/api/v1/integration/bulk-send" \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": 1,
    "messages": [
      {
        "to_number": "6281234567890",
        "message": "Halo, ini pesan pertama"
      },
      {
        "to_number": "6281234567891",
        "message": "Halo, ini pesan kedua"
      }
    ]
  }'
```

### 4. Blast Campaigns

#### Create Campaign
```bash
curl -X POST "https://your-domain.com/api/v1/blast/campaigns" \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Promo Campaign",
    "message": "Halo {name}, ada promo menarik untuk Anda!",
    "phone_numbers": ["6281234567890", "6281234567891"],
    "session_id": 1
  }'
```

#### Start Campaign
```bash
curl -X POST "https://your-domain.com/api/v1/blast/campaigns/1/start" \
  -H "X-API-Key: your-api-key"
```

#### Get Campaign Statistics
```bash
curl -X GET "https://your-domain.com/api/v1/blast/campaigns/1/statistics" \
  -H "X-API-Key: your-api-key"
```

### 5. Contact Management

#### Import Contacts
```bash
curl -X POST "https://your-domain.com/api/v1/integration/import-contacts" \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "contacts": [
      {
        "name": "John Doe",
        "phone_number": "6281234567890",
        "email": "john@example.com",
        "group": "VIP"
      },
      {
        "name": "Jane Smith",
        "phone_number": "6281234567891",
        "email": "jane@example.com",
        "group": "Regular"
      }
    ],
    "overwrite_existing": false
  }'
```

#### Export Contacts
```bash
curl -X GET "https://your-domain.com/api/v1/integration/export-contacts?format=json&group=VIP&status=active" \
  -H "X-API-Key: your-api-key"
```

## SDK Examples

### JavaScript/Node.js
```javascript
const WABlastSDK = require('./wa-blast-sdk.js');

const waBlast = new WABlastSDK({
    baseUrl: 'https://your-domain.com',
    apiKey: 'your-api-key'
});

// Send template message
const result = await waBlast.sendTemplateMessage(
    1,
    '6281234567890',
    'Halo {name}, ada promo menarik untuk Anda: {promo_message}',
    {
        name: 'John Doe',
        promo_message: 'Diskon 50% untuk semua produk!'
    }
);

console.log(result);
```

### PHP
```php
<?php

require_once 'WABlastIntegration.php';

$waBlast = new WABlastIntegration(
    'https://your-domain.com',
    'your-api-key'
);

// Send message
$result = $waBlast->sendMessage(
    1,
    '6281234567890',
    'Halo, ini pesan dari PHP!'
);

print_r($result);
?>
```

### Python
```python
import requests
import json

class WABlastAPI:
    def __init__(self, base_url, api_key):
        self.base_url = base_url
        self.api_key = api_key
        self.headers = {
            'X-API-Key': api_key,
            'Content-Type': 'application/json'
        }
    
    def send_message(self, session_id, to_number, message):
        url = f"{self.base_url}/api/v1/whatsapp/sessions/{session_id}/send"
        data = {
            'to_number': to_number,
            'message': message
        }
        
        response = requests.post(url, headers=self.headers, json=data)
        return response.json()

# Usage
wa_blast = WABlastAPI('https://your-domain.com', 'your-api-key')
result = wa_blast.send_message(1, '6281234567890', 'Halo dari Python!')
print(result)
```

## Integration Examples

### 1. E-commerce Integration

#### Order Confirmation
```javascript
async function sendOrderConfirmation(order) {
    const waBlast = new WABlastSDK({
        baseUrl: 'https://your-domain.com',
        apiKey: 'your-api-key'
    });

    return await waBlast.sendTemplateMessage(
        1,
        order.customer.phone_number,
        'Terima kasih {name}, pesanan Anda dengan ID {order_id} telah dikonfirmasi. Total: {total}',
        {
            name: order.customer.name,
            order_id: order.order_number,
            total: new Intl.NumberFormat('id-ID').format(order.total)
        }
    );
}
```

#### Order Status Update
```javascript
async function sendOrderStatusUpdate(order, status) {
    const waBlast = new WABlastSDK({
        baseUrl: 'https://your-domain.com',
        apiKey: 'your-api-key'
    });

    return await waBlast.sendTemplateMessage(
        1,
        order.customer.phone_number,
        'Halo {name}, status pesanan {order_id} telah diperbarui menjadi: {status}',
        {
            name: order.customer.name,
            order_id: order.order_number,
            status: status
        }
    );
}
```

### 2. CRM Integration

#### Lead Notification
```javascript
async function sendLeadNotification(lead) {
    const waBlast = new WABlastSDK({
        baseUrl: 'https://your-domain.com',
        apiKey: 'your-api-key'
    });

    return await waBlast.sendTemplateMessage(
        1,
        lead.phone_number,
        'Halo {name}, terima kasih telah menghubungi kami. Tim kami akan segera menghubungi Anda.',
        {
            name: lead.name
        }
    );
}
```

### 3. Marketing Automation

#### Promo Campaign
```javascript
async function sendPromoCampaign(customers, promoMessage) {
    const waBlast = new WABlastSDK({
        baseUrl: 'https://your-domain.com',
        apiKey: 'your-api-key'
    });

    const messages = customers.map(customer => ({
        to_number: customer.phone_number,
        message: `Halo ${customer.name}, ${promoMessage}`
    }));

    return await waBlast.bulkSend(1, messages);
}
```

## Webhook Integration

### 1. Configure Webhook
```bash
curl -X POST "https://your-domain.com/api/v1/integration/webhook-config" \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "enabled": true,
    "url": "https://your-app.com/webhook",
    "events": ["message_received", "campaign_completed"],
    "secret": "your-webhook-secret"
  }'
```

### 2. Handle Webhook Events
```javascript
// Express.js example
app.post('/webhook', (req, res) => {
    const { event, data } = req.body;
    
    switch (event) {
        case 'message_received':
            handleMessageReceived(data);
            break;
        case 'campaign_completed':
            handleCampaignCompleted(data);
            break;
    }
    
    res.status(200).json({ success: true });
});

function handleMessageReceived(data) {
    console.log('New message received:', data);
    // Process the message
}

function handleCampaignCompleted(data) {
    console.log('Campaign completed:', data);
    // Update campaign status
}
```

## Error Handling

### Common Error Responses
```json
{
    "success": false,
    "message": "Error message",
    "error_code": "ERROR_CODE"
}
```

### Error Codes
- `AUTH001`: Unauthorized access
- `WHATSAPP001`: Invalid session
- `WHATSAPP002`: Session not connected
- `MESSAGE001`: Message send failed
- `CAMPAIGN001`: Campaign not found
- `CONTACT001`: Contact not found
- `RATE001`: Rate limit exceeded
- `VALID001`: Validation error
- `INTERNAL001`: Internal server error

### Rate Limiting
- 60 requests per minute per IP
- 1000 requests per hour per IP

## Best Practices

### 1. Error Handling
```javascript
try {
    const result = await waBlast.sendMessage(1, '6281234567890', 'Hello');
    console.log('Success:', result);
} catch (error) {
    console.error('Error:', error.message);
    // Handle error appropriately
}
```

### 2. Retry Logic
```javascript
async function sendMessageWithRetry(sessionId, toNumber, message, maxRetries = 3) {
    for (let i = 0; i < maxRetries; i++) {
        try {
            return await waBlast.sendMessage(sessionId, toNumber, message);
        } catch (error) {
            if (i === maxRetries - 1) throw error;
            await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1)));
        }
    }
}
```

### 3. Batch Processing
```javascript
async function sendBatchMessages(messages, batchSize = 10) {
    const results = [];
    
    for (let i = 0; i < messages.length; i += batchSize) {
        const batch = messages.slice(i, i + batchSize);
        const result = await waBlast.bulkSend(1, batch);
        results.push(result);
        
        // Add delay between batches
        if (i + batchSize < messages.length) {
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
    }
    
    return results;
}
```

## Security Considerations

### 1. API Key Security
- Store API keys securely
- Rotate keys regularly
- Use different keys for different environments
- Never expose keys in client-side code

### 2. Input Validation
```javascript
function validatePhoneNumber(phoneNumber) {
    const phoneRegex = /^[0-9+\-\s()]+$/;
    if (!phoneRegex.test(phoneNumber)) {
        throw new Error('Invalid phone number format');
    }
    return phoneNumber;
}
```

### 3. Rate Limiting
```javascript
class RateLimitedWABlastSDK extends WABlastSDK {
    constructor(config) {
        super(config);
        this.requestCount = 0;
        this.lastReset = Date.now();
    }
    
    async request(method, endpoint, data = null) {
        // Check rate limit
        const now = Date.now();
        if (now - this.lastReset > 60000) {
            this.requestCount = 0;
            this.lastReset = now;
        }
        
        if (this.requestCount >= 60) {
            throw new Error('Rate limit exceeded');
        }
        
        this.requestCount++;
        return super.request(method, endpoint, data);
    }
}
```

## Support

Untuk bantuan teknis atau pertanyaan tentang integrasi API, silakan hubungi:

- Email: support@wa-blast.com
- Documentation: https://docs.wa-blast.com
- GitHub Issues: https://github.com/wa-blast/api-issues

## Changelog

### v1.0.0 (2024-01-01)
- Initial API release
- WhatsApp session management
- Message sending capabilities
- Blast campaign features
- Contact management
- Webhook support 