<?php

use App\Enums\GlobalVars;
use Illuminate\Support\Str;

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