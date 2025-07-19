<?php

namespace App\Policies;

use App\Models\TrabajoInforme;
use App\Models\User;

class TrabajoInformePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_trabajo::informe');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrabajoInforme $trabajoInforme): bool
    {
        return $user->can('view_trabajo::informe');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_trabajo::informe');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrabajoInforme $trabajoInforme): bool
    {
        return $user->can('update_trabajo::informe');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrabajoInforme $trabajoInforme): bool
    {
        return $user->can('delete_trabajo::informe');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrabajoInforme $trabajoInforme): bool
    {
        return $user->can('restore_trabajo::informe');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrabajoInforme $trabajoInforme): bool
    {
        return $user->can('force_delete_trabajo::informe');
    }
}
