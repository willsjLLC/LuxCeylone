<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusTransactionHistory extends Model
{
    use HasFactory;

    protected $table = 'bonus_transaction_histories';

    protected $fillable = [
        'user_id',
        'debit_user_id',
        'is_leader',
        'is_top_leader',
        'amount',
        'charge',
        'trx_type',
        'trx', 
        'customers_voucher',
        'customers_festival',
        'customers_saving',
        'leader_bonus',
        'leader_vehicle_lease',
        'leader_petrol',
        // 'max_ref_complete_to_car',
        'top_leader_car',
        'top_leader_house',
        'top_leader_expenses',
        'post_bonus_balance',
        'details',
        'remark',
    ];

    // If you want to cast data types (optional but useful)
    protected $casts = [
        'amount' => 'decimal:8',
        'charge' => 'decimal:8',
        'customers_voucher' => 'decimal:2',
        'customers_festival' => 'decimal:2',
        'customers_saving' => 'decimal:2',
        'leader_bonus' => 'decimal:2',
        'leader_vehicle_lease' => 'decimal:2',
        'leader_petrol' => 'decimal:2',
        'top_leader_car' => 'decimal:2',
        'top_leader_house' => 'decimal:2',
        'top_leader_expenses' => 'decimal:2',
        'post_bonus_balance' => 'decimal:2',
        'is_leader' => 'boolean',
        'is_top_leader' => 'boolean',
    ];

    // If you want to link to User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
