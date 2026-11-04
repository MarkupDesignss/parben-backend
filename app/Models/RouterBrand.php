<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouterBrand extends Model
{
    // Primary key is 'id' (string) – no auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name', 'logo', 'website', 'injector',
        'description', 'color', 'router_type', 'default_router_id', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Optionally hide timestamps if not required in API response
    // protected $hidden = ['created_at', 'updated_at'];
}