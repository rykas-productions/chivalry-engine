<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\UserStats;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

	$user = User::where('email', $input['email'])->firstOrFail();
	$userId = $user->getConnection()->getPdo()->lastInsertId();
	//dd($userId);

	UserStats::create([
            'user_id' => $user->id,
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

	return $user;

    }
}
