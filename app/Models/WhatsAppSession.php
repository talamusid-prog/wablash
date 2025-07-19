<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppSession extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_sessions';

    protected $fillable = [
        'name',
        'session_id',
        'phone_number',
        'status',
        'qr_code',
        'is_active',
        'last_activity',
        'device_info'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity' => 'datetime',
        'device_info' => 'array',
        'status' => 'string'
    ];

    // Valid status values
    const STATUS_CONNECTING = 'connecting';
    const STATUS_CONNECTED = 'connected';
    const STATUS_DISCONNECTED = 'disconnected';
    const STATUS_ERROR = 'error';
    const STATUS_QR_READY = 'qr_ready';
    const STATUS_AUTHENTICATED = 'authenticated';
    const STATUS_AUTH_FAILED = 'auth_failed';

    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'session_id', 'session_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'session_id', 'session_id');
    }

    public function groupContacts()
    {
        return $this->hasMany(Contact::class, 'session_id', 'session_id')->where('type', 'group');
    }

    public function individualContacts()
    {
        return $this->hasMany(Contact::class, 'session_id', 'session_id')->where('type', 'individual');
    }

    // Mutator to ensure status is always a valid string
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = (string) $value;
    }

    // Get valid status values
    public static function getValidStatuses()
    {
        return [
            self::STATUS_CONNECTING,
            self::STATUS_CONNECTED,
            self::STATUS_DISCONNECTED,
            self::STATUS_ERROR,
            self::STATUS_QR_READY,
            self::STATUS_AUTHENTICATED,
            self::STATUS_AUTH_FAILED
        ];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'session_id';
    }
} 