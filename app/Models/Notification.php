<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $message
 * @property string $viewed
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class Notification extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'type', 'message', 'viewed', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
