<?php

namespace App\Services\SupaBase;

use App\Contracts\ProvinceContract;
use App\DataTransferObjects\ProvinceDTO as ProvinceObject;
use Exception;
use Illuminate\Support\Collection;

class Province implements ProvinceContract
{
    private $db_instance;

    public function __construct()
    {
        $this->db_instance = supabase_instance()->initializeDatabase('provinces', 'id');
    }

    public function fetchAll(): Collection
    {
        $provinces = Collection::make($this->db_instance->fetchAll()->getResult())->map(fn ($item) => new ProvinceObject($item->code, $item->name,$item->name_ar,$item->longitude,$item->latitude));

        return $provinces;
    }

    public function findByCode($code): ProvinceObject
    {
        try {
            $province = $this->db_instance->findBy('code', $code)->getResult();

            return new ProvinceObject($province[0]?->code, $province[0]?->name, $province[0]?->name_ar,$province[0]?->longitude,$province[0]?->latitude);
        } catch (Exception $ex) {
            throw $ex;
        }

    }

    public function findBy($column,$value): Collection
    {
        try {
          $provinces = Collection::make($this->db_instance->findBy($column, $value)->getResult())->map(fn ($item) => new ProvinceObject($item->code, $item->name));

          return $provinces;
        } catch (Exception $ex) {
            throw $ex;
        }

    }
    public function findByLike($column, $value): Collection
    {
        try {
            $provinces = Collection::make($this->db_instance->findByLike($column, $value)->getResult())->map(fn ($item) => new ProvinceObject($item->code, $item->name,$item->name_ar,$item->longitude,$item->latitude));

            return $provinces;
        } catch (Exception $ex) {
            throw $ex;
        }

    }
}
