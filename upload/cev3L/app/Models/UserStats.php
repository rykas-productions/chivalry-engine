<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStats extends Model
{
    use HasFactory;

    protected $guarded = []; // Use $guarded instead of guarded

    protected $fillable = [
        'user_id', 'level', 'experience', 'strength', 'agility', 'guard', 'labor',
        'iq', 'energy', 'maxEnergy', 'will', 'maxWill', 'brave', 'maxBrave',
        'hp', 'maxHP', 'primaryCurrencyHeld', 'primaryCurrencyBank'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
