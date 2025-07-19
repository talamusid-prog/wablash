<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Phonebook;

class PhonebookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'John Doe',
                'phone_number' => '628123456789',
                'email' => 'john@example.com',
                'group' => 'VIP',
                'notes' => 'Customer VIP - sering melakukan pembelian',
                'is_active' => true
            ],
            [
                'name' => 'Jane Smith',
                'phone_number' => '628987654321',
                'email' => 'jane@example.com',
                'group' => 'Regular',
                'notes' => 'Customer regular - pembelian menengah',
                'is_active' => true
            ],
            [
                'name' => 'Bob Johnson',
                'phone_number' => '628555666777',
                'email' => 'bob@example.com',
                'group' => 'VIP',
                'notes' => 'VIP customer - pembelian besar',
                'is_active' => true
            ],
            [
                'name' => 'Alice Brown',
                'phone_number' => '628111222333',
                'email' => 'alice@example.com',
                'group' => 'Regular',
                'notes' => 'Customer baru',
                'is_active' => true
            ],
            [
                'name' => 'Charlie Wilson',
                'phone_number' => '628444555666',
                'email' => 'charlie@example.com',
                'group' => 'VIP',
                'notes' => 'VIP customer - loyal',
                'is_active' => true
            ],
            [
                'name' => 'Diana Davis',
                'phone_number' => '628777888999',
                'email' => 'diana@example.com',
                'group' => 'Regular',
                'notes' => 'Customer regular',
                'is_active' => false
            ],
            [
                'name' => 'Edward Miller',
                'phone_number' => '628123123123',
                'email' => 'edward@example.com',
                'group' => 'VIP',
                'notes' => 'VIP customer - sering bertanya',
                'is_active' => true
            ],
            [
                'name' => 'Fiona Garcia',
                'phone_number' => '628456456456',
                'email' => 'fiona@example.com',
                'group' => 'Regular',
                'notes' => 'Customer regular - pembelian kecil',
                'is_active' => true
            ],
            [
                'name' => 'George Martinez',
                'phone_number' => '628789789789',
                'email' => 'george@example.com',
                'group' => 'VIP',
                'notes' => 'VIP customer - pembelian rutin',
                'is_active' => true
            ],
            [
                'name' => 'Helen Anderson',
                'phone_number' => '628321321321',
                'email' => 'helen@example.com',
                'group' => 'Regular',
                'notes' => 'Customer regular - pembelian sedang',
                'is_active' => true
            ],
            [
                'name' => 'Ivan Thompson',
                'phone_number' => '628654654654',
                'email' => 'ivan@example.com',
                'group' => 'VIP',
                'notes' => 'VIP customer - pembelian besar',
                'is_active' => true
            ],
            [
                'name' => 'Julia White',
                'phone_number' => '628987987987',
                'email' => 'julia@example.com',
                'group' => 'Regular',
                'notes' => 'Customer regular - pembelian kecil',
                'is_active' => true
            ],
            [
                'name' => 'Kevin Lee',
                'phone_number' => '628147147147',
                'email' => 'kevin@example.com',
                'group' => 'VIP',
                'notes' => 'VIP customer - loyal',
                'is_active' => true
            ],
            [
                'name' => 'Linda Clark',
                'phone_number' => '628258258258',
                'email' => 'linda@example.com',
                'group' => 'Regular',
                'notes' => 'Customer regular - pembelian menengah',
                'is_active' => true
            ],
            [
                'name' => 'Mike Rodriguez',
                'phone_number' => '628369369369',
                'email' => 'mike@example.com',
                'group' => 'VIP',
                'notes' => 'VIP customer - pembelian besar',
                'is_active' => true
            ]
        ];

        foreach ($contacts as $contact) {
            Phonebook::create($contact);
        }
    }
}
