<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'status',
        'description',
        'image'
    ];

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'sub_category_id', 'id');
    }

    // Helper method to get active products
    public function activeProducts()
    {
        return $this->products()->where('status', 'active');
    }

    // Status accessor for consistency
    public function getStatusLabelAttribute()
    {
        return $this->status == 1 ? 'Active' : 'Inactive';
    }
}
