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
 * @property UserAddress[] $userAddresses
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
    public function userAddresses()
    {
        return $this->hasMany('App\Models\UserAddress');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province()
    {
        return $this->belongsTo('App\Models\Province');
    }
}
