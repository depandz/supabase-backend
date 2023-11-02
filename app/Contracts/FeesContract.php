<?php

namespace App\Contracts;

use Illuminate\Http\File;
use App\DataTransferObjects\FeesDTO as FeeObject;

interface FeesContract extends SupaBaseContract
{
    public function insert(array $data): FeeObject;

    public function update(int $id, array $data): FeeObject;

}
