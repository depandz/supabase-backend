<?php

namespace App\Services\SupaBase\Adminpanel;

use Exception;
use Carbon\Carbon;
use App\Enums\GlobalVars;
use Illuminate\Support\Str;
use App\Enums\AccountStatus;
use App\Enums\PickupRequestStatus;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use App\DataTransferObjects\PanelDriverDTO as DriverObject;
use App\Contracts\Admin\DriverContract as AdminDriversContract;

class PanelDrivers implements AdminDriversContract
{
    private $db_instance;

    public function __construct()
    {
        $this->db_instance = supabase_instance()->initializeDatabase('drivers', 'id');
    }

    public function fetchAll(): Collection
    {

        try {
            $query = [
                'select' => '*',
                'from'   => 'drivers',
            ];
            $drivers = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                ->map(function ($item) {
                    $item = (array)$item;
                    $item['company'] = isset($item['companies']) ? $item['companies'] : null;
                    $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                        url('storage/' . $item['photo']);
                    return new DriverObject(
                        $item['id'],
                        $item['s_id'],
                        $item['first_name'],
                        $item['last_name'],
                        $item['phone_number'],
                        $item['gender'],
                        $item['email'],
                        $item['photo'],
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
                        can_transport_goods: $item['can_transport_goods'],
                        deleted_at: $item['deleted_at'],
                        province_id: $item['province_id'],
                    );
                });

            return $drivers;
        } catch (Exception $ex) {
            if ($ex->getCode() == 401) {
                authenticate_user();
            }
            return Collection::make([]);
        }
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
                return new DriverObject(
                    $item['id'],
                    $item['s_id'],
                    $item['first_name'],
                    $item['last_name'],
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
                    $column => 'eq.' . $value
                ]
            ];
            $drivers = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                ->map(function ($item) {
                    $item = (array) $item;

                    $item['company'] = isset($item['companies']) ? $item['companies'] : null;
                    $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                        url('storage/' . $item['photo']);
                    return new DriverObject(
                        $item['id'],
                        $item['s_id'],
                        $item['first_name'],
                        $item['last_name'],
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
                    return new DriverObject(
                        $item['id'],
                        $item['s_id'],
                        $item['first_name'],
                        $item['last_name'],
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
        try {
            // $data = array_filter($data, fn ($value) => $value);
            $data['s_id'] = generate_sid('driver');
            $data['photo'] =  GlobalVars::getDefaultProfilePicture($data['first_name']);

            $driver = $data = supabase_instance()->initializeDatabase('drivers', 's_id')
                ->insert($data);
            $driver =  (array)$driver[0];

            $driver['company'] = $driver['companies'] ?? null;
            $driver['photo'] = Str::contains($driver['photo'], 'ui-avatars', true) ? $driver['photo'] :
                url('storage/' . $driver['photo']);
            return new DriverObject(
                $driver['id'],
                $driver['s_id'],
                $driver['first_name'],
                $driver['last_name'],
                $driver['phone_number'],
                $driver['gender'],
                $driver['email'],
                $driver['photo'],
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
                can_transport_goods: $driver['can_transport_goods'],
                deleted_at: $driver['deleted_at'],
                province_id: $driver['province_id'],
            );
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function update($s_id, $data): DriverObject
    {
        try {
            $data = array_filter($data, fn ($value) => $value);

            $driver = $data = supabase_instance()->initializeDatabase('drivers', 's_id')
                ->update($s_id, $data);
            $driver =  (array)$driver[0];
            $driver['company'] = $driver['companies'] ?? null;
            $driver['photo'] = Str::contains($driver['photo'], 'ui-avatars', true) ? $driver['photo'] :
                url('storage/' . $driver['photo']);
            return new DriverObject(
                $driver['id'],
                $driver['s_id'],
                $driver['first_name'],
                $driver['last_name'],
                $driver['phone_number'],
                $driver['gender'],
                $driver['email'],
                $driver['photo'],
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
                can_transport_goods: $driver['can_transport_goods'],
                deleted_at: $driver['deleted_at'],
                province_id: $driver['province_id'],
            );
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    public function suspend(string $s_id): void
    {
        try {
            $this->update($s_id, ['account_status' => AccountStatus::$SUSPENDED]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function activateAccount($s_id): void
    {
        try {
            $this->update($s_id, ['account_status' => AccountStatus::$ACTIVE]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function restore($s_id): void
    {
        $data = ['deleted_at' => null];  
        try {

            $driver =  supabase_instance()->initializeDatabase('drivers', 's_id')
                ->update($s_id, $data);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function delete(string $s_id): bool|null
    {
        $data = ['deleted_at' => now()];
        try {

            $driver =  supabase_instance()->initializeDatabase('drivers', 's_id')
                ->update($s_id, $data);
            return true;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
