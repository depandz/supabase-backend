<?php

use App\Enums\GlobalVars;
use Illuminate\Support\Str;
use App\Helpers\ApiResponser;
use App\Contracts\ClientContract;
use App\Services\SupaBase\Client;
use Illuminate\Support\Collection;

if(!function_exists('generate_sid')){
    function generate_sid(string $type){

        switch ($type) {
            case 'client':
                $prefix =  GlobalVars::$CLIENT_SID_IDENTIFICATION;
                break;
            case 'driver':
                $prefix =  GlobalVars::$DRIVER_SID_IDENTIFICATION;
                break;
            default:
                $prefix =  GlobalVars::$PICKUP_SID_IDENTIFICATION;
                break;
            
        }
        return $prefix.'_'.explode('-',Str::uuid())[0];

    }
}
if(!function_exists('firstOf')){
    function firstOf(Collection $items){

        return $items && count($items) ? $items[0] : null;

    }
}
if(!function_exists('isEightPM')){
    function isEightPM(string $date){
        $date_requested = strtotime($date);
    
        // Define the desired time (8:00 PM) as a timestamp
        $desiredTime = strtotime('20:00:00', $date_requested);

        return $date_requested >= $desiredTime;

    }
}
/**
 * check if a client exists by id 
 */
if(!function_exists('checkClient')){
    function checkClientExists($client_id){
        $client = (new Client())->findBy('id', $client_id);
        if(!firstOf($client)) {
              return  (new ApiResponser())
                        ->failed()
                        ->message('No Client exists with the given id')
                        ->send();
        } 
        return true;
    }
  
}