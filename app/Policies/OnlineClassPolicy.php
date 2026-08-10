<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OnlineClass;

class OnlineClassPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OnlineClass $announcement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OnlineClass $onlineClass): bool
    {
        // only teacher can update the class
        return $onlineClass->classroom->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OnlineClass $onlineClass): bool
    {
        // only teacher can delete the class
        return $onlineClass->classroom->user_id === $user->id;
    }
}
