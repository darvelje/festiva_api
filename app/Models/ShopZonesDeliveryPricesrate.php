<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $shop_zones_delivery_id
 * @property integer $currency_id
 * @property float $price
 * @property string $created_at
 * @property string $updated_at
 * @property ShopDeliveryZone $shopDeliveryZone
 * @property Currency $currency
 */
class ShopZonesDeliveryPricesrate extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['shop_zones_delivery_id', 'currency_id', 'price', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shopDeliveryZone()
    {
        return $this->belongsTo('App\Models\ShopDeliveryZone', 'shop_zones_delivery_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo('App\Models\Currency', 'currency_id');
    }
}
