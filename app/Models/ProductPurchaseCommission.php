<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPurchaseCommission extends Model
{
    use HasFactory;

    protected $table = 'product_purchase_commissions';

    protected $fillable = [
        'product_id',
        'company_commission',
        'company_expenses',
        'customers_commission',
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
    ];

    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    
}
