<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingJobCommission extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'pending_job_commissions';

    // Define the fields that can be mass-assigned (Eloquent's fillable property)
    protected $fillable = [
        'amount', 
        'details', 
        'remark', 
        'user_id', 
        'status',
        'send_to_user_id' 
    ];

    // Optionally, you can define the relationship between the PendingJobCommission model and the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
