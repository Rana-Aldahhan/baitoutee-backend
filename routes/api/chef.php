<?php

use App\Http\Controllers\Auth\CommonAuthController;
use App\Http\Controllers\Auth\ChefAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the chef api. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('chef')->group(function () { 
        //unauthenticated (guest) routes
        Route::post('/send-code',[CommonAuthController::class,'sendPhoneNumberVerificationCode']);
        Route::get('/check-code-and-accessibility',[ChefAuthController::class,'checkChefCodeAndRegisterStatus']);
        Route::post('/request-register',[ChefAuthController::class,'makeRegisterRequest'])->middleware('verified.phone');
        // authenticated routes
        Route::middleware(['auth:chef'])->group(function(){  
            Route::delete('/logout',[ChefAuthController::class,'logout']);
        });
});