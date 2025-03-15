<?php

namespace App\Policies;

use App\Models\ArticuloCategoria;
use App\Models\User;

class ArticuloCategoriaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_categoria');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ArticuloCategoria $articuloCategoria): bool
    {
        return $user->can('view_categoria');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_categoria');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ArticuloCategoria $articuloCategoria): bool
    {
        return $user->can('update_categoria');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ArticuloCategoria $articuloCategoria): bool
    {
        return $user->can('delete_categoria');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ArticuloCategoria $articuloCategoria): bool
    {
        return $user->can('restore_categoria');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ArticuloCategoria $articuloCategoria): bool
    {
        return $user->can('force_delete_categoria');
    }
}
