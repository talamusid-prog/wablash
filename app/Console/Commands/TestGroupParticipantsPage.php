<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;

class TestGroupParticipantsPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:group-participants-page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test halaman peserta grup yang baru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== TEST HALAMAN PESERTA GRUP ===');
        $this->newLine();

        // 1. Cek grup yang tersedia untuk testing
        $this->info('1. Grup yang Tersedia untuk Testing:');
        $groups = Contact::where('type', 'group')->take(5)->get();

        foreach ($groups as $group) {
            $this->line("   - Grup: {$group->name}");
            $this->line("     ID: {$group->id}");
            $this->line("     Contact ID: {$group->contact_id}");
            $this->line("     Total Peserta: " . ($group->group_participants_count ?? 0));
            $this->line("     URL: /phonebook/group/{$group->id}/participants");
            $this->line("     Route: phonebook.group-participants");
            $this->newLine();
        }

        // 2. Test data yang akan ditampilkan di halaman peserta
        $this->info('2. Test Data Halaman Peserta:');
        $sampleGroup = Contact::where('type', 'group')->first();
        if ($sampleGroup) {
            $this->line("   Grup Sample: {$sampleGroup->name}");
            $this->line("   - Nama grup akan ditampilkan sebagai judul halaman");
            $this->line("   - ID grup: {$sampleGroup->contact_id}");
            $this->line("   - Total peserta: " . ($sampleGroup->group_participants_count ?? 0));
            
            // Cek peserta dengan nomor telepon
            $participants = Contact::where('type', 'individual')
                ->where('group_id', $sampleGroup->id)
                ->whereNotNull('phone_number')
                ->get();
            
            $this->line("   - Peserta dengan nomor telepon: " . $participants->count());
            
            if ($participants->count() > 0) {
                $this->line("   - Contoh peserta:");
                foreach ($participants->take(3) as $participant) {
                    $adminStatus = $participant->is_admin ? " [ADMIN]" : "";
                    $this->line("     * {$participant->name} ({$participant->phone_number}) - {$participant->status}{$adminStatus}");
                }
            }
            $this->newLine();
        }

        // 3. Test fitur yang tersedia di halaman peserta
        $this->info('3. Fitur yang Tersedia di Halaman Peserta:');
        $this->line("   - Breadcrumb: Kembali ke Phonebook");
        $this->line("   - Info grup: nama, ID, total peserta, peserta dengan nomor");
        $this->line("   - Search box: pencarian berdasarkan nama atau nomor");
        $this->line("   - Filter status: aktif, tidak aktif, diblokir");
        $this->line("   - Export button: untuk mengexport daftar peserta");
        $this->line("   - Daftar peserta dengan avatar, nama, nomor telepon");
        $this->line("   - Badge status dan admin");
        $this->line("   - Responsive design");
        $this->newLine();

        // 4. Test navigasi
        $this->info('4. Test Navigasi:');
        $this->line("   - Dari halaman phonebook utama:");
        $this->line("     * Klik nama grup → langsung ke halaman peserta");
        $this->line("     * Klik icon external link → ke halaman peserta");
        $this->line("   - Dari halaman peserta:");
        $this->line("     * Klik back arrow → kembali ke phonebook");
        $this->line("     * Klik 'Kembali ke Phonebook' button → kembali ke phonebook");
        $this->newLine();

        // 5. Test perubahan pada halaman utama
        $this->info('5. Perubahan pada Halaman Phonebook Utama:');
        $this->line("   - Deskripsi grup dihapus (tidak ditampilkan lagi)");
        $this->line("   - Nama grup menjadi link yang bisa diklik");
        $this->line("   - Tombol expand/collapse dihapus");
        $this->line("   - Modal deskripsi dihapus");
        $this->line("   - JavaScript untuk toggle dihapus");
        $this->line("   - Search hanya mencari nama grup (bukan peserta)");
        $this->newLine();

        // 6. Test URL dan routing
        $this->info('6. Test URL dan Routing:');
        $this->line("   - Route: GET /phonebook/group/{groupId}/participants");
        $this->line("   - Method: PhonebookController@groupParticipants");
        $this->line("   - View: phonebook.group-participants");
        $this->line("   - Parameter: \$groupId (ID grup)");
        $this->line("   - Data: grup dengan relasi participants");
        $this->newLine();

        // 7. Rekomendasi testing
        $this->info('7. Rekomendasi untuk Testing:');
        $this->line("   - Buka halaman /phonebook di browser");
        $this->line("   - Pastikan deskripsi grup tidak ditampilkan");
        $this->line("   - Klik nama grup untuk masuk ke halaman peserta");
        $this->line("   - Test halaman peserta dengan grup yang memiliki peserta");
        $this->line("   - Test halaman peserta dengan grup kosong");
        $this->line("   - Test fitur search dan filter di halaman peserta");
        $this->line("   - Test navigasi kembali ke phonebook");
        $this->line("   - Test responsive design");
        $this->line("   - Test dengan grup yang memiliki banyak peserta");
        $this->newLine();

        $this->info('=== SELESAI ===');
        
        return Command::SUCCESS;
    }
} 