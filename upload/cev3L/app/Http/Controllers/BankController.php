<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userStats = $user->userStats;
        $moduleConfig = config('bank');

        if ($userStats->primaryCurrencyBank == -1) {
            return view('bank.index', compact('user', 'userStats', 'moduleConfig'));
        }

        $action = request()->input('action', '');

        switch ($action) {
            case 'deposit':
                return $this->deposit(request());
            case 'withdraw':
                return $this->withdraw(request());
            default:
                return $this->home();
        }
    }

    public function home()
    {
        $user = Auth::user();
        $userStats = DB::table('user_stats')
            ->where('user_id', $user->id)
            ->first();

        $moduleConfig = config('bank');

        return view('bank.index', compact('user', 'userStats', 'moduleConfig'));
    }


   public function deposit(Request $request)
    {
        $user = Auth::user();
        $userStats = $user->userStats;
        $depositAmount = (int) $request->input('deposit');

        if ($depositAmount <= 0) {
            return redirect()->route('bank.index')->with('error', 'Invalid deposit amount.');
        }

        if ($user->primaryCurrencyHeld < $depositAmount) {
            return redirect()->route('bank.index')->with('error', 'You do not have enough currency to deposit.');
        }

        // Update the user's primary currency held and bank values
        $newHeldCurrency = $userStats->primaryCurrencyHeld - $depositAmount;
        $newBankCurrency = $userStats->primaryCurrencyBank + $depositAmount;

        $user->update([
            'primaryCurrencyHeld' => $newHeldCurrency,
            'primaryCurrencyBank' => $newBankCurrency,
        ]);

        return redirect()->route('bank.index')->with('success', 'You have successfully deposited ' . $depositAmount . ' into your bank account.');
    }


    public function withdraw(Request $request)
    {
        $user = Auth::user();
        $userStats = $user->userStats;
        $withdrawAmount = (int) $request->input('withdraw');

        if ($withdrawAmount <= 0) {
            return redirect()->route('bank.index')->with('error', 'Invalid withdraw amount.');
        }

        if ($userStats->primaryCurrencyBank < $withdrawAmount) {
            return redirect()->route('bank.index')->with('error', 'You do not have enough currency in your bank account to withdraw.');
        }

        // Update the user's primary currency held and bank values
        $newHeldCurrency = $userStats->primaryCurrencyHeld + $withdrawAmount;
        $newBankCurrency = $userStats->primaryCurrencyBank - $withdrawAmount;

        $userStats->update([
            'primaryCurrencyHeld' => $newHeldCurrency,
            'primaryCurrencyBank' => $newBankCurrency,
        ]);

        return redirect()->route('bank.index')->with('success', 'You have successfully withdrawn ' . $withdrawAmount . ' from your bank account.');
    }

    public function purchase(Request $request)
    {
        $user = Auth::user();
        $userStats = DB::table('user_stats')
            ->where('user_id', $user->id)
            ->first();

        $moduleConfig = config('bank');
        $confirmed = $request->input('confirm_purchase', 0);

        if ($confirmed && $userStats && $userStats->primaryCurrencyHeld >= $moduleConfig['bankOpeningFee']) {
            $newHeldCurrency = $userStats->primaryCurrencyHeld - $moduleConfig['bankOpeningFee'];
            $userPrimaryCurrencyBank = 0;

            DB::table('user_stats')
                ->where('user_id', $user->id)
                ->update([
                    'primaryCurrencyHeld' => $newHeldCurrency,
                    'primaryCurrencyBank' => $userPrimaryCurrencyBank,
                ]);

            // Recreate the $userStats object after the update
            $userStats = DB::table('user_stats')
                ->where('user_id', $user->id)
                ->first();

            return view('bank.index', compact('user', 'userStats', 'moduleConfig'))
                ->with('success', 'You have successfully bought a bank account.');
        } else {
            return view('bank.index', compact('user', 'userStats', 'moduleConfig'))
                ->with('error', 'You need at least ' . number_format($moduleConfig['bankOpeningFee']) . ' currency to purchase a bank account.');
        }
    }
}
