<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_promo_type
 * @property string $path_image
 * @property boolean $status
 * @property PromosType $promosType
 */
class Promo extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id_promo_type', 'path_image', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function promosType()
    {
        return $this->belongsTo('App\Models\PromosType', 'id_promo_type');
    }
}
