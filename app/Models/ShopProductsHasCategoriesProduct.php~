<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $category_product_id
 * @property integer $shop_product_id
 * @property CategoriesProduct $categoriesProduct
 * @property ShopProduct $shopProduct
 */
class ShopProductsHasCategoriesProduct extends Model
{
    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoriesProduct()
    {
        return $this->belongsTo('App\Models\CategoriesProduct', 'category_product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shopProduct()
    {
        return $this->belongsTo('App\Models\ShopProduct');
    }
}
