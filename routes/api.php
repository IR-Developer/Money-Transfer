<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('App\Http\Controllers')->group(function (){

    // Guarded Routes
    Route::middleware('auth:sanctum')->group(function (){
        Route::post('/transfer', 'transferController@transfer')->name('api.transfer');
        Route::post('transfer/history', 'transferController@history')->name('api.transfer.history');
    });

    // UnGuarded Routes
    Route::post('/register', 'RegistrationController@register')->name('api.register');
    Route::post('/login', 'LoginController@login')->name('api.login');
});

