<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $municipalitie_id
 * @property string $name
 * @property string $slug
 * @property ShopDeliveryZone[] $shopDeliveryZones
 * @property Municipality $municipality
 * @property UserAddress[] $userAddresses
 */
class Locality extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['municipalitie_id', 'name', 'slug'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopDeliveryZones()
    {
        return $this->hasMany('App\Models\ShopDeliveryZone', 'localitie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function municipality()
    {
        return $this->belongsTo('App\Models\Municipality', 'municipalitie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userAddresses()
    {
        return $this->hasMany('App\Models\UserAddress', 'localitie_id');
    }
}