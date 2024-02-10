<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Contracts\DriverContract;
use App\Events\NoDriverAvailable;
use App\Enums\PickupRequestStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Date;
use App\Events\PickupRequestApproved;
use App\Contracts\PickupRequestContract;
use App\Events\PickupRequestCancelled;
use App\Events\SendDriverLocation;
use App\Events\StartPickupRequestCalling;
use App\Http\Requests\DriverUpdateRequest;

class DriverController extends Controller
{
    public $driver_contract;
    public $pickup_request_contract;
    /**
     * @var DriverContract
     */
    public function __construct(DriverContract $driver_contract, PickupRequestContract $pickup_request_contract)
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
     *                 @OA\Property(property="location",type="object",example={"lng":"45558","lat":"4587.00"}),
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
                'photo' => 'sometimes|nullable|image|mimes:jpg,jpeg,webp,bmp,png,gif,svg',
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
    public function AcceptDeclinePickupRequest($s_id, $pickup_sid)
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

        if (request()->segment(7) == "decline") {
            $exists = false;
            foreach ($drivers as $key => $driver) {

                if ($driver['s_id'] == $s_id) {
                    $exists = true;
                    unset($drivers[$key]);
                    break;
                }
            }
            if (!$exists) {

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
                    ->success()
                    ->code(200)
                    ->message('No driver still available')
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
                            'full_name' => array_key_exists('full_name', $drivers[0]) ? $drivers[0]['full_name'] :  $drivers[0]['first_name'] . ' ' . $drivers[0]['last_name'],
                            'phone_number' => $drivers[0]['phone_number'],
                            'location' => $drivers[0]['location'],
                            'photo' => url('storage/drivers/photos/' . $drivers[0]['photo']),
                        ]
                    ]
                ])
                ->message('Pickup request declined successfully')
                ->send();
        } else {

            $exists = null;
            foreach ($drivers as $key => $driver) {

                if ($driver['s_id'] == $s_id) {
                    $exists = $driver;
                    break;
                }
            }

            if (!$exists) $driver = firstOf($this->driver_contract->findBy('s_id', $s_id));
            if (!$driver) {
                return  $this->api_responser
                    ->failed()
                    ->code(403)
                    ->message('You are not authorized to do deal with this pickup request')
                    ->send();
            }

            $pickup_request = $this->pickup_request_contract->approve($pickup_sid, $driver['id']);

            event(new PickupRequestApproved($pickup_request, $driver));

            return  $this->api_responser
                ->success()
                ->code(200)
                ->message('Pickup request approved successfully')
                ->send();
        }
    }
    /**
     * @OA\Put(
     * path="/api/v1/drivers/{s_id}/switch-online-status",
     * operationId="switch-online-status for a driver",
     * tags={"drivers"},
     * summary="switch-online-status for a driver",
     * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="status",type="boolean",enum={0,1}),
     *             )),
     *    ),
     *    @OA\Response( response=200, description="online status switched successfully", @OA\JsonContent() ),
     *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *     )
     */
    public function switchOnlineStatus(Request $request, $s_id)
    {
        try {
            $this->validate($request, [
                'status' => 'required|boolean|in:0,1'
            ]);
            $driver = $this->driver_contract->switchOnlineStatus($s_id, $request->status);

            return $this->api_responser
                ->success()
                ->message('online status switched successfully')
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
     * @OA\Get(
     * path="/api/v1/drivers/{s_id}/pickups-history",
     * operationId="get driver pickups history",
     * tags={"drivers"},
     * summary="get driver  pickups history using secret id",
     * description="get driver  pickup history using secret id",
     * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
     * @OA\Response( response=200, description="driver  pickups history fetched successfully", @OA\JsonContent() ),
     * @OA\Response( response=404,description="no driver found", @OA\JsonContent()),
     * @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *     )
     */
    public function pickupsHistory($s_id)
    {
        try {
            $driver = firstOf($this->driver_contract->findBy('s_id', $s_id));
            if ($driver) {
                $pickups = $this->pickup_request_contract->history(id: $driver->id, type: "driver");

                return $this->api_responser
                    ->success()
                    ->message('Driver  pickups history fetched successfully')
                    ->payload($pickups)
                    ->send();
            }
            return $this->api_responser
                ->failed()
                ->code(404)
                ->message('no driver found')
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
     * path="/api/v1/drivers/{s_id}/pickup-requests/{pickup_sid}/confirm-reached-to-client",
     * operationId="confirm reached to client location",
     * tags={"drivers"},
     * summary="confirm that driver reached client location",
     * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
     * @OA\Parameter(  name="pickup_sid", in="path", description="pickup secret id ", required=true),
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="secret_code",type="string"),
     *                 @OA\Property(property="date_confirmed",type="date",example="21-12-2023 15:22"),
     *             )),
     *    ),
     *    @OA\Response( response=200, description="Client location Reached successfully", @OA\JsonContent() ),
     *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *    @OA\Response( response=404, description="No pickup request exists with the given s_id", @OA\JsonContent() ),
     *    @OA\Response( response=403, description="You are not authorized to do deal with this pickup request", @OA\JsonContent() ),
     *    @OA\Response( response=204, description="Pickup request has been canceled by client", @OA\JsonContent() ),
     *     )
     */
    public function confirmReachedToClient($s_id, $pickup_sid, Request $request)
    {
        $pickup_request = $this->pickup_request_contract->findByWithSecrets('s_id', $pickup_sid);
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

        $driver = firstOf($this->driver_contract->findBy('s_id', $s_id));
        //if no driver
        if (!$driver) {
            return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('No driver found with this s_id')
                ->send();
        }
        //if a wrong driver sid
        if ($driver && $driver->s_id !== $s_id) {

            return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('You are not authorized to do deal with this pickup request')
                ->send();
        }
        //if a wrong qr_code_secret
        if ($pickup_request && $pickup_request->client_location_qr_code_secret !== $request->secret_code) {

            return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('wrong secret_code provided')
                ->send();
        }
        //TODO:add if client_reached_at later
        $pickup_request = $this->pickup_request_contract->confirmReachedToClient($pickup_sid, $request->date_confirmed);

        return  $this->api_responser
            ->success()
            ->code(200)
            ->message('Client location Reached successfully')
            ->send();
    }
    /**
     * @OA\Post(
     * path="/api/v1/drivers/{s_id}/pickup-requests/{pickup_sid}/confirm-reached-to-destination",
     * operationId="confirm reached to client destinatino",
     * tags={"drivers"},
     * summary="confirm that driver reached client destinatino",
     * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
     * @OA\Parameter(  name="pickup_sid", in="path", description="pickup secret id ", required=true),
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="secret_code",type="string"),
     *                 @OA\Property(property="date_confirmed",type="date",example="21-12-2023 15:22"),
     *             )),
     *    ),
     *    @OA\Response( response=200, description="Client destinatino Reached successfully", @OA\JsonContent() ),
     *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *    @OA\Response( response=404, description="No pickup request exists with the given s_id", @OA\JsonContent() ),
     *    @OA\Response( response=403, description="You are not authorized to do deal with this pickup request", @OA\JsonContent() ),
     *    @OA\Response( response=204, description="Pickup request has been canceled by client", @OA\JsonContent() ),
     *     )
     */
    public function confirmReachedToDestination($s_id, $pickup_sid, Request $request)
    {
        $pickup_request = $this->pickup_request_contract->findByWithSecrets('s_id', $pickup_sid);
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

        $driver = firstOf($this->driver_contract->findBy('s_id', $s_id));
        //if no driver
        if (!$driver) {
            return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('No driver found with this s_id')
                ->send();
        }
        //if a wrong driver sid
        if ($driver && $driver->s_id !== $s_id) {

            return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('You are not authorized to do deal with this pickup request')
                ->send();
        }
        //if a wrong qr_code_secret

        if ($pickup_request && $pickup_request->client_arrival_qr_code_secret !== $request->secret_code) {

            return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('wrong secret_code provided')
                ->send();
        }
        //TODO:add if client_reached_at later
        $pickup_request = $this->pickup_request_contract->confirmReachedToDestination($pickup_sid, $request->date_confirmed);

        return  $this->api_responser
            ->success()
            ->code(200)
            ->message('Client destination Reached successfully')
            ->send();
    }
    /**
     * @OA\Post(
     * path="/api/v1/drivers/{s_id}/pickup-requests/{pickup_sid}/cancel",
     * operationId="cancel pickup request after approve it",
     * tags={"drivers"},
     * summary="cancel pickup request after approve it",
     * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
     * @OA\Parameter(  name="pickup_sid", in="path", description="pickup secret id ", required=true),
     *    @OA\Response( response=200, description="Pickup request cancelled successfully", @OA\JsonContent() ),
     *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *    @OA\Response( response=404, description="No pickup request exists with the given s_id", @OA\JsonContent() ),
     *    @OA\Response( response=403, description="You are not authorized to do deal with this pickup request", @OA\JsonContent() ),
     *    @OA\Response( response=204, description="Pickup request has been canceled by client", @OA\JsonContent() ),
     *     )
     */
    public function CancelPickupRequestAfterApprove($s_id, $pickup_sid)
    {

        $pickup = $this->pickup_request_contract->findByWithDriver('s_id', $pickup_sid);

        if (!$pickup) {
            return  $this->api_responser
                ->failed()
                ->code(404)
                ->message('No Pickup request exists with the given s_id')
                ->send();
        }
        //if is canceled by client
        if ($pickup->status == PickupRequestStatus::CANCELED->value) {
            return  $this->api_responser
                ->failed()
                ->code(204)
                ->message('Pickup request has been canceled by client')
                ->send();
        }
        //if is not the current pickup driver
        if ($s_id !== $pickup->driver_s_id) {

            return  $this->api_responser
                ->failed()
                ->code(403)
                ->message('You are not authorized to do deal with this pickup request')
                ->send();
        }
        //send event to client
        event(new PickupRequestCancelled($pickup));

        //remove driver from list
        $pickup_request = firstOf($this->pickup_request_contract->findBy('s_id', $pickup_sid));

        $drivers = json_decode($pickup_request->drivers, true);


        foreach ($drivers as $key => $driver) {

            if ($driver['s_id'] == $s_id) {
                unset($drivers[$key]);
                break;
            }
        }
        $drivers = array_values($drivers);

        $pickup_request = $this->pickup_request_contract->update(
            $pickup_sid,
            [
                'drivers' => json_encode($drivers),
                'status' => PickupRequestStatus::PENDING->value,
                'driver_id' => null
            ]
        );
        //if no driver still available
        if (!count($drivers)) {

            //get the current driver province to refill the drivers list
            $province_id = $this->driver_contract->getDriverProvince($s_id);
            $new_drivers = $this->driver_contract->findByProvince($province_id);
            foreach ($new_drivers as $key => $driver) {

                if ($driver->s_id == $s_id) {
                    unset($new_drivers[$key]);
                    break;
                }
            }
            $new_drivers = array_values($new_drivers->toArray());
            $pickup_request = $this->pickup_request_contract->update(
                $pickup_sid,
                [
                    'drivers' => json_encode($new_drivers)
                ]
            );
            $drivers  = json_decode($pickup_request->drivers, true);
            //    event(new NoDriverAvailable($pickup_request));
            //    return  $this->api_responser
            //        ->success()
            //        ->code(200)
            //        ->message('No driver still available')
            //        ->send();
        }
        //calling the next driver
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
                ]
            ])
            ->message('Pickup request canceleled successfully')
            ->send();
    }
    /**
     * @OA\Get(
     * path="/api/v1/drivers/{s_id}/today-revenus",
     * operationId="get driver today revenus",
     * tags={"drivers"},
     * summary="get driver  today revenus",
     * description="get driver today revenus",
     * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
     * @OA\Response( response=200, description="driver  revenus fetched successfully", @OA\JsonContent() ),
     * @OA\Response( response=404,description="no driver found", @OA\JsonContent()),
     * @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *     )
     */
    public function todayRevenus($s_id)
    {
        try {
            $driver = firstOf($this->driver_contract->findBy('s_id', $s_id));
            if ($driver) {
                $pickups = $this->pickup_request_contract->todayRevenus(id: $driver->id);

                return $this->api_responser
                    ->success()
                    ->message('Driver  pickups history fetched successfully')
                    ->payload($pickups)
                    ->send();
            }
            return $this->api_responser
                ->failed()
                ->code(404)
                ->message('no driver found')
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
     * path="/api/v1/drivers/{s_id}/pickup-requests/{pickup_sid}/send-location",
     * operationId="send driver location to client",
     * tags={"drivers"},
     * summary="send driver location to client",
     * @OA\Parameter(  name="s_id", in="path", description="driver secret id ", required=true),
     * @OA\Parameter(  name="pickup_sid", in="path", description="pickup secret id ", required=true),
     *  @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="lat",type="integer",example="36.3469"),
     *                 @OA\Property(property="lng",type="integer",example="6.7760"),
     *             )),
     *    ),
     *    @OA\Response( response=200, description="location sent successfully", @OA\JsonContent() ),
     *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *    @OA\Response( response=404, description="No driver found with the given s_id", @OA\JsonContent() ),
     *    @OA\Response( response=403, description="You are not authorized to do deal with this pickup request", @OA\JsonContent() ),
     *    @OA\Response( response=204, description="Pickup request has been canceled by client", @OA\JsonContent() ),
     *     )
     */
    public function sendLocation($s_id, $pickup_sid,Request $request)
    {
        try {
            $pickup = $this->pickup_request_contract->findByWithDriver('s_id', $pickup_sid);

            if (!$pickup) {
                return  $this->api_responser
                    ->failed()
                    ->code(404)
                    ->message('No Pickup request exists with the given s_id')
                    ->send();
            }
            //if is canceled by client
            if ($pickup->status == PickupRequestStatus::CANCELED->value) {
                return  $this->api_responser
                    ->failed()
                    ->code(204)
                    ->message('Pickup request has been canceled by client')
                    ->send();
            }
            //if is not the current pickup driver
            if ($s_id !== $pickup->driver_s_id) {

                return  $this->api_responser
                    ->failed()
                    ->code(403)
                    ->message('You are not authorized to do deal with this pickup request')
                    ->send();
            }
            event(new SendDriverLocation($pickup,$request->all()));
            return $this->api_responser
            ->success()
            ->message('location sent successfully')
            ->send();

        } catch (Exception $ex) {

            return $this->api_responser
                ->failed($ex->getCode())
                ->message($ex->getMessage())
                ->send();
        }
    }
}
