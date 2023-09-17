<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Pipes\FetchDriversList;
use Illuminate\Pipeline\Pipeline;
use App\Pipes\ProvinceDataFetcher;
use App\Http\Controllers\Controller;
use App\Contracts\PickUpRequestContract;
use App\Http\Requests\InitializePickupRequest;
use App\Pipes\DriversDistanceFromClientCalculator;

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
        *                 @OA\Property( property="client_sid",type="string",example="cls_257841"),
        *                 @OA\Property(property="current_province_id",type="integer",example="1"),
        *                 @OA\Property( property="location",type="json",example={"lat":27.895505,"lng":-0.2931788}),
        *                 @OA\Property(property="destination",type="json",example={"lat":27.895505,"lng":-0.2931788}),
        *                 @OA\Property(property="licence_plate",type="string",example="1457854897"),
        *                 @OA\Property(property="is_vehicle_empty",type="boolean",enum={0,1}),
        *                 @OA\Property(property="date_requested",type="date"),
        *             )),
        *    ),
        *    @OA\Response( response=200, description="Pickup request initialized successfully", @OA\JsonContent() ),
        *    @OA\Response(response=500,description="internal server error", @OA\JsonContent() ),
        *     )
        */
    public function initialize(Request $request)
    { 
        $data =$request->all();
        $result= $this->pipeline
        ->send((array)$data)
        ->through([
            ProvinceDataFetcher::class,
            FetchDriversList::class,
            DriversDistanceFromClientCalculator::class
        ])
        ->thenReturn();
        // ->then(function (array $data) {
        //     return $this->api_responser
        //     ->success()
        //     ->message('Pickup request initialized successfully')
        //     ->payload($data)
        //     ->send();
        // });
        return $this->api_responser
            ->success()
            ->message('Pickup request initialized successfully')
            ->payload($result)
            ->send();
       
    }
}
