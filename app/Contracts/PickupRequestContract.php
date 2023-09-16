<?php

namespace App\Contracts;

use Illuminate\Http\File;
use App\DataTransferObjects\PickupRequestDTO as PickupRequest;

interface PickUpRequestContract extends SupaBaseContract
{
    public function insert(array $data): PickupRequest;

    public function update(int $id, array $data): PickupRequest;

}
