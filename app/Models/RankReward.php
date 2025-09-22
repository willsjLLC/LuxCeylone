<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankReward extends Model
{
    protected $fillable = [
        'rank_id',
        'reward',
        'image'
    ];


    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

}
