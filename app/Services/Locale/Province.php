<?php

namespace App\Services\Locale;

use App\Contracts\ProvinceContract;
use App\DataTransferObjects\ProvinceDTO as ProvinceObject;
use App\Models\Province as ProvinceModel;
use Illuminate\Support\Collection;

class Province  implements ProvinceContract
{
    /**
     * find by otp an and phone
     * 
     * @return model intance
     */
    public function findByCode(int $code): ProvinceObject
    {
        $province = ProvinceModel::whereCode($code)->firstOrFail();
        return  new ProvinceObject($province?->code, $province?->name, $province?->name_ar,$province?->longitude,$province?->latitude);
    }
    public function findBy(string $column, string $value): Collection
    {
        return Collection::make([]);
    }
    public function findByLike(string $column, string $value): Collection
    {
        return Collection::make([]); 
    }
    public function fetchAll(): Collection
    {
       
        return Collection::make(ProvinceModel::all())->map(fn ($item) => new ProvinceObject($item->code, $item->name,$item->name_ar,$item->longitude,$item->latitude));
    }
}