<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sid',
        'client_id',
        'driver_id',
        'destination',
        'location',
        'date_requested',
        'estimated_price',
        'estimated_distance',
        'estimated_time',
        'status',
        'total',
        'vehicle_type',
        'is_vehicle_empty',
        'vehicle_licence_plate',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'client_id' => 'integer',
        'driver_id' => 'integer',
        'destination' => 'array',
        'location' => 'array',
        'date_requested' => 'datetime',
        'estimated_price' => 'double',
        'estimated_distance' => 'float',
        'total' => 'double',
        'is_vehicle_empty' => 'boolean',
        'vehicle_licence_plate' => 'integer',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
