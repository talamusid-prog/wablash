<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WhatsAppSession;
use App\Services\WhatsAppService;

class DebugWhatsAppSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:debug-session {session_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug WhatsApp session connection issues';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService)
    {
        $sessionId = $this->argument('session_id');
        
        if ($sessionId) {
            $session = WhatsAppSession::where('session_id', $sessionId)->first();
        } else {
            $session = WhatsAppSession::first();
        }
        
        if (!$session) {
            $this->error('Tidak ada sesi WhatsApp ditemukan');
            return 1;
        }
        
        $this->info("Debugging sesi: {$session->session_id}");
        $this->info("Nama sesi: {$session->name}");
        $this->info("Nomor telepon: {$session->phone_number}");
        $this->info("Status di database: {$session->status}");
        
        // Check engine status
        $this->info("\n1. Memeriksa status engine...");
        $engineStatus = $whatsappService->getEngineStatus();
        if (!$engineStatus['success']) {
            $this->error("Engine tidak berjalan: " . $engineStatus['error']);
            return 1;
        }
        $this->info("✅ Engine berjalan");
        
        // Check session status in engine
        $this->info("\n2. Memeriksa status sesi di engine...");
        $statusResult = $whatsappService->getSessionStatus($session->session_id);
        $this->info("Hasil status engine: " . json_encode($statusResult, JSON_PRETTY_PRINT));
        
        // If session not found, try to recreate
        if (!$statusResult['success'] && strpos($statusResult['error'], 'Session not found') !== false) {
            $this->info("\n3. Sesi tidak ditemukan di engine, mencoba membuat ulang...");
            $createResult = $whatsappService->createSession($session->session_id, $session->name, $session->phone_number);
            $this->info("Hasil pembuatan sesi: " . json_encode($createResult, JSON_PRETTY_PRINT));
            
            if ($createResult['success']) {
                $this->info("\n✅ Sesi berhasil dibuat ulang!");
                $this->info("Silakan scan QR code yang muncul di WhatsApp engine untuk menghubungkan kembali sesi.");
                $this->info("Setelah scan selesai, jalankan perintah ini lagi untuk memeriksa status.");
                return 0;
            } else {
                $this->error("\n❌ Gagal membuat ulang sesi: " . $createResult['error']);
                return 1;
            }
        }
        
        // Try to reconnect if not connected
        if ($statusResult['success']) {
            $statusData = $statusResult['data'];
            $sessionStatus = null;
            
            if (isset($statusData['data']['status'])) {
                $sessionStatus = $statusData['data']['status'];
            } elseif (isset($statusData['status'])) {
                $sessionStatus = $statusData['status'];
            }
            
            $this->info("Status sesi saat ini: " . $sessionStatus);
            
            if ($sessionStatus === 'qr_ready') {
                $this->info("\n📱 Sesi memerlukan scan QR code!");
                $this->info("Silakan scan QR code yang muncul di WhatsApp engine untuk menghubungkan sesi.");
                return 0;
            }
            
            if ($sessionStatus !== 'connected') {
                $this->info("\n4. Sesi tidak terhubung, mencoba menghubungkan kembali...");
                $reconnectResult = $whatsappService->reconnectSession($session->session_id, true);
                $this->info("Hasil reconnect: " . json_encode($reconnectResult, JSON_PRETTY_PRINT));
                
                if ($reconnectResult['success']) {
                    $this->info("\n✅ Perintah reconnect berhasil dikirim!");
                    $this->info("Menunggu sesi terhubung... Silakan periksa kembali status dalam beberapa menit.");
                } else {
                    $this->error("\n❌ Gagal menghubungkan kembali sesi: " . $reconnectResult['error']);
                    return 1;
                }
            } else {
                $this->info("\n✅ Sesi sudah terhubung!");
                
                // Try to send a test message
                $this->info("\n5. Mencoba mengirim pesan tes...");
                $sendResult = $whatsappService->sendMessage($session->session_id, '6285159205506', 'Pesan tes dari WA Blast');
                $this->info("Hasil pengiriman pesan: " . json_encode($sendResult, JSON_PRETTY_PRINT));
                
                if ($sendResult['success']) {
                    $this->info("\n✅ Pesan berhasil dikirim!");
                } else {
                    $this->error("\n❌ Gagal mengirim pesan: " . $sendResult['error']);
                }
            }
        }
        
        $this->info("\n=== Debug Selesai ===");
        return 0;
    }
}