<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Trabajo;

class TrabajoPolicy
{
    /**
     * Determine whether the user can view any jobs.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_trabajo');
    }

    /**
     * Determine whether the user can view the job.
     */
    public function view(User $user, Trabajo $trabajo): bool
    {
        return $user->can('view_trabajo');
    }

    /**
     * Determine whether the user can create jobs.
     */
    public function create(User $user): bool
    {
        return $user->can('create_trabajo');
    }

    /**
     * Determine whether the user can update the job.
     */
    public function update(User $user, Trabajo $trabajo): bool
    {
        return $user->can('update_trabajo');
    }

    /**
     * Determine whether the user can delete the job.
     */
    public function delete(User $user, Trabajo $trabajo): bool
    {
        return $user->can('delete_trabajo');
    }

    /**
     * Determine whether the user can restore the job.
     */
    public function restore(User $user, Trabajo $trabajo): bool
    {
        return $user->can('restore_trabajo');
    }

    /**
     * Determine whether the user can permanently delete the job.
     */
    public function forceDelete(User $user, Trabajo $trabajo): bool
    {
        return $user->can('force_delete_trabajo');
    }
}
