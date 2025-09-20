<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $fillable = [
        'name',
        'rank',
        'no_of_stars',
        'alias',
        'image'
    ];

    public function rankRequirement(){
        return $this->belongsTo(RankRequirement::class);
    }
}
