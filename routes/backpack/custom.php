<?php

use App\Http\Controllers\Admin\JoinRequestsController;
use App\Http\Controllers\Admin\OrdersManagegmentController;
use App\Http\Controllers\Admin\PricingController;
use App\Http\Controllers\Admin\ReportCrudController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Models\Order;
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
    Route::crud('order', 'OrderCrudController');
    Route::crud('user', 'UserCrudController');
    Route::crud('chef', 'ChefCrudController');
    Route::crud('deliveryman', 'DeliverymanCrudController');
    Route::crud('report', 'ReportCrudController');
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
    Route::get('/new-orders',[OrdersManagegmentController::class,'showNewOrders']);
    Route::post('/new-orders/{id}/approve',[OrdersManagegmentController::class,'approveOrder']);
    Route::post('/new-orders/{id}/reject',[OrdersManagegmentController::class,'rejectOrder']);
    Route::post('/user/{id}/block',[UserManagementController::class,'blockUser']);
    Route::post('/user/{id}/unblock',[UserManagementController::class,'unblockUser']);
    Route::post('/chef/{id}/block',[UserManagementController::class,'blockChef']);
    Route::post('/chef/{id}/unblock',[UserManagementController::class,'unblockChef']);
    Route::post('/deliveryman/{id}/block',[UserManagementController::class,'blockDeliveryman']);
    Route::post('/deliveryman/{id}/unblock',[UserManagementController::class,'unblockDeliveryman']);
    Route::get('/profit-values',[PricingController::class,'showProfitValues']);
    Route::post('/profit-values/edit',[PricingController::class,'editProfitValues']);
    Route::post('/report/{id}/mark-as-seen',[ReportCrudController::class,'markAsSeen']);
    
}); // this should be the absolute last line of this file