<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $mode
 * @property string $client_id
 * @property string $client_secret
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 */
class PaymentMethod extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_methods';

    /**
     * @var array
     */
    protected $fillable = ['name', 'mode', 'client_id', 'client_secret', 'status', 'created_at', 'updated_at'];

}
