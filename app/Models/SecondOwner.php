<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecondOwner extends Model
{
    protected $fillable = [
        'original_owner_id',
        'status',
        'first_name',
        'last_name',
        'relationship_to_owner',
        'dial_code',
        'contact_no',
        'address',
        'email_address',
        'nic_front_url',
        'nic_back_url',
        'assigned_date',
        'approved_date',
        'document_verified_at',
        'note'
    ];

    protected $casts = [
        'assigned_date' => 'datetime',
        'approved_date' => 'datetime',
        'document_verified_at' => 'datetime'
    ];

    public function originalOwner()
    {
        return $this->belongsTo(User::class, 'original_owner_id');
    }

}
