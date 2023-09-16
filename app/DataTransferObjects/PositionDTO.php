<?php

namespace App\DataTransferObjects;

use DateTime;

class PositionDTO
{

    public function __construct(
        public ?string $place_id,
        public ?string $zip,
        public ?string $city,
        public ?string $search_string,
        public ?float $lat,
        public ?float $lng,
    ){}


}
