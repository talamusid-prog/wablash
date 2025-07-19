<?php

namespace App\Exports;

use App\Models\Phonebook;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PhonebookExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Phonebook::select('name', 'phone_number', 'group', 'is_active', 'notes')->get();
    }

    public function headings(): array
    {
        return [
            'name',
            'phone_number',
            'group',
            'is_active',
            'notes',
        ];
    }
} 