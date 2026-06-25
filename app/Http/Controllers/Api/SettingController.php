<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function show()
    {
        return Setting::first();
    }

    public function update(Request $request)
    {
        $setting = Setting::first();

        $setting->update(
            $request->validate([

                'restaurant_name' => 'nullable|string',

                'phone' => 'nullable|string',

                'address' => 'nullable|string',

                'delivery_fee' => 'required|numeric|min:0',

                'free_delivery_above' => 'required|numeric|min:0',

                'tax_percentage' => 'required|numeric|min:0',

                'easypaisa_title' => 'required|string',
                'easypaisa_number' => 'required|string',

                'jazzcash_title' => 'required|string',
                'jazzcash_number' => 'required|string',

                'currency' => 'required|string',

                'opening_hours' => 'nullable|string',
            ])
        );

        return $setting;
    }
}
