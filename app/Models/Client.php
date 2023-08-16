<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
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
}
