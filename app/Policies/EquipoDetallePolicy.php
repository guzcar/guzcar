<?php

namespace App\Policies;

use App\Models\EquipoDetalle;
use App\Models\User;

class EquipoDetallePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_equipo::detalle');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EquipoDetalle $equipoDetalle): bool
    {
        return $user->can('view_equipo::detalle');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_equipo::detalle');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EquipoDetalle $equipoDetalle): bool
    {
        return $user->can('update_equipo::detalle');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EquipoDetalle $equipoDetalle): bool
    {
        return $user->can('delete_equipo::detalle');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EquipoDetalle $equipoDetalle): bool
    {
        return $user->can('restore_equipo::detalle');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EquipoDetalle $equipoDetalle): bool
    {
        return $user->can('force_delete_equipo::detalle');
    }
}
