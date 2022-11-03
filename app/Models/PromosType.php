<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $type
 * @property string $nivel
 */
class PromosType extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', 'type', 'nivel'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function promo()
    {
        return $this->hasOne('App\Models\Promo');
    }
}
