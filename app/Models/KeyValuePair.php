<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyValuePair extends Model
{
    use HasFactory;

    protected $table = 'key_value_pair';

    protected $fillable = ['key', 'value'];
}