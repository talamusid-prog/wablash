<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WhatsAppSession;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;

class SendWhatsAppMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:send-message {to} {message} {session_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pesan WhatsApp dengan pendekatan langsung ke engine';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService)
    {
        $to = $this->argument('to');
        $message = $this->argument('message');
        $sessionId = $this->argument('session_id');

        $this->info("=== Kirim Pesan WhatsApp ===");
        $this->info("Ke: {$to}");
        $this->info("Pesan: {$message}");

        // Jika tidak ada session_id yang diberikan, gunakan session pertama yang tersedia
        if (!$sessionId) {
            $session = WhatsAppSession::where('status', 'connected')->first();
            if (!$session) {
                $this->error('Tidak ada session WhatsApp yang terhubung');
                return 1;
            }
            $sessionId = $session->session_id;
            $this->info("Menggunakan session: {$sessionId}");
        } else {
            $session = WhatsAppSession::where('session_id', $sessionId)->first();
            if (!$session) {
                $this->error("Session dengan ID {$sessionId} tidak ditemukan");
                return 1;
            }
        }

        // Coba kirim pesan langsung ke engine
        $this->info("Mengirim pesan langsung ke engine...");
        
        try {
            $result = $this->sendDirectToEngine($sessionId, $to, $message);
            
            if ($result['success']) {
                $this->info('✅ Pesan berhasil dikirim!');
                $this->info('Data respons: ' . json_encode($result, JSON_PRETTY_PRINT));
                return 0;
            } else {
                $this->error('❌ Gagal mengirim pesan');
                $this->error('Error: ' . ($result['error'] ?? 'Unknown error'));
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Exception saat mengirim pesan:');
            $this->error($e->getMessage());
            return 1;
        }
    }

    /**
     * Kirim pesan langsung ke WhatsApp engine
     */
    private function sendDirectToEngine(string $sessionId, string $to, string $message): array
    {
        try {
            $engineUrl = config('services.whatsapp.engine_url', 'http://localhost:3000');
            $apiKey = config('services.whatsapp.api_key', 'your_api_key');
            
            $this->info("Mengirim ke: {$engineUrl}/sessions/{$sessionId}/send");
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $apiKey,
                    'Content-Type' => 'application/json'
                ])
                ->post("{$engineUrl}/sessions/{$sessionId}/send", [
                    'to' => $to,
                    'message' => $message
                ]);

            $this->info("Status response: " . $response->status());
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
}