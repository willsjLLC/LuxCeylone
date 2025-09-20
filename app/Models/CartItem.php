<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_id',
        'customer_name',
        'product_name',
        'original_price',
        'selling_price',
        'discount',
        'sub_total',
        'net_total',
        'quantity'
    ];

    /**
     * Relationship with Customer
     */
    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
