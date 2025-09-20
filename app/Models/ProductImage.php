<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
   protected $fillable = [
        'product_id',
        'image',
        'is_primary',
        'sort_order'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
