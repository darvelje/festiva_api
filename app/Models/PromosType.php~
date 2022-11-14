<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $ubication
 * @property string $created_at
 * @property string $updated_at
 * @property string $name
 * @property Promo[] $promos
 */
class PromosType extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['ubication', 'created_at', 'updated_at', 'name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function promos()
    {
        return $this->hasMany('App\Models\Promo', 'id_promo_type');
    }
}
