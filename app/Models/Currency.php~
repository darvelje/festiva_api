<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $main
 * @property string $code
 * @property float $rate
 * @property Order[] $orders
 * @property ShopProductsPricesrate[] $shopProductsPricesrates
 * @property ShopCurrency[] $shopCurrencies
 * @property ShopDeliveryZone[] $shopDeliveryZones
 */
class Currency extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'created_at', 'updated_at', 'main', 'code', 'rate'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopProductsPricesrates()
    {
        return $this->hasMany('App\Models\ShopProductsPricesrate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopCurrencies()
    {
        return $this->hasMany('App\Models\ShopCurrency');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopDeliveryZones()
    {
        return $this->hasMany('App\Models\ShopDeliveryZone');
    }
}
