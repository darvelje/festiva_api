<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $province_id
 * @property string $name
 * @property string $slug
 * @property string $created_at
 * @property string $updated_at
 * @property Locality[] $localities
 * @property ShopDeliveryZone[] $shopDeliveryZones
 * @property Province $province
 */
class Municipality extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['province_id', 'name', 'slug', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function localities()
    {
        return $this->hasMany('App\Models\Locality', 'municipalitie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopDeliveryZones()
    {
        return $this->hasMany('App\Models\ShopDeliveryZone', 'municipalitie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province()
    {
        return $this->belongsTo('App\Models\Province');
    }
}
