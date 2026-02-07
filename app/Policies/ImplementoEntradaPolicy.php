<?php

namespace App\Policies;

use App\Models\ImplementoEntrada;
use App\Models\User;

class ImplementoEntradaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_implemento::entrada');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImplementoEntrada $implementoEntrada): bool
    {
        return $user->can('view_implemento::entrada');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_implemento::entrada');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImplementoEntrada $implementoEntrada): bool
    {
        return $user->can('update_implemento::entrada');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImplementoEntrada $implementoEntrada): bool
    {
        return $user->can('delete_implemento::entrada');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImplementoEntrada $implementoEntrada): bool
    {
        return $user->can('restore_implemento::entrada');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImplementoEntrada $implementoEntrada): bool
    {
        return $user->can('force_delete_implemento::entrada');
    }
}
