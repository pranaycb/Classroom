<?php

namespace App\Observers;

use App\Action\Permission;
use App\Models\Submission;
use App\Notifications\NewSubmission;
use App\Notifications\SubmissionEvaluated;
use Illuminate\Support\Facades\Notification;

class SubmissionObserver
{
    /**
     * Handle the Submission "created" event.
     */
    public function created(Submission $submission): void
    {
        $assignment = $submission->assignment;
        $classroom = $assignment->classroom;
        $user = $submission->user;
        $moderators = $classroom->participants;

        $receivers = [];

        // add teacher
        $receivers[] = $classroom->teacher;

        // add moderators
        if($moderators->isNotEmpty()){
            foreach ($moderators as $moderator) {
                if (Permission::has('assignment.view_submissions', $moderator->id)) {
                    $receivers[] = $moderator;
                }
            }
        }

        Notification::send($receivers, new NewSubmission($user, $classroom, $assignment, $submission));
    }

    /**
     * Handle the Submission "updated" event.
     */
    public function updated(Submission $submission): void
    {
        if($submission->isDirty('marks')) {

            $user = $submission->user;
            $assignment = $submission->assignment;
            $classroom = $assignment->classroom;

            $user->notify(new SubmissionEvaluated($classroom, $assignment));
        }
    }
}
