<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    public function Advertisement(){
        $this->hasMany(Advertisement::class,'district_id');
    }
}
