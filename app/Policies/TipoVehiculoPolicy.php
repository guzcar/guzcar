<?php

namespace App\Policies;

use App\Models\TipoVehiculo;
use App\Models\User;

class TipoVehiculoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tipo::vehiculo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TipoVehiculo $tipoVehiculo): bool
    {
        return $user->can('view_tipo::vehiculo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tipo::vehiculo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TipoVehiculo $tipoVehiculo): bool
    {
        return $user->can('update_tipo::vehiculo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TipoVehiculo $tipoVehiculo): bool
    {
        return $user->can('delete_tipo::vehiculo');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TipoVehiculo $tipoVehiculo): bool
    {
        return $user->can('restore_tipo::vehiculo');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TipoVehiculo $tipoVehiculo): bool
    {
        return $user->can('force_delete_tipo::vehiculo');
    }
}
