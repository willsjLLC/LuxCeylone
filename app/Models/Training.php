<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'image',
        'min_income_threshold',
        'ticket_price',
    ];
}
