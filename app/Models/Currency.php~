<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $main
 * @property string $code
 * @property float $rate
 * @property ShopCurrency $shopCurrency
 */
class Currency extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'created_at', 'updated_at', 'main', 'code', 'rate'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shopCurrency()
    {
        return $this->hasOne('App\Models\ShopCurrency', 'currency_code', 'code');
    }
}
