<?php

namespace App\Jobs;

use App\Models\BlastMessage;
use App\Models\WhatsAppSession;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBlastMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected BlastMessage $blastMessage
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        try {
            $campaign = $this->blastMessage->campaign;
            $session = $campaign->session;

            if (!$session || $session->status !== 'connected') {
                $this->fail(new \Exception('WhatsApp session is not connected'));
                return;
            }

            $result = $whatsAppService->sendMessage(
                $session->session_id,
                $this->blastMessage->phone_number,
                $this->blastMessage->message_content,
                'text'
            );

            if ($result['success']) {
                $this->blastMessage->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'whatsapp_message_id' => $result['data']['messageId'] ?? null,
                ]);

                // Update campaign statistics
                $campaign->increment('sent_count');
            } else {
                $this->blastMessage->update([
                    'status' => 'failed',
                    'error_message' => $result['error'],
                ]);

                // Update campaign statistics
                $campaign->increment('failed_count');

                Log::error('Failed to send blast message', [
                    'blast_message_id' => $this->blastMessage->id,
                    'phone_number' => $this->blastMessage->phone_number,
                    'error' => $result['error']
                ]);
            }
        } catch (\Exception $e) {
            $this->blastMessage->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $campaign = $this->blastMessage->campaign;
            $campaign->increment('failed_count');

            Log::error('Exception in SendBlastMessage job', [
                'blast_message_id' => $this->blastMessage->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->blastMessage->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        $campaign = $this->blastMessage->campaign;
        $campaign->increment('failed_count');

        Log::error('SendBlastMessage job failed', [
            'blast_message_id' => $this->blastMessage->id,
            'error' => $exception->getMessage()
        ]);
    }
}
