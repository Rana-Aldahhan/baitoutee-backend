<?php

use App\Http\Controllers\Auth\ChefAuthController;
use App\Http\Controllers\Auth\CommonAuthController;
use App\Http\Controllers\ChefController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SubscriptionController;
use App\Models\Chef;
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
    Route::post('/send-code', [CommonAuthController::class, 'sendPhoneNumberVerificationCode']);
    Route::post('/check-code-and-accessibility', [ChefAuthController::class, 'checkChefCodeAndRegisterStatus']);
    Route::post('/request-register', [ChefAuthController::class, 'makeRegisterRequest'])->middleware('verified.phone');
    Route::delete('/logout', [ChefAuthController::class, 'logout']);
    // authenticated routes
    Route::middleware(['auth:chef'])->group(function () {
        //restricted routes
        Route::middleware([ 'notRestricted'])->group(function () {
            Route::group(['prefix' => 'meals'], function () {
                Route::post('/category', [MealController::class, 'storeCategory']);
                Route::get('/categories', [MealController::class, 'indexCategories']);
                Route::get('/active-count', [MealController::class, 'getActiveMealsCount']);
                Route::get('/meal/{price}', [MealController::class, 'getPriceForStudent']);
                Route::get('/categories/{id}', [MealController::class, 'getMealOfCategory']);
                Route::get('/', [MealController::class, 'indexForChef']);
                Route::post('/', [MealController::class, 'store']);
                Route::put('/{meal}', [MealController::class, 'update']);
                Route::put('/{meal}/add-portion', [MealController::class, 'addMealNumber']);
                Route::put('/{meal}/subtract-portion', [MealController::class, 'subtractMealNumber']);
                Route::delete('/{meal}', [MealController::class, 'destroy']);
                //Route::put('/{meal}/edit-max-meal-num',[MealController::class, 'editMaximumMealNumber']);
                Route::put('/{meal}/edit-availability', [MealController::class, 'editAvailability']);
            });
            Route::group(['prefix' => 'orders'], function () {
                Route::get('/meals', [OrderController::class, 'indexForChefOrderedMeals']);
                Route::get('/', [OrderController::class, 'indexForChefOrders']);
                Route::put('/{order}/change-status', [OrderController::class, 'changeStatusToPrepared']);
            });
            Route::group(['prefix' => 'subscriptions'], function () {
                Route::post('/', [SubscriptionController::class, 'store']);
                Route::get('/', [SubscriptionController::class, 'indexForChef']);
                Route::put('/{subscription}', [SubscriptionController::class, 'update']);
                Route::delete('/{subscription}', [SubscriptionController::class, 'destroy']);
                Route::put('/{subscription}/edit-availability', [SubscriptionController::class, 'editAvailability']);
            });
            Route::group(['prefix' => 'profile'], function () {
                Route::get('/', [ChefController::class, 'getProfile']);
                Route::get('/balance', [ChefController::class, 'getBalance']);
                Route::get('/order-history', [OrderController::class, 'getChefOrderHistory']);
                Route::put('/edit-profile-pic', [ChefController::class, 'editProfile']);
                Route::put('/edit-deliver-meal-time', [ChefController::class, 'editDeliverMealTime']);
                Route::put('/edit-max-meal', [ChefController::class, 'editMaxMealsPerDay']);
                Route::get('/notes', [OrderController::class, 'getNotes']);
            });
            Route::put('/change-availability-status',[ChefController::class,'changeAvailabilityStatus']);
        });
    });
});
