<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $cover
 * @property string $avatar
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property string $url
 * @property string $facebook_link
 * @property string $instagram_link
 * @property string $twitter_link
 * @property string $wa_link
 * @property string $telegram_link
 * @property string $created_at
 * @property string $updated_at
 * @property string $user_id
 * @property ShopProduct[] $shopProducts
 * @property ShopCoupon[] $shopCoupons
 * @property Order[] $orders
 */
class Shop extends Model
{

    public function shopProducts(){
        return $this->hasMany('App\Models\ShopProduct');
    }

    public function shopCoupons(){
        return $this->hasMany('App\Models\ShopCoupon');
    }

    public function orders(){
        return $this->hasMany('App\Models\Order');
    }

    public function users(){
        return $this->belongsTo('App\Models\User');
    }
}
