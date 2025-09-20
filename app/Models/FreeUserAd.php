<?php

namespace App\Models;

use App\Notify\Textmagic\Services\Models\User;
use Illuminate\Database\Eloquent\Model;

class FreeUserAd extends Model
{
    protected $fillable = [
        'status',
        'user_id',
        'total_ads',
        'used_ads'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }
}
