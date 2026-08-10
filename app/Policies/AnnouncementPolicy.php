<?php

namespace App\Policies;

use App\Models\User;
use App\Action\Permission;
use App\Models\Announcement;

class AnnouncementPolicy
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
    public function view(User $user, Announcement $announcement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Permission::has('announcement.can_announce');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Announcement $announcement): bool
    {
        // if self announcement
        if ($announcement->user_id === $user->id) {
            return Permission::has('announcement.update_self_announcement');
        }

        return Permission::has('announcement.update_teacher_announcement');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        // if self announcement
        if ($announcement->user_id === $user->id) {
            return Permission::has('announcement.delete_self_announcement');
        }

        return Permission::has('announcement.delete_teacher_announcement');
    }
}
