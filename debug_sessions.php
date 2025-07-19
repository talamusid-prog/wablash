<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WhatsAppSession;

echo "=== WhatsApp Sessions Debug ===\n";

$sessions = WhatsAppSession::all(['id', 'session_id', 'name', 'phone_number', 'status']);

if ($sessions->isEmpty()) {
    echo "No sessions found in database\n";
} else {
    echo "Found " . $sessions->count() . " sessions:\n";
    foreach ($sessions as $session) {
        echo "- ID: {$session->id}, Session ID: {$session->session_id}, Name: {$session->name}, Phone: {$session->phone_number}, Status: {$session->status}\n";
    }
}

echo "\n=== Connected Sessions ===\n";
$connectedSessions = WhatsAppSession::where('status', 'connected')->get(['id', 'session_id', 'name', 'phone_number', 'status']);

if ($connectedSessions->isEmpty()) {
    echo "No connected sessions found\n";
} else {
    echo "Found " . $connectedSessions->count() . " connected sessions:\n";
    foreach ($connectedSessions as $session) {
        echo "- ID: {$session->id}, Session ID: {$session->session_id}, Name: {$session->name}, Phone: {$session->phone_number}, Status: {$session->status}\n";
    }
} 