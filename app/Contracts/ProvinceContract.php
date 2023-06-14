<?php

namespace App\Contracts;

use App\DataTransferObjects\ProvinceDTO as ProvinceObject;

interface ProvinceContract extends SupaBaseContract
{
    public function findByCode(int $code): ProvinceObject;
}
