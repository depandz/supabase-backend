<?php

namespace App\DataTransferObjects;

class FeesDTO
{
   

    public function __construct(
        public ?int $province_id,
        public ?float $heavy,
        public ?float $light,
        public ?float $truck,
        public ?int $full_percentage,
    ){}


}
