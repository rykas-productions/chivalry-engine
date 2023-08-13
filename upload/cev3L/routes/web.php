<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/Explore', function () {
        return view('Explore');
    })->name('Explore');
    Route::get('/Documentation', function () {
        return view('Documentation');
    })->name('Documentation');

    Route::get('/ItemMarket', function () {
        return view('ItemMarket');
    })->name('ItemMarket');

    Route::get('/Market', function () {
        return view('Market');
    })->name('Market');

    Route::get('/Bank', [BankController::class, 'index'])->name('bank.index');
    Route::post('/Bank/deposit', [BankController::class, 'deposit'])->name('bank.Deposit');
    Route::post('/Bank/withdraw', [BankController::class, 'withdraw'])->name('bank.Withdraw');
    Route::post('/bank/purchase', [BankController::class, 'purchase'])->name('bank.purchase');

    Route::get('/Estate', function () {
        return view('Estate');
    })->name('Estate');

    Route::get('/Travel', function () {
        return view('Travel');
    })->name('Travel');


    Route::get('/Gym', function () {
        return view('Gym');
    })->name('Gym');

    Route::get('/Crimes', function () {
        return view('Crimes');
    })->name('Crimes');

    Route::get('/Academy', function () {
        return view('Academy');
    })->name('Academy');

    Route::get('/Work', function () {
        return view('Work');
    })->name('Work');


    Route::get('/Users', function () {
        return view('Users');
    })->name('Users');


    Route::get('/UserManagement', [UserController::class, 'userManagement'])->name('user.management');

    Route::post('/UserManagement/updateCurrency/{id}', [UserController::class, 'updateCurrency'])->name('user.updateCurrency');

    Route::get('/GameStaff', function () {
        return view('GameStaff');
    })->name('GameStaff');

    Route::get('/FederalDungeon', function () {
        return view('FederalDungeon');
    })->name('FederalDungeon');

    Route::get('/GameStats', function () {
        return view('GameStats');
    })->name('GameStats');

    Route::get('/PlayerReport', function () {
        return view('PlayerReport');
    })->name('PlayerReport');

    Route::get('/Announcements', function () {
        return view('Announcements');
    })->name('Announcements');

    Route::get('/ItemAppendix', function () {
        return view('ItemAppendix');
    })->name('ItemAppendix');


    Route::get('/Slots', function () {
        return view('Slots');
    })->name('Slots');

    Route::get('/Roulette', function () {
        return view('Roulette');
    })->name('Roulette');


    Route::get('/Dungeon', function () {
        return view('Dungeon');
    })->name('Dungeon');

    Route::get('/Infirmary', function () {
        return view('Infirmary');
    })->name('Infirmary');


    Route::get('/Forums', function () {
        return view('Forums');
    })->name('Forums');

    Route::get('/Newspaper', function () {
        return view('Newspaper');
    })->name('Newspaper');

    Route::get('/HallOfFame', function () {
        return view('HallOfFame');
    })->name('HallOfFame');

    Route::get('/PollingCenter', function () {
        return view('PollingCenter');
    })->name('PollingCenter');

    Route::get('/GameTutorial', function () {
        return view('GameTutorial');
    })->name('GameTutorial');
});
