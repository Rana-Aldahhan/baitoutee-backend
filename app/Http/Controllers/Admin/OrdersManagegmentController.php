<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class OrdersManagegmentController extends Controller
{
    public function showNewOrders()
    {
        $orders=Order::where('status','pending')->paginate(10);
        return view('admin.orders_admin.new_orders',['orders'=>$orders]);
    }
    
    public function approveOrder($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('manage-orders');
        $order=Order::find($id);
        $order->status='approved';
        $order->save();
        //TODO send notification to chef & student
        return response()->json([]);
        
    }
    public function rejectOrder($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('manage-orders');
        $order=Order::find($id);
        $order->status='not approved';
        $order->save();
        //TODO send notification to student
        return response()->json([]);
        
    }

}
