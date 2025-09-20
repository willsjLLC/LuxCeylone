<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementBoostPackage extends Model
{
    protected $fillable = [
        'status',
        'name',
        'description',
        'package_code',
        'type',
        'price',
        'duration',
        'highlighted_color',
        'priority_level',
    ];

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function packageActivations()
    {
        return $this->hasMany(EmployeePackageActivationHistory::class);
    }

    public function advertisementHistory()
    {
        return $this->hasMany(AdvertisementBoostedHistory::class);
    }
}
