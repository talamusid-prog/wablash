<?php

require_once 'vendor/autoload.php';

use App\Models\WhatsAppSession;
use App\Services\WhatsAppService;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';

// Create application instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the first session
$session = WhatsAppSession::first();
if (!$session) {
    echo "No session found\n";
    exit(1);
}

echo "Session ID: " . $session->session_id . "\n";
echo "Current status: " . $session->status . "\n";

// Get WhatsApp service
$whatsappService = app(WhatsAppService::class);

// Check current status first
echo "Checking current session status...\n";
$statusResult = $whatsappService->getSessionStatus($session->session_id);
echo "Current status from engine: " . json_encode($statusResult, JSON_PRETTY_PRINT) . "\n";

// If session not found or disconnected, reconnect
if (!$statusResult['success'] || (isset($statusResult['data']['status']) && $statusResult['data']['status'] !== 'connected')) {
    echo "Reconnecting session...\n";
    // Reconnect with fresh parameter
    $result = $whatsappService->reconnectSession($session->session_id, true);
    echo "Reconnect result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
    // Wait a few seconds for the session to initialize
    echo "Waiting for session to initialize...\n";
    sleep(5);
    
    // Check the session status again
    $statusResult = $whatsappService->getSessionStatus($session->session_id);
    echo "Session status after reconnect: " . json_encode($statusResult, JSON_PRETTY_PRINT) . "\n";
}

// Update the session in the database
if ($statusResult['success'] && isset($statusResult['data']['status'])) {
    $session->update(['status' => $statusResult['data']['status']]);
    echo "Session status updated in database to: " . $statusResult['data']['status'] . "\n";
} else {
    echo "Failed to get session status\n";
}