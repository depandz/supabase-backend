<?php

use App\Models\Client;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


if (!function_exists('generate_otp')) {
    function generate_otp($phone_number)
    {
       
        $record = DB::table('otp_verifications')        
        ->where('phone_number',$phone_number)
        ->first();
        if(!$record) {
            DB::table('otp_verifications')
                        ->insert(
                            ['phone_number' => $phone_number]
                        );
        }

       
        $record = DB::table('otp_verifications')        
        ->where('phone_number',$phone_number);

        $now = now();
        if($auth = $record->first()) {
            
            if(!is_null($auth->otp_verification_code) && !is_null($auth->otp_expire_at) && $now->isBefore($auth->otp_expire_at)) {
                return $auth->otp_verification_code;
            }

            $record->update([
                'otp_verification_code' =>  rand(12345, 99999),
                'otp_expire_at' =>  $now->addMinutes(10),

            ]);

            return $auth->otp_verification_code;
        }

    }
}
if(!function_exists('send_sms')){

    function sendSMS($receiverNumber,$message,$content)
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