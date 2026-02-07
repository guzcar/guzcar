<?php

namespace App\Policies;

use App\Models\EntregaEquipo;
use App\Models\User;

class EntregaEquipoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_entrega::equipo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EntregaEquipo $entregaEquipo): bool
    {
        return $user->can('view_entrega::equipo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_entrega::equipo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EntregaEquipo $entregaEquipo): bool
    {
        return $user->can('update_entrega::equipo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EntregaEquipo $entregaEquipo): bool
    {
        return $user->can('delete_entrega::equipo');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EntregaEquipo $entregaEquipo): bool
    {
        return $user->can('restore_entrega::equipo');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EntregaEquipo $entregaEquipo): bool
    {
        return $user->can('force_delete_entrega::equipo');
    }
}
