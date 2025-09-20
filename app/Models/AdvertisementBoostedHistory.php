<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementBoostedHistory extends Model
{
    protected $table = 'advertisement_boosted_histories';

    protected $fillable = [
        'status',
        'user_id',
        'advertisement_id',
        'user_package_id',
        'is_package_boost',
        'boost_package_id',
        'payment_option_id',
        'transaction_id',
        'is_free_advertisement',
        'price',
        'impressions',
        'clicks',
        'boosted_date',
        'expiry_date',
        'remaining',
    ];

    // Disable Laravel mass assignment protection for calculated column
    protected $guarded = ['remaining'];

    protected $casts = [
        'boosted_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boostedLocations()
    {
        return $this->hasMany(AdvertisementBoostedLocation::class, 'advertisement_boosted_history_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class, 'advertisement_id');
    }

    public function boostPackage()
    {
        return $this->belongsTo(AdvertisementBoostPackage::class, 'boost_package_id');
    }
}
