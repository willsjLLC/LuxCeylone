<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('status', Status::CATEGORIES_ENABLE);
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
    public function jobPosts()
    {
        return $this->hasMany(JobPost::class);
    }

    public function freeAds()
    {
        return $this->belongsToMany(FreeAd::class, 'category_free_ad');
    }
}
