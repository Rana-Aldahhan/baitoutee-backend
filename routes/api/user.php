<?php

use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\LocationController;
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
        Route::post('/send-code',[UserAuthController::class,'sendPhoneNumberVerificationCode']);
        Route::get('/check-phone-number-code',[UserAuthController::class,'checkUserCodeAndRegisterStatus']);
        Route::get('/locations',[LocationController::class,'getCampusLocations']);
        Route::post('/request-register',[UserAuthController::class,'makeRegisterRequest'])->middleware('verified.phone');
        Route::post('/login',[UserAuthController::class,'login'])->middleware('verified.phone');
        // authenticated routes
        Route::middleware(['auth:user'])->group(function(){  
            Route::delete('/logout',[UserAuthController::class,'logout']);//TODO remove checkNotRestricted middleware from this route
        });
});