<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralLog extends Model
{


    protected $guarded = ['id'];

    protected $fillable = [
        'referrer_id',
        'referred_id',

    ];

   

   public function referrer()
   {
       return $this->belongsTo(User::class, 'referrer_id', 'id');
   }

   /**
    * Get the user who was referred
    */
   public function referred()
   {
       return $this->belongsTo(User::class, 'referred_id', 'id');
   }
}
