<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Advertisement extends Model
{
    // Mass assignable attributes
    protected $fillable = [
        'user_id',
        'contact_name',
        'category_id',
        'subcategory_id',
        'package_id',
        'is_featured',
        'advertisement_code',
        'title',
        'description',
        'condition',
        'contact_mobile',
        'contact_email',
        'payment_option_id',
        'file_name',
        'impressions',
        'clicks',
        'cpc',
        'district_id',
        'city_id',
        'price',
        'advertisement_cost',
        'is_price_negotiable',
        'is_boosted',
        'is_free',
        'account_type',
        'posted_date',
        'expiry_date',
        'status',
        'rejection_reason',
        'watermark',
        'watermark_text',
    ];

    // Cast attributes to native types
    protected $casts = [
        'user_id' => 'integer',
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
        'package_id' => 'integer',
        'is_featured' => 'boolean',
        'payment_option_id' => 'integer',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'cpc' => 'integer',
        'district_id' => 'integer',
        'city_id' => 'integer',
        'price' => 'decimal:2',
        'advertisement_cost' => 'decimal:2',
        'is_price_negotiable' => 'boolean',
        'is_boosted' => 'boolean',
        'posted_date' => 'datetime',
        'expiry_date' => 'datetime',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function package()
    {
        return $this->belongsTo(AdvertisementPackage::class, 'package_id');
    }

    public function paymentOption()
    {
        return $this->belongsTo(PaymentOption::class, 'payment_option_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function images()
    {
        return $this->hasMany(AdvertisementImage::class, 'advertisement_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::AD_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::AD_APPROVED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', Status::AD_COMPLETED);
    }

    public function scopePause($query)
    {
        return $query->where('status', Status::AD_ONGOING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', Status::AD_REJECTED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', Status::AD_EXPIRED);
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', Status::AD_CANCELED);
    }

    public function boostHistories()
    {
        return $this->hasMany(AdvertisementBoostedHistory::class);
    }

    public function scopeWithBoostDetails(Builder $query)
    {
        return $query->with(['district', 'city', 'category', 'subCategory'])
            ->whereIn('status', [Status::AD_APPROVED, Status::AD_COMPLETED])
            ->whereHas('boostHistories', function (Builder $q) {
                $q->where('status', 1)
                    ->orderByDesc('id')
                    ->limit(1);
            })
            ->with(['boostHistories' => function ($q) {
                $q->where('status', 1)
                    ->orderByDesc('id')
                    ->limit(1);
            }, 'boostHistories.boostPackage']);
    }

    public function latestBoostHistory()
    {
        return $this->hasOne(AdvertisementBoostedHistory::class)->latestOfMany();
    }
}
