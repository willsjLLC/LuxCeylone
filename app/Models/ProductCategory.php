<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'status'
    ];

    // Relationship with Products
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    // public function productSubcategory()
    // {
    //     return $this->hasMany(ProductSubCategory::class);
    // }

    // Updated relationship name for consistency
    public function productSubcategories()
    {
        return $this->hasMany(ProductSubCategory::class, 'category_id', 'id');
    }

    // Helper method to get active subcategories
    public function activeSubcategories()
    {
        return $this->productSubcategories()->where('status', 1);
    }



}
