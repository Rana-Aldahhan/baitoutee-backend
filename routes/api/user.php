<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the user api. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('user')->group(function () { 
        //unauthenticated (guest) routes
        Route::get('/check-phone-number-code',[]);
        Route::get('/locations',[]);
        Route::post('/request-register',[]);
        Route::post('/login',[]);
        // authenticated routes
        Route::middleware(['auth:user'])->group(function(){  
            Route::delete('/logout',[]);//TODO remove checkNotRestricted middleware from this route
        });
});