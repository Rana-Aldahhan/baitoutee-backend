<?php

use App\Http\Controllers\Admin\JoinRequestsController;
use App\Http\Controllers\Admin\PricingController;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('admin', 'AdminCrudController');
    Route::crud('user-join-request', 'UserJoinRequestCrudController');
    Route::crud('chef-join-request', 'ChefJoinRequestCrudController');
    Route::crud('deliveryman-join-request', 'DeliverymanJoinRequestCrudController');
    Route::crud('meal', 'MealCrudController');
    Route::crud('price-change-request', 'PriceChangeRequestCrudController');
    Route::get('/user-join-request/{id}/approve',[JoinRequestsController::class,'approveUser']);
    Route::get('/chef-join-request/{id}/approve',[JoinRequestsController::class,'approveChef']);
    Route::get('/deliveryman-join-request/{id}/approve',[JoinRequestsController::class,'approveDeliveryman']);
    Route::get('/user-join-request/{id}/reject',[JoinRequestsController::class,'rejectUser']);
    Route::get('/chef-join-request/{id}/reject',[JoinRequestsController::class,'rejectChef']);
    Route::get('/deliveryman-join-request/{id}/reject',[JoinRequestsController::class,'rejectDeliveryman']);
    Route::get('/meal/{id}/approve',[PricingController::class,'approveMeal']);
    Route::get('/meal/{id}/reject',[PricingController::class,'rejectMeal']);
    Route::get('/price-change-request/{id}/approve',[PricingController::class,'approvePriceChangeRequest']);
    Route::get('/price-change-request/{id}/reject',[PricingController::class,'rejectPriceChangeRequest']);
}); // this should be the absolute last line of this file