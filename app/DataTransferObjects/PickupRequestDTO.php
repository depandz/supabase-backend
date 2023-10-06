<?php

namespace App\DataTransferObjects;

use DateTime;
use App\Enums\VehicleTypes;
use App\Enums\PickupRequestStatus;
use App\DataTransferObjects\PositionDTO as Position;
use Illuminate\Support\Facades\Date;

class PickupRequestDTO
{
    
    public function __construct(
        public ?string $s_id=null,
        public ?int $client_id,
        public ?int $driver_id=null,
        public ?Position $location=null,
        public ?Position $destination=null,
        public ?float $estimated_distance=null,
        public ?int $estimated_price=null,
        public ?string $estimated_duration=null,
        public ?string $vehicle_type,
        public ?bool $is_vehicle_empty=null,
        public ?string $vehicle_licence_plate=null,
        public $date_requested=null,
        public ?string $status=null,
    ){}
    public function asArray(){
        return  (array) $this;
    }
}
