<?php

namespace App\Services\SupaBase\Adminpanel;

use Exception;
use Carbon\Carbon;
use App\Enums\GlobalVars;
use Illuminate\Support\Str;
use App\Contracts\ClientContract;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use App\DataTransferObjects\ClientDTO as CLientObject;

class AdminPanel
{
    private $db_instance;

    public function __construct()
    {
        // $this->client_instance = supabase_instance()->initializeDatabase('clients', 'id');
    }

    public function fetchStatistics(): array
    {
        $data = [];
        $clients = [
            'select' => 'count: id',
            'from'   => 'clients',
        ];
        $today_clients = [
            'select' => 'count: id',
            'from'   => 'clients',
            'where'   => [
                'registered_at'=> 'gte.'.Carbon::today()
            ],
        ];
        $drivers = [
            'select' => 'count: id',
            'from'   => 'drivers',
        ];
        $today_drivers = [
            'select' => 'count: id',
            'from'   => 'drivers',
            'where'   => [
                'registered_at'=> 'gte.'.Carbon::today()
            ],
        ];
        $pickups = [
            'select' => 'count: id',
            'from'   => 'pickup_requests',
        ];
        $today_pickups = [
            'select' => 'count: id',
            'from'   => 'pickup_requests',
            'where'   => [
                'updated_at'=> 'gte.'.Carbon::today()
            ],
        ];
        $data['clients_count'] = count(supabase_instance()->initializeDatabase('clients', 's_id')
        ->createCustomQuery($clients)->getResult());
        $data['today_clients_count'] = count(supabase_instance()->initializeDatabase('clients', 's_id')
        ->createCustomQuery($today_clients)->getResult());
        $data['drivers_count'] = count(supabase_instance()->initializeDatabase('drivers', 's_id')
        ->createCustomQuery($drivers)->getResult());
        $data['today_drivers_count'] = count(supabase_instance()->initializeDatabase('drivers', 's_id')
        ->createCustomQuery($today_drivers)->getResult());
        $data['pickups_count'] = count(supabase_instance()->initializeDatabase('pickups', 's_id')
        ->createCustomQuery($pickups)->getResult());
        $data['today_pickups_count'] = count(supabase_instance()->initializeDatabase('pickups', 's_id')
        ->createCustomQuery($today_pickups)->getResult());
        
        return $data;
        
    }
    public function fetchChartData()
    {
        // $data = [];
  
        // $clients = [
        //     'select' => 'rpc: id',
        //     'from'   => 'clients',
          
        // ];
       
        // $drivers = [
        //     'select' => 'count: id',
        //     'from'   => 'drivers',
        // ];
        // $today_drivers = [
        //     'select' => 'count: id',
        //     'from'   => 'drivers',
        //     'where'   => [
        //         'registered_at'=> 'gte.'.Carbon::today()
        //     ],
        // ];
        // $pickups = [
        //     'select' => 'count: id',
        //     'from'   => 'pickup_requests',
        // ];
        // $today_pickups = [
        //     'select' => 'count: id',
        //     'from'   => 'pickup_requests',
        //     'where'   => [
        //         'updated_at'=> 'gte.'.Carbon::today()
        //     ],
        // ];
        // $data['clients_count'] = count(supabase_instance()->initializeDatabase('clients', 's_id')
        // ->createCustomQuery($clients)->getResult());
        // dd($data['clients_count']);

        // $data['drivers_count'] = count(supabase_instance()->initializeDatabase('drivers', 's_id')
        // ->createCustomQuery($drivers)->getResult());
        // $data['today_drivers_count'] = count(supabase_instance()->initializeDatabase('drivers', 's_id')
        // ->createCustomQuery($today_drivers)->getResult());
        // $data['pickups_count'] = count(supabase_instance()->initializeDatabase('pickups', 's_id')
        // ->createCustomQuery($pickups)->getResult());
        // $data['today_pickups_count'] = count(supabase_instance()->initializeDatabase('pickups', 's_id')
        // ->createCustomQuery($today_pickups)->getResult());
        
        // return $data;
        
    }

    public function findByLike($column, $value): Collection
    {
         
        try {
            $clients = Collection::make($this->db_instance
                                    ->findByLike($column, $value)
                                    ->getResult())
                                    ->map(function ($item) {
                                        $item = (array) $item;
                                        $item['photo'] = Str::contains($item['photo'],'ui-avatars',true) ? $item['photo'] :
                                                    url('storage/'.$item['photo']);
                                        return new ClientObject(
                                            $item['id'],
                                            $item['s_id'],
                                            $item['frist_name'],
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
                                        );
            });

            return $clients;
        } catch (Exception $ex) {
            throw $ex;
        }

    }
    public function findBy($column, $value): Collection
    {
         
        try {
            $clients = Collection::make($this->db_instance
                                    ->findBy($column, $value)
                                    ->getResult()
                                    )
                                    ->map(function ($item) {
                                        $item = (array) $item;
                                        $item['photo'] = Str::contains($item['photo'],'ui-avatars',true) ? $item['photo'] :
                                                    url('storage/'.$item['photo']);
                                        return new ClientObject(
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
            $data['s_id'] = generate_sid('client');
            $data['photo'] = GlobalVars::getDefaultProfilePicture($data['first_name']);

            $client = (array)$this->db_instance->insert($data)[0];
            
            $client['photo'] = Str::contains($client['photo'],'ui-avatars',true)
             ? $client['photo'] :
            url('storage/'.$client['photo']);
            
            return new ClientObject(
                $client['id'],
                $client['s_id'],
                $client['first_name'],
                $client['last_name'],
                $client['phone_number'],
                $client['gender'],
                $client['location'],
                $client['email'],
                $client['photo'],
                $client['messaging_token'],
                $client['reported_count'],
                $client['account_status'],
                $client['registered_at'],
            );

        } catch (Exception $ex) {
            if($ex->getCode() == '409')
            {
                throw ValidationException::withMessages(['phone_number' => 'The phone number has already been taken']);
            }
            throw $ex;
        }

    }
    public function update($s_id, $data): ClientObject
    {
        try {
            $data=array_filter($data, fn ($value) => $value );
            if(array_key_exists('location',$data)){
                $data['location'] = json_encode($data['location']);
            }
            $client = $data = supabase_instance()->initializeDatabase('clients', 's_id')->update($s_id,$data);
            $client =  (array)$client[0];

            $client['photo'] = Str::contains($client['photo'],'ui-avatars',true) ? $client['photo'] :
                     url('storage/'.$client['photo']);

            return new ClientObject(
                $client['id'],
                $client['s_id'],
                $client['first_name'],
                $client['last_name'],
                $client['phone_number'],
                $client['gender'],
                $client['location'],
                $client['email'],
                $client['photo'],
                $client['messaging_token'],
                $client['reported_count'],
                $client['account_status'],
                $client['registered_at'],
            );

        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function updatePhoto($s_id, $photo): string
    {
        $old_photo =null;
        $new_photo =null;

        try {

            $client =  $this->findBy('s_id', $s_id);

            if(isset($photo)) {
                if(isset($client) && $client[0] && file_exists($client[0]->photo)) {
                    $old_photo = $client[0]->photo;
                    unlink($client[0]->photo);
                };
                $new_photo = $photo->storePublicly(
                    "clients/photos",  
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
}
