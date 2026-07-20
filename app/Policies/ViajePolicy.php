<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Viaje;

class ViajePolicy
{
    public function update(User $user, Viaje $viaje): bool
    {
        return $user->id === $viaje->user_id || $user->isAdmin();
    }

    public function delete(User $user, Viaje $viaje): bool
    {
        return $user->id === $viaje->user_id || $user->isAdmin();
    }
}
