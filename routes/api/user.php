<?php

use App\Http\Controllers\Auth\CommonAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\ChefController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
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
        //show entities
        Route::get('/show-chef/{chef}', [ChefController::class, 'show']);
        Route::get('/show-chef/{chef}/categories', [ChefController::class, 'getChefCategories']);
        Route::get('/show-chef/{chef}/categories/{categoryId}', [ChefController::class, 'getChefMealsOfCategory']);
        Route::get('/show-chef/{chef}/subscriptions', [SubscriptionController::class, 'getChefSubscriptions']);
        Route::get('/show-meal/{meal}', [MealController::class, 'show']);
        Route::get('/show-subscription/{subscription}', [SubscriptionController::class, 'show']);
        Route::get('/show-subscription-meals/{subscription}', [SubscriptionController::class, 'showMeals']);
        Route::post('/add-meal-to-favorite/{meal}', [MealController::class, 'addToFavorite']);
        Route::delete('/delete-meal-from-favorite/{meal}', [MealController::class, 'deleteFromFavorite']);
        //order & subscribe
        Route::post('/make-order', [OrderController::class, 'makeOrder']);
        Route::post('/subscriptions/{subscription}/subscribe', [SubscriptionController::class, 'subscribe']);
        //student orders and tracking
        Route::get('/current-orders',[UserController::class,'getCurrentOrders']);
        Route::put('/orders/{order}/cancel',[UserController::class,'cancelOrder']);
        Route::get('/orders/{order}/show',[UserController::class,'showOrder']);
        Route::post('/orders/{order}/rate',[UserController::class,'rateOrder']);
        Route::post('/orders/{order}/report',[UserController::class,'reportOrder']);
        Route::get('/previous-orders',[UserController::class,'getOrdersHistory']);
        //student subscriptions
        Route::get('/current-subscriptions',[UserController::class,'getCurrentSubscriptions']);
        Route::get('/current-subscriptions/{subscription}/orders',[UserController::class,'getSubscriptionOrders']);
        //student search
        Route::group(['prefix' => 'search'], function () {
            Route::get('/meals', [MealController::class, 'searchAndSort']);
            Route::get('/subscriptions', [SubscriptionController::class, 'searchAndFilter']);
            Route::get('/chefs', [ChefController::class, 'search']);
        });
    });
});
