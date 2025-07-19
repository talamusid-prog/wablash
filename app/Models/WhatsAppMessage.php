<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'session_id',
        'message_id',
        'from_number',
        'to_number',
        'phone_number',
        'message',
        'message_type',
        'content',
        'media_url',
        'status',
        'direction',
        'timestamp',
        'metadata',
        'sent_at',
        'error_message'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'sent_at' => 'datetime',
        'metadata' => 'array'
    ];

    public function session()
    {
        return $this->belongsTo(WhatsAppSession::class, 'session_id', 'session_id');
    }
} 