<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VehiculoMarca;

class VehiculoMarcaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_vehiculo::marca');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VehiculoMarca $vehiculoMarca): bool
    {
        return $user->can('view_vehiculo::marca');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_vehiculo::marca');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VehiculoMarca $vehiculoMarca): bool
    {
        return $user->can('update_vehiculo::marca');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VehiculoMarca $vehiculoMarca): bool
    {
        return $user->can('delete_vehiculo::marca');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VehiculoMarca $vehiculoMarca): bool
    {
        return $user->can('restore_vehiculo::marca');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VehiculoMarca $vehiculoMarca): bool
    {
        return $user->can('force_delete_vehiculo::marca');
    }
}
