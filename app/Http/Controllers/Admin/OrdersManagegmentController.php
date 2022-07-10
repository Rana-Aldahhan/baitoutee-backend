<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Services\FCMService;


class OrdersManagegmentController extends Controller
{
    public function showNewOrders()
    {
        $orders=Order::where('status','pending')->orderByDesc('created_at')->paginate(10);
        return view('admin.orders_admin.new_orders',['orders'=>$orders]);
    }
    
    public function approveOrder($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('manage-orders');
        $order=Order::find($id);
        $order->status='approved';
        $order->accepted_at=now();
        $order->save();
        //send notification to user
        $user=$order->user;
        FCMService::sendPushNotification(
            $user->fcm_token,
            'تم قبول طلبك',
             $order->id.'لقد تم قبول طلبك رقم'
        );
        //send notification to chef
        $chef=$order->chef;
        FCMService::sendPushNotification(
            $chef->fcm_token,
            'طلب جديد',
            '! وصلك طلب جديد قم بتفقّده '
        );
        return response()->json([]);
        
    }
    public function rejectOrder($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('manage-orders');
        $order=Order::find($id);
        $order->status='notApproved';
        $order->save();
        //send notification to user
        $user=$order->user;
        FCMService::sendPushNotification(
            $user->fcm_token,
            'تم رفض طلبك',
            $order->id.'عذراً لقد تم رفض طلبك رقم'
        );
        return response()->json([]);
        
    }

}
