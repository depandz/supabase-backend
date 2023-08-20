<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Http\Request;
use App\Contracts\ClientContract;
use App\Contracts\DriverContract;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    public $client_contract;
    public $driver_contract;

     /**
         * @var ClientContract
         * @var DriverContract
         */
        public function __construct(ClientContract $client_contract,DriverContract $driver_contract)
        {
            parent::__construct();
            $this->client_contract = $client_contract;
            $this->driver_contract = $driver_contract;
        }
        /**
        * @OA\Post(
        * path="/api/v1/otp/send",
        * operationId="send_otp_code",
        * tags={"commun"},
        * summary="send otp to a client or driver using phone number",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="application/x-www-form-urlencoded",
        *             @OA\Schema(
        *                 @OA\Property(property="phone_number",type="string",example="+213664419425"),
        *             )),
        *    ),
        *    @OA\Response( response=200, description="The otp sended successfully", @OA\JsonContent() ),
        *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
        *     )
        */
        public function sendOtp(Request $request)
        {
            try {
                $validated = $request->validate([
                    'phone_number' => 'required|regex:/^\+213[567]\d{8}$/',
                ]);
                // $validated['phone_number'] = ltrim($validated['phone_number'], '+');
                $otp = generate_otp($request->phone_number);
                return sendSMS($request->phone_number, 'Your OTP Verification code is ', $otp);

            }
            catch(Exception $ex){
                
                return $this->api_responser
                    ->failed($ex->getCode())
                    ->message($ex->getMessage())
                    ->send();
            }
        }
    /**
     * @OA\Post(
     * path="/api/v1/otp/verify",
     * operationId="verify_sended_otp",
     * tags={"commun"},
     * summary="verify otp code if match to login",
     * description="verify otp code if match to login using the phone_number and the otp",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="phone_number",type="string",example="+213664419425"),
     *                 @OA\Property(property="otp",type="string",example="55555"),
     *                 @OA\Property(property="operation_type",type="string",enum={"register","login"}),
     *                 @OA\Property(property="auth_type",type="string",enum={"client","driver"}),
     *             ) ),
     *    ),
     *      @OA\Response( response=200,description="The verification passed successfully",@OA\JsonContent()),
     *      @OA\Response( response=422,description="Your OTP Or Phone Number is not correct",@OA\JsonContent()),
     *      @OA\Response( response=419,description="Your OTP has been expired",@OA\JsonContent()),
     *      @OA\Response(response=500,description="internal server error",@OA\JsonContent())
     *     )
     */
    public function verifyOtp(Request $request)
    {
        /* Validation */
        $request->validate([
            'phone_number' => 'required|regex:/^(\+\d{1,2}\d{10})$/',
            'otp' => 'required',
            'operation_type'=>'required|in:register,login',
            'auth_type'=>'required|in:client,driver'
        ]);

        try {
            $record = DB::table('otp_verifications')        
                        ->where('phone_number',$request['phone_number']);
            $auth = $record->first();
            if(!$auth) throw new ModelNotFoundException();
            //if otp is correct
            if($auth && $auth->otp_verification_code != $request->otp){
                return $this->api_responser->success()
                        ->code(401)
                        ->message("Your OTP is not correct, Please Verify Or Send Again")
                        ->send();
            }
            $now = now();

            if ($auth && $now->isAfter($auth->otp_expire_at)) {
                return $this->api_responser->failed()->code(419)
                    ->message('Your OTP has been expired')
                    ->send();
            }

            //validate the otp
            $record->update([
                'otp_verification_code' => null,
                'otp_expire_at' =>   now()
            ]);
            $users = null;
            if($request['operation_type'] == 'login') {
                if($request['auth_type'] == 'client') {
                    $users = $this->client_contract->findBy('phone_number', ltrim($request['phone_number'],'+'));
                } else {
                    $users = $this->driver_contract->findBy('phone_number',  ltrim($request['phone_number'],'+'));
                }
            }
            // else 
            // {
            //     if($request['auth_type'] == 'client') {
            //         $user = $this->client_contract->findBy('phone_number', $request['phone_number']);
            //     } else {
            //         $user = $this->driver_contract->findBy('phone_number', $request['phone_number']);
            //     }
            // }
            return $this->api_responser->success()
                ->message('The verification passed successfully')
                ->payload([
                   'auth' =>firstOf($users)
                ])
                ->send();
        } catch (Exception $ex) {
            return handleTwoCommunErrors($ex, "No record Found with the given phone number,Please send otp again");
        }
    }
}
