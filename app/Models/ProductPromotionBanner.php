<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPromotionBanner extends Model
{
    protected $fillable = [
        'status',
        'title',
        'image',
        'description',
    ];
}
