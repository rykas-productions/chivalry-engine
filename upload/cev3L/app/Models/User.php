<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the user's statistics.
     */
    public function userStats()
    {
        return $this->hasOne(UserStats::class, 'user_id');
    }

    /**
     * Deposit currency into the user's bank account.
     */
    public function depositToBank($amount)
    {
        $this->update([
            'primaryCurrencyHeld' => $this->primaryCurrencyHeld - $amount,
            'primaryCurrencyBank' => $this->primaryCurrencyBank + $amount,
        ]);
    }

    /**
     * Withdraw currency from the user's bank account.
     */
    public function withdrawFromBank($amount)
    {
        $this->update([
            'primaryCurrencyHeld' => $this->primaryCurrencyHeld + $amount,
            'primaryCurrencyBank' => $this->primaryCurrencyBank - $amount,
        ]);
    }

    /**
     * Buy a bank account.
     */
    public function buyBankAccount($openingFee)
    {
        if ($this->primaryCurrencyHeld >= $openingFee) {
            $this->update([
                'primaryCurrencyHeld' => $this->primaryCurrencyHeld - $openingFee,
                'primaryCurrencyBank' => 0,
            ]);
            return true;
        }
        return false;
    }
}
