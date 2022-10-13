<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $order_id
 * @property integer $shop_product_id
 * @property integer $amount
 * @property string $created_at
 * @property string $updated_at
 * @property Order $order
 * @property ShopProduct $shopProduct
 */
class OrderProduct extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['order_id', 'shop_product_id', 'amount', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shopProduct()
    {
        return $this->belongsTo('App\Models\ShopProduct');
    }
}
