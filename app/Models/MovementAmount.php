<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $currency_id
 * @property string $model
 * @property string $status
 * @property string $url
 * @property string $orders_id
 * @property integer $model_id
* @property float $amount
 * @property string $type
 * @property string $detail
 * @property string $method
 * @property boolean $only_register
 * @property string $created_at
 * @property string $updated_at
 */
class MovementAmount extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cont_movement';

    /**
     * @var array
     */
    protected $fillable = ['url','orders_id','model', 'model_id', 'amount', 'method', 'detail', 'currency_id',  'only_register',   'status',   'type',  'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

}
