<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $created_at
 * @property string $updated_at
 * @property Municipality[] $municipalities
 * @property ShopDeliveryZone[] $shopDeliveryZones
 */
class Province extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'slug', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function municipalities()
    {
        return $this->hasMany('App\Models\Municipality');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopDeliveryZones()
    {
        return $this->hasMany('App\Models\ShopDeliveryZone');
    }
}
