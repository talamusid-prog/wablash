<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;

class TestPhonebookView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:phonebook-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test tampilan phonebook baru berbasis grup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TEST TAMPILAN PHONEBOOK BARU BERBASIS GRUP ===');
        $this->newLine();

        // 1. Cek data grup yang tersedia
        $this->info('1. Data Grup yang Tersedia:');
        $groups = Contact::where('type', 'group')->with('participants')->get();

        if ($groups->count() > 0) {
            foreach ($groups as $group) {
                $this->line("   - Grup: {$group->name}");
                $this->line("     ID: {$group->contact_id}");
                $this->line("     Deskripsi: " . ($group->group_description ?: 'Tidak ada'));
                $this->line("     Total Peserta: " . ($group->group_participants_count ?: 0));
                $this->line("     Peserta dengan Nomor: " . $group->participants->count());
                
                if ($group->participants->count() > 0) {
                    $this->line("     Daftar Peserta:");
                    foreach ($group->participants as $participant) {
                        $adminStatus = $participant->is_admin ? " [ADMIN]" : "";
                        $this->line("       * {$participant->name} ({$participant->phone_number}) - {$participant->status}{$adminStatus}");
                    }
                }
                $this->newLine();
            }
        } else {
            $this->line("   Tidak ada grup yang tersedia");
            $this->newLine();
        }

        // 2. Cek kontak individual yang tidak dalam grup
        $this->info('2. Kontak Individual (Tidak dalam Grup):');
        $individualContacts = Contact::where('type', 'individual')
            ->whereNull('group_id')
            ->whereNotNull('phone_number')
            ->get();

        if ($individualContacts->count() > 0) {
            foreach ($individualContacts as $contact) {
                $this->line("   - {$contact->name} ({$contact->phone_number}) - {$contact->status}");
            }
        } else {
            $this->line("   Tidak ada kontak individual yang tersedia");
        }
        $this->newLine();

        // 3. Statistik untuk tampilan
        $this->info('3. Statistik untuk Tampilan:');
        $allContacts = Contact::where('type', 'individual')->whereNotNull('phone_number')->get();
        $activeContacts = $allContacts->where('status', 'active');
        $groupCount = Contact::where('type', 'group')->count();
        $individualCount = Contact::where('type', 'individual')
            ->whereNull('group_id')
            ->whereNotNull('phone_number')
            ->count();

        $this->line("   - Total Kontak: " . $allContacts->count());
        $this->line("   - Kontak Aktif: " . $activeContacts->count());
        $this->line("   - Jumlah Grup: " . $groupCount);
        $this->line("   - Kontak Individual: " . $individualCount);
        $this->newLine();

        // 4. Test fitur pencarian (simulasi)
        $this->info('4. Test Fitur Pencarian:');
        $searchTerm = "test";
        $this->line("   Mencari: '$searchTerm'");

        // Simulasi pencarian di grup
        foreach ($groups as $group) {
            $groupMatch = stripos($group->name, $searchTerm) !== false;
            $participantMatches = $group->participants->filter(function($participant) use ($searchTerm) {
                return stripos($participant->name, $searchTerm) !== false || 
                       stripos($participant->phone_number, $searchTerm) !== false;
            });
            
            if ($groupMatch || $participantMatches->count() > 0) {
                $this->line("   ✓ Grup '{$group->name}' akan ditampilkan");
                if ($participantMatches->count() > 0) {
                    $this->line("     Peserta yang cocok: " . $participantMatches->count());
                }
            }
        }

        // Simulasi pencarian di kontak individual
        $individualMatches = $individualContacts->filter(function($contact) use ($searchTerm) {
            return stripos($contact->name, $searchTerm) !== false || 
                   stripos($contact->phone_number, $searchTerm) !== false;
        });

        if ($individualMatches->count() > 0) {
            $this->line("   ✓ Kontak individual yang cocok: " . $individualMatches->count());
        }
        $this->newLine();

        // 5. Test fitur filter grup
        $this->info('5. Test Fitur Filter Grup:');
        $groupNames = Contact::where('type', 'group')->pluck('name')->sort()->values();
        $this->line("   Grup yang tersedia untuk filter:");
        foreach ($groupNames as $groupName) {
            $this->line("   - $groupName");
        }
        $this->newLine();

        // 6. Test fitur expand/collapse grup
        $this->info('6. Test Fitur Expand/Collapse Grup:');
        foreach ($groups as $group) {
            $this->line("   Grup '{$group->name}' (ID: {$group->id}):");
            $this->line("   - Tombol toggle: data-group-id='{$group->id}'");
            $this->line("   - Container peserta: id='participants-{$group->id}'");
            $this->line("   - Jumlah peserta: " . $group->participants->count());
            $this->line("   - Status default: HIDDEN");
            $this->newLine();
        }

        // 7. Rekomendasi untuk testing
        $this->info('7. Rekomendasi untuk Testing:');
        $this->line("   - Buka halaman /phonebook di browser");
        $this->line("   - Pastikan grup ditampilkan dalam card layout");
        $this->line("   - Klik tombol expand (panah) pada grup untuk melihat peserta");
        $this->line("   - Test fitur pencarian dengan mengetik di search box");
        $this->line("   - Test filter grup dengan dropdown");
        $this->line("   - Test filter status untuk kontak individual");
        $this->line("   - Pastikan statistik di header menampilkan angka yang benar");
        $this->line("   - Test responsive design di berbagai ukuran layar");
        $this->newLine();

        $this->info('=== SELESAI ===');
        
        return Command::SUCCESS;
    }
} 