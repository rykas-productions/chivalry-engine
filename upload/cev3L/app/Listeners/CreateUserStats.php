<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\UserStats;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateUserStats implements ShouldQueue
{
    public function handle(UserRegistered $event)
    {
        UserStats::create([
            'user_id' => $event->user->id,
            'level' => 1,
            'experience' => 0,
            'strength' => 10,
            'agility' => 10,
            'guard' => 10,
            'labor' => 10,
            'iq' => 10,
            'energy' => 10,
            'maxEnergy' => 10,
            'will' => 100,
            'maxWill' => 100,
            'brave' => 5,
            'maxBrave' => 5,
            'hp' => 100,
            'maxHP' => 100,
            'primaryCurrencyHeld' => 100,
            'primaryCurrencyBank' => -1,
        ]);
    }
}
