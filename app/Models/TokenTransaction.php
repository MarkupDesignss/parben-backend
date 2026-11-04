<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'investigator_id',
        'pack_id',
        'amount',
        'type',
        'reference',
        'description',
        'balance_after',
    ];

    // Relations
    public function investigator()
    {
        return $this->belongsTo(User::class, 'investigator_id');
    }

    public function pack()
    {
        return $this->belongsTo(TokenPack::class, 'pack_id');
    }
}