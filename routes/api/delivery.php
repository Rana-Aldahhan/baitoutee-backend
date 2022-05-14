<?php

use App\Http\Controllers\Auth\CommonAuthController;
use App\Http\Controllers\Auth\DeliverymanAuthController;
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
        Route::post('/send-code',[CommonAuthController::class,'sendPhoneNumberVerificationCode']);
        Route::get('/check-code-and-accessibility',[DeliverymanAuthController::class,'checkDeliverymanCodeAndRegisterStatus']);
        Route::post('/request-register',[DeliverymanAuthController::class,'makeRegisterRequest'])->middleware('verified.phone');
        // authenticated routes
        Route::middleware(['auth:deliveryman'])->group(function(){  
            Route::delete('/logout',[DeliverymanAuthController::class,'logout']);
        });
});