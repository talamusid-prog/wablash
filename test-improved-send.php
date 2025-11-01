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

echo "=== WhatsApp Message Test ===\n";

// Get the first session
$session = WhatsAppSession::first();
if (!$session) {
    echo "❌ Tidak ada sesi ditemukan\n";
    exit(1);
}

echo "Session ID: " . $session->session_id . "\n";
echo "Status saat ini di DB: " . $session->status . "\n";

// Get WhatsApp service
$whatsappService = app(WhatsAppService::class);

// Check engine status first
echo "\n1. Memeriksa status engine...\n";
$engineStatus = $whatsappService->getEngineStatus();
if (!$engineStatus['success']) {
    echo "❌ Engine tidak berjalan: " . $engineStatus['error'] . "\n";
    echo "Silakan mulai WhatsApp engine terlebih dahulu.\n";
    exit(1);
}
echo "✅ Engine berjalan\n";

// Check current session status
echo "\n2. Memeriksa status sesi...\n";
$statusResult = $whatsappService->getSessionStatus($session->session_id);
echo "Status dari engine: " . json_encode($statusResult, JSON_PRETTY_PRINT) . "\n";

// If session not found or disconnected, try to reconnect
if (!$statusResult['success'] || (isset($statusResult['data']['data']['status']) && $statusResult['data']['data']['status'] !== 'connected')) {
    echo "\n3. Sesi tidak terhubung atau tidak ditemukan. Mencoba menghubungkan kembali...\n";
    
    // Try fresh reconnect first
    $reconnectResult = $whatsappService->reconnectSession($session->session_id, true);
    echo "Hasil reconnect fresh: " . json_encode($reconnectResult, JSON_PRETTY_PRINT) . "\n";
    
    if ($reconnectResult['success']) {
        // Wait for reconnection
        echo "Menunggu koneksi kembali...\n";
        sleep(15);
        
        // Check status again
        $statusResult = $whatsappService->getSessionStatus($session->session_id);
        echo "Status setelah reconnect fresh: " . json_encode($statusResult, JSON_PRETTY_PRINT) . "\n";
    } else {
        // If fresh reconnect fails, try regular reconnect
        echo "\nReconnect fresh gagal, mencoba reconnect biasa...\n";
        $reconnectResult = $whatsappService->reconnectSession($session->session_id, false);
        echo "Hasil reconnect biasa: " . json_encode($reconnectResult, JSON_PRETTY_PRINT) . "\n";
        
        if ($reconnectResult['success']) {
            // Wait for reconnection
            echo "Menunggu koneksi kembali...\n";
            sleep(10);
            
            // Check status again
            $statusResult = $whatsappService->getSessionStatus($session->session_id);
            echo "Status setelah reconnect biasa: " . json_encode($statusResult, JSON_PRETTY_PRINT) . "\n";
        }
    }
}

// Update the session in the database
if ($statusResult['success']) {
    // Extract status from nested data structure
    $statusData = $statusResult['data'];
    $sessionStatus = null;
    
    // Handle different response structures
    if (isset($statusData['data']['status'])) {
        $sessionStatus = $statusData['data']['status'];
        $session->update(['status' => $sessionStatus]);
        echo "\n4. Status sesi di database diperbarui menjadi: " . $sessionStatus . "\n";
    } elseif (isset($statusData['status'])) {
        $sessionStatus = $statusData['status'];
        $session->update(['status' => $sessionStatus]);
        echo "\n4. Status sesi di database diperbarui menjadi: " . $sessionStatus . "\n";
    } else {
        echo "\n4. Status tidak ditemukan di data respons\n";
    }
} else {
    echo "\n4. Gagal mendapatkan status sesi\n";
}

// Now try to send a message
echo "\n5. Mencoba mengirim pesan tes...\n";
$toNumber = '6285159205506';
$message = 'Pesan tes dari WA Blast - Versi Ditingkatkan ' . date('Y-m-d H:i:s');

// Try multiple times with delays
$maxAttempts = 5;
for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
    echo "\n--- Percobaan $attempt dari $maxAttempts ---\n";
    
    try {
        $sendResult = $whatsappService->sendMessage($session->session_id, $toNumber, $message);
        echo "Hasil pengiriman pesan: " . json_encode($sendResult, JSON_PRETTY_PRINT) . "\n";
        
        if ($sendResult['success']) {
            echo "\n✅ Pesan berhasil dikirim!\n";
            echo "ID Pesan: " . ($sendResult['message_id'] ?? 'N/A') . "\n";
            break; // Success, exit the loop
        } else {
            echo "\n❌ Gagal mengirim pesan: " . ($sendResult['error'] ?? 'Kesalahan tidak diketahui') . "\n";
            
            // If it's a session issue, wait longer before retrying
            if (strpos($sendResult['error'], 'Sesi') !== false || strpos($sendResult['error'], 'Session') !== false) {
                echo "Masalah sesi terdeteksi, menunggu 20 detik sebelum mencoba lagi...\n";
                sleep(20);
            } else {
                echo "Menunggu 10 detik sebelum mencoba lagi...\n";
                sleep(10);
            }
        }
    } catch (Exception $e) {
        echo "\n❌ Exception saat mengirim pesan: " . $e->getMessage() . "\n";
        echo "Menunggu 15 detik sebelum mencoba lagi...\n";
        sleep(15);
    }
    
    // If this is the last attempt, show final result
    if ($attempt === $maxAttempts) {
        echo "\n❌ Semua percobaan gagal mengirim pesan\n";
        echo "Rekomendasi: Silakan scan ulang QR code untuk menghubungkan kembali sesi WhatsApp.\n";
    }
}

echo "\n=== Test Selesai ===\n";