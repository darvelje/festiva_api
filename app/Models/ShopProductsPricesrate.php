<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $currency_code
 * @property integer $shop_product_id
 * @property float $price
 * @property string $created_at
 * @property string $updated_at
 * @property ShopCurrency $shopCurrency
 * @property ShopProduct $shopProduct
 */
class ShopProductsPricesrate extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['currency_code', 'shop_product_id', 'price', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shopCurrency()
    {
        return $this->belongsTo('App\Models\ShopCurrency', 'currency_code', 'currency_code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shopProduct()
    {
        return $this->belongsTo('App\Models\ShopProduct');
    }
}
