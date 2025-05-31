<?php

namespace App\Policies;

use App\Models\TrabajoDetalle;
use App\Models\User;

class TrabajoDetallePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_trabajo::detalle');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrabajoDetalle $trabajoDetalle): bool
    {
        return $user->can('view_trabajo::detalle');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_trabajo::detalle');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrabajoDetalle $trabajoDetalle): bool
    {
        return $user->can('update_trabajo::detalle');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrabajoDetalle $trabajoDetalle): bool
    {
        return $user->can('delete_trabajo::detalle');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrabajoDetalle $trabajoDetalle): bool
    {
        return $user->can('restore_trabajo::detalle');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrabajoDetalle $trabajoDetalle): bool
    {
        return $user->can('force_delete_trabajo::detalle');
    }
}
