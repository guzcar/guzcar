<?php

namespace App\Policies;

use App\Models\Ubicacion;
use App\Models\User;

class UbicacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_ubicacion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ubicacion $ubicacion): bool
    {
        return $user->can('view_ubicacion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_ubicacion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ubicacion $ubicacion): bool
    {
        return $user->can('update_ubicacion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ubicacion $ubicacion): bool
    {
        return $user->can('delete_ubicacion');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ubicacion $ubicacion): bool
    {
        return $user->can('restore_ubicacion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ubicacion $ubicacion): bool
    {
        return $user->can('force_delete_ubicacion');
    }
}
