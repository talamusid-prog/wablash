<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ApiKey;

class ApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        ApiKey::truncate();

        // Create sample API keys
        ApiKey::create([
            'name' => 'Production API Key',
            'description' => 'Main API key for production environment',
            'key' => 'wa-blast-prod-' . \Illuminate\Support\Str::random(32),
            'permissions' => ['read', 'write', 'admin'],
            'rate_limit' => 10000,
            'expires_at' => null,
            'last_used_at' => now()->subMinutes(5),
            'usage_count' => 1234,
            'status' => 'active'
        ]);

        ApiKey::create([
            'name' => 'Development API Key',
            'description' => 'API key for development and testing',
            'key' => 'wa-blast-dev-' . \Illuminate\Support\Str::random(32),
            'permissions' => ['read', 'write'],
            'rate_limit' => 1000,
            'expires_at' => now()->addDays(30),
            'last_used_at' => now()->subHours(2),
            'usage_count' => 567,
            'status' => 'active'
        ]);

        ApiKey::create([
            'name' => 'Read-Only API Key',
            'description' => 'API key with read-only permissions',
            'key' => 'wa-blast-read-' . \Illuminate\Support\Str::random(32),
            'permissions' => ['read'],
            'rate_limit' => 5000,
            'expires_at' => now()->addDays(90),
            'last_used_at' => now()->subDays(1),
            'usage_count' => 89,
            'status' => 'active'
        ]);

        // Add expired API keys for testing
        ApiKey::create([
            'name' => 'Expired Test Key 1',
            'description' => 'This key has expired for testing purposes',
            'key' => 'wa-blast-expired1-' . \Illuminate\Support\Str::random(32),
            'permissions' => ['read'],
            'rate_limit' => 1000,
            'expires_at' => now()->subDays(5),
            'last_used_at' => now()->subDays(10),
            'usage_count' => 45,
            'status' => 'active'
        ]);

        ApiKey::create([
            'name' => 'Expired Test Key 2',
            'description' => 'Another expired key for testing',
            'key' => 'wa-blast-expired2-' . \Illuminate\Support\Str::random(32),
            'permissions' => ['read', 'write'],
            'rate_limit' => 2000,
            'expires_at' => now()->subDays(2),
            'last_used_at' => now()->subDays(3),
            'usage_count' => 123,
            'status' => 'active'
        ]);

        ApiKey::create([
            'name' => 'Inactive API Key',
            'description' => 'This key is manually set to inactive',
            'key' => 'wa-blast-inactive-' . \Illuminate\Support\Str::random(32),
            'permissions' => ['read'],
            'rate_limit' => 1000,
            'expires_at' => null,
            'last_used_at' => now()->subDays(5),
            'usage_count' => 67,
            'status' => 'inactive'
        ]);
    }
}
