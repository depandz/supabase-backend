<?php

namespace App\Services\SupaBase;


use DateTime;
use Exception;
use App\Enums\GlobalVars;
use Illuminate\Support\Str;
use App\Enums\AccountStatus;
use App\Contracts\DriverContract;
use App\Enums\PickupRequestStatus;
use Illuminate\Support\Collection;
use App\DataTransferObjects\DriverDTO as DriverObject;

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
                $item = (array)$item;
                $item['company'] = isset($item['companies']) ? $item['companies'] : null;
                $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                    url('storage/' . $item['photo']);
                $item['full_name']  = $item['first_name'] . ' ' . $item['last_name'];
                return new DriverObject(
                    $item['id'],
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
                'province_id' => 'eq.' . $province_id,
                'is_online' => 'eq.' . true,
                'reported_count' => 'lt.3',
                'account_status' => 'eq.active',
            ]
        ];
        $drivers = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
            ->map(function ($item) {
                $item = (array)$item;
                $item['company'] = isset($item['companies']) ? $item['companies'] : null;

                $rating = supabase_instance()->initializeQueryBuilder()
                    ->select('rating', ['count' => 'rating', 'head' => true])
                    ->from('pickup_requests')
                    ->where('driver_id', 'eq.' . $item['id'])
                    ->where('status', 'eq.' . PickupRequestStatus::VALIDATED->value)
                    ->execute()
                    ->getResult();
                $item['rating'] = $rating ?? null;
                $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                    url('storage/' . $item['photo']);

                $rating_count = isset($item['rating']) ? count($item['rating']) : 0;
                $rating_sum = 0;
                foreach ($item['rating'] as $v) {
                    $rating_sum += $v->rating;
                }

                $rating_final = $rating_count > 0  ? (round(($rating_sum / $rating_count) * 2) / 2) : 0;
                $item['full_name']  = $item['first_name'] . ' ' . $item['last_name'];
                return new DriverObject(
                    $item['id'],
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
            if(count($drivers)){
                $drivers[1] =$drivers[0];
               $drivers[2] =$drivers[0];
               $drivers[3] =$drivers[0];
               $drivers[4] =$drivers[0];
                   }
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
                    $column => 'eq.' . $value
                ]
            ];
            $drivers = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                ->map(function ($item) {
                    $item = (array) $item;

                    $item['company'] = isset($item['companies']) ? $item['companies'] : null;
                    $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                        url('storage/' . $item['photo']);
                    $item['full_name']  = $item['first_name'] . ' ' . $item['last_name'];
                    return new DriverObject(
                        $item['id'],
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
                    $column => 'like.%' . $value . '%'
                ]
            ];
            $drivers = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                ->map(function ($item) {
                    $item = (array)$item;
                    $item['company'] = $item['companies'];
                    $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                        url('storage/' . $item['photo']);
                    $item['full_name']  = $item['first_name'] . ' ' . $item['last_name'];
                    return new DriverObject(
                        $item['id'],
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
        return new DriverObject(phone_number: '55445f');
    }
    public function update($s_id, $data): DriverObject
    {
        try {
            $data = array_filter($data, fn ($value) => $value);
            if ($data['location']) {
                $data['location'] = json_decode($data['location']);
            }
            $driver  = supabase_instance()->initializeDatabase('drivers', 's_id')->update($s_id, $data);
            $driver =  (array)$driver[0];

            $driver['photo'] = Str::contains($driver['photo'], 'ui-avatars', true) ? $driver['photo'] :

                $driver['company'] = $driver['companies'];

            $item['full_name']  = $driver['first_name'] . ' ' . $driver['last_name'];
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

    public function updatePhoto($s_id, $photo): string
    {
        $old_photo = null;
        $driver =  $this->findBy('s_id', $s_id);

        try {

            if(isset($photo)) {
                if(isset($driver) && $driver[0] && file_exists($driver[0]->photo)) {
                    $old_photo = $driver[0]->photo;
                    unlink($driver[0]->photo);
                };
                $new_photo = $photo->storePublicly(
                    "drivers/photos",
                    ['disk' => 'public']
                );
                 $drivers = supabase_instance()->initializeDatabase('drivers', 's_id')->update($s_id,array('photo'=>$new_photo = $new_photo));
                 return url('storage/'.$drivers[0]?->photo) ?? '';
            }
           return $driver[0]?->photo ?? '';
        } catch (Exception $ex) {
            $this->update($s_id, array('photo'=> GlobalVars::getDefaultProfilePicture($driver[0]?->first_name ?? 'Test Name')));
            throw $ex;
        }
    }
    public function suspend($id): void
    {
        $this->update($id, ['account_status' => AccountStatus::$SUSPENDED]);
    }
    public function activateAccount($id): void
    {
        $this->update($id, ['account_status' => AccountStatus::$ACTIVE]);
    }
    public function switchOnlineStatus(string $s_id, bool $status): bool|null
    {
        try {
            $data = ['is_online'=>$status];
            supabase_instance()->initializeDatabase('drivers', 's_id')->update($s_id, $data);
            return true;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
