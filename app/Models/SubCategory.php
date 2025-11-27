<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $guarded = [];

    public function parentCategory(){
        return $this->hasOne(Category::class,'id','category_id');
    }
}
