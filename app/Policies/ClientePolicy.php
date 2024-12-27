<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    /**
     * Determine whether the user can view any clients.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_cliente');
    }

    /**
     * Determine whether the user can view the client.
     */
    public function view(User $user, Cliente $cliente): bool
    {
        return $user->can('view_cliente');
    }

    /**
     * Determine whether the user can create clients.
     */
    public function create(User $user): bool
    {
        return $user->can('create_cliente');
    }

    /**
     * Determine whether the user can update the client.
     */
    public function update(User $user, Cliente $cliente): bool
    {
        return $user->can('update_cliente');
    }

    /**
     * Determine whether the user can delete the client.
     */
    public function delete(User $user, Cliente $cliente): bool
    {
        return $user->can('delete_cliente');
    }

    /**
     * Determine whether the user can restore the client.
     */
    public function restore(User $user, Cliente $cliente): bool
    {
        return $user->can('restore_cliente');
    }

    /**
     * Determine whether the user can permanently delete the client.
     */
    public function forceDelete(User $user, Cliente $cliente): bool
    {
        return $user->can('force_delete_cliente');
    }
}
