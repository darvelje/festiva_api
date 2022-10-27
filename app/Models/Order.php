<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $shop_id
 * @property integer $user_id
 * @property integer $user_address_id
 * @property integer $shop_coupon_id
 * @property integer $currency_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $delivery_type
 * @property integer $status_payment
 * @property float $total_price
 * @property OrderProduct[] $orderProducts
 * @property UserAddress $userAddress
 * @property Shop $shop
 * @property User $user
 * @property Currency $currency
 * @property ShopCoupon $shopCoupon
 */
class Order extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['shop_id', 'user_id', 'user_address_id', 'shop_coupon_id', 'currency_id', 'created_at', 'updated_at', 'delivery_type', 'status_payment', 'total_price'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderProducts()
    {
        return $this->hasMany('App\Models\OrderProduct');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userAddress()
    {
        return $this->belongsTo('App\Models\UserAddress');
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
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

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
    public function shopCoupon()
    {
        return $this->belongsTo('App\Models\ShopCoupon');
    }
}
