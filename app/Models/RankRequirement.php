<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankRequirement extends Model
{
    protected $fillable = [
        'rank_id',
        'min_rank_id',
        'level_one_user_count',
        'level_two_user_count',
        'level_three_user_count',
        'level_four_user_count'
    ];

    public function rank(){
        return $this->belongsTo(Rank::class);
    }

    public function minRank(){
        return $this->belongsTo(Rank::class, 'min_rank_id', 'id');
    }
}
