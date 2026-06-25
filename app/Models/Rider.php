<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'is_online',
        'is_available',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function locations()
    {
        return $this->hasMany(RiderLocation::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(RiderLocation::class)
            ->latestOfMany();
    }

    public function notifications()
    {
        return $this->morphMany(
            Notification::class,
            'notifiable'
        );
    }
}
