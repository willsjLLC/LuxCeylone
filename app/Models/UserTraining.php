<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class UserTraining extends Model
{
    protected $fillable = [
        'user_id',
        'training_id',
        'status'
    ];

    public function training()
    {
        return $this->belongsTo(related: Training::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::TRAINING_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', Status::TRAINING_COMPLETED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', Status::TRAINING_REJECTED);
    }

    public function user(){
        return $this->belongsTo(related: User::class);
    }
}
