<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiderLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'rider_id',
        'latitude',
        'longitude',
    ];

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }
}