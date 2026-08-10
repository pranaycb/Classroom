<?php

namespace App\Http\Controllers\Dashboard\Classroom;

use Firebase\JWT\JWT;
use App\Models\Classroom;
use App\Action\Permission;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\OnlineClass;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\UploaderService;
use App\Http\Resources\SubmissionResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class SubmissionController extends ClassroomController implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('hasaccess:assignment.view_submissions', only: ['index']),
            new Middleware('hasaccess:assignment.update_marks', only: ['update']),
            new Middleware('hasaccess:assignment.delete_submission', only: ['delete']),
        ];
    }

    /**
     * Show all classes
     */
    public function index(Classroom $classroom, Assignment $assignment)
    {
        $submissions = $assignment->submissions()
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->onEachSide(0)
            ->withQueryString();

        $data = [
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'marks' => round($assignment->marks, 2),
            ],
            'submissions' => new SubmissionResource($submissions),
        ];

        return $this->render('Dashboard/Classroom/Assignment/Submission', $data);
    }

    /**
     * Store a created class record
     */
    public function store(Request $request, UploaderService $uploader, Classroom $classroom, Assignment $assignment)
    {
        abort_if(!now()->lt($assignment->due), 404);

        abort_if(!Permission::isStudent(), 401);

        $request->validate([
            'attachment' => [
                'required', 'file',
                'mimes:' . config('app.upload.mimetypes'),
                'max:' . config('app.upload.max'),
            ],
        ]);

        $attachment = $request->file('attachment');

        $submission = $assignment->submissions()->create([
            'user_id' => Auth::id(),
        ]);

        $file = $uploader->upload($attachment, $classroom->code, 'attachments');

        $submission->attachment()->create([
            'name' => $attachment->getClientOriginalName(),
            'file' => $file,
            'type' => $attachment->extension(),
            'size' => $attachment->getSize(),
        ]);

        return redirect()->back()->with('success', 'Assignment submitted successfully');
    }

    /**
     * Update a class
     */
    public function update(Request $request, Classroom $classroom, Assignment $assignment, Submission $submission)
    {
        $request->validate([
            'marks' => 'required|lte:' . $assignment->marks,
        ]);

        $submission->update([
            'marks' => $request->marks,
            'feedback' => $request->feedback,
        ]);

        return redirect()->back()->with('success', 'Submission evaluated successfully');
    }

    /**
     * Delete a class
     */
    public function destroy(Classroom $classroom, Assignment $assignment, Submission $submission)
    {
        $submission->delete();

        return redirect()->back()->with('success', 'Submission deleted successfully');
    }
}
