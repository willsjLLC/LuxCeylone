<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = [
        'user_id',
        'debit_user_id',
        'amount',
        'post_balance',
        'charge',
        'trx_type',
        'details',
        'trx',
        'remark',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function boostHistory()
    {
        return $this->hasOne(AdvertisementBoostedHistory::class, 'transaction_id');
    }
}
