<?php

namespace App\Services\SupaBase;

use DateTime;
use Exception;
use App\Contracts\PickupRequestContract;
use App\DataTransferObjects\DriverDTO as Driver;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use App\DataTransferObjects\PickupRequestDTO as PickpRequestObject;
use App\DataTransferObjects\PositionDTO;
use App\Enums\GlobalVars;
use App\Enums\PickupRequestStatus;
use Illuminate\Support\Facades\Date;

class PickupRequest implements PickupRequestContract
{
    private $db_instance;

    public function __construct()
    {
        $this->db_instance = supabase_instance()->initializeDatabase('pickup_requests', 'id');
    }

    public function fetchAll(): Collection
    {
        try{
        $pickup_requests = Collection::make($this->db_instance->fetchAll()->getResult())
            ->map(function ($item) {
                $item = (array) $item;
                return new PickpRequestObject(
                    $item['s_id'],
                    $item['client_id'],
                    $item['driver_id'],
                    new PositionDTO(lat: $item['location']->lat, lng: $item['location']->lng),
                    new PositionDTO(lat: $item['destination']->lat, lng: $item['destination']->lng),
                    $item['estimated_distance'],
                    $item['estimated_price'],
                    $item['estimated_duration'],
                    $item['vehicle_type'],
                    $item['is_vehicle_empty'],
                    $item['vehicle_licence_plate'],
                    $item['date_requested'],
                    $item['status'],
                    $item['drivers'],
                );
            });

        return $pickup_requests;
        } catch (Exception $ex) {
            throw $ex;
        }
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
                        new PositionDTO(lat: $item['location']->lat, lng: $item['location']->lng),
                        new PositionDTO(lat: $item['destination']->lat, lng: $item['destination']->lng),
                        $item['estimated_distance'],
                        $item['estimated_price'],
                        $item['estimated_duration'],
                        $item['vehicle_type'],
                        $item['is_vehicle_empty'],
                        $item['vehicle_licence_plate'],
                        $item['date_requested'],
                        $item['status'],
                        $item['drivers'],
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
            $pickup_requests = Collection::make(
                $this->db_instance
                    ->findBy($column, $value)
                    ->getResult()
            )
                ->map(function ($item) {
                    $item = (array) $item;

                    return new PickpRequestObject(
                        $item['s_id'],
                        $item['client_id'],
                        $item['driver_id'],
                        new PositionDTO(lat: $item['location']->lat, lng: $item['location']->lng),
                        new PositionDTO(lat: $item['destination']->lat, lng: $item['destination']->lng),
                        $item['estimated_distance'],
                        $item['estimated_price'],
                        $item['estimated_duration'],
                        $item['vehicle_type'],
                        $item['is_vehicle_empty'],
                        $item['vehicle_licence_plate'],
                        $item['date_requested'],
                        $item['status'],
                        $item['drivers'],
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
            $data['s_id'] = generate_sid('pickup');
            // $data['date_requested'] = strtotime($data['date_requested']);

            //return if exists
            if ($pickup_request = $this->checkExist($data['client_id'], $data['estimated_distance'])) return  $pickup_request;

            $pickup_request = (array)$this->db_instance->insert($data)[0];

            $location = $this->constructPosition($pickup_request['location']);
            $destination = $this->constructPosition($pickup_request['destination']);
            return new PickpRequestObject(
                $pickup_request['s_id'],
                $pickup_request['client_id'],
                $pickup_request['driver_id'],
                $location,
                $destination,
                $pickup_request['estimated_distance'],
                $pickup_request['estimated_price'],
                $pickup_request['estimated_duration'],
                $pickup_request['vehicle_type'],
                $pickup_request['is_vehicle_empty'],
                $pickup_request['vehicle_licence_plate'],
                $pickup_request['date_requested'],
                $pickup_request['status'],
                $pickup_request['drivers'],
            );
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function update($s_id, $data): PickpRequestObject
    {
        try {
            $data = array_filter($data, fn ($value) => $value);
            // if(array_key_exists('location',$data)){
            //     $data['location'] = json_encode($data['location']);
            // }
            $pickup_request = $data = supabase_instance()->initializeDatabase('pickup_requests', 's_id')->update($s_id, $data);
            $pickup_request =  (array)$pickup_request[0];

            return new PickpRequestObject(
                $pickup_request['s_id'],
                $pickup_request['client_id'],
                $pickup_request['driver_id'],
                new PositionDTO(lat: $pickup_request['location']->lat, lng: $pickup_request['location']->lng),
                new PositionDTO(lat: $pickup_request['destination']->lat, lng: $pickup_request['destination']->lng),
                $pickup_request['estimated_distance'],
                $pickup_request['estimated_price'],
                $pickup_request['estimated_duration'],
                $pickup_request['vehicle_type'],
                $pickup_request['is_vehicle_empty'],
                $pickup_request['vehicle_licence_plate'],
                $pickup_request['date_requested'],
                $pickup_request['status'],
                $pickup_request['drivers'],
            );
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function constructPosition($position)
    {
        return new PositionDTO(
            $position->place_id,
            $position->zip,
            $position->city,
            $position->search_string,
            $position->lat,
            $position->lng,
        );
    }
    public function checkExist(int $client_id, int $distance): PickpRequestObject|null
    {
        try{
        $query = [
            'select' => '*',
            'from'   => 'pickup_requests',
            'where' =>
            [
                'estimated_distance' => 'eq.' . $distance,
                'status' => 'eq.' . PickupRequestStatus::INITIALIZED->value,
                'client_id' => 'eq.' . $client_id,
            ]
        ];
        $pickup_requests = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
            ->map(function ($pickup_request) {

                $pickup_request = (array) $pickup_request;
                $location = $this->constructPosition($pickup_request['location']);
                $destination = $this->constructPosition($pickup_request['destination']);
                return new PickpRequestObject(
                    $pickup_request['s_id'],
                    $pickup_request['client_id'],
                    $pickup_request['driver_id'],
                    $location,
                    $destination,
                    $pickup_request['estimated_distance'],
                    $pickup_request['estimated_price'],
                    $pickup_request['estimated_duration'],
                    $pickup_request['vehicle_type'],
                    $pickup_request['is_vehicle_empty'],
                    $pickup_request['vehicle_licence_plate'],
                    $pickup_request['date_requested'],
                    $pickup_request['status'],
                    $pickup_request['drivers'],
                );
            });
        return firstOf($pickup_requests);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function confirm(string $s_id, $date_confirmed): PickpRequestObject | null
    {
        try {
            $data = [
                'updated_at' => Date::createFromTimeString($date_confirmed),
                'status' => PickupRequestStatus::PENDING->value
            ];
            return $this->update($s_id, $data);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function approve(string $s_id, int $driver_id): PickpRequestObject | null
    {
        try {
            $data = [
                'driver_id' => $driver_id,
                'status' => PickupRequestStatus::APPROVED->value
            ];
            return $this->update($s_id, $data);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function finish(string $s_id,$date_finished): bool | null
    {
        try {
            $data = [
                'updated_at' => Date::createFromTimeString($date_finished),
                'status' => PickupRequestStatus::VALIDATED->value,
            ];
            return $this->update($s_id, $data) ? true : false;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function cancel(string $s_id, $date_cancelled): bool | null
    {
        try {
            $data = [
                'updated_at' => Date::createFromTimeString($date_cancelled),
                'status' => PickupRequestStatus::CANCELED->value
            ];
            return $this->update($s_id, $data) ? true : false;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function history(int $id, string $type): Collection | null
    {
        try {
            $query = [
                'select' => 'location,destination,date_requested,estimated_distance,estimated_price,estimated_duration,status,due_date,due_address',
                'from'   => 'pickup_requests',
                'where' =>
                [
                    $type . '_id' => 'eq.' . $id,
                    'status' => 'not.eq.pending',
                    // 'status' => 'eq.'.PickupRequestStatus::APPROVED->value,
                ],
                'order' => 'date_requested.desc'
            ];
            $pickups = Collection::make([]);
            $pickup_requests = Collection::make($this->db_instance->createCustomQuery($query)->getResult());
            $pickups = $pickup_requests->filter(fn ($item) => !in_array(!$item->status,['cancelled','approved']));

            $result = Collection::make([]);
            $result['history'] = ($pickups->filter(fn ($item) => !$item->due_date))->values();
            $result['upcoming'] = ($pickups->filter(fn ($item) => $item->due_date))->values();

            return $result;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function rate(string $s_id, int $rating, ?string $rating_comment): bool | null
    {
        try {
            $data = [
                'rating' => $rating,
                'rating_comment' => $rating_comment,
            ];
            return $this->update($s_id, $data) ? true : false;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
