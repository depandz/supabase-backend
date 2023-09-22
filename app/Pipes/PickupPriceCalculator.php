<?php

namespace App\Pipes;

use App\Contracts\DriverContract;
use App\Enums\VehicleTypes;
use Closure;
use DateTime;
use Exception;

class PickupPriceCalculator
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
            $distance_in_km = $data['distance'];
            $is_vehicle_empty = $data['is_vehicle_empty'];
            $vehicle_type_fee = $this->getVehicleFee($data['province_fee'],$data['vehicle_type'] ?? VehicleTypes::LIGHT->value);

            $base_price = $distance_in_km * $vehicle_type_fee;
            if (isEightPM($data['date_requested'])) {
                $base_price = $distance_in_km * ($vehicle_type_fee + env('NIGHT_TARIFF'));
            }

            if (!(bool)$is_vehicle_empty) {
                $data['estimated_price'] = round($base_price + $data['province_fee']->full_percentage);
            }else 
            {
                $data['estimated_price'] = round($base_price);
            }
            return $next($data);
        }
        catch(Exception $ex){
            throw $ex;
        }
    }
    public function getVehicleFee(object $province_fee,string $vehicle_type){
        if(!$province_fee) return env('DEFAULT_KILOMETER_PRICE');
        return $province_fee->asArray()[$vehicle_type];
    }
}