<?php

use Illuminate\Support\Facades\Route;

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
})->name('home');

Route::group(['middleware' => 'guest:api'], function () {
    Route::get('auth/redirect', [\App\AzureAd\Http\Controllers\RedirectAuthenticateController::class, 'index'])
        ->name('auth.redirect');

    Route::get('auth/login', [\App\AzureAd\Http\Controllers\AuthenticatedController::class, 'store'])
        ->name('auth.login');
});
