<?php

namespace App\Contracts;

use Illuminate\Http\File;
use App\DataTransferObjects\PickupRequestDTO as PickupRequest;

interface PickUpRequestContract extends SupaBaseContract
{
    public function insert(array $data): PickupRequest;

    public function update(string $s_id, array $data): PickupRequest;

    public function checkExist(int $client_id, int $distance): PickupRequest|null;

    public function confirm(string $s_id, $date_confirmed): PickupRequest|null;
}
