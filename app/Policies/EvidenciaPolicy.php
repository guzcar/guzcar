<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Evidencia;

class EvidenciaPolicy
{
    /**
     * Determine whether the user can view any evidence.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_evidencia') || $user->can('update_trabajo');
    }

    /**
     * Determine whether the user can view the evidence.
     */
    public function view(User $user, Evidencia $evidencia): bool
    {
        return $user->can('view_evidencia') || $user->can('update_trabajo');
    }

    /**
     * Determine whether the user can create evidence.
     */
    public function create(User $user): bool
    {
        return $user->can('create_evidencia') || $user->can('update_trabajo');
    }

    /**
     * Determine whether the user can update the evidence.
     */
    public function update(User $user, Evidencia $evidencia): bool
    {
        return $user->can('update_evidencia') || $user->can('update_trabajo');
    }

    /**
     * Determine whether the user can delete the evidence.
     */
    public function delete(User $user, Evidencia $evidencia): bool
    {
        return $user->can('delete_evidencia') || $user->can('update_trabajo');
    }

    /**
     * Determine whether the user can restore the evidence.
     */
    public function restore(User $user, Evidencia $evidencia): bool
    {
        return $user->can('restore_evidencia') || $user->can('update_trabajo');
    }

    /**
     * Determine whether the user can permanently delete the evidence.
     */
    public function forceDelete(User $user, Evidencia $evidencia): bool
    {
        return $user->can('force_delete_evidencia') || $user->can('update_trabajo');
    }
}
