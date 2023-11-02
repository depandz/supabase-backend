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
    public function asArray(){
        return [
            'province_id'=>$this->province_id,
            'heavy'=>$this->heavy,
            'light'=>$this->light,
            'truck'=>$this->truck,
        ];
    }

}
