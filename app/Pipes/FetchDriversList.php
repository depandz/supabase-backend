<?php

namespace App\Pipes;

use App\Contracts\DriverContract;
use Closure;
use Exception;

class FetchDriversList
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
            $drivers = $this->driver_contract->findByProvince($data['current_province_id']);
            $data['available_drivers'] = $drivers;
            return $next($data);
        }
        catch(Exception $ex){
            handleTwoCommunErrors($ex);
        }
    }
}