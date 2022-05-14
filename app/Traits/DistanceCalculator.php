<?php

namespace App\Traits;

use App\Models\Location;
use Illuminate\Support\Facades\Http;


trait DistanceCalculator{

    protected function calculateDistanceBetween(Location $location1,Location $location2)
	{
		$response=Http::retry(3, 500)->withOptions([
            'proxy' => config('app.proxy'),
        ])
        ->withBody(
           ' {"locations": [
    
                {
             "latLng": {
               "lat":'.$location1->latitude.',
               "lng": '.$location1->longitude.'
             }
           },
                 {
             "latLng": {
                "lat": '.$location2->latitude.',
                "lng": '.$location2->longitude.'
             }
           }
        ]}' ,'text/plain'
        )->post('http://open.mapquestapi.com/directions/v2/routematrix?key=le8apjRfdPnenbbE1Y8rypVVYlxm0RGn');
        $distanceInMiles=$response['distance'][1];
        return $this->convertMileToKm($distanceInMiles) ;
	}
   protected function convertMileToKm($distanceInMiles){
      return $distanceInMiles* 1.6;
     }

}