<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopPack extends Model
{
    use HasFactory;

    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    public function products(){
        return $this->hasMany( ShopPackProduct::class,'pack_id');
    }

    public function shopProductPhotos()
    {
        return $this->hasMany('App\Models\ShopProductPhoto','shop_pack_id');
    }

    public function shopProductsHasCategoriesProducts()
    {
        return $this->hasMany('App\Models\ShopProductsHasCategoriesProduct','shop_pack_id');
    }

    public function shopProductsPricesrates()
    {
        return $this->hasMany('App\Models\ShopProductsPricesrate','shop_pack_id');
    }

}
