<?php

namespace App\Http\Controllers\Api\V1;

use DateTime;
use App\Enums\VehicleTypes;
use Illuminate\Http\Request;
use App\Models\PickupRequest;
use App\Pipes\FetchDriversList;
use Illuminate\Pipeline\Pipeline;
use App\Enums\PickupRequestStatus;
use App\Pipes\ProvinceDataFetcher;
use App\Http\Controllers\Controller;
use App\Pipes\PickupPriceCalculator;
use Illuminate\Support\Facades\Date;
use App\Contracts\PickUpRequestContract;
use App\DataTransferObjects\PositionDTO;
use App\Events\StartPickupRequestCalling;
use App\Http\Requests\CancelPickupRequest;
use App\Http\Requests\ConfirmPickupRequest;
use App\Http\Requests\InitializePickupRequest;
use App\Pipes\DriversDistanceFromClientCalculator;
use App\DataTransferObjects\PickupRequestDTO as PickupRequestObject;

class PickupRequestController extends Controller
{

    protected $pickup_request_contract;
    protected $pipeline;

    public function __construct(PickUpRequestContract $pickup_request_contract, Pipeline $pipeline)
    {
        parent::__construct();
        $this->pickup_request_contract = $pickup_request_contract;
        $this->pipeline = $pipeline;
    }
    /**
     * @OA\Post(
     * path="/api/v1/pickup-requests/initialize",
     * operationId="initialize_pickup_request",
     * tags={"pickup_requests"},
     * summary="initialize pickup request ",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property( property="client_id",type="integer",example="9"),
     *                 @OA\Property(property="current_province_id",type="integer",example="1"),
     *                 @OA\Property( property="location",type="json",example={"lat":27.895505,"lng":-0.2931788}),
     *                 @OA\Property(property="destination",type="json",example={"lat":27.895505,"lng":-0.2931788}),
     *                 @OA\Property(property="distance",type="float",example="3"),
     *                 @OA\Property(property="duration",type="string",example="20 min"),
     *                 @OA\Property(property="licence_plate",type="string",example="1457854897"),
     *                 @OA\Property(property="is_vehicle_empty",type="boolean",enum={0,1}),
     *                 @OA\Property(property="vehicle_type",type="string",enum={"light","heavy","truck"}),
     *                 @OA\Property(property="date_requested",type="date",example="17-09-2023 15:22"),
     *             )),
     *    ),
     *    @OA\Response( response=200, description="Pickup request initialized successfully", @OA\JsonContent() ),
     *    @OA\Response( response=404, description="No client exists with the given id", @OA\JsonContent() ),
     *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *     )
     */
    public function initialize(InitializePickupRequest $request)
    {
        $data = $request->all();
        //check client exists
        $client = checkClientExists($request->client_id);
        $result = $this->pipeline
            ->send((array)$data)
            ->through([
                ProvinceDataFetcher::class,
                FetchDriversList::class,
                DriversDistanceFromClientCalculator::class,
                PickupPriceCalculator::class
            ])
            // ->thenReturn();
            ->then(function (array $data) {
                $pickup_request = $this->store($data);
                $pickup_request->drivers = collect($data['available_drivers'])->map(function ($driver) {
                    return [
                        "s_id" => $driver->s_id,
                        "full_name" => $driver->full_name,
                        "phone_number" => $driver->phone_number,
                        "location" => $driver->location,
                        "photo" => $driver->photo,
                        "reported_count" => $driver->reported_count,
                        "capacity" => $driver->capacity
                    ];
                });
                return $pickup_request;
                // return $this->api_responser
                // ->success()
                // ->message('Pickup request initialized successfully')
                // ->payload(
                //     [
                //         'pickup_request'=>$pickup_request,
                //     ]
                // )
                // ->send();
            });
        return $this->api_responser
            ->success()
            ->message('Pickup request initialized successfully')
            ->payload([
                'pickup_request' => $result,
            ])
            ->send();
    }
    public function store(array $data)
    {
        try {

            $pickup_request = new PickupRequestObject(
                s_id: null,
                client_id: $data['client_id'],
                driver_id: null,
                location: new PositionDTO(lat: json_decode($data['location'])->lat, lng: json_decode($data['location'])->lng),
                destination: new PositionDTO(lat: json_decode($data['location'])->lat, lng: json_decode($data['location'])->lng),
                estimated_distance: $data['distance'],
                estimated_price: $data['estimated_price'],
                estimated_duration: $data['duration'],
                vehicle_type: $data['vehicle_type'] ?? VehicleTypes::LIGHT->value,
                is_vehicle_empty: (bool)$data['is_vehicle_empty'],
                vehicle_licence_plate: $data['licence_plate'],
                date_requested: Date::createFromTimeString($data['date_requested']),
                status: PickupRequestStatus::INITIALIZED->value,
                drivers: json_encode($data['available_drivers']),
            );

            $pickup_initialized = $this->pickup_request_contract->insert($pickup_request->asArray());
            return $pickup_initialized;
        } catch (\Exception $ex) {
            return $this->api_responser
                ->failed()
                ->message('Error when initalize pickup request ' . $ex->getMessage())
                ->send();
        }
    }
    /**
     * @OA\Post(
     * path="/api/v1/pickup-requests/{s_id}/confirm",
     * operationId="confirm_pickup_request",
     * tags={"pickup_requests"},
     * summary="confirm pickup request ",
     * @OA\Parameter(  name="s_id", in="path", description="Pickup request secret id ", required=true),
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="date_confirmed",type="date",example="17-09-2023 15:22"),
     *             )),
     *    ),
     *    @OA\Response( response=200, description="Pickup request confirmed successfully", @OA\JsonContent() ),
     *    @OA\Response( response=404, description="No pickup request exists with the given id", @OA\JsonContent() ),
     *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *     )
     */
    public function confirm(ConfirmPickupRequest $request, $s_id)
    {
        $pickup_request = $this->pickup_request_contract->findBy('s_id', $s_id);
        if (!firstOf($pickup_request)) {
            return  $this->api_responser
                ->failed()
                ->code(404)
                ->message('No Pickup request exists with the given s_id')
                ->send();
        }
        $pickup_request = $this->pickup_request_contract->confirm(
            $s_id,
            Date::createFromTimeString($request->date_confirmed)
        );
        $driver = json_decode($pickup_request->drivers, true)[0];

        event(new StartPickupRequestCalling($pickup_request, $driver));
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
                        's_id' => $driver['s_id'],
                        'full_name' => $driver['full_name'],
                        'phone_number' => $driver['phone_number'],
                        'location' => $driver['location'],
                        'photo' => url('storage/drivers/photos/' . $driver['photo']),
                    ]
                ]
            ])
            ->message('Pickup request confirmed successfully')
            ->send();
    }
    /**
     * @OA\Post(
     * path="/api/v1/pickup-requests/{s_id}/cancel",
     * operationId="cancel_pickup_request",
     * tags={"pickup_requests"},
     * summary="cancel pickup request ",
     * @OA\Parameter(  name="s_id", in="path", description="Pickup request secret id ", required=true),
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="date_cancelled",type="date",example="17-09-2023 15:22"),
     *             )),
     *    ),
     *    @OA\Response( response=200, description="Pickup request cancelled successfully", @OA\JsonContent() ),
     *    @OA\Response( response=404, description="No pickup request exists with the given id", @OA\JsonContent() ),
     *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
     *     )
     */
    public function cancel(CancelPickupRequest $request, $s_id)
    {
        $pickup_request = $this->pickup_request_contract->findBy('s_id', $s_id);
        if (!firstOf($pickup_request)) {
            return  $this->api_responser
                ->failed()
                ->code(404)
                ->message('No Pickup request exists with the given s_id')
                ->send();
        }
        if (firstOf($pickup_request)->status != PickupRequestStatus::CANCELED->value) {
            $pickup_request = $this->pickup_request_contract->cancel(
                $s_id,
                Date::createFromTimeString($request->date_cancelled)
            );
        }
        return $this->api_responser
            ->success()
            ->message('Pickup request cancelled successfully')
            ->send();
    }
}
