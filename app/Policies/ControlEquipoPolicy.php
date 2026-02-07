<?php

namespace App\Policies;

use App\Models\ControlEquipo;
use App\Models\User;

class ControlEquipoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_control::equipo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ControlEquipo $controlEquipo): bool
    {
        return $user->can('view_control::equipo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_control::equipo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ControlEquipo $controlEquipo): bool
    {
        return $user->can('update_control::equipo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ControlEquipo $controlEquipo): bool
    {
        return $user->can('delete_control::equipo');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ControlEquipo $controlEquipo): bool
    {
        return $user->can('restore_control::equipo');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ControlEquipo $controlEquipo): bool
    {
        return $user->can('force_delete_control::equipo');
    }
}
