<?php

use App\Models\Chef;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/download-student-app',function(){
    return redirect()->away(env('STUDENT_APP_URL'));
});
Route::get('/download-chef-app',function(){
    return redirect()->away(env('CHEF_APP_URL'));
});
Route::get('/download-delivery-app',function(){
    return redirect()->away(env('DELIVERY_APP_URL'));
});
