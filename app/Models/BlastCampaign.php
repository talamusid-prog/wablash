<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlastCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'message_template',
        'target_numbers',
        'status',
        'scheduled_at',
        'sent_count',
        'failed_count',
        'total_count',
        'session_id',
        'created_by'
    ];

    protected $casts = [
        'target_numbers' => 'array',
        'scheduled_at' => 'datetime',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
        'total_count' => 'integer'
    ];

    public function session()
    {
        return $this->belongsTo(WhatsAppSession::class, 'session_id', 'session_id');
    }

    public function messages()
    {
        return $this->hasMany(BlastMessage::class, 'campaign_id');
    }

    public function getSentCountAttribute()
    {
        // If the database field is set, use it; otherwise calculate from relations
        if (isset($this->attributes['sent_count'])) {
            return $this->attributes['sent_count'];
        }
        return $this->messages()->where('status', 'sent')->count();
    }

    public function getFailedCountAttribute()
    {
        // If the database field is set, use it; otherwise calculate from relations
        if (isset($this->attributes['failed_count'])) {
            return $this->attributes['failed_count'];
        }
        return $this->messages()->where('status', 'failed')->count();
    }
} 