<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $currency_code
 * @property string $type
 * @property string $method
 * @property float $amount
 * @property string $referer
 * @property integer $referer_id
 * @property string $created_at
 * @property string $updated_at
 */
class ContEarnings extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['currency_code', 'type', 'method', 'amount', 'referer', 'referer_id', 'created_at', 'updated_at'];


}
