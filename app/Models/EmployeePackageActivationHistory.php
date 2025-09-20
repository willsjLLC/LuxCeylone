<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePackageActivationHistory extends Model
{
    use HasFactory;


    protected $table = 'employee_package_activation_histories';

    protected $fillable = [
        'user_id',
        'transaction_id',
        'payment_method',
        'total_jobs_did',
        'activation_expired',
        'payment_status',
        'expiry_date',
        'package_id',
        'total_ads',
        'used_ads',
        'can_boost',
        'boost_package_id',
        'total_boosted_ads',
        'used_boosted_ads',
        'activated_for_earning_tier'
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function package()
    {
        return $this->belongsTo(AdvertisementPackage::class, 'package_id');
    }

    public function getRemainingAdsAttribute()
    {
        return $this->total_ads - $this->used_ads;
    }

    public function getRemainingBoostedAdsAttribute()
    {
        return $this->total_boosted_ads - $this->used_boosted_ads;
    }

    public function boostPackage()
    {
        return $this->belongsTo(AdvertisementBoostPackage::class, 'boost_package_id');
    }
}
