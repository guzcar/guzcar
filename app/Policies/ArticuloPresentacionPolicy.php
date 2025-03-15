<?php

namespace App\Policies;

use App\Models\ArticuloPresentacion;
use App\Models\User;

class ArticuloPresentacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_articulo::presentacion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ArticuloPresentacion $articuloPresentacion): bool
    {
        return $user->can('view_articulo::presentacion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_articulo::presentacion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ArticuloPresentacion $articuloPresentacion): bool
    {
        return $user->can('update_articulo::presentacion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ArticuloPresentacion $articuloPresentacion): bool
    {
        return $user->can('delete_articulo::presentacion');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ArticuloPresentacion $articuloPresentacion): bool
    {
        return $user->can('restore_articulo::presentacion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ArticuloPresentacion $articuloPresentacion): bool
    {
        return $user->can('force_delete_articulo::presentacion');
    }
}
