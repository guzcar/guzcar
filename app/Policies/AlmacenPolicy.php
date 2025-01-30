<?php

namespace App\Policies;

use App\Models\Almacen;
use App\Models\User;

class AlmacenPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_almacen');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Almacen $almacen): bool
    {
        return $user->can('view_almacen');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_almacen');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Almacen $almacen): bool
    {
        return $user->can('update_almacen');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Almacen $almacen): bool
    {
        return $user->can('delete_almacen');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Almacen $almacen): bool
    {
        return $user->can('restore_almacen');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Almacen $almacen): bool
    {
        return $user->can('force_delete_almacen');
    }
}
