<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $terms_conditions
 * @property boolean $terms_conditions_status
 * @property string $privacy_policy
 * @property string $legal_warning
 * @property boolean $legal_warning_status
 * @property string $payment_methods
 * @property boolean $payment_methods_status
 * @property string $product_returns
 * @property boolean $product_returns_status
 * @property string $transportation
 * @property boolean $transportation_status
 * @property string $refund_guarantees
 * @property boolean $refund_guarantees_status
 */
class SettingsPage extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['terms_conditions', 'terms_conditions_status', 'privacy_policy', 'legal_warning', 'legal_warning_status', 'payment_methods', 'payment_methods_status', 'product_returns', 'product_returns_status', 'transportation', 'transportation_status', 'refund_guarantees', 'refund_guarantees_status'];
}
