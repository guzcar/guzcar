<?php

namespace App\Policies;

use App\Models\ArticuloGrupo;
use App\Models\User;

class ArticuloGrupoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_articulo::grupo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ArticuloGrupo $articuloGrupo): bool
    {
        return $user->can('view_articulo::grupo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_articulo::grupo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ArticuloGrupo $articuloGrupo): bool
    {
        return $user->can('update_articulo::grupo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ArticuloGrupo $articuloGrupo): bool
    {
        return $user->can('delete_articulo::grupo');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ArticuloGrupo $articuloGrupo): bool
    {
        return $user->can('restore_articulo::grupo');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ArticuloGrupo $articuloGrupo): bool
    {
        return $user->can('force_delete_articulo::grupo');
    }
}
