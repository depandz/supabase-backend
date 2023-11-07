<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Http\Request;
use App\Contracts\DriverContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\DriverUpdateRequest;

class DriverController extends Controller
{
    public $driver_contract;

    /**
     * @var DriverContract
     */
    public function __construct(DriverContract $driver_contract)
    {
        parent::__construct();
        $this->driver_contract = $driver_contract;
    }

    public function index()
    {
        try{
            $drivers = $this->driver_contract->fetchAll();

            return $this->api_responser
                ->success()
                ->message('drivers fetched successfully')
                ->payload($drivers)
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
    * path="/api/v1/drivers/{s_id}",
    * operationId="get driver details",
    * tags={"drivers"},
    * summary="get driver details using secret id",
    * description="get driver details using secret id",
    * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
    * @OA\Response( response=200, description="driver details fetched successfully", @OA\JsonContent() ),
    * @OA\Response( response=404,description="no driver found", @OA\JsonContent()),
    * @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
    *     )
    */
    public function show($s_id)
    {
        try {
            $driver = $this->driver_contract->findBy('s_id', $s_id);

            return $this->api_responser
                ->success()
                ->message(count($driver) ? 'driver details fetched successfully' : 'no driver found')
                ->payload(count($driver) ? $driver[0] : null)
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
    * path="/api/v1/drivers/login",
    * operationId="login_driver",
    * tags={"drivers"},
    * summary="login a driver using phone number",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="application/x-www-form-urlencoded",
    *             @OA\Schema(
    *                 @OA\Property(property="phone_number",type="string",example="+213664419425"),
    *             )),
    *    ),
    *    @OA\Response( response=200, description="driver logged in successfully", @OA\JsonContent() ),
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
    
            $driver = $this->driver_contract->findBy('phone_number',$validated['phone_number']);

            if($driver)
            {
            return $this->api_responser
                ->success()
                ->message('driver logged in successfully')
                ->payload($driver[0])
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
    * path="/api/v1/drivers/{s_id}/update",
    * operationId="update_driver",
    * tags={"drivers"},
    * summary="update a driver informations",
    * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
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
    *    @OA\Response( response=200, description="driver updated successfully", @OA\JsonContent() ),
    *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
    *     )
    */
    public function update(DriverUpdateRequest $request, $s_id)
    {
        try{
            $driver = $this->driver_contract->update($s_id,$request->validated());

            return $this->api_responser
                ->success()
                ->message('driver updated successfully')
                ->payload($driver)
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
    * path="/api/v1/drivers/{s_id}/update-photo",
    * operationId="update_driver_photo",
    * tags={"drivers"},
    * summary="update a driver photo",
    * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
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

            $driver = $this->driver_contract->updatePhoto($s_id,$request->photo);

            return $this->api_responser
                ->success()
                ->message('Photo updated successfully')
                ->payload($driver)
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
