<?php

namespace App\Policies;

use App\Models\Cotizacion;
use App\Models\User;

class CotizacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_cotizacion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cotizacion $cotizacion): bool
    {
        return $user->can('view_cotizacion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_cotizacion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cotizacion $cotizacion): bool
    {
        return $user->can('update_cotizacion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cotizacion $cotizacion): bool
    {
        return $user->can('delete_cotizacion');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cotizacion $cotizacion): bool
    {
        return $user->can('restore_cotizacion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cotizacion $cotizacion): bool
    {
        return $user->can('force_delete_cotizacion');
    }
}
