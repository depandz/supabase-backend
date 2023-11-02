<?php

namespace App\Http\Controllers\Api\V1;

use DateTime;
use App\Enums\VehicleTypes;
use Illuminate\Http\Request;
use App\Pipes\FetchDriversList;
use Illuminate\Pipeline\Pipeline;
use App\Enums\PickupRequestStatus;
use App\Pipes\ProvinceDataFetcher;
use App\Http\Controllers\Controller;
use App\Pipes\PickupPriceCalculator;
use Illuminate\Support\Facades\Date;
use App\Contracts\PickUpRequestContract;
use App\DataTransferObjects\PositionDTO;
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
        *                 @OA\Property(property="duration",type="string",example="20:00"),
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
        $data =$request->all();
        //check client exists
        $client = checkClientExists($request->client_id);
        $result= $this->pipeline
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
            $pickup_request->drivers =collect($data['available_drivers'])->map(function($driver){
                return [
                    "s_id"=>$driver->s_id,
                    "full_name"=>$driver->full_name,
                    "phone_number"=>$driver->phone_number,
                    "location"=>$driver->location,
                    "photo"=>$driver->photo,
                    "reported_count"=>$driver->reported_count,
                    "capacity"=>$driver->capacity
                ];
            });
            return $this->api_responser
            ->success()
            ->message('Pickup request initialized successfully')
            ->payload(
                [
                    'pickup_request'=>$pickup_request,
                ]
            )
            ->send();
        });
        return $this->api_responser
            ->success()
            ->message('Pickup request initialized successfully')
            ->payload($result)
            ->send();
       
    }
    public function store(array $data)
    {
        try{
          
            $pickup_request = new PickupRequestObject(
                s_id:null,
                client_id:$data['client_id'],
                driver_id:null,
                location:new PositionDTO(lat: json_decode($data['location'])->lat,lng:json_decode($data['location'])->lng),
                destination:new PositionDTO(lat: json_decode($data['location'])->lat,lng:json_decode($data['location'])->lng),
                estimated_distance:$data['distance'],
                estimated_price:$data['estimated_price'],
                estimated_duration:$data['duration'],
                vehicle_type:$data['vehicle_type'] ?? VehicleTypes::LIGHT->value,
                is_vehicle_empty:$data['is_vehicle_empty'],
                vehicle_licence_plate:$data['licence_plate'],
                date_requested:Date::createFromTimeString($data['date_requested']),
                status:PickupRequestStatus::INITIALIZED->value,
             );
           
           $pickup_initialized = $this->pickup_request_contract->insert($pickup_request->asArray());
           return $pickup_initialized;
        }
        catch(\Exception $ex)
        {
            return $this->api_responser
            ->failed()
            ->message('Error when initalize pickup request '.$ex->getMessage())
            ->send();
        }
    }
   
}
