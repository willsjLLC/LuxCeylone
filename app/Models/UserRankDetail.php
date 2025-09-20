<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRankDetail extends Model
{
    protected $fillable = [
        'user_id',
        'current_rank_id',
        'level_one_user_count',
        'level_two_user_count',
        'level_three_user_count',
        'level_four_user_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class, 'current_rank_id');
    }
    
}
