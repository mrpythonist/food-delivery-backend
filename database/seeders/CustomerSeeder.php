<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::insert([
            [
                'first_name' => 'Muhammad',
                'last_name' => 'Aqib',
                'email' => 'aqib@test.com',
                'phone' => '03001234567',
            ],
            [
                'first_name' => 'Ahmad',
                'last_name' => 'Jalal',
                'email' => 'ahmad@test.com',
                'phone' => '03022563584',
            ],
            [
                'first_name' => 'Ayesha',
                'last_name' => 'Khan',
                'email' => 'ayesha@test.com',
                'phone' => '03142863574',
            ],
        ]);
    }
}
