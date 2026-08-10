<?php

namespace App\Observers;

use App\Models\Classroom;
use Illuminate\Support\Facades\Storage;

class ClassroomObserver
{
    /**
     * Handle the Classroom "creating" event.
     */
    public function creating(Classroom $classroom): void
    {
        $classroom->moderator_permissions = Storage::json('permissions/moderator.json');
    }
}
