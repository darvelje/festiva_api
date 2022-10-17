<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $name
 * @property float $rate
 * @property float $main
 * @property string $created_at
 * @property string $updated_at
 * @property ShopProductsPricesrate[] $shopProductsPricesrates
 */
class Currency extends Model
{


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shopProductsPricesrates()
    {
        return $this->hasMany('App\Models\ShopProductsPricesrate');
    }
}
