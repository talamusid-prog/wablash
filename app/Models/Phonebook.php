<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phonebook extends Model
{
    use HasFactory;

    protected $table = 'phonebook';

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'notes',
        'group',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scope untuk kontak aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone_number', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('group', 'like', "%{$search}%");
        });
    }

    // Scope untuk filter berdasarkan grup
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    // Accessor untuk format nomor telepon
    public function getFormattedPhoneNumberAttribute()
    {
        $phone = $this->phone_number;
        
        // Hapus semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika belum ada 62 di depan, tambahkan
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}
