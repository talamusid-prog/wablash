<?php

namespace App\Console\Commands;

use App\Models\BlastCampaign;
use App\Models\BlastMessage;
use App\Jobs\SendBlastMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StartWhatsAppEngine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:start-engine {--campaign-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start WhatsApp blast engine to send messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting WhatsApp Blast Engine...');

        $campaignId = $this->option('campaign-id');

        if ($campaignId) {
            $this->processSpecificCampaign($campaignId);
        } else {
            $this->processAllCampaigns();
        }

        $this->info('WhatsApp Blast Engine completed.');
    }

    /**
     * Process a specific campaign
     */
    protected function processSpecificCampaign($campaignId)
    {
        $campaign = BlastCampaign::find($campaignId);

        if (!$campaign) {
            $this->error("Campaign with ID {$campaignId} not found.");
            return;
        }

        $this->info("Processing campaign: {$campaign->name}");
        $this->processCampaign($campaign);
    }

    /**
     * Process all running campaigns
     */
    protected function processAllCampaigns()
    {
        $campaigns = BlastCampaign::where('status', 'running')
            ->with(['session', 'messages'])
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info('No running campaigns found.');
            return;
        }

        foreach ($campaigns as $campaign) {
            $this->info("Processing campaign: {$campaign->name}");
            $this->processCampaign($campaign);
        }
    }

    /**
     * Process a single campaign
     */
    protected function processCampaign(BlastCampaign $campaign)
    {
        // Check if session is connected
        if (!$campaign->session || $campaign->session->status !== 'connected') {
            $this->warn("Campaign {$campaign->name} has no connected session. Skipping...");
            $campaign->update(['status' => 'failed']);
            return;
        }

        // Get pending messages
        $pendingMessages = $campaign->messages()
            ->where('status', 'pending')
            ->get();

        if ($pendingMessages->isEmpty()) {
            $this->info("No pending messages for campaign {$campaign->name}");
            $campaign->update(['status' => 'completed']);
            return;
        }

        $this->info("Found {$pendingMessages->count()} pending messages");

        $successCount = 0;
        $failedCount = 0;

        foreach ($pendingMessages as $message) {
            try {
                // Dispatch job to send message
                SendBlastMessage::dispatch($message);
                $successCount++;
                
                $this->info("Dispatched message to {$message->phone_number}");
                
                // Add delay to avoid rate limiting
                usleep(500000); // 500ms delay
                
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("Failed to dispatch message to {$message->phone_number}: {$e->getMessage()}");
                
                Log::error('Failed to dispatch blast message', [
                    'campaign_id' => $campaign->id,
                    'message_id' => $message->id,
                    'phone_number' => $message->phone_number,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Campaign {$campaign->name} processed: {$successCount} dispatched, {$failedCount} failed");

        // Update campaign status if all messages are processed
        $remainingMessages = $campaign->messages()->where('status', 'pending')->count();
        if ($remainingMessages === 0) {
            $campaign->update(['status' => 'completed']);
            $this->info("Campaign {$campaign->name} completed");
        }
    }
}
