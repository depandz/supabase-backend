<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Contracts\DriverContract;
use App\Enums\PickupRequestStatus;
use App\Http\Controllers\Controller;
use App\Contracts\PickUpRequestContract;
use App\Events\NoDriverAvailable;
use App\Events\PickupRequestApproved;
use App\Events\StartPickupRequestCalling;
use App\Http\Requests\DriverUpdateRequest;

class DriverController extends Controller
{
    public $driver_contract;
    public $pickup_request_contract;
    /**
     * @var DriverContract
     */
    public function __construct(DriverContract $driver_contract, PickUpRequestContract $pickup_request_contract)
    {
        parent::__construct();
        $this->driver_contract = $driver_contract;
        $this->pickup_request_contract = $pickup_request_contract;
    }

    public function index()
    {
        try {
            $drivers = $this->driver_contract->fetchAll();

            return $this->api_responser
                ->success()
                ->message('drivers fetched successfully')
                ->payload($drivers)
                ->send();
        } catch (Exception $ex) {

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
        } catch (Exception $ex) {

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

            $driver = $this->driver_contract->findBy('phone_number', $validated['phone_number']);

            if ($driver) {
                return $this->api_responser
                    ->success()
                    ->message('driver logged in successfully')
                    ->payload($driver[0])
                    ->send();
            }
        } catch (Exception $ex) {

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
        try {
            $driver = $this->driver_contract->update($s_id, $request->validated());

            return $this->api_responser
                ->success()
                ->message('driver updated successfully')
                ->payload($driver)
                ->send();
        } catch (Exception $ex) {

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
        try {
            $this->validate($request, [
                'photo' => 'sometimes|nullable|string|image|mimes:jpg,jpeg,webp,bmp,png,gif,svg',
            ]);

            $driver = $this->driver_contract->updatePhoto($s_id, $request->photo);

            return $this->api_responser
                ->success()
                ->message('Photo updated successfully')
                ->payload($driver)
                ->send();
        } catch (Exception $ex) {

            return $this->api_responser
                ->failed($ex->getCode())
                ->message($ex->getMessage())
                ->send();
        }
    }
    /**
     * @OA\Post(
     * path="/api/v1/drivers/{s_id}/pickup-requests/{pickup_sid}/{action}",
     * operationId="accept_or_decline_pickup_request",
     * tags={"drivers"},
     * summary="accept or decline pickup request",
     * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
     * @OA\Parameter(  name="pickup_sid", in="path", description="pickup secret id ", required=true),
     * @OA\Parameter(  name="action", in="path",description="accept / decline ", required=true),
     *    @OA\Response( response=200, description="Pickup request declined / approved successfully", @OA\JsonContent() ),
     *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *    @OA\Response( response=404, description="No pickup request exists with the given s_id", @OA\JsonContent() ),
     *    @OA\Response( response=403, description="You are not authorized to do deal with this pickup request", @OA\JsonContent() ),
     *    @OA\Response( response=202, description="Pickup request has been approved by another driver", @OA\JsonContent() ),
     *    @OA\Response( response=204, description="Pickup request has been canceled by client", @OA\JsonContent() ),
     *     )
     */
    public function AcceptDeclinePickupRequest($s_id, $pickup_sid, $action)
    {
        $pickup_request = firstOf($this->pickup_request_contract->findBy('s_id', $pickup_sid));
        if (!$pickup_request) {
            return  $this->api_responser
                ->failed()
                ->code(404)
                ->message('No Pickup request exists with the given s_id')
                ->send();
        }
        //if is canceled by client
        if ($pickup_request->status == PickupRequestStatus::CANCELED->value) {
            return  $this->api_responser
                ->failed()
                ->code(204)
                ->message('Pickup request has been canceled by client')
                ->send();
        }
        //if is accepted by a driver
        if ($pickup_request->status == PickupRequestStatus::APPROVED->value) {
            return  $this->api_responser
                ->failed()
                ->code(202)
                ->message('Pickup request has been approved by another driver')
                ->send();
        }

        $drivers = json_decode($pickup_request->drivers, true);
        //no drivers
        if (!count($drivers)) {
            return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('You are not authorized to do deal with this pickup request')
                ->send();
        }
        if ($action == "decline") {
            $exists = false;
            foreach ($drivers as $key => $driver) {
                
                if ($driver['s_id'] == $s_id) {
                    $exists = true;
                    unset($drivers[$key]);
                    break;
                }
                
            }
            if(!$exists) {
             
                return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('You are not authorized to do deal with this pickup request')
                ->send();
            }

            $drivers = array_values($drivers);

            $pickup_request = $this->pickup_request_contract->update(
                $pickup_sid,
                ['drivers' => json_encode($drivers)]
            );
            //if no driver still available
            if (!count($drivers)) {
                event(new NoDriverAvailable($pickup_request));
                return  $this->api_responser
                    ->failed()
                    ->code(200)
                    ->message('Pickup request declined successfully')
                    ->send();
            }
            event(new StartPickupRequestCalling($pickup_request, $drivers[0]));

            return $this->api_responser
                ->success()
                ->payload([
                    'pickup_request' =>
                    [
                        's_id' => $pickup_request->s_id,
                        'location' => $pickup_request->location,
                        'destination' => $pickup_request->destination,
                        'estimated_price' => $pickup_request->estimated_price,
                        'estimated_duration' => $pickup_request->estimated_duration,
                        'driver' =>
                        [
                            's_id' => $drivers[0]['s_id'],
                            'full_name' => $drivers[0]['full_name'],
                            'phone_number' => $drivers[0]['phone_number'],
                            'location' => $drivers[0]['location'],
                            'photo' => url('storage/drivers/photos/' . $drivers[0]['photo']),
                        ]
                    ]
                ])
                ->message('Pickup request declined successfully')
                ->send();
        } 
        else {
            $exists = null;
            foreach ($drivers as $key => $driver) {
                
                if ($driver['s_id'] == $s_id) {
                    $exists = $driver;
                    break;
                }
                
            }
    
            if(!$exists) $driver= firstOf($this->driver_contract->findBy('s_id',$s_id));
            if(!$driver){
                return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('You are not authorized to do deal with this pickup request')
                ->send();
            }
          
            $pickup_request = $this->pickup_request_contract->approve($pickup_sid,$driver['id']);  

            event(new PickupRequestApproved($pickup_request,$driver));

            return  $this->api_responser
                ->failed()
                ->code(200)
                ->message('Pickup request approved successfully')
                ->send();
        }
    }
}
