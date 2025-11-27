<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
        protected $guarded = [];

        public function product()
        {
                return $this->belongsTo(Product::class, 'product_id', 'id');
        }

        public function sku()
        {
                return $this->belongsTo(ProductSku::class, 'sku_id', 'id');
        }
        public function cart()
        {
                return $this->belongsTo(Cart::class);
        }
}
