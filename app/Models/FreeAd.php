<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeAd extends Model
{
    protected $fillable = [
        'status',
        'no_of_advertisements',
        'advertisement_duration'
    ];


    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_free_ads');
    }
}
