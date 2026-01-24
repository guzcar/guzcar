<?php

namespace App\Policies;

use App\Models\EntregaMaleta;
use App\Models\User;

class EntregaMaletaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_entrega::maleta');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EntregaMaleta $entregaMaleta): bool
    {
        return $user->can('view_entrega::maleta');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_entrega::maleta');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EntregaMaleta $entregaMaleta): bool
    {
        return $user->can('update_entrega::maleta');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EntregaMaleta $entregaMaleta): bool
    {
        return $user->can('delete_entrega::maleta');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EntregaMaleta $entregaMaleta): bool
    {
        return $user->can('restore_entrega::maleta');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EntregaMaleta $entregaMaleta): bool
    {
        return $user->can('force_delete_entrega::maleta');
    }
}
