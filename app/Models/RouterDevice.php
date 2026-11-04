<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouterDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'router_id',
        'router_name',
        'router_type',
        'device_name',
        'mac_address',
        'ip_address',
        'device_type',
        'band',
        'tx_rate',
        'rx_rate',
        'duration',
        'is_blocked'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_blocked' => 'boolean',
        'tx_rate' => 'integer',
        'rx_rate' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the device.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include devices of a specific router.
     */
    public function scopeForRouter($query, $routerId)
    {
        return $query->where('router_id', $routerId);
    }

    /**
     * Scope a query to only include blocked or unblocked devices.
     */
    public function scopeBlocked($query, $isBlocked = true)
    {
        return $query->where('is_blocked', $isBlocked);
    }

    /**
     * Scope a query to search by device name or MAC address.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('device_name', 'LIKE', "%{$search}%")
              ->orWhere('mac_address', 'LIKE', "%{$search}%")
              ->orWhere('ip_address', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        return $this->duration;
    }

    /**
     * Check if device is blocked
     */
    public function isBlocked(): bool
    {
        return (bool) $this->is_blocked;
    }
}