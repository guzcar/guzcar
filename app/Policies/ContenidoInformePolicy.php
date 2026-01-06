<?php

namespace App\Policies;

use App\Models\ContenidoInforme;
use App\Models\User;

class ContenidoInformePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_contenido::informe');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContenidoInforme $contenidoInforme): bool
    {
        return $user->can('view_contenido::informe');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_contenido::informe');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContenidoInforme $contenidoInforme): bool
    {
        return $user->can('update_contenido::informe');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContenidoInforme $contenidoInforme): bool
    {
        return $user->can('delete_contenido::informe');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContenidoInforme $contenidoInforme): bool
    {
        return $user->can('restore_contenido::informe');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContenidoInforme $contenidoInforme): bool
    {
        return $user->can('force_delete_contenido::informe');
    }
}
