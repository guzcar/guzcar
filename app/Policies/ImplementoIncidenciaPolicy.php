<?php

namespace App\Policies;

use App\Models\ImplementoIncidencia;
use App\Models\User;

class ImplementoIncidenciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_implemento::incidencia');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImplementoIncidencia $implementoIncidencia): bool
    {
        return $user->can('view_implemento::incidencia');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_implemento::incidencia');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImplementoIncidencia $implementoIncidencia): bool
    {
        return $user->can('update_implemento::incidencia');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImplementoIncidencia $implementoIncidencia): bool
    {
        return $user->can('delete_implemento::incidencia');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImplementoIncidencia $implementoIncidencia): bool
    {
        return $user->can('restore_implemento::incidencia');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImplementoIncidencia $implementoIncidencia): bool
    {
        return $user->can('force_delete_implemento::incidencia');
    }
}
