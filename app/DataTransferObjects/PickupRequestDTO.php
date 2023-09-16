<?php

namespace App\DataTransferObjects;

use DateTime;
use App\Enums\VehicleTypes;
use App\Enums\PickupRequestStatus;
use App\DataTransferObjects\PositionDTO as Position;

class PickupRequestDTO
{
    
    public function __construct(
        public ?string $s_id,
        public ?string $client_id,
        public ?string $driver_id,
        public ?int $current_province_id,
        public ?Position $location=null,
        public ?Position $destination=null,
        public ?float $distance=null,
        public ?int $duration=null,
        public ?VehicleTypes $vehicle_type,
        public ?bool $is_vehicle_empty=null,
        public ?string $licence_plate=null,
        public ?DateTime $date_requested=null,
        public ?PickupRequestStatus $status=null,
    ){}

}
