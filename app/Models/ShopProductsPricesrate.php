<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $currency_id
 * @property integer $shop_product_id
 * @property float $price
 * @property string $created_at
 * @property string $updated_at
 * @property Currency $currency
 * @property ShopProduct $shopProduct
 */
class ShopProductsPricesrate extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['currency_id', 'shop_product_id', 'price', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shopProduct()
    {
        return $this->belongsTo('App\Models\ShopProduct');
    }
}
