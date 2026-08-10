<?php

namespace App\Observers;

use App\Models\Classroom;
use App\Models\Announcement;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AnnouncementNotification;

class AnnouncementObserver
{
    /**
     * Handle the Announcement "created" event.
     */
    public function created(Announcement $announcement): void
    {
        $classroom = Classroom::find($announcement->classroom_id);

        $participants = $classroom->participants()
            ->wherePivot('status', 'approved')->get();

        if ($participants->isEmpty()) {
            return;
        }

        Notification::send($participants, new AnnouncementNotification($classroom, $announcement));
    }
}
