<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $shop_product_id
 * @property integer $user_id
 * @property ShopProduct $shopProduct
 * @property User $user
 */
class UserFavoritesHasShopProduct extends Model
{
    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shopProduct()
    {
        return $this->belongsTo('App\Models\ShopProduct');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
