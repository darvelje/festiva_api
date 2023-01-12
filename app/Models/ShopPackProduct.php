<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopPackProduct extends Model
{
    use HasFactory;

    protected $table = 'shop_packs_products';

    public function product(){
        return $this->belongsTo(ShopProduct::class,'shop_product_id');
    }
}
