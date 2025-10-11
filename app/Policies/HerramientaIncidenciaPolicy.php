<?php

namespace App\Policies;

use App\Models\HerramientaIncidencia;
use App\Models\User;

class HerramientaIncidenciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_herramienta::incidencia');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HerramientaIncidencia $herramientaEntrada): bool
    {
        return $user->can('view_herramienta::incidencia');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_herramienta::incidencia');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HerramientaIncidencia $herramientaEntrada): bool
    {
        return $user->can('update_herramienta::incidencia');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HerramientaIncidencia $herramientaEntrada): bool
    {
        return $user->can('delete_herramienta::incidencia');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HerramientaIncidencia $herramientaEntrada): bool
    {
        return $user->can('restore_herramienta::incidencia');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HerramientaIncidencia $herramientaEntrada): bool
    {
        return $user->can('force_delete_herramienta::incidencia');
    }
}
