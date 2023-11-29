<?php

namespace App\Contracts\Admin;

use Illuminate\Http\File;
use Illuminate\Support\Collection;
use App\Contracts\SupaBaseContract;
use App\DataTransferObjects\PanelDriverDTO as DriverObject;

interface DriverContract extends SupaBaseContract
{
    public function insert(array $data): DriverObject;

    public function update(int $id, array $data): DriverObject;

    public function suspend(int $id): void;

    public function activateAccount(int $id): void;

    public function findByProvince(int $province_id): Collection;
}
