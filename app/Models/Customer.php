<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'is_active',
        'last_order_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_order_at' => 'datetime',
        ];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function notifications()
    {
        return $this->morphMany(
            Notification::class,
            'notifiable'
        );
    }
}
