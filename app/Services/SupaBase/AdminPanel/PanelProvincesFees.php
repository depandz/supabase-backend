<?php

namespace App\Services\SupaBase\AdminPanel;

use Exception;
use Illuminate\Support\Collection;
use App\Contracts\Admin\FeesContract;
use App\DataTransferObjects\PanelFeesDTO as FeeObject;

class PanelProvincesFees implements FeesContract
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
            $fee['id'],
            $fee['province_id'],
            $fee['heavy'],
            $fee['light'],
            $fee['truck'],
            $fee['full_percentage'],
            $fee['deleted_at'],
        ); 
    }
    public function update($id,$data):FeeObject
    {
        $fee = (array)$this->db_instance->update('id',$data)[0];
        
        return new FeeObject(
            $fee['id'],
            $fee['province_id'],
            $fee['heavy'],
            $fee['light'],
            $fee['truck'],
            $fee['full_percentage'],
            $fee['deleted_at'],
        );  
    }
    public function fetchAll(): Collection
    {
        try{
        $query = [
            'select' => '*',
            'from'   => 'fees',
        ];
        $fees = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                            ->map(fn ($item) =>  new FeeObject(
                                $item->id,
                                $item->province_id,
                                $item->heavy,
                                $item->light,
                                $item->truck,
                                $item->full_percentage,
                                $item->deleted_at
                            )
                            );

        return $fees;
        } catch (Exception $ex) {
            if ($ex->getCode() == 401) {
                authenticate_user();
            }
            return Collection::make([]);
        }
    }



    public function findBy($column,$value): Collection
    {
        try {
            $query = [
                'select' => '*',
                'from'   => 'fees',
                'join'   => [
                    [
                        'table' => 'provinces',
                        'tablekey' => 'id'
                    ]
                ],
            ];
          $fee = Collection::make($this->db_instance->createCustomQuery($query)->findBy($column, $value)->getResult())
                            ->map(fn ($item) =>  new FeeObject(
                                $item->id,
                                $item->province_id,
                                $item->heavy,
                                $item->light,
                                $item->truck,
                                $item->full_percentage,
                                $item->deleted_at
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
            $query = [
                'select' => '*',
                'from'   => 'fees',
            ];
            $fees = Collection::make($this->db_instance->createCustomQuery($query)->findByLike($column, $value)->getResult())->map(fn ($item) => new FeeObject(
                $item->id,
                $item->province_id,
                $item->heavy,
                $item->light,
                $item->truck,
                $item->full_percentage,
                $item->deleted_at
            ));

            return $fees;
        } catch (Exception $ex) {
            throw $ex;
        }

    }
    public function findByProvince(int $province_id): FeeObject
    {
      
        try {
                $query = [
                    'select' => '*',
                    'from'   => 'fees',
                    'where'  =>[
                        'province_id' => 'eq.' . $province_id,
                    ]
                ];
                $fees = Collection::make($this->db_instance->createCustomQuery($query)->findByLike($column, $value)->getResult())->map(fn ($item) => new FeeObject(
                    $item->id,
                    $item->province_id,
                    $item->heavy,
                    $item->light,
                    $item->truck,
                    $item->full_percentage,
                    $item->deleted_at
                ));
                $fee = firstOf($fees);
                return $fee;

            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function restore($id): void
    {
        $data = ['deleted_at' => null];  
        try {

            $fee =  supabase_instance()->initializeDatabase('fees', 'id')
                ->update($id, $data);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function delete(int $id): bool|null
    {
        $data = ['deleted_at' => now()];
        try {

            $fee =  supabase_instance()->initializeDatabase('fees', 's_id')
                ->update($id, $data);
            return true;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
