<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Contact;

class ContactPolicy
{
    public function bulkDelete(User $user)
    {
        return $user->role === 'admin';
    }

    // Optional: allow all other actions for admin
    public function before(User $user)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }
}