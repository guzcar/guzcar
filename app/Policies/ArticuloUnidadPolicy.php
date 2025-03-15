<?php

namespace App\Policies;

use App\Models\ArticuloUnidad;
use App\Models\User;

class ArticuloUnidadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_articulo::unidad');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ArticuloUnidad $articuloUnidad): bool
    {
        return $user->can('view_articulo::unidad');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_articulo::unidad');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ArticuloUnidad $articuloUnidad): bool
    {
        return $user->can('update_articulo::unidad');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ArticuloUnidad $articuloUnidad): bool
    {
        return $user->can('delete_articulo::unidad');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ArticuloUnidad $articuloUnidad): bool
    {
        return $user->can('restore_articulo::unidad');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ArticuloUnidad $articuloUnidad): bool
    {
        return $user->can('force_delete_articulo::unidad');
    }
}
