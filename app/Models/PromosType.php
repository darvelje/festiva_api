<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $ubication
 * @property integer $category_id
 * @property Promo[] $promos
 */
class PromosType extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['ubication', 'category_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function promos()
    {
        return $this->hasMany('App\Models\Promo', 'id_promo_type');
    }
}
