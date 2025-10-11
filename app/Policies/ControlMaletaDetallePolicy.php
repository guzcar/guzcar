<?php

namespace App\Policies;

use App\Models\ControlMaletaDetalle;
use App\Models\User;

class ControlMaletaDetallePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_control::maleta::detalle');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ControlMaletaDetalle $controlMaletaDetalle): bool
    {
        return $user->can('view_control::maleta::detalle');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_control::maleta::detalle');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ControlMaletaDetalle $controlMaletaDetalle): bool
    {
        return $user->can('update_control::maleta::detalle');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ControlMaletaDetalle $controlMaletaDetalle): bool
    {
        return $user->can('delete_control::maleta::detalle');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ControlMaletaDetalle $controlMaletaDetalle): bool
    {
        return $user->can('restore_control::maleta::detalle');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ControlMaletaDetalle $controlMaletaDetalle): bool
    {
        return $user->can('force_delete_control::maleta::detalle');
    }
}
