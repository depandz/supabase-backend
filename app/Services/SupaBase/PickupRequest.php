<?php

namespace App\Services\SupaBase;

use DateTime;
use Exception;
use App\Contracts\PickupRequestContract;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use App\DataTransferObjects\PickupRequestDTO as PickpRequestObject;
use App\Enums\GlobalVars;

class PickupRequest implements PickupRequestContract
{
    private $db_instance;

    public function __construct()
    {
        $this->db_instance = supabase_instance()->initializeDatabase('pickup_requests', 'id');
    }

    public function fetchAll(): Collection
    {
        
        $pickup_requests = Collection::make($this->db_instance->fetchAll()->getResult())
                                ->map(function ($item) {
                                    $item = (array) $item;
                                    return new PickpRequestObject(
                                        $item['s_id'],
                                        $item['client_id'],
                                        $item['driver_id'],
                                        $item['current_province_id'],
                                        $item['location'],
                                        $item['destination'],
                                        $item['estimated_distance'],
                                        $item['estimated_duration'],
                                        $item['vehicle_type'],
                                        $item['is_vehicle_empty'],
                                        $item['licence_plate'],
                                        $item['date_requested'],
                                        $item['status'],
                                    );
                                });

        return $pickup_requests;
    }

    public function findByLike($column, $value): Collection
    {
         
        try {
            $pickup_requests = Collection::make($this->db_instance
                                    ->findByLike($column, $value)
                                    ->getResult())
                                    ->map(function ($item) {
                                        $item = (array) $item;
                                        
                                        return new PickpRequestObject(
                                            $item['s_id'],
                                            $item['client_id'],
                                            $item['driver_id'],
                                            $item['current_province_id'],
                                            $item['location'],
                                            $item['destination'],
                                            $item['estimated_distance'],
                                            $item['estimated_duration'],
                                            $item['vehicle_type'],
                                            $item['is_vehicle_empty'],
                                            $item['licence_plate'],
                                            $item['date_requested'],
                                            $item['status'],
                                        );
            });

            return $pickup_requests;
        } catch (Exception $ex) {
            throw $ex;
        }

    }
    public function findBy($column, $value): Collection
    {
         
        try {
            $pickup_requests = Collection::make($this->db_instance
                                    ->findBy($column, $value)
                                    ->getResult()
                                    )
                                    ->map(function ($item) {
                                        $item = (array) $item;
                                        
                                        return new PickpRequestObject(
                                            $item['s_id'],
                                            $item['client_id'],
                                            $item['driver_id'],
                                            $item['current_province_id'],
                                            $item['location'],
                                            $item['destination'],
                                            $item['estimated_distance'],
                                            $item['estimated_duration'],
                                            $item['vehicle_type'],
                                            $item['is_vehicle_empty'],
                                            $item['licence_plate'],
                                            $item['date_requested'],
                                            $item['status'],
                                        );
            });

            return $pickup_requests;
        } catch (Exception $ex) {
            throw $ex;
        }

    }

    public function insert($data): PickpRequestObject
    {
        try {
            $data['s_id'] = generate_sid('client');
            $data['photo'] = GlobalVars::getDefaultProfilePicture($data['full_name']);

            $pickup_request = (array)$this->db_instance->insert($data)[0];
            
 
            return new PickpRequestObject(
                $pickup_request['s_id'],
                $pickup_request['client_id'],
                $pickup_request['driver_id'],
                $pickup_request['current_province_id'],
                $pickup_request['location'],
                $pickup_request['destination'],
                $pickup_request['estimated_distance'],
                $pickup_request['estimated_duration'],
                $pickup_request['vehicle_type'],
                $pickup_request['is_vehicle_empty'],
                $pickup_request['licence_plate'],
                $pickup_request['date_requested'],
                $pickup_request['status'],
            );

        } catch (Exception $ex) {
            if($ex->getCode() == '409')
            {
                throw ValidationException::withMessages(['phone_number' => 'Error when inserting']);
            }
            throw $ex;
        }

    }
    public function update($s_id, $data): PickpRequestObject
    {
        try {
            $data=array_filter($data, fn ($value) => $value );
            if(array_key_exists('location',$data)){
                $data['location'] = json_encode($data['location']);
            }
            $pickup_request = $data = supabase_instance()->initializeDatabase('pickup_requests', 's_id')->update($s_id,$data);
            $pickup_request =  (array)$pickup_request[0];

            return new PickpRequestObject(
                $pickup_request['s_id'],
                $pickup_request['client_id'],
                $pickup_request['driver_id'],
                $pickup_request['current_province_id'],
                $pickup_request['location'],
                $pickup_request['destination'],
                $pickup_request['estimated_distance'],
                $pickup_request['estimated_duration'],
                $pickup_request['vehicle_type'],
                $pickup_request['is_vehicle_empty'],
                $pickup_request['licence_plate'],
                $pickup_request['date_requested'],
                $pickup_request['status'],
            );

        } catch (Exception $ex) {
            throw $ex;
        }
    }

    
}
