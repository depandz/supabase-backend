<?php

use App\Models\Client;
use App\Models\Driver;

use Illuminate\Support\Facades\Http;


if (!function_exists('generate_otp')) {
    function generate_otp($phone_number,$model)
    {
       
        if($model == 'Client'){
            $model_record = Client::whereDeletedAt(null)->firstOrCreate(['phone_number'=>$phone_number],array(['phone_number'=>$phone_number]));
            //TODO: throw error if account is not yet accepted
        }else{
            //TODO: check if account is deleted
            $model_record = Driver::whereDeletedAt(null)->firstOrCreate(['phone_number'=>$phone_number],array(['phone_number'=>$phone_number]));
        }
  
        $now = now();
        if($model_record && isset($model_record->otp_verification_code) && $model_record->otp_expire_at && $now->isBefore($model_record->otp_expire_at)){
            return $model_record->otp_verification_code;
        }
        
        $model_record->otp_verification_code =  rand(12345, 99999);
        $model_record->otp_expire_at =  $now->addMinutes(10);
        $model_record->save();
        
        return $model_record->otp_verification_code;

    }
}
if(!function_exists('send_sms')){

    function sendSMS($receiverNumber,$message,$content,$model)
    {
        $message = $message.' '.$content;
    
        try {
  
            
            return response()->json([
                "success"=>true,
                'message'=>$message
                
            ],200);
    
        } catch (\Exception $e) {
            return response()->json([
                "success"=>false,
                'message'=>$e->getMessage(),
            ],500);
        }
    }
}
if(!function_exists('sanctum_logout'))
{
    function sanctum_logout():void
    {
        request()->user()->tokens()->delete();
    }
}