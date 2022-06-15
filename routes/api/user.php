<?php

use App\Http\Controllers\Auth\CommonAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\ChefController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SubscriptionController;
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
    Route::post('/send-code', [CommonAuthController::class, 'sendPhoneNumberVerificationCode']);
    Route::post('/check-code-and-accessibility', [UserAuthController::class, 'checkUserCodeAndRegisterStatus']);
    Route::get('/locations', [LocationController::class, 'getCampusLocations']);
    Route::post('/request-register', [UserAuthController::class, 'makeRegisterRequest'])->middleware('verified.phone');
    // authenticated routes
    Route::middleware(['auth:user', 'notRestricted'])->group(function () {
        Route::delete('/logout', [UserAuthController::class, 'logout']); //TODO remove checkNotRestricted middleware from this route
        //student chef home page
        Route::get('/filter-top-rated-chefs', [ChefController::class, 'filterTopRated']);
        Route::get('/filter-nearest-chefs', [ChefController::class, 'filterNearest']);
        Route::get('/filter-top-ordered-chefs', [ChefController::class, 'filterTopOrders']);
        Route::get('/filter-newest-chefs', [ChefController::class, 'filterNewestChefs']);
        //student meal & subscription home page
        Route::get('/get-top-rated-meals', [MealController::class, 'getTopTenRated']);
        Route::get('/get-meal-offers', [MealController::class, 'getMealTopTenOffers']);
        Route::get('/get-all-meal-offers', [MealController::class, 'getAllOffers']);
        Route::get('/get-recent-meals', [MealController::class, 'getTopTenRecent']);
        Route::get('/get-top-ordered-meals', [MealController::class, 'getTopTenOrdered']);
        Route::get('/get-top-subscriptions', [SubscriptionController::class, 'getTopTenAvaialble']);
        Route::get('/get-all-subscriptions', [SubscriptionController::class, 'getAllAvaialble']);
        Route::get('/show_meal/{meal}', [MealController::class, 'show']);
        Route::get('/add_meal_to_favorite/{meal}', [MealController::class, 'addToFavorite']);
        Route::get('/delete_meal_from_favorite/{meal}', [MealController::class, 'deleteFromFavorite']);
        //ordering
        //get chef delivery times?or it will be there when showing a meal
        //get delivery fee
        Route::get('/get-delivery-fee',[OrderController::class,'getCurrentDeliveryFee']);
        //make the order
        Route::post('/make-order',[OrderController::class,'makeOrder']);
    });
});