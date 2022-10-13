<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $app_name
 * @property string $app_favicon
 * @property string $app_logo
 * @property string $created_at
 * @property string $comission_porcent
 * @property string $updated_at
 */
class Setting extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['app_name', 'app_favicon', 'app_logo', 'created_at', 'comission_porcent', 'updated_at'];
}
