<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlastMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'phone_number',
        'message_content',
        'status',
        'sent_at',
        'error_message',
        'whatsapp_message_id',
        'attachment_path'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    public function campaign()
    {
        return $this->belongsTo(BlastCampaign::class, 'campaign_id');
    }

    public function whatsappMessage()
    {
        return $this->belongsTo(WhatsAppMessage::class, 'whatsapp_message_id', 'message_id');
    }
} 