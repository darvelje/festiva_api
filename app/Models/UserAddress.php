<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $localitie_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $zip_code
 * @property string $name
 * @property string $address
 * @property Locality $locality
 * @property User $user
 * @property Order[] $orders
 */
class UserAddress extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'localitie_id', 'created_at', 'updated_at', 'contact_name', 'contact_phone', 'zip_code', 'name', 'address'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locality()
    {
        return $this->belongsTo('App\Models\Locality', 'localitie_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
}
