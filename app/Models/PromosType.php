<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $ubication
 * @property string $category_id
 */
class PromosType extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', '$ubication', '$category_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function promo()
    {
        return $this->hasOne('App\Models\Promo');
    }
}
