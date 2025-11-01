<?php

// Test sending a message using PHP
$sessionId = 'c47e68d4-008e-4edf-b1d2-1b0288c0bb04';
$to = '6285159205506';
$message = 'Test pesan dari PHP script - ' . date('Y-m-d H:i:s');

$url = "http://127.0.0.1:3000/sessions/{$sessionId}/send-simple";
$apiKey = 'your_secure_api_key_here';

$data = [
    'to' => $to,
    'message' => $message
];

$options = [
    'http' => [
        'header' => [
            'Content-Type: application/json',
            'X-API-Key: ' . $apiKey
        ],
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "Error sending message\n";
} else {
    echo "Response:\n";
    echo $result . "\n";
}