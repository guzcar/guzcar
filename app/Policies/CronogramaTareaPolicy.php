<?php

namespace App\Policies;

use App\Models\CronogramaTarea;
use App\Models\User;

class CronogramaTareaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_cronograma::tarea');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CronogramaTarea $cronogramaTarea): bool
    {
        return $user->can('view_cronograma::tarea');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_cronograma::tarea');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CronogramaTarea $cronogramaTarea): bool
    {
        return $user->can('update_cronograma::tarea');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CronogramaTarea $cronogramaTarea): bool
    {
        return $user->can('delete_cronograma::tarea');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CronogramaTarea $cronogramaTarea): bool
    {
        return $user->can('restore_cronograma::tarea');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CronogramaTarea $cronogramaTarea): bool
    {
        return $user->can('force_delete_cronograma::tarea');
    }
}
