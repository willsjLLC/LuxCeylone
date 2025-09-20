<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopLeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pkg_id',
        'pkg_activation_comm_id',
        'leader_id',
        'for_car',
        'for_house',
        'for_expenses',
        'temp_for_car',
        'temp_for_house',
        'temp_for_expenses',
        'temp_total',
        'total_balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
