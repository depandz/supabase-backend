<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;
    use HasApiTokens;
    use Notifiable;
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
}
