<?php

namespace App\Services\SupaBase;

use App\Contracts\FeesContract;
use App\DataTransferObjects\FeesDTO as FeeObject;
use Exception;
use Illuminate\Support\Collection;

class Fees implements FeesContract
{
    private $db_instance;

    public function __construct()
    {
        $this->db_instance = supabase_instance()->initializeDatabase('fees', 'id');
    }
    public function insert($data):FeeObject
    {

        $fee = (array)$this->db_instance->insert($data)[0];
        
        return new FeeObject(
            $fee['province_id'],
            $fee['heavy'],
            $fee['light'],
            $fee['truck'],
            $fee['full_percentage']
        ); 
    }
    public function update($id,$data):FeeObject
    {
        $fee = (array)$this->db_instance->update('id',$data)[0];
        
        return new FeeObject(
            $fee['province_id'],
            $fee['heavy'],
            $fee['light'],
            $fee['truck'],
            $fee['full_percentage']
        );  
    }
    public function fetchAll(): Collection
    {
        $fees = Collection::make($this->db_instance->fetchAll()->getResult())
                            ->map(fn ($item) =>  new FeeObject(
                                $item['province_id'],
                                $item['heavy'],
                                $item['light'],
                                $item['truck'],
                                $item['full_percentage']
                            )
                            );

        return $fees;
    }



    public function findBy($column,$value): Collection
    {
        try {
          $fee = Collection::make($this->db_instance->findBy($column, $value)->getResult())
                            ->map(fn ($item) =>  new FeeObject(
                                $item->province_id,
                                $item->heavy,
                                $item->light,
                                $item->truck,
                                $item->full_percentage
                            )
                            );

          return $fee;
        } catch (Exception $ex) {
            throw $ex;
        }

    }
    public function findByLike($column, $value): Collection
    {
        try {
            $fees = Collection::make($this->db_instance->findByLike($column, $value)->getResult())->map(fn ($item) => new FeeObject($item->code, $item->name,$item->name_ar,$item->longitude,$item->latitude));

            return $fees;
        } catch (Exception $ex) {
            throw $ex;
        }

    }
}
