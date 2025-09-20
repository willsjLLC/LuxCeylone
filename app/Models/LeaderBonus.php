<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderBonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pkg_id',
        'pkg_activation_comm_id',
        'status',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'bonus',
        'leasing_amount',
        'petrol_allowance',
        'temp_bonus',
        'temp_leasing_amount',
        'temp_petrol_allowance',
        'temp_total',
        'current_referral_count',
        'total_levels',
        'total_users',
        'is_progress_completed',
        'joined_at',
        'total_balance',
    ];

    protected $casts = [
        'status' => 'boolean',
        'joined_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
