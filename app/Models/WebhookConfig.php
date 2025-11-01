<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookConfig extends Model
{
    use HasFactory;

    protected $table = 'webhook_configs';

    protected $fillable = [
        'url',
        'secret',
        'events',
        'enabled'
    ];

    protected $casts = [
        'events' => 'array',
        'enabled' => 'boolean'
    ];

    /**
     * Get the current webhook configuration
     */
    public static function getCurrentConfig()
    {
        $config = self::first();
        
        if ($config) {
            // Config exists in database
            return $config;
        }
        
        // No config exists, return new instance with defaults (but don't save yet)
        $newConfig = new self();
        $newConfig->url = '';
        $newConfig->secret = '';
        $newConfig->events = [
            'message_sent' => true,
            'message_delivered' => true,
            'message_failed' => true,
            'session_connected' => true,
            'session_disconnected' => true,
            'campaign_started' => true,
            'campaign_completed' => true
        ];
        $newConfig->enabled = false;
        
        return $newConfig;
    }

    /**
     * Update or create webhook configuration
     */
    public static function updateConfig(array $data)
    {
        $config = self::first();
        if ($config) {
            $config->update($data);
        } else {
            $config = self::create($data);
        }
        return $config;
    }
}
