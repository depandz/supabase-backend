<?php

namespace App\Contracts\Admin;

use Illuminate\Http\File;
use Illuminate\Support\Collection;
use App\Contracts\SupaBaseContract;
use App\DataTransferObjects\PanelDriverDTO as DriverObject;

interface DriverContract extends SupaBaseContract
{
    public function insert(array $data): DriverObject;

    public function update(string $s_id, array $data): DriverObject;

    public function suspend(string $s_id): void;

    public function activateAccount(string $s_id): void;

    public function findByProvince(int $province_id): Collection;

    public function delete(string $s_id): bool|null;
    
    public function restore(string $s_id): void;
}
