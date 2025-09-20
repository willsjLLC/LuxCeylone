<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageActivationCommission extends Model
{
    use HasFactory;

    protected $table = 'package_activation_commissions';

    protected $fillable = [
        'pkg_id',
        'company_commission',
        'company_expenses',
        'level_one_commission',
        'level_two_commission',
        'level_three_commission',
        'level_four_commission',
        'customers_voucher',
        'customers_festival',
        'customers_saving',
        'leader_bonus',
        'leader_vehicle_lease',
        'leader_petrol',
        'max_ref_complete_to_car',
        'top_leader_car',
        'top_leader_house',
        'top_leader_expenses',
    ];

    public function AdvertisementPackage()
    {
        return $this->belongsTo(AdvertisementPackage::class);
    }
}
