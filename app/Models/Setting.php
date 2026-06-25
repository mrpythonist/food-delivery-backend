<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [

        'restaurant_name',
        'phone',
        'address',

        'delivery_fee',
        'free_delivery_above',
        'tax_percentage',

        'easypaisa_title',
        'easypaisa_number',

        'jazzcash_title',
        'jazzcash_number',
        'currency',
        'opening_hours',
    ];
}
