<?php

namespace App\Policies;

use App\Models\ControlMaleta;
use App\Models\User;

class ControlMaletaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_control::maleta');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ControlMaleta $controlMaleta): bool
    {
        return $user->can('view_control::maleta');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_control::maleta');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ControlMaleta $controlMaleta): bool
    {
        return $user->can('update_control::maleta');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ControlMaleta $controlMaleta): bool
    {
        return $user->can('delete_control::maleta');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ControlMaleta $controlMaleta): bool
    {
        return $user->can('restore_control::maleta');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ControlMaleta $controlMaleta): bool
    {
        return $user->can('force_delete_control::maleta');
    }
}
