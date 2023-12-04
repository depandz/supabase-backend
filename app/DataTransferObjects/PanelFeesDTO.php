<?php

namespace App\DataTransferObjects;

class PanelFeesDTO
{
   

    public function __construct(
        public ?int $province_id,
        public ?float $heavy,
        public ?float $light,
        public ?float $truck,
        public ?int $full_percentage,
        public ?string $deleted_at = null,
    ){}
    public function asArray(){
        return [
            'province_id'=>$this->province_id,
            'heavy'=>$this->heavy,
            'light'=>$this->light,
            'truck'=>$this->truck,
            'full_percentage'=>$this->full_percentage,
            'deleted_at'=>$this->deleted_at,
        ];
    }

}
