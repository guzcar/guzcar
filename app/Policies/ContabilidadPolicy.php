<?php

namespace App\Policies;

use App\Models\Trabajo;
use App\Models\User;

class ContabilidadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_contabilidad');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Trabajo $trabajo): bool
    {
        return $user->can('view_contabilidad');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_contabilidad');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Trabajo $trabajo): bool
    {
        return $user->can('update_contabilidad');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Trabajo $trabajo): bool
    {
        return $user->can('delete_contabilidad');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Trabajo $trabajo): bool
    {
        return $user->can('restore_contabilidad');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Trabajo $trabajo): bool
    {
        return $user->can('force_delete_contabilidad');
    }
}
