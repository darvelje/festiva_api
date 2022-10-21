<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $category_product_id
 * @property integer $shop_product_id
 * @property string $created_at
 * @property string $updated_at
 * @property CategoriesProduct $categoriesProduct
 * @property ShopProduct $shopProduct
 */
class ShopProductsHasCategoriesProduct extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['category_product_id', 'shop_product_id', 'created_at', 'updated_at'];

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
