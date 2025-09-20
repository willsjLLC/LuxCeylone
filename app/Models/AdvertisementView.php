<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertisementView extends Model
{
    protected $fillable = [
        'advertisement_id',
        'user_id',
        'viewer_ip',
        'viewed_at'
    ];

    protected $casts = [
        'viewed_at' => 'datetime'
    ];

    // Relationship to Advertisement
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    // Relationship to User (if viewer is logged in)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
