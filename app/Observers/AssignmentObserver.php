<?php

namespace App\Observers;

use App\Models\Classroom;
use App\Models\Assignment;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AssignmentNotification;

class AssignmentObserver
{
    /**
     * Handle the Assignment "created" event.
     */
    public function created(Assignment $assignment): void
    {
        $classroom = Classroom::find($assignment->classroom_id);

        $participants = $classroom->participants()
            ->wherePivot('status', 'approved')->get();

        if ($participants->isEmpty()) {
            return;
        }

        Notification::send($participants, new AssignmentNotification($classroom, $assignment));
    }

    /**
     * Handle the Assignment "updated" event.
     */
    public function updated(Assignment $assignment): void
    {
        //
    }

    /**
     * Handle the Assignment "deleted" event.
     */
    public function deleted(Assignment $assignment): void
    {
        //
    }

    /**
     * Handle the Assignment "restored" event.
     */
    public function restored(Assignment $assignment): void
    {
        //
    }

    /**
     * Handle the Assignment "force deleted" event.
     */
    public function forceDeleted(Assignment $assignment): void
    {
        //
    }
}
