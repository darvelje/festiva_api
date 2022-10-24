<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $last_name
 * @property string $email
 * @property string $updated_at
 * @property string $phone
 * @property string $avatar
 * @property string $email_verified_at
 * @property string $password
 * @property string $created_at
 * @property Notification[] $notifications
 * @property ShopProduct[] $shopProducts
 * @property UserAddress[] $userAddresses
 * @property Order[] $orders
 */
class User extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'last_name', 'email', 'updated_at', 'phone', 'avatar', 'email_verified_at', 'password', 'created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shopProducts()
    {
        return $this->belongsToMany('App\Models\ShopProduct', 'user_favorites_has_shop_products');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userAddresses()
    {
        return $this->hasMany('App\Models\UserAddress');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
}
