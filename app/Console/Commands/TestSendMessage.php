<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WhatsAppSession;
use App\Services\WhatsAppService;

class TestSendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:send-message {to_number} {message} {session_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test kirim pesan WhatsApp ke nomor tertentu';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsappService)
    {
        $toNumber = $this->argument('to_number');
        $message = $this->argument('message');
        $sessionId = $this->argument('session_id');

        $this->info("Mengirim pesan test ke: {$toNumber}");
        $this->info("Pesan: {$message}");

        // Jika tidak ada session_id yang diberikan, gunakan session pertama yang tersedia
        if (!$sessionId) {
            $session = WhatsAppSession::first();
            if (!$session) {
                $this->error('Tidak ada session WhatsApp yang tersedia');
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

        $this->info("Mengirim pesan melalui session: {$sessionId}");

        try {
            // Kirim pesan
            $result = $whatsappService->sendMessage($sessionId, $toNumber, $message);

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
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}