<?php

namespace App\Services\SupaBase\AdminPanel;

use Exception;
use Carbon\Carbon;
use App\Enums\GlobalVars;
use Illuminate\Support\Str;
use App\Enums\AccountStatus;
use App\Enums\PickupRequestStatus;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use App\DataTransferObjects\PanelClientDTO as ClientObject;
use App\Contracts\Admin\ClientContract as AdminClientsContract;

class PanelClients implements AdminClientsContract
{
    private $db_instance;

    public function __construct()
    {
        $this->db_instance = supabase_instance()->initializeDatabase('clients', 'id');
    }

    public function fetchAll(): Collection
    {

        try {
            $query = [
                'select' => '*',
                'from'   => 'clients',
            ];
            $clients = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                ->map(function ($item) {
                    $item = (array)$item;
                    
                    $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                        url('storage/' . $item['photo']);
                    return new ClientObject( 
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
                        $item['registered_at']
                    );
                });

            return $clients;
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
            'from'   => 'clients',
            'where' =>
            [
                'province_id' => 'eq.' . $province_id,
                'is_online' => 'eq.' . true,
                'reported_count' => 'lt.3',
                'account_status' => 'eq.active',
            ]
        ];
        $clients = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
            ->map(function ($item) {
                $item = (array)$item;
                

                $rating = supabase_instance()->initializeQueryBuilder()
                    ->select('rating', ['count' => 'rating', 'head' => true])
                    ->from('pickup_requests')
                    ->where('client_id', 'eq.' . $item['id'])
                    ->where('status', 'eq.' . PickupRequestStatus::VALIDATED->value)
                    ->execute()
                    ->getResult();
                $item['rating'] = $rating ?? null;
                $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                    url('storage/' . $item['photo']);

                return new ClientObject(
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
                    $item['registered_at']
                );
            });

        return $clients;
    }
    public function findBy($column, $value): Collection
    {

        try {
            $query = [
                'select' => '*',
                'from'   => 'clients',
                'where' =>
                [
                    $column => 'eq.' . $value
                ]
            ];
            $clients = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                ->map(function ($item) {
                    $item = (array) $item;

                    
                    $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                        url('storage/' . $item['photo']);
                    return new ClientObject(
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
                        $item['registered_at']
                    );
                });

            return $clients;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function findByLike($column, $value): Collection
    {
        try {
            $query = [
                'select' => '*',
                'from'   => 'clients',
                'where' =>
                [
                    $column => 'like.%' . $value . '%'
                ]
            ];
            $clients = Collection::make($this->db_instance->createCustomQuery($query)->getResult())
                ->map(function ($item) {
                    $item = (array)$item;
                    $item['photo'] = Str::contains($item['photo'], 'ui-avatars', true) ? $item['photo'] :
                        url('storage/' . $item['photo']);
                    return new ClientObject(
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
                        $item['registered_at']
                    );
                });

            return $clients;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function insert($data): ClientObject
    {
        try {
            // $data = array_filter($data, fn ($value) => $value);
            $data['s_id'] = generate_sid('client');
            $data['photo'] =  GlobalVars::getDefaultProfilePicture($data['first_name']);

            $client = $data = supabase_instance()->initializeDatabase('clients', 's_id')
                ->insert($data);
            $client =  (array)$client[0];

            
            $client['photo'] = Str::contains($client['photo'], 'ui-avatars', true) ? $client['photo'] :
                url('storage/' . $client['photo']);
            return new ClientObject(
                $client['id'],
                $client['s_id'],
                $client['first_name'],
                $client['last_name'],
                $client['phone_number'],
                $client['gender'],
                $client['email'],
                $client['photo'],
                $client['reported_count'],
                $client['account_status'],
                $client['registered_at']
            );
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function update($s_id, $data): ClientObject
    {
        try {
            $data = array_filter($data, fn ($value) => $value);

            $client = $data = supabase_instance()->initializeDatabase('clients', 's_id')
                ->update($s_id, $data);
            $client =  (array)$client[0];
            
            $client['photo'] = Str::contains($client['photo'], 'ui-avatars', true) ? $client['photo'] :
                url('storage/' . $client['photo']);
            return new ClientObject(
                $client['id'],
                $client['s_id'],
                $client['first_name'],
                $client['last_name'],
                $client['phone_number'],
                $client['gender'],
                $client['email'],
                $client['photo'],
                $client['reported_count'],
                $client['account_status'],
                $client['registered_at']
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

            $client =  supabase_instance()->initializeDatabase('clients', 's_id')
                ->update($s_id, $data);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    public function delete(string $s_id): bool|null
    {
        $data = ['deleted_at' => now()];
        try {

            $client =  supabase_instance()->initializeDatabase('clients', 's_id')
                ->update($s_id, $data);
            return true;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
