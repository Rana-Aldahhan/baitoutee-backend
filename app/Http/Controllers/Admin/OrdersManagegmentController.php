<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Events\OrderIsPrepared;
use Illuminate\Http\Request;
use App\Jobs\AssignOrderToDelivery;
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
        //send notification to user
        $user=$order->user;
        FCMService::sendPushNotification(
            $user->fcm_token,
            'تم قبول طلبك',
            ' لقد تم قبول طلبك رقم' .$order->id
        );
        //send notification to chef
        $chef=$order->chef;
        FCMService::sendPushNotification(
            $chef->fcm_token,
            'طلب جديد',
            '! وصلك طلب جديد قم بتفقّده '
        );
        $order->save();
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
            'عذراً لقد تم رفض طلبك رقم'.$order->id
        );
        return response()->json([]);
        
    }

    public function reassignOrder($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('manage-orders');
        $order=Order::findOrFail($id);
        //braodcast event to deliverymen
        broadcast(new OrderIsPrepared($order))->toOthers();

        \Alert::add('info', trans('adminPanel.messages.order_reassigned'))->flash();;
        return redirect('/admin/order');
    }
    public function cancelOrder($id)
    {
       \Auth::shouldUse('backpack');
       Gate::authorize('manage-orders');
       $order=Order::findOrFail($id);
       $order->status='canceled';
       $order->save();

       FCMService::sendPushNotification(
        $order->user->fcm_token,
        'تم إلغاء طلبك',
        'تم إلغاء طلبك ذو الرقم '.$id
        );
        
        \Alert::add('info', trans('adminPanel.messages.order_canceled'))->flash();;
        return redirect('/admin/order');
    }

}
