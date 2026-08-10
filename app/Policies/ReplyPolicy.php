<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reply;
use App\Action\Permission;

class ReplyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reply $reply): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reply $reply): bool
    {
        return Permission::isTeacher() ||
            Permission::isModerator() ||
            $reply->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reply $reply): bool
    {
        return Permission::isTeacher() ||
            Permission::isModerator() ||
            $reply->user_id === $user->id;
    }
}
