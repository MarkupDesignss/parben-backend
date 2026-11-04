<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Help extends Model
{
    protected $fillable = ['name', 'email','status', 'phone', 'issue', 'message', 'attachment'];
}
