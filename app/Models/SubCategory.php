<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = ['id', 'category_id', 'name', 'supports_condition', 'description', 'status', 'image', 'created_at', 'updated_at'];

    // Define relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(JobPost::class, 'subcategory_id');
    }
}
