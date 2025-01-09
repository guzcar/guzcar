<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Taller;

class TallerPolicy
{
    /**
     * Determine whether the user can view any workshops.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_taller');
    }

    /**
     * Determine whether the user can view the workshop.
     */
    public function view(User $user, Taller $taller): bool
    {
        return $user->can('view_taller');
    }

    /**
     * Determine whether the user can create workshops.
     */
    public function create(User $user): bool
    {
        return $user->can('create_taller');
    }

    /**
     * Determine whether the user can update the workshop.
     */
    public function update(User $user, Taller $taller): bool
    {
        return $user->can('update_taller');
    }

    /**
     * Determine whether the user can delete the workshop.
     */
    public function delete(User $user, Taller $taller): bool
    {
        return $user->can('delete_taller');
    }

    /**
     * Determine whether the user can restore the workshop.
     */
    public function restore(User $user, Taller $taller): bool
    {
        return $user->can('restore_taller');
    }

    /**
     * Determine whether the user can permanently delete the workshop.
     */
    public function forceDelete(User $user, Taller $taller): bool
    {
        return $user->can('force_delete_taller');
    }
}
