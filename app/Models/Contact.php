<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
        'session_id',
        'contact_id',
        'name',
        'phone_number',
        'type', // 'individual' atau 'group'
        'group_id',
        'group_name',
        'group_description',
        'group_participants_count',
        'is_admin',
        'profile_picture',
        'status',
        'grabbed_at'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'group_participants_count' => 'integer',
        'grabbed_at' => 'datetime',
        'status' => 'string'
    ];

    // Valid types
    const TYPE_INDIVIDUAL = 'individual';
    const TYPE_GROUP = 'group';

    // Valid status values
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_BLOCKED = 'blocked';

    public function session()
    {
        return $this->belongsTo(WhatsAppSession::class, 'session_id', 'session_id');
    }

    public function group()
    {
        return $this->belongsTo(Contact::class, 'group_id')->where('type', self::TYPE_GROUP);
    }

    public function participants()
    {
        return $this->hasMany(Contact::class, 'group_id', 'contact_id')->where('type', self::TYPE_INDIVIDUAL);
    }

    // Scope untuk kontak individual
    public function scopeIndividual($query)
    {
        return $query->where('type', self::TYPE_INDIVIDUAL);
    }

    // Scope untuk kontak grup
    public function scopeGroup($query)
    {
        return $query->where('type', self::TYPE_GROUP);
    }

    // Scope untuk kontak aktif
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // Get valid types
    public static function getValidTypes()
    {
        return [
            self::TYPE_INDIVIDUAL,
            self::TYPE_GROUP
        ];
    }

    // Get valid statuses
    public static function getValidStatuses()
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_BLOCKED
        ];
    }

    // Mutator untuk memastikan type selalu valid
    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = in_array($value, self::getValidTypes()) ? $value : self::TYPE_INDIVIDUAL;
    }

    // Mutator untuk memastikan status selalu valid
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = in_array($value, self::getValidStatuses()) ? $value : self::STATUS_ACTIVE;
    }
} 