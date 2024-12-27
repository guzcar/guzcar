<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehiculo;

class VehiculoPolicy
{
    /**
     * Determine whether the user can view any vehicles.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_vehiculo');
    }

    /**
     * Determine whether the user can view the vehicle.
     */
    public function view(User $user, Vehiculo $vehiculo): bool
    {
        return $user->can('view_vehiculo');
    }

    /**
     * Determine whether the user can create vehicles.
     */
    public function create(User $user): bool
    {
        return $user->can('create_vehiculo');
    }

    /**
     * Determine whether the user can update the vehicle.
     */
    public function update(User $user, Vehiculo $vehiculo): bool
    {
        return $user->can('update_vehiculo');
    }

    /**
     * Determine whether the user can delete the vehicle.
     */
    public function delete(User $user, Vehiculo $vehiculo): bool
    {
        return $user->can('delete_vehiculo');
    }

    /**
     * Determine whether the user can restore the vehicle.
     */
    public function restore(User $user, Vehiculo $vehiculo): bool
    {
        return $user->can('restore_vehiculo');
    }

    /**
     * Determine whether the user can permanently delete the vehicle.
     */
    public function forceDelete(User $user, Vehiculo $vehiculo): bool
    {
        return $user->can('force_delete_vehiculo');
    }
}
