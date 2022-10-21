<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $shop_id
 * @property string $currency_code
 * @property float $rate
 * @property boolean $main
 * @property string $created_at
 * @property string $updated_at
 * @property Currency $currency
 * @property Shop $shop
 * @property ShopDeliveryZone[] $shopDeliveryZones
 * @property ShopProductsPricesrate[] $shopProductsPricesrates
 * @property Order[] $orders
 */
class ShopCurrency extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['shop_id', 'currency_code', 'rate', 'main', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopDeliveryZones()
    {
        return $this->hasMany('App\Models\ShopDeliveryZone', 'currency_code', 'currency_code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopProductsPricesrates()
    {
        return $this->hasMany('App\Models\ShopProductsPricesrate', 'currency_code', 'currency_code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'currency_code', 'currency_code');
    }
}
