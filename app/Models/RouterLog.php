<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouterLog extends Model
{
    use HasFactory;

    protected $table = 'router_logs';

    protected $fillable = [
        'user_id',
        'case_id',
        'router_id',
        'router_model',
        'log_generated_at',
        'log_content',
        'file_path',
        'file_size',
    ];

    protected $casts = [
        'log_generated_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}