<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $shop_product_id
 * @property string $path_photo
 * @property boolean $main
 * @property string $created_at
 * @property string $updated_at
 * @property ShopProduct $shopProduct
 */
class ShopProductPhoto extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['shop_product_id', 'path_photo', 'main', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shopProduct()
    {
        return $this->belongsTo('App\Models\ShopProduct');
    }
}
