<?php

namespace App\Policies;

use App\Models\TrabajoPagoDetalle;
use App\Models\User;

class TrabajoPagoDetallePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_trabajo::pago::detalle');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrabajoPagoDetalle $trabajoPagoDetalle): bool
    {
        return $user->can('view_trabajo::pago::detalle');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_trabajo::pago::detalle');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrabajoPagoDetalle $trabajoPagoDetalle): bool
    {
        return $user->can('update_trabajo::pago::detalle');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrabajoPagoDetalle $trabajoPagoDetalle): bool
    {
        return $user->can('delete_trabajo::pago::detalle');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrabajoPagoDetalle $trabajoPagoDetalle): bool
    {
        return $user->can('restore_trabajo::pago::detalle');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrabajoPagoDetalle $trabajoPagoDetalle): bool
    {
        return $user->can('force_delete_trabajo::pago::detalle');
    }
}
