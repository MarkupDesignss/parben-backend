<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnsupportedDeviceReport extends Model
{
    use HasFactory;

    protected $table = 'unsupported_device_reports';

    protected $fillable = [
        'user_id',
        'router_brand',
        'router_model',
        'router_id',
        'router_ip',
        'platform',
        'app_version',
        'os_version',
        'capture_failed',
        'failure_reason',
        'router_page',
        'diagnostic_timestamp',
        'email_sent',
        'email_sent_at',
        'status',
    ];

    protected $casts = [
        'capture_failed'       => 'boolean',
        'email_sent'           => 'boolean',
        'email_sent_at'        => 'datetime',
        'diagnostic_timestamp' => 'datetime',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    /**
     * Relationship: Report belongs to a user (investigator).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Only pending reports.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Reports jinka email abhi tak nahi bheja gaya.
     */
    public function scopeEmailNotSent($query)
    {
        return $query->where('email_sent', false);
    }
}