<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $shop_id
 * @property integer $currency_id
 * @property float $rate
 * @property boolean $main
 * @property string $created_at
 * @property string $updated_at
 * @property Currency $currency
 * @property Shop $shop
 */
class ShopCurrency extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['shop_id', 'currency_id', 'rate', 'main', 'created_at', 'updated_at'];

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
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }
}
