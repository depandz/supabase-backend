<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'sub_title',
        'slug',
        'content',
        'is_publishable',
        'language',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_publishable'=>'boolean'
    ];
    /**
     * The attributes that should be hidden.
     *
     * @var array
     */
    protected $hidden = [
        'created_at','updated_at','language_id','is_publishable'
    ];

    //----------------------------Relationships--------------------
    // public function language():BelongsTo
    // {
    //     return $this->belongsTo(Language::class);
    // }
}
