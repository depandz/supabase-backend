<?php

namespace App\DataTransferObjects;

use DateTime;

class PositionDTO
{

    public function __construct(
        public ?string $place_id=null,
        public ?string $zip=null,
        public ?string $city=null,
        public ?string $search_string=null,
        public ?float $lat=null,
        public ?float $lng=null,
    ){}


}
