<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

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
 * @property string $remember_token
 * @property UserAddress[] $userAddresses
 * @property Notification[] $notifications
 * @property ShopProduct[] $shopProducts
 * @property Order[] $orders
 */
class User extends  Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $guard_name ='api';

    /**
     * @var array
     */
    protected $fillable = ['name', 'last_name', 'email', 'updated_at', 'phone', 'avatar', 'email_verified_at', 'password', 'created_at', 'remember_token'];

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
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shop()
    {
        return $this->hasOne(Shop::class);
    }
}
