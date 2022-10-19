<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $shop_id
 * @property integer $localitie_id
 * @property integer $municipalitie_id
 * @property integer $province_id
 * @property string $currency_code
 * @property integer $time
 * @property string $time_type
 * @property float $price
 * @property string $created_at
 * @property string $updated_at
 * @property ShopCurrency $shopCurrency
 * @property Shop $shop
 * @property Locality $locality
 * @property Municipality $municipality
 * @property Province $province
 */
class ShopDeliveryZone extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['shop_id', 'localitie_id', 'municipalitie_id', 'province_id', 'currency_code', 'time', 'time_type', 'price', 'created_at', 'updated_at'];

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
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locality()
    {
        return $this->belongsTo('App\Models\Locality', 'localitie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function municipality()
    {
        return $this->belongsTo('App\Models\Municipality', 'municipalitie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province()
    {
        return $this->belongsTo('App\Models\Province');
    }
}
