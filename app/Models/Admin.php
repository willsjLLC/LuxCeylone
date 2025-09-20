<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles, HasPermissions;

    protected $guarded = [];

    protected $guard_name = 'admin';
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'designation',
        'status',
        'joined_at',
        'last_login_at',
        'created_by',
        'username',
        'email_verified_at',
        'image',
        'password',
        'remember_token',
    ];
}
