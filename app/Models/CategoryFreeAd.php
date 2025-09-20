<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFreeAd extends Model
{
    use HasFactory;

    protected $table = 'category_free_ads';

    protected $fillable = [
        'category_id',
        'free_ad_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function freeAd()
    {
        return $this->belongsTo(FreeAd::class);
    }
}
