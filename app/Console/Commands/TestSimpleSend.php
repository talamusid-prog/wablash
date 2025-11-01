<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WhatsAppSession;
use App\Services\WhatsAppService;

class TestSimpleSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test-simple-send {to?} {message?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test pengiriman pesan dengan pendekatan sederhana';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService)
    {
        $to = $this->argument('to') ?? '6285159205506';
        $message = $this->argument('message') ?? 'Test pesan sederhana dari WA Blast - ' . date('Y-m-d H:i:s');

        $this->info("=== Test Pengiriman Pesan Sederhana ===");
        $this->info("Ke: {$to}");
        $this->info("Pesan: {$message}");

        // Dapatkan session pertama yang tersedia
        $session = WhatsAppSession::where('status', 'connected')->first();
        
        if (!$session) {
            $this->error('Tidak ada session WhatsApp yang terhubung');
            // Coba dapatkan session apa pun
            $session = WhatsAppSession::first();
            if (!$session) {
                $this->error('Tidak ada session WhatsApp sama sekali');
                return 1;
            }
            $this->info("Menggunakan session yang tersedia: {$session->session_id} (status: {$session->status})");
        } else {
            $this->info("Menggunakan session terhubung: {$session->session_id}");
        }

        $sessionId = $session->session_id;

        // Cek status engine
        $this->info("\n1. Memeriksa status engine...");
        $engineStatus = $whatsappService->getEngineStatus();
        if (!$engineStatus['success']) {
            $this->error("Engine tidak berjalan: " . $engineStatus['error']);
            return 1;
        }
        $this->info("✅ Engine berjalan");

        // Cek status session
        $this->info("\n2. Memeriksa status session...");
        $statusResult = $whatsappService->getSessionStatus($sessionId);
        $this->info("Status dari engine: " . json_encode($statusResult, JSON_PRETTY_PRINT));

        if ($statusResult['success']) {
            $statusData = $statusResult['data'];
            $sessionStatus = null;
            
            if (isset($statusData['data']['status'])) {
                $sessionStatus = $statusData['data']['status'];
            } elseif (isset($statusData['status'])) {
                $sessionStatus = $statusData['status'];
            }
            
            $this->info("Status session: {$sessionStatus}");
            
            // Jika session tidak terhubung, beri peringatan
            if ($sessionStatus !== 'connected') {
                $this->warn("Session tidak dalam status 'connected'. Mungkin pengiriman akan gagal.");
                $this->warn("Status saat ini: {$sessionStatus}");
            }
        }

        // Coba kirim pesan dengan pendekatan sederhana
        $this->info("\n3. Mencoba mengirim pesan dengan pendekatan sederhana...");
        
        try {
            $result = $whatsappService->sendMessageSimple($sessionId, $to, $message);
            
            $this->info("Hasil pengiriman: " . json_encode($result, JSON_PRETTY_PRINT));
            
            if ($result['success']) {
                $this->info("\n✅ PESAN BERHASIL DIKIRIM!");
                if (isset($result['message_id'])) {
                    $this->info("ID Pesan: {$result['message_id']}");
                }
                return 0;
            } else {
                $this->error("\n❌ GAGAL MENGIRIM PESAN");
                $this->error("Error: " . ($result['error'] ?? 'Unknown error'));
                
                // Coba dengan pendekatan biasa sebagai fallback
                $this->info("\n4. Mencoba dengan pendekatan biasa sebagai fallback...");
                $fallbackResult = $whatsappService->sendMessage($sessionId, $to, $message);
                $this->info("Hasil fallback: " . json_encode($fallbackResult, JSON_PRETTY_PRINT));
                
                if ($fallbackResult['success']) {
                    $this->info("\n✅ PESAN BERHASIL DIKIRIM DENGAN PENDEKATAN FALLBACK!");
                    return 0;
                } else {
                    $this->error("\n❌ KEDUA PENDEKATAN GAGAL");
                    return 1;
                }
            }
        } catch (\Exception $e) {
            $this->error("\n❌ EXCEPTION SAAT MENGIRIM PESAN:");
            $this->error($e->getMessage());
            
            return 1;
        }
    }
}