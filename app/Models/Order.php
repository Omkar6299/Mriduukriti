<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 Order has many OrderItems
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // 🔹 Order has many OrderPayments
    public function orderPayments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    // 🔹 Order has one latest OrderPayment
    public function latestPayment()
    {
        return $this->hasOne(OrderPayment::class)->latestOfMany();
    }
}
