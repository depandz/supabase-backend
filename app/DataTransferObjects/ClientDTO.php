<?php

namespace App\DataTransferObjects;

use DateTime;

class ClientDTO
{



    public function __construct(
        public ?string $id=null,
        public ?string $s_id=null,
        public ?string $first_name=null,
        public ?string $last_name=null,
        public ?string $phone_number=null,
        public ?string $gender=null,
        public ?object $location=null,
        public ?string $email=null,
        public ?string $photo=null,
        public ?string $messaging_token=null,
        public ?int $reported_count=null,
        public ?string $account_status=null,
        public ?string $registered_at=null,
    ){
        $this->registered_at = (new DateTime($this->registered_at))->format('Y-m-d');
        $this->phone_number = '+'.$this->phone_number;
    }

   //TODO: set private and create getters and setters later
}
