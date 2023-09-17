<?php

namespace App\Pipes;

use App\Contracts\DriverContract;
use App\DataTransferObjects\PositionDTO;
use Closure;
use Exception;

class DriversDistanceFromClientCalculator
{


    public $driver_contract;

    /**
     * @var ProvinceRepository
     */
    public function __construct(DriverContract $driver_contract)
    {
        $this->driver_contract = $driver_contract;
    }

    public function handle(array $data, Closure $next)
    {
        try {
            $drivers = $data['available_drivers'];
            $location =  new PositionDTO(lat: json_decode($data['location'])->lat,lng:json_decode($data['location'])->lng);
            
            for ($i = 0; $i < count($drivers); $i++) {
                $driver = $drivers[$i];
                $driver_location =new PositionDTO(lat: $driver->location->lat,lng:$driver->location->lat);
                $distance = $this->getDistanceFromLatLonInKm(location_lat:$location->lat,location_lng: $location->lng,driver_location_lat: $driver_location->lat,driver_location_lng: $driver_location->lng);
                $drivers[$i]->distance_from_client = $distance;
            } 
            $final_drivers = $drivers->toArray();
            usort($final_drivers, function($a, $b) {return    $b->distance_from_client - $a->distance_from_client;});
          
            $data['available_drivers'] = $final_drivers;
            return $next($data);
        }
        catch(Exception $ex){
            throw $ex;
        }
    }
    public function getDistanceFromLatLonInKm(float $location_lat,float $location_lng,float $driver_location_lat,float $driver_location_lng)
    {
        $earthRadius = 6371; // Radius of the earth in km
        $latDifference = deg2rad($driver_location_lat - $location_lat);
        $lngDifference = deg2rad($driver_location_lng - $location_lng);
        $a =     sin($latDifference / 2) * sin($latDifference / 2) +
          cos(deg2rad($location_lat)) * cos(deg2rad($driver_location_lat)) *
          sin($lngDifference / 2) * sin($lngDifference / 2);
      
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // in km
        return $distance;
    }
}