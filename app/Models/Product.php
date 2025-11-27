<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function productSkus()
    {
        return $this->hasOne(ProductSku::class, 'product_id', 'id');
    }

     public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    
}
