<?php

namespace App\Policies;

use App\Models\Entrada;
use App\Models\User;

class EntradaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_entrada');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Entrada $entrada): bool
    {
        return $user->can('view_entrada');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_entrada');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Entrada $entrada): bool
    {
        return $user->can('update_entrada');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Entrada $entrada): bool
    {
        return $user->can('delete_entrada');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Entrada $entrada): bool
    {
        return $user->can('restore_entrada');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Entrada $entrada): bool
    {
        return $user->can('force_delete_entrada');
    }
}
