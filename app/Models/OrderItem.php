<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

      public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 🔹 Each item belongs to a Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 🔹 Each item belongs to a SKU
    public function sku()
    {
        return $this->belongsTo(ProductSku::class);
    }
    
}
