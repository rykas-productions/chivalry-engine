<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function userManagement()
{
    $usersWithStats = DB::table('users')
            ->leftJoin('user_stats', 'users.id', '=', 'user_stats.user_id')
            //->select('user_stats.*')
            ->select('users.*', 'user_stats.*')
            ->get();

            //dd($usersWithStats); // Debugging output

        return view('user-management', compact('usersWithStats'));
}

public function updateCurrency(Request $request)
{
    $user = Auth::user();
    $amount = (int) $request->input('amount');

    if ($amount > 0) {
        $userStats = DB::table('user_stats')
            ->where('user_id', $user->id) // Use 'user_id' instead of 'id'
            ->first();

        if ($userStats) {
            $newCurrencyHeld = $userStats->primaryCurrencyHeld + $amount;

            DB::table('user_stats')
                ->where('user_id', $user->id) // Use 'user_id' instead of 'id'
                ->update(['primaryCurrencyHeld' => $newCurrencyHeld]);

            return redirect()->route('user.management')->with('success', 'Currency added successfully.');
        }
    }

    return redirect()->route('user.management')->with('error', 'Invalid amount.');
}
}