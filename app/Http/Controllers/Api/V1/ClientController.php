<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Http\Request;
use App\Contracts\ClientContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientUpdateRequest;

class ClientController extends Controller
{
        public $api_responser;

        public $client_contract;

        /**
         * @var ClientContract
         */
        public function __construct(ClientContract $client_contract)
        {
            parent::__construct();
            $this->client_contract = $client_contract;
        }

        public function index()
        {
            try{
                $clients = $this->client_contract->fetchAll();

                return $this->api_responser
                    ->success()
                    ->message('clients fetched successfully')
                    ->payload($clients)
                    ->send();
            } 
            catch(Exception $ex){
                
                return $this->api_responser
                    ->failed($ex->getCode())
                    ->message($ex->getMessage())
                    ->send();
            }

        }
        /**
        * @OA\Get(
        * path="/api/v1/clients/{s_id}",
        * operationId="get client details",
        * tags={"clients"},
        * summary="get client details using secret id",
        * description="get client details using secret id",
        * @OA\Parameter(  name="s_id", in="path", description="Client secret id ", required=true),
        * @OA\Response( response=200, description="Client details fetched successfully", @OA\JsonContent() ),
        * @OA\Response( response=404,description="no client found", @OA\JsonContent()),
        * @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
        *     )
        */
        public function show($s_id)
        {
            try {
                $client = $this->client_contract->findBy('s_id', $s_id);

                return $this->api_responser
                    ->success()
                    ->message(count($client) ? 'Client details fetched successfully' : 'no client found')
                    ->payload(count($client) ? $client[0] : null)
                    ->send();
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
        * path="/api/v1/clients/register",
        * operationId="register_client",
        * tags={"clients"},
        * summary="register new client ",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="application/x-www-form-urlencoded",
        *             @OA\Schema(
        *                 @OA\Property( property="full_name",type="string",example="full Name"),
        *                 @OA\Property(property="phone_number",type="string",example="+213664419425"),
        *             )),
        *    ),
        *    @OA\Response( response=200, description="Client registered successfully", @OA\JsonContent() ),
        *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
        *     )
        */
        public function register(Request $request)
        {
            try {
                $validated = $request->validate([
                    'phone_number' => 'required|regex:/^\+213[567]\d{8}$/',
                    'full_name' => 'required|string|max:140'
                ]);

                $client = $this->client_contract->insert($validated);
                //TODO: send notification to admin
                return $this->api_responser
                    ->success()
                    ->message('Client registered successfully')
                    ->payload($client)
                    ->send();
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
        * path="/api/v1/clients/login",
        * operationId="login_client",
        * tags={"clients"},
        * summary="login a client using phone number",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="application/x-www-form-urlencoded",
        *             @OA\Schema(
        *                 @OA\Property(property="phone_number",type="string",example="+213664419425"),
        *             )),
        *    ),
        *    @OA\Response( response=200, description="Client logged in successfully", @OA\JsonContent() ),
        *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
        *     )
        */
        public function login(Request $request)
        {
            try {
                $validated = $request->validate([
                    'phone_number' => 'required|regex:/^\+213[567]\d{8}$/',
                ]);
                $validated['phone_number'] = ltrim($validated['phone_number'], '+');
        
                $client = $this->client_contract->findBy('phone_number',$validated['phone_number']);

                if($client)
                {
                return $this->api_responser
                    ->success()
                    ->message('Client logged in successfully')
                    ->payload($client[0])
                    ->send();
                }

            }
            catch(Exception $ex){
                
                return $this->api_responser
                    ->failed($ex->getCode())
                    ->message($ex->getMessage())
                    ->send();
            }
        }
        /**
        * @OA\Put(
        * path="/api/v1/clients/{s_id}/update",
        * operationId="update_client",
        * tags={"clients"},
        * summary="update a client informations",
        * @OA\Parameter(  name="s_id", in="path", description="Client secret id ", required=true),
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="application/x-www-form-urlencoded",
        *             @OA\Schema(
        *                 @OA\Property( property="full_name",type="string",nullable=true),
        *                 @OA\Property(property="phone_number",type="string",nullable=true),
        *                 @OA\Property(property="location",type="object",example={"lang":"45558","lat":"4587.00"}),
        *                 @OA\Property(property="email",type="string",nullable=true),
        *                 @OA\Property(property="messaging_token",type="string",nullable=true),
        *             )),
        *    ),
        *    @OA\Response( response=200, description="Client updated successfully", @OA\JsonContent() ),
        *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
        *     )
        */
        public function update(ClientUpdateRequest $request, $s_id)
        {
            try{
                $client = $this->client_contract->update($s_id,$request->validated());

                return $this->api_responser
                    ->success()
                    ->message('client updated successfully')
                    ->payload($client)
                    ->send();
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
        * path="/api/v1/clients/{s_id}/update-photo",
        * operationId="update_client_photo",
        * tags={"clients"},
        * summary="update a client photo",
        * @OA\Parameter(  name="s_id", in="path", description="Client secret id ", required=true),
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="application/x-www-form-urlencoded",
        *             @OA\Schema(
        *                 @OA\Property( property="photo",type="file"),
        *             )),
        *    ),
        *    @OA\Response( response=200, description="Photo updated successfully", @OA\JsonContent() ),
        *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
        *     )
        */
        public function updatePhoto(Request $request, $s_id)
        {
            try
            {
                $this->validate($request,[
                    'photo' => 'sometimes|nullable|string|image|mimes:jpg,jpeg,webp,bmp,png,gif,svg',
                ]);

                $client = $this->client_contract->updatePhoto($s_id,$request->photo);

                return $this->api_responser
                    ->success()
                    ->message('Photo updated successfully')
                    ->payload($client)
                    ->send();
            }
            catch(Exception $ex){
                
                return $this->api_responser
                    ->failed($ex->getCode())
                    ->message($ex->getMessage())
                    ->send();
            }
     
        }
}
