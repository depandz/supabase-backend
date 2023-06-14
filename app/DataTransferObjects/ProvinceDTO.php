<?php

namespace App\DataTransferObjects;

class ProvinceDTO
{
   

    public function __construct(
        public ?int $code,
        public ?string $name 
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
