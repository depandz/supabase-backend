<?php

namespace App\DataTransferObjects;

use DateTime;

class PanelDriverDTO
{
    public function __construct(
        public ?string $id = null,
        public ?string $s_id = null,
        public ?string $first_name = null,
        public ?string $last_name = null,
        public ?string $phone_number = null,
        public ?string $gender = null,
        public ?string $email = null,
        public ?string $photo = null,
        public ?int $reported_count = 0,
        public ?string $account_status = null,
        public ?string $registered_at = null,
        public ?string $vehicle_type = null,
        public ?int $identity_card_number = null,
        public ?int $commercial_register_number = null,
        public ?float $capacity = null,
        public ?int $licence_plate = null,
        public ?bool $is_online = false,
        public ?object $company = null,
        public ?bool $is_default_for_company = false,
        public ?bool $can_transport_goods = false,
        public  ?float $rating=0,
        public  ?string $deleted_at= null,
        public  ?int $province_id= null,

    ) {
        $this->registered_at = (new DateTime($this->registered_at))->format('d-m-Y H:i');
        $this->phone_number = '+'.$this->phone_number;
    }

   //TODO: set private and create getters and setters later
}
