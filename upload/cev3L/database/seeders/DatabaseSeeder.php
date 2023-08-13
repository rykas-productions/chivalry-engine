<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserStats;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Insert placeholder user
        DB::table('users')->insert([
            'id' => 0,
            'name' => 'Placeholder',
            'email' => 'placeholder@example.com',
            'password' => bcrypt('SanIsGay'), // Replace with a hashed password
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert default user_stats for placeholder user
        DB::table('user_stats')->insert([
            'user_id' => 0,
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

        // Create other users and their associated user_stats
       // User::factory(10)->create()->each(function ($user) {
       //     UserStats::factory()->create(['user_id' => $user->id]);
       // });
    }
}