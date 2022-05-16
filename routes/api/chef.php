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
        Route::middleware(['auth:chef'])->group(function(){  
            Route::delete('/logout',[ChefAuthController::class,'logout']);
            Route::group(['prefix' => 'meals'], function () {
                Route::get('/categories', [MealController::class, 'indexCategories']);
                Route::get('/categories/{id}', [MealController::class, 'getMealOfCategory']);
                Route::post('/',[MealController::class, 'store']);
                Route::get('/{meal}',[MealController::class, 'show']);
                Route::put('/{meal}',[MealController::class, 'update']);
                Route::delete('/{meal}',[MealController::class, 'destroy']);
                Route::put('/{meal}/edit-max-meal-num',[MealController::class, 'editMaximumMealNumber']);
                Route::get('/{meal}/edit-availability',[MealController::class, 'editAvailability']); //because i dont need request like edit?
            });
        });
});