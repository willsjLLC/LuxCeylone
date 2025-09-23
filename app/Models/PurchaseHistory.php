<?php

namespace App\Models;

use Google\Service\Monitoring\Custom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseHistory extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'order_id',
        'transaction_id',
        'total_purchase_did',
        'payment_method',
        'payment_status',
        'delivery_method',
        'delivery_charge',
        'total_amount',
        'amount_paid',
        'discount',
        'currency'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function order_id()
    {
        return $this->belongsTo(Order::class);
    }
}
