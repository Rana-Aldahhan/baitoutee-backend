<?php

use App\Http\Controllers\Controller;
use App\Services\FCMService;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Broadcast::routes(['middleware' => ['auth:deliveryman']]);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/support', [Controller::class, 'getSupport']);

Route::get('/send-notification', function(){
        // $percentage=50;
        // $name='رز ببزاليا';
        // FCMService::sendPushNotification('/topics/user','عنوان عنوان' , "تم إضافة خصم جديد على وجبة ".$name."بنسبة".$percentage."%");
        // return response()->json('notification sent');
        $order=Order::find(143);
        $order->status='approved';
        $order->accepted_at=now();
        //send notification to user
        $user=$order->user;
        // dd($order->user);
        FCMService::sendPushNotification(
            $user->fcm_token,
            'تم قبول طلبك',
            ' لقد تم قبول طلبك رقم' .$order->id
        );
        // //send notification to chef
        // $chef=$order->chef;
        // FCMService::sendPushNotification(
        //     $chef->fcm_token,
        //     'طلب جديد',
        //     '! وصلك طلب جديد قم بتفقّده '
        // );
        // $order->save();
        return response()->json([]);

});


