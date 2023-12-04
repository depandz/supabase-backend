<?php

namespace App\Contracts\Admin;

use Illuminate\Http\File;
use App\Contracts\SupaBaseContract;
use App\DataTransferObjects\PanelFeesDTO as FeeObject;

interface FeesContract extends SupaBaseContract
{
    public function insert(array $data): FeeObject;

    public function update(int $id, array $data): FeeObject;

    public function findByProvince(int $province_id): FeeObject;

    public function delete(int $id): bool|null;
    
    public function restore(int $id): void;
}
