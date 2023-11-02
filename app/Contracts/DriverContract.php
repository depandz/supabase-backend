<?php

namespace App\Contracts;

use Illuminate\Http\File;
use App\DataTransferObjects\DriverDTO as DriverObject;
use Illuminate\Support\Collection;

interface DriverContract extends SupaBaseContract
{
    public function insert(array $data): DriverObject;

    public function update(int $id, array $data): DriverObject;

    public function updatePhoto(int $id, File $file): string;

    public function suspend(int $id): void;

    public function activateAccount(int $id): void;

    public function findByProvince(int $province_id): Collection;
}
