<?php

use App\Http\Controllers\Auth\CommonAuthController;
use App\Http\Controllers\Auth\ChefAuthController;
use App\Http\Controllers\MealController;
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
        Route::post('/check-code-and-accessibility',[ChefAuthController::class,'checkChefCodeAndRegisterStatus']);
        Route::post('/request-register',[ChefAuthController::class,'makeRegisterRequest'])->middleware('verified.phone');
        // authenticated routes
        Route::middleware(['auth:chef','notRestricted'])->group(function(){  
            Route::delete('/logout',[ChefAuthController::class,'logout']);
            Route::group(['prefix' => 'meals'], function () {
                Route::post('/category/',[MealController::class, 'storeCategory']);
                Route::get('/categories', [MealController::class, 'indexCategories']);
                Route::get('/active-count', [MealController::class, 'getActiveMealsCount']);
                Route::get('/meal/{price}', [MealController::class, 'getPriceForStudent']);
                Route::get('/categories/{id}', [MealController::class, 'getMealOfCategory']);
                Route::post('/',[MealController::class, 'store']);
                //Route::get('/{meal}',[MealController::class, 'show']); no need to it because all data is returned to the index
                Route::put('/{meal}',[MealController::class, 'update']);
                Route::put('/{meal}/add-portion',[MealController::class, 'addMealNumber']);
                Route::put('/{meal}/subtract-portion',[MealController::class, 'subtractMealNumber']);
                Route::delete('/{meal}',[MealController::class, 'destroy']);
                //Route::put('/{meal}/edit-max-meal-num',[MealController::class, 'editMaximumMealNumber']);
                Route::put('/{meal}/edit-availability',[MealController::class, 'editAvailability']);
            });

        });
});