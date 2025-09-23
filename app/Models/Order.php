<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'status',
        'customer_id',
        'customer_name',
        'discount',
        'sub_total',
        'net_total',
        'quantity',
        'payment_method',
        'payment_status',
        'shipping_address',
        'email',
        'mobile',
        'alternative_mobile',
        'zip',
        'city',
        'country',
        'delivery_stats',
        'delivery_method',
        'delivery_charge',
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Order Items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

}
