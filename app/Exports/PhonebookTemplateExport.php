<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PhonebookTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['John Doe', '+6281234567890', 'john@example.com', 'Kontak keluarga', 'Keluarga', 'true'],
            ['Jane Smith', '+6282345678901', 'jane@example.com', 'Teman kantor', 'Teman', 'true'],
            ['Bob Johnson', '+6283456789012', 'bob@example.com', 'Rekan kerja', 'Kerja', 'true'],
            ['Alice Brown', '+6284567890123', 'alice@example.com', 'Teman lama', 'Keluarga', 'false'],
            ['Charlie Wilson', '+6285678901234', 'charlie@example.com', 'Teman kuliah', 'Teman', 'true'],
        ];
    }

    public function headings(): array
    {
        return ['Nama', 'Nomor Telepon', 'Email', 'Catatan', 'Grup', 'Status Aktif (true/false)'];
    }
} 