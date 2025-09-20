<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBonus extends Model
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
        'commission_balance',
        'voucher_balance',
        'festival_bonus_balance',
        'saving',
        'temp_commission_balance',
        'temp_voucher_balance',
        'temp_festival_bonus_balance',
        'temp_saving',
        'temp_total',
        'is_voucher_open',
        'voucher_remaining_to_open',
        'joined_at',
        'total_balance',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
