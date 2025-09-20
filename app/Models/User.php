<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, UserNotify;
    use Notifiable;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance',
        'kyc_data'
    ];
    // Allow all fields to be mass assignable
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime',
        'balance' => 'decimal:8',
    ];




    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn() => $this->dial_code . $this->mobile,
        );
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeKycApproved($query)
    {
        return $query->where('kv', Status::KYC_VERIFIED);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    public function scopeLeaders($query)
    {
        return $query->where('role', Status::LEADER);
    }

    public static function topLeaders()
    {
        return self::where('role', 2)->where('is_top_leader', 1);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }



    public function user_categories()
    {
        return $this->hasMany(UserCategories::class, 'user_id');
    }


    /**
     * Get the user who referred this user
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_user_id', 'id');
    }

    /**
     * Get users directly referred by this user
     */
    public function directReferrals()
    {
        return $this->hasMany(User::class, 'referred_user_id', 'id');
    }

    /**
     * Check if this user can refer more users (limit is 2)
     */
    public function canReferMore()
    {
        return $this->directReferrals()->count() < 2;
    }

    /**
     * Get the level of this user in the referral hierarchy
     * Level 1 = direct referral of root user
     * Level 2 = referral of a level 1 user, and so on
     */
    public function getReferralLevel($rootUserId, $currentLevel = 1, $maxLevel = 12)
    {
        if ($this->referred_user_id == $rootUserId) {
            return $currentLevel;
        }

        if ($currentLevel >= $maxLevel || !$this->referrer) {
            return null;
        }

        return $this->referrer->getReferralLevel($rootUserId, $currentLevel + 1, $maxLevel);
    }

    public function packageActivations()
    {
        return $this->hasMany(EmployeePackageActivationHistory::class);
    }

    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    public function topLeaderBonuses()
    {
        return $this->hasOne(TopLeader::class);
    }

    public function leaderBonuses()
    {
        return $this->hasOne(LeaderBonus::class);
    }

    public function customerBonuses()
    {
        return $this->hasOne(CustomerBonus::class);
    }

    public function userTrainings()
    {
        return $this->hasMany(UserTraining::class);
    }

    public function employeePackageActivationHistories()
    {
        return $this->hasMany(EmployeePackageActivationHistory::class);
    }
}
