<?php

namespace App\DataTransferObjects;

class ProvinceDTO
{
   

    public function __construct(
        public ?int $code,
        public ?string $name ,
        public ?string $name_ar ,
        public ?string $longitude ,
        public ?string $latitude ,
    ){}

    //TODO: private properties
    // public function code()
    // {
    //     return $this->code;
    // }

    // public function name()
    // {
    //     return $this->name;
    // }
}
