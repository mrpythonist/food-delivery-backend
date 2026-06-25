<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Setting::create([

            'restaurant_name' => 'Flavors',

            'phone' => '03001234567',

            'address' => 'Zarif Shaheed, Shujabad, Multan.',

            'delivery_fee' => 200,

            'free_delivery_above' => 3000,

            'tax_percentage' => 0,

            'easypaisa_title' => 'Muhammad Aqib',
            'easypaisa_number' => '03055475970',

            'jazzcash_title' => 'Abdul Majeed',
            'jazzcash_number' => '03055475970',

            'currency' => 'PKR',
        ]);
    }
}
