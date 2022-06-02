<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function getTopTenAvaialble()
    {
        $subscriptions=Subscription::where('is_available',true)->take(10)->get();
        return $this->successResponse($subscriptions);
    }
}
