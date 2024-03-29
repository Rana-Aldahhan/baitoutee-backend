<?php

namespace App\Traits;

use App\Models\Location;
use Illuminate\Support\Facades\Http;


trait DistanceCalculator{

   public function calculateDistanceBetween(Location $location1,Location $location2)
	{
		$response=Http::retry(3, 500)
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
        if($response->successful() && property_exists($response,'distance'))//case of successful response status code
         { 
                  $distanceInMiles=$response['distance'][1];
                  return $this->convertMileToKm($distanceInMiles) ;
         }
         else //case of api request failure
         {
           return $this->calculateDistanceBetweenTwoPoints($location1,$location2);
         }
	}

   protected function convertMileToKm($distanceInMiles){
      return $distanceInMiles* 1.6;
   }

   public function calculateDistanceBetweenTwoPoints(Location $location1,Location $location2) {
      $lat1=$location1->latitude;
      $lat2=$location2->latitude;
      $lon1=$location1->longitude;
      $lon2=$location2->longitude;
       $R = 6371; // Radius of the earth in km
       $dLat = deg2rad($lat2-$lat1);  // deg2rad below
       $dLon = deg2rad($lon2-$lon1); 
       $a = 
        sin($dLat/2) *sin($dLat/2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
        sin($dLon/2) * sin($dLon/2)
        ; 
       $c = 2 *atan2(sqrt($a),sqrt(1-$a)); 
       $d = $R * $c; // Distance in km
      return abs($d);
   }
   
   protected function deg2rad($deg) {
      return $deg * (M_PI/180);
   }

}