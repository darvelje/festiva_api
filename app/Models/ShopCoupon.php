<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $shop_id
 * @property string $name
 * @property string $code
 * @property float $value
 * @property string $status
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property Shop $shop
 */
class ShopCoupon extends Model
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }
}
