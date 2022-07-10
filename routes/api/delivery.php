<?php

use App\Events\TestEvent;
use App\Http\Controllers\Auth\CommonAuthController;
use App\Http\Controllers\Auth\DeliverymanAuthController;
use App\Http\Controllers\DeliverymanController;
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
        Route::post('/check-code-and-accessibility',[DeliverymanAuthController::class,'checkDeliverymanCodeAndRegisterStatus']);
        Route::post('/request-register',[DeliverymanAuthController::class,'makeRegisterRequest'])->middleware('verified.phone');
        // authenticated routes
        Route::middleware(['auth:deliveryman','notRestricted'])->group(function(){
            Route::get('/test',function(){
                broadcast(new TestEvent());
                return response()->json(['message'=>'event is broadcasted']);
            });
            Route::get('/current-delivery',[DeliverymanController::class,'getCurrentDeliveryInfoAndOrders']);
            Route::get('/current-delivery/orders/{order}',[DeliverymanController::class,'getOrderForDelivery']);
            Route::put('/current-delivery/orders/{order}/change-status',[DeliverymanController::class,'changeOrderStatus']);
            Route::post('/current-delivery/orders/{order}/report',[DeliverymanController::class,'reportOrder']);
            // Route::get('/current-delivery/chef-location',[DeliverymanController::class,'getChefLocation']);
            Route::put('/update-current-location',[DeliverymanController::class,'updateCurrentLocation']);
            Route::put('/change-availability-status',[DeliverymanController::class,'changeAvailabilityStatus']);
            Route::delete('/logout',[DeliverymanAuthController::class,'logout']);
            Route::get('/balance', [DeliverymanController::class, 'getBalance']);
        });
});
