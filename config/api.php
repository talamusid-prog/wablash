<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk API WA Blast
    |
    */

    // API Version
    'version' => env('API_VERSION', 'v1'),

    // Rate Limiting
    'rate_limit' => [
        'requests_per_minute' => env('API_RATE_LIMIT_PER_MINUTE', 60),
        'requests_per_hour' => env('API_RATE_LIMIT_PER_HOUR', 1000),
    ],

    // Authentication
    'authentication' => [
        'methods' => ['api_key', 'bearer_token', 'basic_auth'],
        'api_key_header' => 'X-API-Key',
        'token_expiry' => env('API_TOKEN_EXPIRY', 60 * 24 * 30), // 30 days
    ],

    // Valid API Keys (for development - in production, store in database)
    'valid_api_keys' => [
        env('API_KEY_1', ''),
        env('API_KEY_2', ''),
        env('API_KEY_3', ''),
    ],

    // Webhook Configuration
    'webhook' => [
        'enabled' => env('WEBHOOK_ENABLED', false),
        'url' => env('WEBHOOK_URL', ''),
        'secret' => env('WEBHOOK_SECRET', ''),
        'events' => [
            'message_received',
            'message_sent',
            'campaign_started',
            'campaign_completed',
            'session_connected',
            'session_disconnected',
        ],
        'timeout' => env('WEBHOOK_TIMEOUT', 30),
        'retry_attempts' => env('WEBHOOK_RETRY_ATTEMPTS', 3),
    ],

    // WhatsApp Engine Configuration
    'whatsapp_engine' => [
        'url' => env('WHATSAPP_ENGINE_URL', 'http://localhost:3000'),
        'timeout' => env('WHATSAPP_ENGINE_TIMEOUT', 30),
        'retry_attempts' => env('WHATSAPP_ENGINE_RETRY_ATTEMPTS', 3),
    ],

    // Message Templates
    'message_templates' => [
        'welcome' => 'Halo {name}, selamat datang di layanan kami!',
        'promo' => 'Halo {name}, ada promo menarik untuk Anda: {promo_message}',
        'reminder' => 'Halo {name}, jangan lupa untuk {reminder_message}',
        'custom' => '{custom_message}',
    ],

    // Export Formats
    'export_formats' => [
        'json' => [
            'mime_type' => 'application/json',
            'extension' => 'json',
        ],
        'csv' => [
            'mime_type' => 'text/csv',
            'extension' => 'csv',
        ],
        'xml' => [
            'mime_type' => 'application/xml',
            'extension' => 'xml',
        ],
    ],

    // Bulk Operations
    'bulk_operations' => [
        'max_messages_per_request' => env('BULK_MAX_MESSAGES', 100),
        'max_contacts_per_import' => env('BULK_MAX_CONTACTS', 1000),
        'timeout_per_message' => env('BULK_TIMEOUT_PER_MESSAGE', 5),
    ],

    // Logging
    'logging' => [
        'enabled' => env('API_LOGGING_ENABLED', true),
        'level' => env('API_LOG_LEVEL', 'info'),
        'channels' => ['api', 'webhook', 'whatsapp'],
    ],

    // CORS Configuration
    'cors' => [
        'allowed_origins' => explode(',', env('API_CORS_ORIGINS', '*')),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-API-Key'],
        'exposed_headers' => ['X-Total-Count', 'X-Page-Count'],
        'max_age' => 86400, // 24 hours
    ],

    // Response Headers
    'response_headers' => [
        'X-API-Version' => env('API_VERSION', 'v1'),
        'X-Powered-By' => 'WA Blast API',
    ],

    // Error Codes
    'error_codes' => [
        'UNAUTHORIZED' => 'AUTH001',
        'INVALID_SESSION' => 'WHATSAPP001',
        'SESSION_NOT_CONNECTED' => 'WHATSAPP002',
        'MESSAGE_SEND_FAILED' => 'MESSAGE001',
        'CAMPAIGN_NOT_FOUND' => 'CAMPAIGN001',
        'CONTACT_NOT_FOUND' => 'CONTACT001',
        'RATE_LIMIT_EXCEEDED' => 'RATE001',
        'VALIDATION_ERROR' => 'VALID001',
        'INTERNAL_ERROR' => 'INTERNAL001',
    ],
]; 