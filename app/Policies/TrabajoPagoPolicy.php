<?php

namespace App\Policies;

use App\Models\TrabajoPago;
use App\Models\User;

class TrabajoPagoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_trabajo::pago');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrabajoPago $trabajoPago): bool
    {
        return $user->can('view_trabajo::pago');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_trabajo::pago');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrabajoPago $trabajoPago): bool
    {
        return $user->can('update_trabajo::pago');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrabajoPago $trabajoPago): bool
    {
        return $user->can('delete_trabajo::pago');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrabajoPago $trabajoPago): bool
    {
        return $user->can('restore_trabajo::pago');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrabajoPago $trabajoPago): bool
    {
        return $user->can('force_delete_trabajo::pago');
    }
}
