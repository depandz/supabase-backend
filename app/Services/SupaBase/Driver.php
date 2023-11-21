<?php

namespace App\Services\SupaBase;


use DateTime;
use Exception;
use Illuminate\Support\Str;
use App\Enums\AccountStatus;
use App\Contracts\DriverContract;
use Illuminate\Support\Collection;
use App\DataTransferObjects\DriverDTO as DriverObject;
use App\Enums\PickupRequestStatus;

class Driver implements DriverContract
{
    private $db_instance;

    public function __construct()
    {
        $this->db_instance = supabase_instance()->initializeDatabase('drivers', 'id');
    }

    public function fetchAll(): Collection
    {
        
         
        $drivers = Collection::make($this->db_instance->join('companies', 'id')->getResult())
            ->map(function ($item) {
                $item =(array)$item;
                $item['company'] = isset($item['companies']) ? $item['companies'] : null;
                $item['photo'] = Str::startsWith('https://ui-avatars',$item['photo']) ? $item['photo'] :
                                                    url('storage/'.$item['photo']);
                return new DriverObject(
                    $item['id'], $item['s_id'],
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
                    $item['vehicle_type'],
                    $item['identity_card_number'],
                    $item['commercial_register_number'],
                    $item['capacity'],
                    $item['licence_plate'],
                    $item['is_online'],
                    $item['company'],
                    $item['can_transport_goods'],
                );
        });

        return $drivers;
    }
    public function findByProvince(int $province_id): Collection
    {         
        $query = [
            'select' => '*',
            'from'   => 'drivers',
            'where' => 
            [
                'province_id' => 'eq.'.$province_id,
                'is_online' => 'eq.'.true,
                'reported_count' => 'lt.3',
                'account_status' => 'eq.active',
            ]
        ];
        $drivers = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
            ->map(function ($item) {
                $item =(array)$item;
                $item['company'] = isset($item['companies']) ? $item['companies'] : null;

                $rating = supabase_instance()->initializeQueryBuilder()
                                    ->select('rating', ['count' => 'rating', 'head'=> true])
                                    ->from('pickup_requests')
                                    ->where('driver_id', 'eq.'.$item['id'])
                                    ->where('status', 'eq.'.PickupRequestStatus::VALIDATED->value)
                                    ->execute()
                                    ->getResult();
                $item['rating'] = $rating ?? null;
                $item['photo'] = Str::startsWith('https://ui-avatars',$item['photo']) ? $item['photo'] :
                                                    url('storage/'.$item['photo']);
                
                $rating_count = isset($item['rating']) ? count( $item['rating']) : 0;
                $rating_sum = 0;
                foreach( $item['rating'] as $v){
                    $rating_sum+=$v->rating;
                }
                
                $rating_final = $rating_count > 0  ? (round(($rating_sum / $rating_count) *2) /2) : 0;
                return new DriverObject(
                    $item['id'], $item['s_id'], 
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
                    $item['vehicle_type'],
                    $item['identity_card_number'],
                    $item['commercial_register_number'],
                    $item['capacity'],
                    $item['licence_plate'],
                    $item['is_online'],
                    $item['company'],
                    $item['is_default_for_company'],
                    $item['can_transport_goods'],
                    $rating_final
                );
        });

        return $drivers;
    }
    public function findBy($column, $value): Collection
    {
         
        try {
            $query = [
                'select' => '*',
                'from'   => 'drivers',
                'join'   => [
                    [
                        'table' => 'companies',
                        'tablekey' => 'id'
                    ]
                ],
                'where' => 
                [
                    $column => 'eq.'.$value
                ]
            ];
            $drivers = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                                    ->map(function ($item) {
                                        $item = (array) $item;
                                   
                                        $item['company'] = isset($item['companies']) ? $item['companies'] : null;
                                        $item['photo'] = Str::startsWith('https://ui-avatars',$item['photo']) ? $item['photo'] :
                                                    url('storage/'.$item['photo']);
                                        return new DriverObject(
                                            $item['id'], $item['s_id'], 
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
                                            $item['vehicle_type'],
                                            $item['identity_card_number'],
                                            $item['commercial_register_number'],
                                            $item['capacity'],
                                            $item['licence_plate'],
                                            $item['is_online'],
                                            $item['company'],
                                            $item['can_transport_goods'],
                                        );
            });

            return $drivers;
        } catch (Exception $ex) {
            throw $ex;
        }

    }
    public function findByLike($column, $value): Collection
    {
        try {
            $query = [
                'select' => '*',
                'from'   => 'drivers',
                'join'   => [
                    [
                        'table' => 'companies',
                        'tablekey' => 'id'
                    ]
                ],
                'where' => 
                [
                    $column => 'like.%'.$value.'%'
                ]
            ];
            $drivers = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                ->map(function ($item) {
                    $item = (array)$item;
                    $item['company'] = $item['companies'];
                    $item['photo'] = Str::startsWith('https://ui-avatars',$item['photo']) ? $item['photo'] :
                                                    url('storage/'.$item['photo']);
                    return new DriverObject(
                        $item['id'], $item['s_id'], 
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
                        $item['vehicle_type'],
                        $item['identity_card_number'],
                        $item['commercial_register_number'],
                        $item['capacity'],
                        $item['licence_plate'],
                        $item['is_online'],
                        $item['company'],
                        $item['is_default_for_company'],
                        $item['can_transport_goods'],
                    );
                });

            return $drivers;
        } catch (Exception $ex) {
            throw $ex;
        }

    }

    public function insert($data): DriverObject
    {
        return new DriverObject(phone_number :'55445f');
    }
    public function update($s_id, $data): DriverObject
    {
        try {
            $data=array_filter($data, fn ($value) => $value );
            if($data['location']){
                $data['location'] = json_decode($data['location']);
            }
            $query = [
                'select' => '*',
                'from'   => 'drivers',
                'join'   => [
                    [
                        'table' => 'companies',
                        'tablekey' => 'id'
                    ]
                ]
            ];
            $driver = $data = supabase_instance()->initializeDatabase('drivers', 's_id')
            ->createCustomQuery($query)
            ->update($s_id,$data);
            $driver =  (array)$driver[0];

            $driver['company'] = $driver['companies'];
            $driver['photo'] = Str::startsWith('https://ui-avatars',$driver['photo']) ? $driver['photo'] :
                                                    url('storage/'.$driver['photo']);
                return new DriverObject(
                    $driver['id'],
                    $driver['s_id'],
                    $driver['full_name'],
                    $driver['phone_number'],
                    $driver['gender'],
                    $driver['location'],
                    $driver['email'],
                    $driver['photo'],
                    $driver['messaging_token'],
                    $driver['reported_count'],
                    $driver['account_status'],
                    $driver['registered_at'],
                    $driver['vehicle_type'],
                    $driver['identity_card_number'],
                    $driver['commercial_register_number'],
                    $driver['capacity'],
                    $driver['licence_plate'],
                    $driver['is_online'],
                    $driver['company'],
                    $driver['is_default_for_company'],
                    $driver['can_transport_goods'],
                );

        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function updatePhoto($s_id, $request): string
    {
        $old_photo =null;
        $new_photo =null;

        try {

            $driver =  $this->findBy('s_id', $s_id);

            if(isset($photo)) {
                if(isset($driver) && $driver[0] && file_exists($driver[0]->photo)) {
                    $old_photo = $driver[0]->photo;
                    unlink($driver[0]->photo);
                };
                $new_photo = $photo->storePublicly(
                    "drivers",
                    ['disk' => 'public']
                );
            }
            $this->update($s_id, array('photo'=>$new_photo));
            return $new_photo;
        }
        catch(Exception $ex){
            $this->update($s_id, array('photo'=>$old_photo));
            throw $ex;
        }
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
