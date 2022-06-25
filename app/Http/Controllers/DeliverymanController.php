<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliverymanController extends Controller
{
    public function updateCurrentLocation(Request $request)
    {
        $deliveryman=auth('deliveryman')->user();
        $deliveryman->update([
            'current_longitude'=>$request->current_longitude,
            'current_latitude'=>$request->current_latitude,
        ]);
        return $this->successResponse([],200);
    }
}
