<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property string $slug
 * @property ShopProductsHasCategoriesProduct[] $shopProductsHasCategoriesProducts
 */
class CategoriesProduct extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'created_at', 'updated_at', 'slug'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopProductsHasCategoriesProducts()
    {
        return $this->hasMany('App\Models\ShopProductsHasCategoriesProduct', 'category_product_id');
    }
}
