<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * @return App/Location
     */
    public function getCampusLocations()
    {
        return $this->successResponse(Location::take(3)->get());
    }
}
