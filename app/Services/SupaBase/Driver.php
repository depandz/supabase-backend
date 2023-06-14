<?php

namespace App\Services\SupaBase;


use DateTime;
use App\Contracts\DriverContract;
use App\DataTransferObjects\DriverDTO as DriverObject;
use App\Enums\AccountStatus;
use Exception;
use Illuminate\Support\Collection;

class Driver implements DriverContract
{
    private $db_instance;

    public function __construct()
    {
        $this->db_instance = supabase_instance()->initializeDatabase('drivers', 'id');
    }

    public function fetchAll(): Collection
    {
         //TODO:only account active client
        $provinces = Collection::make($this->db_instance->fetchAll()->getResult())
            ->map(function ($item) {
                //TODO: get all attribute of item
                return new DriverObject($item->s_id, $item->full_name, $item->phone_number);
            });

        return $provinces;
    }
    public function findBy($column, $value): Collection
    {
         //TODO:only account active client
        try {
            $clients = Collection::make($this->db_instance->findBy($column, $value)->getResult())
                                    ->map(function ($item) {
                                        $item = (array) $item;
                                        //TODO: get all attribute of item
                                        return new DriverObject(
                                            $item['s_id'],
                                            $item['full_name'],
                                            $item['phone_number'],
                                            $item['gender'],
                                            $item['location'],
                                            $item['email'],
                                            $item['photo'],
                                            $item['messaging_token'],
                                            $item['reported_count'],
                                            $item['account_status'],
                                            $item['registered_at'],
                                        );
            });

            return $clients;
        } catch (Exception $ex) {
            throw $ex;
        }

    }
    public function findByLike($column, $value): Collection
    {
         //TODO:only account active client
        try {
            $provinces = Collection::make($this->db_instance->findByLike($column, $value)->getResult())
                ->map(function ($item) {
                    //TODO: get all attribute of item
                    return new DriverObject($item->s_id, $item->full_name, $item->phone_number);
                });

            return $provinces;
        } catch (Exception $ex) {
            throw $ex;
        }

    }

    public function insert($data): DriverObject
    {
        return new DriverObject($phone_number = '55445f');
    }
    public function update($id, $data): DriverObject
    {
        return new DriverObject($phone_number = '55445f');
    }

    public function updatePhoto($id, $request): string
    {
        return '/photos/sdfsdf.png';
    }
    public function suspend($id): void
    {
         $this->update($id,['account_status'=>AccountStatus::$SUSPENDED]);
 
    }
    public function activateAccount($id): void
    {
         $this->update($id,['account_status'=>AccountStatus::$ACTIVE]);

    }
}
