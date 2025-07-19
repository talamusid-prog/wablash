<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--email=admin@wablast.com : Admin email} {--password=admin123 : Admin password} {--name=Administrator : Admin name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a default admin user for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Check if admin user already exists
        $adminExists = User::where('email', $email)->exists();
        
        if ($adminExists) {
            $this->error("Admin user with email '{$email}' already exists!");
            return 1;
        }

        // Create admin user
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->info('✅ Admin user created successfully!');
        $this->info("📧 Email: {$admin->email}");
        $this->info("🔑 Password: {$password}");
        $this->info("👤 Name: {$admin->name}");
        
        $this->newLine();
        $this->info('You can now login to the application using these credentials.');

        return 0;
    }
} 