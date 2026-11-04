<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'investigator_id',
        'balance',
    ];

    // Relation with User (Investigator)
    public function investigator()
    {
        return $this->belongsTo(User::class, 'investigator_id');
    }

    // Relation with Transactions
    public function transactions()
    {
        return $this->hasMany(TokenTransaction::class, 'investigator_id');
    }
}