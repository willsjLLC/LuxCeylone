<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementBoostedLocation extends Model
{
    protected $table = 'advertisement_boosted_locations';

    protected $fillable = [
        'advertisement_boosted_history_id',
        'user_id',
        'boost_package_id',
        'advertisement_id',
        'district_id',
        'city_id',
    ];

    public function boostedHistory()
    {
        return $this->belongsTo(AdvertisementBoostedHistory::class, 'advertisement_boosted_history_id');
    }
}
