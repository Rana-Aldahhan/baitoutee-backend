<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the delivery api. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('delivery')->group(function () { 
        //unauthenticated (guest) routes
        Route::get('/check-phone-number-code',[]);
        Route::post('/request-register',[]);
        Route::post('/login',[]);
        // authenticated routes
        Route::middleware(['auth:delivery'])->group(function(){  
            Route::delete('/logout',[]);
        });
});