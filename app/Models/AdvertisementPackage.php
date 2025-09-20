<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementPackage extends Model
{
    protected $fillable = [
        'status',
        'name',
        'description',
        'package_code',
        'type',
        'no_of_advertisements',
        'price',
        'advertisement_duration',
        'package_duration',
        'includes_boost',
        'boost_package_id',
        'no_of_boost',
    ];

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class, 'package_id');
    }

    public function activations()
    {
        return $this->hasMany(EmployeePackageActivationHistory::class, 'package_id');
    }

    public function boostPackage()
    {
        return $this->belongsTo(AdvertisementBoostPackage::class, 'boost_package_id');
    }

    public function companyExpensesSavingHistory()
    {
        return $this->belongsTo(AdvertisementPackage::class, 'boost_package_id');
    }
}
