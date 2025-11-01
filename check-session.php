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

// Check current status
echo "Checking current session status...\n";
$statusResult = $whatsappService->getSessionStatus($session->session_id);
echo "Current status from engine: " . json_encode($statusResult, JSON_PRETTY_PRINT) . "\n";

// Update the session in the database
if ($statusResult['success']) {
    // Extract status from nested data structure
    $statusData = $statusResult['data'];
    if (isset($statusData['data']['status'])) {
        $status = $statusData['data']['status'];
        $session->update(['status' => $status]);
        echo "Session status updated in database to: " . $status . "\n";
    } else {
        echo "Status not found in response data\n";
    }
} else {
    echo "Failed to get session status\n";
}