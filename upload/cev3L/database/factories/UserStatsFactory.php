<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\UserStats;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserStats>
 */
class UserStatsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    use HasFactory;

    protected $table = 'user_stats'; // Make sure the table name is correct
    protected $primaryKey = 'user_id'; // Make sure the primary key is correct


    protected $model = UserStats::class;

    public function definition(): array
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
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
        ];
    }
}
