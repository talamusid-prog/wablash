<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'key',
        'permissions',
        'rate_limit',
        'expires_at',
        'last_used_at',
        'usage_count',
        'status'
    ];

    protected $casts = [
        'permissions' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Generate a new API key
     */
    public static function generateKey(): string
    {
        return 'wa-blast-' . Str::random(32);
    }

    /**
     * Check if the API key is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the API key is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Update usage statistics
     */
    public function updateUsage(): void
    {
        $this->update([
            'last_used_at' => now(),
            'usage_count' => $this->usage_count + 1
        ]);
    }

    /**
     * Check if the API key has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return true; // If no permissions set, allow all
        }

        return in_array($permission, $this->permissions) || in_array('admin', $this->permissions);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-gray-100 text-gray-800',
            'expired' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get status text
     */
    public function getStatusText(): string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }

        return ucfirst($this->status);
    }
}
