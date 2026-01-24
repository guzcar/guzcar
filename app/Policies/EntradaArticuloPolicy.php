<?php

namespace App\Policies;

use App\Models\EntradaArticulo;
use App\Models\User;

class EntradaArticuloPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_entrada::articulo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EntradaArticulo $entradaArticulo): bool
    {
        return $user->can('view_entrada::articulo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_entrada::articulo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EntradaArticulo $entradaArticulo): bool
    {
        return $user->can('update_entrada::articulo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EntradaArticulo $entradaArticulo): bool
    {
        return $user->can('delete_entrada::articulo');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EntradaArticulo $entradaArticulo): bool
    {
        return $user->can('restore_entrada::articulo');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EntradaArticulo $entradaArticulo): bool
    {
        return $user->can('force_delete_entrada::articulo');
    }
}
