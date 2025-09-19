<?php

namespace App\Policies;

use App\Models\Maleta;
use App\Models\User;

class MaletaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_maleta');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Maleta $maleta): bool
    {
        return $user->can('view_maleta');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_maleta');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Maleta $maleta): bool
    {
        return $user->can('update_maleta');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Maleta $maleta): bool
    {
        return $user->can('delete_maleta');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Maleta $maleta): bool
    {
        return $user->can('restore_maleta');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Maleta $maleta): bool
    {
        return $user->can('force_delete_maleta');
    }
}
