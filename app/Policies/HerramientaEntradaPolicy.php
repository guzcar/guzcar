<?php

namespace App\Policies;

use App\Models\HerramientaEntrada;
use App\Models\User;

class HerramientaEntradaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_herramienta::entrada');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HerramientaEntrada $herramientaEntrada): bool
    {
        return $user->can('view_herramienta::entrada');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_herramienta::entrada');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HerramientaEntrada $herramientaEntrada): bool
    {
        return $user->can('update_herramienta::entrada');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HerramientaEntrada $herramientaEntrada): bool
    {
        return $user->can('delete_herramienta::entrada');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HerramientaEntrada $herramientaEntrada): bool
    {
        return $user->can('restore_herramienta::entrada');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HerramientaEntrada $herramientaEntrada): bool
    {
        return $user->can('force_delete_herramienta::entrada');
    }
}
