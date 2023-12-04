<?php

namespace App\Models;

use App\Services\SupaBase\AdminPanel\PanelProvincesFees;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sushi\Sushi;

class Fee extends Model
{
    use HasFactory,Sushi,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'province_id',
        'heavy',
        'light',
        'truck',
        'full_percentage',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'province_id' => 'integer',
        'heavy' => 'float',
        'light' => 'float',
        'truck' => 'float',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
    /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        //API
        $drivers = (new PanelProvincesFees())->fetchAll();
 
        //filtering some attributes
        $drivers = $drivers->map(function ($item) {
            return collect((array)$item)->all();
        });

        return $drivers->toArray();
    }
}
