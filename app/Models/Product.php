<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'user_id',
        'product_code',
        'category_id',
        'sub_category_id',
        'description',
        'original_price',
        'cost',
        'profit',
        'discount',
        'selling_price',
        'quantity',
        'unit',
        'sku',
        'brand',
        'image_url',
        'watermark',
        'watermark_text',
        'status',
        'weight',
    ];

    // Relationship with User
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id', 'id');
    // }

    // Relationship with User
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }


    // Relationship with Product Category
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id', 'id');
    }

    public function subCategory()
    {
        return $this->belongsTo(ProductSubCategory::class, 'sub_category_id', 'id');
    }

    // Status Label Accessor
    public function getStatusLabelAttribute()
    {
        return $this->status === 'active' ? 'Active' : 'Inactive';
    }

    public function productPurchaseCommission()
    {
        return $this->hasOne(ProductPurchaseCommission::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

     public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
