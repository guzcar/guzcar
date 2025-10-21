<?php

namespace App\Policies;

use App\Models\TrabajoOtro;
use App\Models\User;

class TrabajoOtroPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_trabajo::otro');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrabajoOtro $trabajoOtro): bool
    {
        return $user->can('view_trabajo::otro');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_trabajo::otro');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrabajoOtro $trabajoOtro): bool
    {
        return $user->can('update_trabajo::otro');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrabajoOtro $trabajoOtro): bool
    {
        return $user->can('delete_trabajo::otro');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrabajoOtro $trabajoOtro): bool
    {
        return $user->can('restore_trabajo::otro');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrabajoOtro $trabajoOtro): bool
    {
        return $user->can('force_delete_trabajo::otro');
    }
}
