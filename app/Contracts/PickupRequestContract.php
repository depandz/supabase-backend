<?php

namespace App\Contracts;

use Illuminate\Http\File;
use Illuminate\Support\Collection;
use App\DataTransferObjects\PickupRequestDTO as PickupRequest;

interface PickupRequestContract extends SupaBaseContract
{
    public function insert(array $data): PickupRequest;

    public function update(string $s_id, array $data): PickupRequest;

    public function checkExist(int $client_id, int $distance): PickupRequest|null;

    public function confirm(string $s_id, $date_confirmed): PickupRequest|null;

    public function approve(string $s_id, int $driver): PickupRequest|null;

    public function finish(string $s_id, $date_finished): bool|null;

    public function cancel(string $s_id, $date_cancelled): bool |null;

    public function history(int $id,string $type): Collection |null;

    public function rate(string $s_id,int $rating,?string $rating_comment): bool |null;
}
