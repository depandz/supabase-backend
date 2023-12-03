<?php

namespace App\Models;

use Sushi\Sushi;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\SupaBase\Adminpanel\PanelClients;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    use HasApiTokens;
    use SoftDeletes;
    use Notifiable;
    use Sushi;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        's_id',
        'full_name',
        'phone_number',
        'gender',
        'location',
        'email',
        'photo',
        'messaging_token',
        'reported_count',
        'account_status',
        'registered_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'location' => 'array',
        'photo' => 'integer',
        'registered_at' => 'datetime',
    ];
        /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        //API
        $drivers = (new PanelClients())->fetchAll();
 
        //filtering some attributes
        $drivers = $drivers->map(function ($item) {
            return collect((array)$item)
        ->only([
            'id',
            's_id',
            'first_name',
            'last_name',
            'phone_number',
            'gender',
            'photo',
            'email',
            'reported_count',
            'account_status',
            'registered_at',
            'deleted_at',
        ])
        ->all();
            // return [
            //     's_id',
            //     'full_name',
            //     'phone_number',
            //     'gender',
            //     'identity_card_number',
            //     'licence_plate',
            //     'photo',
            //     'email',
            //     'reported_count',
            //     'account_status',
            //     'vehicle_type',
            //     'commercial_register_number',
            //     'capacity',
            //     'can_transport_goods',
            // ];
        });

        return $drivers->toArray();
    }
}
