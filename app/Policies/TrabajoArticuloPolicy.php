<?php

namespace App\Policies;

use App\Models\TrabajoArticulo;
use App\Models\User;

class TrabajoArticuloPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_salida');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrabajoArticulo $trabajoArticulo): bool
    {
        return $user->can('view_salida');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_salida');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrabajoArticulo $trabajoArticulo): bool
    {
        return $user->can('update_salida');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrabajoArticulo $trabajoArticulo): bool
    {
        return $user->can('delete_salida');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrabajoArticulo $trabajoArticulo): bool
    {
        return $user->can('restore_salida');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrabajoArticulo $trabajoArticulo): bool
    {
        return $user->can('force_delete_salida');
    }
}
