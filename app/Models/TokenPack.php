<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenPack extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token_count',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relations
    public function transactions()
    {
        return $this->hasMany(TokenTransaction::class, 'pack_id');
    }
}