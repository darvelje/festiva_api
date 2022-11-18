<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property string $slug
 * @property integer $parent_id
 * @property Promo[] $promos
 * @property ShopProductsHasCategoriesProduct[] $shopProductsHasCategoriesProducts
 */
class CategoriesProduct extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'created_at', 'updated_at', 'slug', 'parent_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function promos()
    {
        return $this->hasMany('App\Models\Promo', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopProductsHasCategoriesProducts()
    {
        return $this->hasMany('App\Models\ShopProductsHasCategoriesProduct', 'category_product_id');
    }

    public function shopProducts(){
        return $this->hasManyThrough(ShopProduct::class, ShopProductsHasCategoriesProduct::class, 'category_product_id', 'id', 'id', 'shop_product_id')->orderByDesc('sales');
    }
}
