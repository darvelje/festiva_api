<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $currency_id
 * @property string $method
 * @property float $amount
 * @property string $created_at
 * @property string $updated_at
 */
class ContEarnings extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['currency_id', 'method', 'amount', 'created_at', 'updated_at'];


}
