<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'address_id',
        'rider_id',
        'coupon_id',
        'coupon_code',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'transaction_id',
        'payment_receipt',
        'payment_verified_at',
        'payment_verified_by',
        'subtotal',
        'delivery_fee',
        'discount',
        'total',
        'notes',
        'placed_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'placed_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'prepared_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(
            User::class,
            'payment_verified_by'
        );
    }
}
