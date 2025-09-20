<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'district_id',
        'name',
        'slug'
    ];

    public function advertisements(){
        $this->hasMany(Advertisement::class, 'city_id');
    }
}
