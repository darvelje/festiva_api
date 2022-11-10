<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $id_promo_type
 * @property string $path_image
 * @property boolean $status
 * @property string $url
 * @property string $created_at
 * @property string $updated_at
 * @property integer $category_id
 * @property PromosType $promosType
 * @property CategoriesProduct $categoriesProduct
 */
class Promo extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id_promo_type', 'path_image', 'status', 'url', 'created_at', 'updated_at', 'category_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function promosType()
    {
        return $this->belongsTo('App\Models\PromosType', 'id_promo_type');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoriesProduct()
    {
        return $this->belongsTo('App\Models\CategoriesProduct', 'category_id');
    }
}
