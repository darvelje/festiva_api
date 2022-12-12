<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $shop_id
 * @property integer $currency_code
 * @property float $pending_amount
 * @property float $amount
 * @property string $created_at
 * @property string $updated_at
 * @property Shop $shop
 */
class ShopsAmounts extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['shop_id', 'currency_code', 'pending_amount', 'amount', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }
}
