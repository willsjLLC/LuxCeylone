<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCategories extends Model
{
    protected $guarded = ['id'];


    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
