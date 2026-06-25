<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\Customer;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();

        foreach ($customers as $customer) {

            Address::create([
                'customer_id' => $customer->id,
                'label' => 'Home',
                'recipient_name' => $customer->first_name . ' ' . $customer->last_name,
                'phone' => $customer->phone,
                'address_line_1' => 'Street ' . rand(1, 100),
                'address_line_2' => null,
                'city' => 'Multan',
                'state' => 'Punjab',
                'postal_code' => '60000',
                'latitude' => null,
                'longitude' => null,
                'is_default' => true,
            ]);

            Address::create([
                'customer_id' => $customer->id,
                'label' => 'Office',
                'recipient_name' => $customer->first_name . ' ' . $customer->last_name,
                'phone' => $customer->phone,
                'address_line_1' => 'Business Street ' . rand(1, 100),
                'address_line_2' => 'Building ' . rand(1, 20),
                'city' => 'Multan',
                'state' => 'Punjab',
                'postal_code' => '60000',
                'latitude' => null,
                'longitude' => null,
                'is_default' => false,
            ]);
        }
    }
}