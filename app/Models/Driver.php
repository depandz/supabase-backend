<?php

namespace App\Models;

use Sushi\Sushi;
use Illuminate\Support\Arr;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Services\SupaBase\Adminpanel\PanelDrivers;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;
    use HasApiTokens;
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
        'identity_card_number',
        'licence_plate',
        'photo',
        'province_id',
        'location',
        'email',
        'is_online',
        'reported_count',
        'messaging_token',
        'account_status',
        'vehicle_type',
        'commercial_register_number',
        'capacity',
        'company_id',
        'is_default_for_company',
        'can_transport_goods',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'licence_plate' => 'integer',
        'province_id' => 'integer',
        'location' => 'array',
        'is_online' => 'boolean',
        'commercial_register_number' => 'integer',
        'company_id' => 'integer',
        'is_default_for_company' => 'boolean',
        'can_transport_goods' => 'boolean',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        //API
        $drivers = (new PanelDrivers())->fetchAll();
 
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
            'identity_card_number',
            'licence_plate',
            'photo',
            'email',
            'reported_count',
            'account_status',
            'vehicle_type',
            'commercial_register_number',
            'capacity',
            'can_transport_goods',
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
