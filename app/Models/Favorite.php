<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Favorite extends Model
{
    use HasFactory;
    protected $table = 'favorites';
    protected $fillable = ['user_id', 'job_id'];

    public function job()
    {
        return $this->belongsTo(JobPost::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
