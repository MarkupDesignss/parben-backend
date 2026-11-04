<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouterCase extends Model
{
    protected $fillable = [
        'investigator_id',
        'case_id',
        'agent_first_name',
        'agent_middle_name',
        'agent_last_name',
        'router_image',
        'zip_path',
        'organization'
    ];
}
