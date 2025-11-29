<?php

namespace App\Policies;

use App\Models\Cronograma;
use App\Models\User;

class CronogramaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_cronograma');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cronograma $cronograma): bool
    {
        return $user->can('view_cronograma');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_cronograma');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cronograma $cronograma): bool
    {
        return $user->can('update_cronograma');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cronograma $cronograma): bool
    {
        return $user->can('delete_cronograma');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cronograma $cronograma): bool
    {
        return $user->can('restore_cronograma');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cronograma $cronograma): bool
    {
        return $user->can('force_delete_cronograma');
    }
}
