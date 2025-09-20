<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyExpensesSavingHistory extends Model
{
    use HasFactory;

    protected $table = 'company_expenses_saving_histories';

    protected $fillable = [
        'company_id',
        'pkg_id',
        'pkg_activation_comm_id',
        'user_id',
        'remark',
        'charge',
        'current_expenses_balance',
        'trx_type',
        'trx',
        'details',
        'amount',
        'post_saving_balance',
    ];

    protected $casts = [
        'charge' => 'decimal:2',
        'current_expenses_balance' => 'decimal:2',
        'amount' => 'decimal:8',
        'post_saving_balance' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(AdvertisementPackage::class, 'pkg_id');
    }

    public function packageCommission()
    {
        return $this->belongsTo(PackageActivationCommission::class, 'pkg_activation_comm_id');
    }
}
